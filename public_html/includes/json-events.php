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

// dates to use for our query
$startDisplayUnix   = strtotime($start . ' 00:00:00');
$endDisplayUnix     = strtotime($end   . ' 24:00:00');

$events = array();

$dt = new Date('now',$_USER['tzid']);

$sql = "SELECT * FROM {$_TABLES['ac_events']} AS events LEFT JOIN {$_TABLES['ac_event']} AS event ON events.parent_id=event.parent_id WHERE events.start BETWEEN '{$startDisplayUnix}' AND '{$endDisplayUnix}'";

$result = DB_query($sql);
while ( $row = DB_fetchArray($result) ) {
    if ( $row['allday'] ) {
        $dt->setTimestamp($row['start']);
        $eventArray['when'] =  $dt->format($_CONF['dateonly'], true);
        $eventArray['start'] = $dt->toISO8601(true);
        $dt->setTimestamp($row['end']);
        $eventArray['end'] = $dt->toISO8601(true);
        $dt->setTimestamp(strtotime($row['end_date'].' 23:59:00'));
        if ( $eventArray['when'] != $dt->format($_CONF['dateonly'], true)) {
            $eventArray['when'] .= ' to ' . $dt->format($_CONF['dateonly'], true);
        }
    } else {
        $dt->setTimestamp($row['start']);
        $tStartDate = $dt->format($_CONF['daytime'], true);
        $eventArray['start'] = $dt->toISO8601(true);
        $dt->setTimestamp($row['end']);
        $tEndDate = $dt->format($_CONF['timeonly'], true);
        $eventArray['when'] = $tStartDate . ' to ' . $tEndDate;
        $eventArray['end'] = $dt->toISO8601(true);
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