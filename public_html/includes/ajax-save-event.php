<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Event save / edit / delete controller
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
    case 'edit-event-series' : // save an event series edit
        $rc = saveEditEventSeries();
        break;
    case 'delete-event' : // delete a SINGLE event
        $rc = deleteEvent();
        break;

    case 'delete-event-series' :
        $rc = deleteEventSeries();
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
    $repeat_freq = 0;
    $allday = 0;
    $queued = 0;

// parse submitted data
    $title          = $_POST['title'];
    $start_date     = $_POST['event-date'];
    $end_date       = $_POST['event-end-date'];
    $start_time     = $_POST['start-time'];
    $end_time       = $_POST['end-time'];
    $location       = $_POST['location'];
    $description    = $_POST['description'];
    if (isset($_POST['repeats'])) {
        $repeats     = 1;
        $repeat_freq = COM_applyFilter($_POST['repeat-freq'],true);
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

    $start_time = date("H:i", strtotime($start_time));
    $end_time   = date("H:i", strtotime($end_time));

    // create the full start / end time for the event
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

    // initilize the date objects for start / end

    $dtStart = new Date($start,$_USER['tzid']);
    $dtEnd   = new Date($end,  $_USER['tzid']);

    if ($repeats == 0 ) {   // non-repeating event
        // create the unix timestamp start / end dates

        $db_start = $dtStart->toUnix(false);
        $db_end   = $dtEnd->toUnix(false);

        $db_start_date = DB_escapeString($start_date);
        $db_end_date   = DB_escapeString($end_date);

        // escape out the other ones
        $db_title           = DB_escapeString($title);
        $db_location        = DB_escapeString($location);
        $db_description     = DB_escapeString($description);

        $queued = 0;

        if ( COM_isAnonUser() ) {
            $owner_id = 1;
        } else {
            $owner_id = $_USER['uid'];
        }

        $sql = "INSERT INTO {$_TABLES['ac_event']} (
                title,
                location,
                description,
                allday,
                start_date,
                end_date,
                start,
                end,
                repeats,
                repeat_freq,
                queued,
                owner_id ) ";

        $sql .= " VALUES (
            '{$db_title}',
            '{$db_location}',
            '{$db_description}',
            '{$allday}',
            '{$db_start_date}',
            '{$db_end_date}',
            '{$db_start}',
            '{$db_end}',
            '{$repeats}',
            '{$repeat_freq}',
            '{$queued}',
            '{$owner_id}' )";

        $result = DB_query($sql,1);

        if ( DB_error() ) {
            $errorCode = 1;
        } else {
            $parent_id = DB_insertId($result);

            $sql = "INSERT INTO {$_TABLES['ac_events']}
                (   parent_id,
                    title,
                    location,
                    description,
                    allday,
                    start_date,
                    end_date,
                    start,
                    end,
                    repeats,
                    repeat_freq,
                    exception,
                    owner_id ) ";
            $sql .= "VALUES (
                 $parent_id,
                '{$db_title}',
                '{$db_location}',
                '{$db_description}',
                 {$allday},
                '{$db_start_date}',
                '{$db_end_date}',
                 $db_start,
                 $db_end,
                 $repeats,
                 $repeat_freq,
                 0,
                 $owner_id )";

            DB_query($sql,1);

            if ( DB_error() ) {
                $errorCode = 2;
            }
        }
    } else {
        // repeating events
        $future = 365;
        if ( $repeat_freq == 365 ) $future = 3650;

        $until = ($future/$repeat_freq);

        if ( isset($_POST['event-allday'] ) ) {
            $allday = 1;
        } else {
            $allday = 0;
        }

        $db_start = $dtStart->toUnix(false);
        $db_end   = $dtEnd->toUnix(false);

        $db_start_date = DB_escapeString($start_date);
        $db_end_date   = DB_escapeString($end_date);

        // prepare vars for DB
        $db_title = DB_escapeString($title);
        $db_location = DB_escapeString($location);
        $db_description = DB_escapeString($description);

        $queued = 0;

        if ( COM_isAnonUser() ) {
            $owner_id = 1;
        } else {
            $owner_id = $_USER['uid'];
        }
        // save the parent event
        $sql = "INSERT INTO {$_TABLES['ac_event']} (
                title,
                location,
                description,
                allday,
                start_date,
                end_date,
                start,
                end,
                repeats,
                repeat_freq,
                queued,
                owner_id ) ";

        $sql .= " VALUES (
            '{$db_title}',
            '{$db_location}',
            '{$db_description}',
            '{$allday}',
            '{$db_start_date}',
            '{$db_end_date}',
            '{$db_start}',
            '{$db_end}',
            '{$repeats}',
            '{$repeat_freq}',
            '{$queued}',
            '{$owner_id}' )";

        $result = DB_query($sql,1);

        if ( DB_error() ) {
            $errorCode = 1;
        }

        $parent_id = DB_insertId($result);

        // save the initial event
        $sql = "INSERT INTO {$_TABLES['ac_events']}
            (   parent_id,
                title,
                location,
                description,
                allday,
                start_date,
                end_date,
                start,
                end,
                repeats,
                repeat_freq,
                exception,
                owner_id ) ";
        $sql .= "VALUES (
             $parent_id,
            '{$db_title}',
            '{$db_location}',
            '{$db_description}',
             {$allday},
            '{$db_start_date}',
            '{$db_end_date}',
             $db_start,
             $db_end,
             $repeats,
             $repeat_freq,
             0,
             $owner_id )";

        DB_query($sql,1);

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

            // add the start_date_ad / end_date_ad
            $db_start_date = DB_escapeString($dtStart->format('Y-m-d',true));
            $db_end_date   = DB_escapeString($dtEnd->format('Y-m-d',true));

            $sql = "INSERT INTO {$_TABLES['ac_events']}
                (   parent_id,
                    title,
                    location,
                    description,
                    allday,
                    start_date,
                    end_date,
                    start,
                    end,
                    repeats,
                    repeat_freq,
                    exception,
                    owner_id ) ";
            $sql .= "VALUES (
                 $parent_id,
                '{$db_title}',
                '{$db_location}',
                '{$db_description}',
                 {$allday},
                '{$db_start_date}',
                '{$db_end_date}',
                 $db_start,
                 $db_end,
                 $repeats,
                 $repeat_freq,
                 0,
                 $owner_id )";

            DB_query($sql,1);
        }
    }
    PLG_itemSaved($parent_id, 'agenda');
    CACHE_remove_instance('agenda');

    return $errorCode;
}

