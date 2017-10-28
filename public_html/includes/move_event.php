<?php
/**
* glFusion CMS
*
* Calendar - Calendar Plugin for glFusion
*
* Move Event
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$sqlValues = '';

//COM_errorLog(print_r($_POST,true));


$eventID = isset($_POST['id']) ? COM_applyFilter($_POST['id'],true) : -1;
COM_errorLog("moving event " . $eventID);
if ( $eventID == -1 ) die;

// get the parent_id

$parentID = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.$eventID);
if ( $parentID == null ) die;

$repeats = DB_getItem($_TABLES['ac_events'],'repeats','event_id='.$eventID);

// we need to update the event date

$dt = new Date($_POST['date'],$_USER['tzid']);
$db_start_date = $dt->toUnix();

//$start_date = $_POST['date'];
//$db_start_date = DB_escapeString($start_date);

$sqlValues = "start='{$db_start_date}' ";

if ( isset($_POST['end'])) {
    $dt = new Date($_POST['end'],$_USER['tzid']);
    $db_end_date = $dt->toUnix();

//    $end_date = $_POST['end'];
//    $db_end_date = DB_escapeString($end_date);
    $sqlValues .= ", end='{$db_end_date}' ";
} else {
    $db_end_date = $db_start_date;
    $sqlValues .= ", end='{$db_end_date}' ";
}

// check if part of a series - if yes, then flag as an exception
if ( $repeats == 1 ) {
    $sqlValues .= ", exception=1 ";
}

$sql = "UPDATE {$_TABLES['ac_events']} SET ".$sqlValues." WHERE event_id=".$eventID;

DB_query($sql,1);

$retval = array();
$retval['errorCode'] = 0;
$retval['statusMessage'] = 'Event Moved';
$return["json"] = json_encode($retval);
echo json_encode($return);
?>