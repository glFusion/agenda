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

COM_errorLog(print_r($_POST,true));


$eventID = isset($_POST['id']) ? COM_applyFilter($_POST['id'],true) : -1;
COM_errorLog("moving event " . $eventID);
if ( $eventID == -1 ) die;

// get the parent_id

$parentID = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.$eventID);
if ( $parentID == null ) die;

$repeats = DB_getItem($_TABLES['ac_events'],'repeats','event_id='.$eventID);

$allday = 0;
if ( isset($_POST['allday'])) {
    switch ($_POST['allday']) {
        case 'true' :
            $allday = 1;
            break;
        default :
            $allday = 0;
            break;
    }
}

COM_errorLog("All day is " . $allday);

// we need to update the event date

$dt = new Date($_POST['date'],$_USER['tzid']);
$db_start_date = $dt->toUnix();

$sqlValues = "start='{$db_start_date}' ";

$sqlValues .= ",allday=".(int) $allday . " ";

if ( isset($_POST['end'])) {
    $dt = new Date($_POST['end'],$_USER['tzid']);
    $db_end_date = $dt->toUnix();
    $sqlValues .= ", end='{$db_end_date}' ";
} else {
    $db_end_date = $db_start_date;
    $sqlValues .= ", end='{$db_end_date}' ";
}

// check if part of a series - if yes, then flag as an exception
if ( $repeats == 1 ) {
    $sqlValues .= ", exception=1 ";
}

// set my all day dates
if ( $allday ) {
    COM_errorLog("in all day processing");
    if ( isset($_POST['date'])) {
        $start_date = COM_applyFilter($_POST['date']);
    }
    if ( isset($_POST['end'])) {
        $end_date = COM_applyFilter($_POST['end']);
    } else {
        $end_date = $start_date;
    }
    $db_start_date_ad = DB_escapeString($start_date);
    if ( $start_date != $end_date ) {
        $db_end_date_ad = DB_escapeString(date('Y-m-d', strtotime('-1 day', strtotime($end_date))));
    } else {
        $db_end_date_ad = $db_start_date_ad;
    }
    $sqlValues .= ", start_date='{$db_start_date_ad}', end_date='{$db_end_date_ad}' ";
}

$sql = "UPDATE {$_TABLES['ac_events']} SET ".$sqlValues." WHERE event_id=".$eventID;
COM_errorLog($sql);
DB_query($sql,1);

$retval = array();
$retval['errorCode'] = 0;
$retval['statusMessage'] = 'Event Moved';
$return["json"] = json_encode($retval);
echo json_encode($return);
?>