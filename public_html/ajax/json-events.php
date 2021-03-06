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
*  Copyright (C) 2016-2019 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

if ( !COM_isAjax() ) die;

// use \glFusion\Cache\Cache;

if ( !isset($_VARS['agenda_maintenance']) || $_VARS['agenda_maintenance'] < (time() - $_AC_CONF['maintenance_check_frequency']) ) {
    AC_eventMaintenance();
}

$filter = new sanitizer();

$start = DB_escapeString($_GET['start']);
$end   = DB_escapeString($_GET['end']);

// dates to use for our query
if ( strstr($start,'T') === false ) {
    $sText = $start . ' 00:00:00';
    $eText = $end   . ' 24:00:00';
} else {
    $start = substr($start,0,strpos($start,'T'));
    $end   = substr($end,0,strpos($end,'T'));
    $sText = $start . ' 00:00:00';
    $eText = $end   . ' 24:00:00';
}

$dtStart = new \Date($sText,$_CONF['timezone']);
$dtEnd   = new \Date($eText,$_CONF['timezone']);

$startDisplayUnix = DB_escapeString($dtStart->toUnix());
$endDisplayUnix   = DB_escapeString($dtEnd->toUnix());

$events = array();

$dt = new Date('now',$_USER['tzid']);

$sql = "SELECT *
        FROM {$_TABLES['ac_events']} AS e LEFT JOIN {$_TABLES['ac_category']} AS c ON e.category=c.category_id
        WHERE (start BETWEEN '{$startDisplayUnix}' AND '{$endDisplayUnix}')
        OR (end BETWEEN '{$startDisplayUnix}' AND '{$endDisplayUnix}')
        ";

/*
$sqlkey = 'agenda__'.md5($sql);
$c = Cache::getInstance();
$eventDataJSON = $c->get($sqlkey);
if ( $eventDataJSON !== null ) {
    echo $eventDataJSON;
    exit;
}
*/
$result = DB_query($sql);
while ( $row = DB_fetchArray($result) ) {
    if ( $row['allday'] ) {
        $acStartDate = $row['start_date'];
        $acStartTime = '00:00:00';
        $acEndDate = $row['end_date'];
        $acEndTime = '24:00:00';
        $eventArray['start'] = $acStartDate.'T'.$acStartTime;
        $eventArray['end']   = $acEndDate.'T'.$acEndTime;
        $dt->setTimestamp(strtotime($acStartDate.' '.$acStartTime));
        $eventArray['when'] =  $dt->format('l d-M-Y', false);
        $dt->setTimestamp(strtotime($acEndDate. ' ' . '23:00:00'));
        if ( $row['start_date'] != $row['end_date']) {
            $eventArray['when'] .= ' to ' . $dt->format('l d-M-Y', false);
        }
    } else {
        $dt->setTimestamp($row['start']);
        $cmpStart = $dt->format('Ymd',true);
        $tStartDate = $dt->format("l   d-M-Y", true);
        $tStartTime = $dt->format("h:i a", true);
        $eventArray['start'] = $dt->toISO8601(true);
        $dt->setTimestamp($row['end']);
        $cmpEnd = $dt->format('Ymd',true);
        $tEndDateFormat = "h:i a";
        if ( $cmpStart != $cmpEnd ) {
            $tEndDateFormat = "l d-M-Y h:i a";
        }
        $tEndDate = $dt->format($tEndDateFormat, true);
        $eventArray['when'] = $tStartDate .'<br>' . $tStartTime .' to ' . $tEndDate;
        $eventArray['end'] = $dt->toISO8601(true);
    }

// set permissions for the event

    if (SEC_hasRights('agenda.admin')) {
        $eventArray['editable'] = true;
    } else {
        $eventArray['editable'] = false;
    }

    $newdescription = AC_truncate($filter->censor($row['description']), 250, '...');

    $eventArray['id']           = $row['event_id'];
    $eventArray['parent_id']    = $row['parent_id'];
    $eventArray['allDay']       = ($row['allday'] == 1 ? true : false );
    $eventArray['title']        = $filter->censor(strip_tags(htmlspecialchars_decode($row['title'])));
    $eventArray['location']     = $filter->censor(strip_tags($row['location']));
    $eventArray['description']  = nl2br($newdescription);
    $eventArray['repeats']      = $row['repeats'];
    $eventArray['backgroundColor'] = $row['bgcolor'];
    $eventArray['textColor']       = $row['fgcolor'];
    $eventArray['exception']    = $row['exception'];

    $events[]                   = $eventArray;
/*
    $eventDataJSON = json_encode($events);
    $c->set($sqlkey,$eventDataJSON,'agenda_sql');
*/
}
echo json_encode($events);
?>