//
// saves an edited event
//
//  rules - a non-recurring event cannot be changed to a recurring event and vice versa
//
// this only saves a single, non-recurring event
//
function saveEditEvent()
{
    global $_CONF, $_AC_CONF, $_USER, $_TABLES;

    $errorCode = 0;
    $errors = 0;

// set defaults
    $allday = 0;

    // get the parentid and the event id

    $parent_id      = (int) COM_applyFilter($_POST['parent_id'],true);
    $event_id       = (int) COM_applyFilter($_POST['event_id'],true);

    // we can retrieve the original event and determine if it is a recurring
    // event - if it is - we are only updating the single instance, which means
    // we set exception to 1
    // we can change any other attribute about the event - even the allday or not flags

    $recurring = DB_getItem($_TABLES['ac_events'],'repeats','event_id='.(int) $event_id);
    COM_errorLog("Recurring Flag is " . $recurring);

    $exception = 0;
    if ( $recurring == 1 ) {
        $exception = 1;
    }


// parse submitted data
    $title          = $_POST['title'];
    $start_date     = $_POST['event-date'];
    $end_date       = $_POST['event-end-date'];
    $start_time     = $_POST['start-time'];
    $end_time       = $_POST['end-time'];
    $location       = $_POST['location'];
    $description    = $_POST['description'];
    if ( isset($_POST['event-allday'] ) ) {
        $allday = 1;
        $end_time = '24:00:00';
    }
    if ( $start_time == '' ) {
        $start_time = '00:00';
    }
    if ( $end_time == '' ) {
        $end_time = '24:00:00';
    }
    $start_time = date("H:i", strtotime($start_time));
    $end_time   = date("H:i", strtotime($end_time));
    // create the full start / end time for the event
    $start          = trim($start_date . " " . $start_time);
    $end            = trim($end_date . " " . $end_time);

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

    // filter our input
    $filter = new sanitizer();
    $filter->setPostmode('text');
    $description = $filter->filterText($description);
    $title = $filter->filterText($title);
    $location = $filter->filterText($location);

    // calculate the dates

    $dtStart = new Date($start,$_USER['tzid']);
    $dtEnd   = new Date($end,  $_USER['tzid']);
    // create the unix timestamp start / end dates
    $db_start = $dtStart->toUnix(false);
    $db_end   = $dtEnd->toUnix(false);
    // use the entered start / end date
    $db_start_date = DB_escapeString($start_date);
    $db_end_date   = DB_escapeString($end_date);

    // prepare other stuff for db
    $db_title           = DB_escapeString($title);
    $db_location        = DB_escapeString($location);
    $db_description     = DB_escapeString($description);

    // update parent record
    $sql = "UPDATE {$_TABLES['ac_event']} SET
            title = '{$db_title}',
            location = '{$db_location}',
            description = '{$db_description}',
            allday = {$allday},
            start_date = '{$db_start_date}',
            end_date = '{$db_end_date}',
            start = '{$db_start}',
            end = '{$db_end}'";

    $sql .= " WHERE parent_id=".(int) $parent_id;
    DB_query($sql,1);

    if ( DB_error() ) {
        $errorCode = 1;
    } else {
        // updat events record
        $sql = "UPDATE {$_TABLES['ac_events']} SET
                title = '{$db_title}',
                location = '{$db_location}',
                description = '{$db_description}',
                allday = {$allday},
                start_date = '{$db_start_date}',
                end_date = '{$db_end_date}',
                start = '{$db_start}',
                end = '{$db_end}',
                exception = {$exception} ";
        $sql .= " WHERE event_id=".(int) $event_id;
        DB_query($sql,1);

        if ( DB_error() ) {
            $errorCode = 2;
        }
    }

    PLG_itemSaved($parent_id, 'agenda');
    CACHE_remove_instance('agenda');

    return $errorCode;
}

