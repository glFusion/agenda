<?php
/**
* glFusion CMS
*
* Calendar - Calendar Plugin for glFusion
*
* Delete Event
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$eventID = isset($_POST['id']) ? COM_applyFilter($_POST['id'],true) : -1;
COM_errorLog("deleting event " . $eventID);
if ( $eventID == -1 ) die;

// get the parent_id

$parentID = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.$eventID);
if ( $parentID == null ) die;

DB_query("DELETE FROM {$_TABLES['ac_events']} WHERE parent_id=".$parentID);

// now check to see if other events are tied to this one

$count = DB_count($_TABLES['ac_events'],'parent_id',$parentID);
if ( $count == 0 ) {
    DB_query("DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".$parentID);
}

$retval = array();
$retval['errorCode'] = 0;
$retval['statusMessage'] = 'Event Deleted';
$return["json"] = json_encode($retval);
echo json_encode($return);
?>