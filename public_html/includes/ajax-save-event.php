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
$errors    = 0;

// we need to account for new events and updated events.
// probably need an 'exception' flag for events that have been
// moved outside of the recurrence pattern. For example.
//   Out of recurrence pattern:
//     - deleted - not a problem won't be there to udate.
//     - moved - we would skip any edits for edit
//     - if we delete the master - we delete all the exceptions too.



$title          = $_POST['title'];
$start_date     = $_POST['event-date'];
$end_date       = $_POST['event-end-date'];
$weekday        = date('N', strtotime($start_date));
$start_time     = $_POST['start-time'];
$end_time       = $_POST['end-time'];

if ( $start_time == '' ) {
    $start_time = '00:00';
}
if ( $end_time == '' ) {
    $end_time = '00:00';
}

$start          = $start_date . " " . $start_time;
$end            = $end_date . " " . $end_time;

$location       = $_POST['location'];
$description    = $_POST['description'];




// validation
/*
if ( !agenda_validateDate($start_date, 'Y-m-d') ) {
    $errorCode = 1;
    $errors++;
}

if ( !agenda_validateDate($end_date, 'Y-m-d') ) {
    $errorCode = 1;
    $errors++;
}

if ( $errors ) {
    $retval = array();
    $retval['errorCode'] = $errorCode;
    $retval['statusMessage'] = 'Error with date';
    $return["json"] = json_encode($retval);
    echo json_encode($return);
    die;
}
*/

if (!isset($_POST['repeats'])) {
    $repeats = 0;
    $repeat_freq = 0;

    // prepare vars for DB
    $db_title = DB_escapeString($title);
    $db_start_date = DB_escapeString($start_date);
    $db_end_date = DB_escapeString($end_date);
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
    $db_location = DB_escapeString($location);
    $db_description = DB_escapeString($description);

    // save parent event
    $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,location,description,allday,start_date,start_time,end_time,repeats,repeat_freq ) ";
    $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},'{$db_start_date}','{$db_start_time}','{$db_end_time}',$repeats,$repeat_freq)";

    $result = DB_query($sql,1);

    if ( DB_error() ) {
        $errorCode = 1;
    } else {
        $parent_id = DB_insertId($result);
        // save child events
        $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,location,description,allday,start,end,parent_id) ";
        $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday}, '{$db_start}','{$db_end}',$parent_id)";

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
    $db_start_time = DB_escapeString($start_time);

    $db_end_date = DB_escapeString($end_date);
    $db_end_time = DB_escapeString($end_time);

// full date / time
    $db_start = DB_escapeString($start);
    $db_end = DB_escapeString($end);

    $db_location = DB_escapeString($location);
    $db_description = DB_escapeString($description);

    // save parent event
    $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,location,description,allday,start_date,start_time,end_time,repeats,repeat_freq ) ";
    $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},'{$db_start_date}','{$db_start_time}','{$db_end_time}',$repeats,$repeat_freq)";

    $result = DB_query($sql);
    $parent_id = DB_insertId($result);

    // insert the initial event
    $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,location,description,allday,start,end,parent_id) ";
    $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},'{$db_start}','{$db_end}',$parent_id)";
    DB_query($sql);

// now do the recurrence

    if ( $end_date === '' ) $end_date = $start_date;
    $orig_start_date = $start_date;
    $orig_end_date   = $end_date;

    switch ( $repeat_freq) {
        case 1 : // daily
            $toInsert = 365;
            $function = 'agendaAddDay';
            break;

        case 7 : // weekly
            $toInsert = 52;
            $function = 'agendaAddWeek';
            break;

        case 14 : // bi weekly
            $toInsert = 26;
            $function = 'agendaAddTwoWeek';
            break;

        case 30 : // monthly
            $toInsert = 12;
            $function = 'agendaAddMonth';
            break;

        case 365 : // yearly
            $toInsert = 5;
            $function = 'agendaAddYear';
            break;
    }

    for ( $x = 1; $x <= $toInsert; $x++ ) {
        $start_date = $function($orig_start_date,$x);
        $end_date   = $function($orig_end_date,$x);

        $start          = $start_date . " " . $start_time;
        $end            = $end_date . " " . $end_time;

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



function agendaAddDay( $date, $interval = 1 ) {

    $dt = new DateTime($date);

    $addInterval = "P" . $interval . "D";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    return $dt->format("Y-m-d");
}


function agendaAddWeek( $date, $interval = 1 ) {

    $dt = new DateTime($date);

    $addInterval = "P" . $interval . "W";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    return $dt->format("Y-m-d");
}

function agendaAddTwoWeek( $date, $interval = 1 ) {

    $dt = new DateTime($date);

    $addInterval = "P" . $interval * 2 . "W";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    return $dt->format("Y-m-d");
}


function agendaAddMonth( $date, $interval = 1 ) {
COM_errorLog("in agendaAddMonth");
    $dt = new DateTime($date);

    $addInterval = "P" . $interval . "M";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    $newDay = $dt->format("d");

    if($oldDay != $newDay) {
        $dt->sub(new DateInterval("P" . $newDay . "D"));
    }
COM_errorLog($dt->format('Y-m-d'));
    return $dt->format("Y-m-d");
}

function agendaAddYear( $date, $interval = 1 ) {

    $dt = new DateTime($date);

    $addInterval = "P" . $interval . "Y";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    $newDay = $dt->format("d");

    if($oldDay != $newDay) {
        $dt->sub(new DateInterval("P" . $newDay . "D"));
    }
    return $dt->format("Y-m-d");
}

?>