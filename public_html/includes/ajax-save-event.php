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


$retval = array();
$action = '';

if (isset($_POST['action'])) {
    $action = COM_applyFilter($_POST['action']);
}

switch ($action) {
    case 'new-event' : // save a new event
        $rc = saveNewEvent();
        break;
    case 'edit-event' : // save an edit
        $rc = saveEditEvent();
        break;
    case 'delete-event' : // delete a SINGLE event
        $rc = deleteEvent();
        break;

    default :
        $rc = -1;
        break;
}

$retval['errorCode'] = $rc;
$return["json"] = json_encode($retval);
echo json_encode($return);
exit;

/*
 * Saves a new event
 */
function saveNewEvent()
{
    global $_CONF, $_AC_CONF, $_USER, $_TABLES;

// initialize vars
    $errorCode = 0;
    $errors = 0;

// set defaults
    $repeats = 0;
    $repeats_freq = 0;
    $allday = 0;

// pull submitted data
    $title          = $_POST['title'];
    $start_date     = $_POST['event-date'];
    $end_date       = $_POST['event-end-date'];
    $start_time     = $_POST['start-time'];
    $end_time       = $_POST['end-time'];
    $location       = $_POST['location'];
    $description    = $_POST['description'];
    if (isset($_POST['repeats'])) {
        $repeats = $_POST['repeats'];
        $repeat_freq = $_POST['repeat-freq'];
    }
    if ( isset($_POST['event-allday'] ) ) {
        $allday = 1;
        $end_time = '24:00:00';
    }

// check / validate submitted data

    if ( $start_time == '' ) {
        $start_time = '00:00';
    }
    if ( $end_time == '' ) {
        $end_time = '24:00:00';
    }
    $start          = trim($start_date . " " . $start_time);
    $end            = trim($end_date . " " . $end_time);

    // validation checks

    if ( !agenda_validateDate($start_date, 'Y-m-d') ) {
COM_errorLog("start date failed validation");
        $errorCode = 1;
        $errors++;
    }

    if ( !agenda_validateDate($end_date, 'Y-m-d') ) {
COM_errorLog("end date failed validation");
        $errorCode = 1;
        $errors++;
    }

    if ( $errors ) {
        return $errorCode;
    }

    // let's do a little filtering here:

    $filter = new sanitizer();
    $filter->setPostmode('text');

    $description = $filter->filterText($description);
    $title = $filter->filterText($title);
    $location = $filter->filterText($location);

// now do our date conversions

    $dtStart = new Date($start,$_USER['tzid']);
    $dtEnd   = new Date($end,  $_USER['tzid']);

    if (!isset($_POST['repeats'])) {
        $repeats = 0;
        $repeat_freq = 0;

        // prepare vars for DB
        $db_title           = DB_escapeString($title);

$start_date = $dtStart->format('Y-m-d',true);
$start_time = $dtStart->format('H:i',true);


        $db_start_date      = DB_escapeString($start_date);
        $db_start_time      = DB_escapeString($start_time);

$end_date = $dtEnd->format('Y-m-d',true);
$end_time = $dtEnd->format('H:i',true);


        $db_end_date        = DB_escapeString($end_date);
        $db_end_time        = DB_escapeString($end_time);

        $db_start = $dtStart->toUnix(false);
        $db_end   = $dtEnd->toUnix(false);

        $db_location        = DB_escapeString($location);
        $db_description     = DB_escapeString($description);

        // save parent event
        $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,location,description,allday,start_date,start_time,end_date,end_time,repeats,repeat_freq ) ";
        $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},'{$db_start_date}','{$db_start_time}','{$db_end_date}','{$db_end_time}',$repeats,$repeat_freq)";

        $result = DB_query($sql,1);

        if ( DB_error() ) {
            $errorCode = 1;
        } else {
            $parent_id = DB_insertId($result);
            // save child events
            $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,location,description,allday,repeats,start,end,parent_id) ";
            $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},{$repeats},'{$db_start}','{$db_end}',$parent_id)";

            DB_query($sql,1);

            if ( DB_error() ) $errorCode = 2;
        }
    } else {
        $repeats = $_POST['repeats'];
        $repeat_freq = $_POST['repeat-freq'];

        $future = 365;
        if ( $repeat_freq == 365 ) $future = 3650;

        $until = ($future/$repeat_freq);

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

        $dtStart = new Date($start,$_USER['tzid']);
        $dtEnd   = new Date($end,  $_USER['tzid']);
        $db_start = $dtStart->toUnix(false);
        $db_end   = $dtEnd->toUnix(false);

        $db_location = DB_escapeString($location);
        $db_description = DB_escapeString($description);

        // save parent event
        $sql = "INSERT INTO {$_TABLES['ac_event']} ( title,location,description,allday,start_date,start_time,end_date,end_time,repeats,repeat_freq ) ";
        $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},'{$db_start_date}','{$db_start_time}','{$db_end_date}','{$db_end_time}',$repeats,$repeat_freq)";

        $result = DB_query($sql);
        $parent_id = DB_insertId($result);

        // insert the initial event
        $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,location,description,allday,repeats,start,end,parent_id) ";
        $sql .= "VALUES ('{$db_title}','{$db_location}','{$db_description}',{$allday},{$repeats},'{$db_start}','{$db_end}',$parent_id)";
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

            $dtStart = new Date($start,$_USER['tzid']);
            $dtEnd   = new Date($end,  $_USER['tzid']);
            $db_start = $dtStart->toUnix(false);
            $db_end   = $dtEnd->toUnix(false);

            $sql =  "INSERT INTO {$_TABLES['ac_events']} (title,allday,repeats,start,end,parent_id) ";
            $sql .= "VALUES ('{$db_title}',{$allday},{$repeats},'{$db_start}','{$db_end}',$parent_id)";
            DB_query($sql);
        }
    }

    return $errorCode;
}