function saveEditEventSeries()
{
    global $_CONF, $_AC_CONF, $_USER, $_TABLES;

COM_errorLog("in saveEditEventSeries");

    $errorCode = 0;
    $errors = 0;

    // get the parentid and the event id

    $parent_id      = (int) COM_applyFilter($_POST['parent_id'],true);
    $event_id       = (int) COM_applyFilter($_POST['event_id'],true);

    $title          = $_POST['title'];
    $location       = $_POST['location'];
    $description    = $_POST['description'];

    // filter our input
    $filter = new sanitizer();
    $filter->setPostmode('text');
    $description = $filter->filterText($description);
    $title = $filter->filterText($title);
    $location = $filter->filterText($location);

    // prepare other stuff for db
    $db_title           = DB_escapeString($title);
    $db_location        = DB_escapeString($location);
    $db_description     = DB_escapeString($description);
COM_errorLog("preparing to update parent");
   // update parent record
    $sql = "UPDATE {$_TABLES['ac_event']} SET
            title = '{$db_title}',
            location = '{$db_location}',
            description = '{$db_description}'";

    $sql .= " WHERE parent_id=".(int) $parent_id;
COM_errorLog($sql);
    DB_query($sql,1);

    if ( DB_error() ) {
        $errorCode = 1;
    } else {
        // updat events record
        $sql = "UPDATE {$_TABLES['ac_events']} SET
                title = '{$db_title}',
                location = '{$db_location}',
                description = '{$db_description}'";
        $sql .= " WHERE parent_id=".(int) $parent_id . " AND exception = 0";
        DB_query($sql,1);

        if ( DB_error() ) {
            $errorCode = 2;
        }
    }

    PLG_itemSaved($parent_id, 'agenda');
    CACHE_remove_instance('agenda');

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

    PLG_itemDeleted($parent_id,'agenda');

    return 0;
}


function deleteEventSeries()
{
    global $_CONF, $_AC_CONF, $_TABLES;

    $parent_id      = (int) COM_applyFilter($_POST['parent_id'],true);
    $event_id       = (int) COM_applyFilter($_POST['event_id'],true);

    $sql = "DELETE FROM {$_TABLES['ac_events']} WHERE parent_id=".(int) $parent_id;
    DB_query($sql);
    $sql = "DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $parent_id;
    DB_query($sql);

    PLG_itemDeleted($parent_id,'agenda');

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