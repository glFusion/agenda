<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Add Event
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$errorCode = 0;

$title          = $_POST['title'];
$start_date     = $_POST['event-date'];
$weekday        = date('N', strtotime($start_date));
$start_time     = $_POST['start-time'];
$end_time       = $_POST['end-time'];
$start          = $start_date . " " . $start_time;
$end            = $start_date . " " . $end_time;

if ( $start_time == '' ) {
    $start_time = '00:00';
}
if ( $end_time == '' ) {
    $end_time = '00:00';
}


if (!isset($_POST['repeats'])) {
    $repeats = 0;
    $repeat_freq = 0;

    // prepare vars for DB
    $db_title = DB_escapeString($title);
    $db_start_date = DB_escapeString($start_date);
    $db_weekday = DB_escapeString($weekday);
    $db_start_time = DB_escapeString($start_time);
    $db_end_time = DB_escapeString($end_time);
    $db_start = DB_escapeString($start);
    $db_end = DB_escapeString($end);
    if ( isset($_POST['event-allday'] ) ) {
        $allday = 1;
    } else {
        $allday = 0;
    }


    // save parent event
    $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,start_date,start_time,end_time,weekday,repeats,repeat_freq ) ";
    $sql .= "VALUES ('{$db_title}','{$db_start_date}','{$db_start_time}','{$db_end_time}','{$db_weekday}',$repeats,$repeat_freq)";

    $result = DB_query($sql,1);

    if ( DB_error() ) {
        $errorCode = 1;
    } else {
        $parent_id = DB_insertId($result);

        // save child events
        $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,allday,start,end,parent_id) ";
        $sql .= "VALUES ('{$db_title}',{$allday}, '{$db_start}','{$db_end}',$parent_id)";

        DB_query($sql,1);

        if ( DB_error() ) $errorCode = 2;
    }
} else {
    $repeats = $_POST['repeats'];
    $repeat_freq = $_POST['repeat-freq'];

    $future = 365;
    if ( $repeat_freq == 365 ) $future = 3650;

    $until = ($future/$repeat_freq);
    if ($repeat_freq == 1) {
        $weekday = 0;
    }

    if ( isset($_POST['event-allday'] ) ) {
        $allday = 1;
    } else {
        $allday = 0;
    }

    // prepare vars for DB
    $db_title = DB_escapeString($title);
    $db_start_date = DB_escapeString($start_date);
    $db_weekday = DB_escapeString($weekday);
    $db_start_time = DB_escapeString($start_time);
    $db_end_time = DB_escapeString($end_time);
    $db_start = DB_escapeString($start);
    $db_end = DB_escapeString($end);

    // save parent event
    $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,start_date,start_time,end_time,weekday,repeats,repeat_freq ) ";
    $sql .= "VALUES ('{$db_title}','{$db_start_date}','{$db_start_time}','{$db_end_time}','{$db_weekday}',$repeats,$repeat_freq)";

    $result = DB_query($sql);
    $parent_id = DB_insertId($result);

    // insert the initial event
    $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,allday,start,end,parent_id) ";
    $sql .= "VALUES ('{$db_title}',{$allday},'{$db_start}','{$db_end}',$parent_id)";
    DB_query($sql);

    for($x = 0; $x < $until; $x++){

        $start_date = strtotime($start . '+' . $repeat_freq . 'DAYS');
        $end_date   = strtotime($end . '+' . $repeat_freq . 'DAYS');
        $start      = date("Y-m-d H:i", $start_date);
        $end        = date("Y-m-d H:i", $end_date);

        $db_start_date = DB_escapeString($start_date);
        $db_end_date = DB_escapeString($end_date);
        $db_start    = DB_escapeString($start);
        $db_end      = DB_escapeString($end);

        $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,allday,start,end,parent_id) ";
        $sql .= "VALUES ('{$db_title}',{$allday},'{$db_start}','{$db_end}',$parent_id)";
        DB_query($sql);
    }

}

$retval = array();

$retval['errorCode'] = $errorCode;
$retval['statusMessage'] = 'New Event Saved';

$return["json"] = json_encode($retval);
echo json_encode($return);

?>