function saveEditEvent()
{
    global $_CONF, $_AC_CONF, $_USER, $_TABLES;

    $errorCode = 0;
    $errors = 0;

    $parent_id      = (int) COM_applyFilter($_POST['parent_id'],true);
    $event_id       = (int) COM_applyFilter($_POST['event_id'],true);

    $title          = $_POST['title'];
    $start_date     = $_POST['event-date'];
    $end_date       = $_POST['event-end-date'];
    $start_time     = $_POST['start-time'];
    $end_time       = $_POST['end-time'];
    $location       = $_POST['location'];
    $description    = $_POST['description'];
    if ( isset($_POST['event-allday'] ) ) {
        $allday = 1;
    } else {
        $allday = 0;
    }

    if ( $allday == 1 ) {
        $start_time = '';
        $end_time = '';
    }
    if ( $start_time == '' ) {
        $start_time = '00:00:00';
    }
    if ( $end_time == '' ) {
        $end_time = '24:00:00';
    }
    if ( $end_date == '' || empty($end_date)) {
        $end_date = $start_date;
    }
    $start          = $start_date . " " . $start_time;
    $end            = $end_date . " " . $end_time;

    // validation checks

    if ( !agenda_validateDate($start_date, 'Y-m-d') ) {
        $errorCode = 1;
        $errors++;
    }

    if ( !agenda_validateDate($end_date, 'Y-m-d') ) {
        $errorCode = 1;
        $errors++;
    }

    if ( $errors ) {
        return $errorCode;
    }

    $repeats = 0;
    $repeat_freq = 0;

    $filter = new sanitizer();
    $filter->setPostmode('text');
    $description = $filter->filterText($description);
    $title = $filter->filterText($title);
    $location = $filter->filterText($location);

    $dtStart = new Date($start,$_USER['tzid']);
    $dtEnd   = new Date($end,  $_USER['tzid']);
    $db_start = $dtStart->toUnix(false);
    $db_end   = $dtEnd->toUnix(false);


    // prepare vars for DB
    $db_title           = DB_escapeString($title);
    $db_start_date      = DB_escapeString($start_date);
    $db_end_date        = DB_escapeString($end_date);
    $db_start_time      = DB_escapeString($start_time);
    $db_end_time        = DB_escapeString($end_time);
//    $db_start           = DB_escapeString($start);
//    $db_end             = DB_escapeString($end);

    $db_location = DB_escapeString($location);
    $db_description = DB_escapeString($description);

    // update parent event
    // save parent event
    $sql = "UPDATE {$_TABLES['ac_event']} SET
        title = '{$db_title}',
        location = '{$db_location}',
        description = '{$db_description}',
        allday = {$allday},
        start_date = '{$db_start_date}',
        start_time = '{$db_start_time}',
        end_date   = '{$db_end_date}',
        end_time = '{$db_end_time}',
        repeats = {$repeats},
        repeat_freq = {$repeat_freq}";
    $sql .= " WHERE parent_id=".(int) $parent_id;

    $result = DB_query($sql,1);

    if ( DB_error() ) {
        $errorCode = 1;
    } else {
        $sql =  "UPDATE {$_TABLES['ac_events']} SET
            title = '{$db_title}',
            location = '{$db_location}',
            description = '{$db_description}',
            allday = {$allday},
            repeats = {$repeats},
            start = '{$db_start}',
            end = '{$db_end}' ";
        $sql .= " WHERE event_id=".(int) $event_id . " AND parent_id = " . (int) $parent_id;

        DB_query($sql,1);

        if ( DB_error() ) $errorCode = 2;
    }
    return $errorCode;
}


// deletes a single event
function deleteEvent()
{
    global $_CONF, $_AC_CONF, $_TABLES;

    $parent_id      = (int) COM_applyFilter($_POST['parent_id'],true);
    $event_id       = (int) COM_applyFilter($_POST['event_id'],true);

    $sql = "DELETE FROM {$_TABLES['ac_events']} WHERE event_id=".(int) $event_id . " AND parent_id=".(int) $parent_id;
    DB_query($sql);
    $sql = "DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $parent_id;
    DB_query($sql);

    return 0;
}


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

    $dt = new DateTime($date);

    $addInterval = "P" . $interval . "M";

    $oldDay = $dt->format("d");
    $dt->add(new DateInterval($addInterval));
    $newDay = $dt->format("d");

    if($oldDay != $newDay) {
        $dt->sub(new DateInterval("P" . $newDay . "D"));
    }

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