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

if ( !COM_isAjax() ) die;

$start = DB_escapeString($_GET['start']);
$end   = DB_escapeString($_GET['end']);

// dates to use for our query
if ( strstr($start,'T') === false ) {
    $startDisplayUnix   = strtotime($start . ' 00:00:00');
    $endDisplayUnix     = strtotime($end   . ' 24:00:00');
} else {
    $startDisplayUnix = strtotime($start);
    $endDisplayUnix   = strtotime($end);
}

$events = array();

$dt = new Date('now',$_USER['tzid']);

$sql = "SELECT * FROM {$_TABLES['ac_events']}  WHERE start BETWEEN '{$startDisplayUnix}' AND '{$endDisplayUnix}'";

$result = DB_query($sql);
while ( $row = DB_fetchArray($result) ) {
    if ( $row['allday'] ) {
        $acStartDate = $row['start_date'];
        $acStartTime = '00:00:00';
        $acEndDate = $row['end_date'];
        $acEndTime = '24:00:00';

// send start at 0 - end at 24:00 to ensure it spans the full days on calendar
        $eventArray['start'] = $acStartDate.'T'.$acStartTime;
        $eventArray['end']   = $acEndDate.'T'.$acEndTime;

// set the pop-up when info - NO TIMEZONE support needed for this.
        $dt->setTimestamp(strtotime($acStartDate.' '.$acStartTime));
        $eventArray['when'] =  $dt->format('l d-M-Y', false);

        $dt->setTimestamp(strtotime($acEndDate. ' ' . '23:00:00'));
        $eventArray['when'] .= ' to ' . $dt->format('l d-M-Y', false);
    } else {
        $dt->setTimestamp($row['start']);
        $tStartDate = $dt->format("l   d-M-Y", true);
        $tStartTime = $dt->format("h:i a", true);
        $eventArray['start'] = $dt->toISO8601(true);
        $dt->setTimestamp($row['end']);
        $tEndDate = $dt->format("h:i a", true);
        $eventArray['when'] = $tStartDate .'<br>' . $tStartTime . ' to ' . $tEndDate;

        $eventArray['end'] = $dt->toISO8601(true);
    }

// set permissions for the event

    if (SEC_inGroup('Agenda Admin')) {
        $eventArray['editable'] = true;
    } else {
        $eventArray['editable'] = false;
    }

    $eventArray['id']           = $row['event_id'];
    $eventArray['parent_id']    = $row['parent_id'];
    $eventArray['allDay']       = ($row['allday'] == 1 ? true : false );
    $eventArray['title']        = strip_tags(htmlspecialchars_decode($row['title']));
    $eventArray['location']     = strip_tags($row['location']);
    $eventArray['description']  = nl2br($row['description']);
    $eventArray['repeats']      = $row['repeats'];
    $events[]                   = $eventArray;
}
echo json_encode($events);
?>