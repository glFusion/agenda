<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Retrieve events
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

//if ( !COM_isAjax() ) die;

$start = DB_escapeString($_GET['start']);
$end   = DB_escapeString($_GET['end']);

$events = array();

$sql = "SELECT * FROM {$_TABLES['ac_events']} WHERE `start` BETWEEN '{$start}' AND '{$end}'";

$result = DB_query($sql);
while ( $row = DB_fetchArray($result) ) {
    $eventArray['id']           = $row['event_id'];
    $eventArray['parent_id']    = $row['parent_id'];
    $eventArray['allDay']       = ($row['allday'] == 1 ? true : false );
    $eventArray['title']        = stripslashes($row['title']);
    $eventArray['start']        = $row['start'];
    $eventArray['end']          = $row['end'];
    $events[]                   = $eventArray;
}
echo json_encode($events);
?>