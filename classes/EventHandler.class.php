<?php
/**
*   Agenda Plugin for glFusion CMS
*
*   Event management (save, update, delete)
*
*   @author     Mark R. Evans <mark@lglfusion.org>
*   @copyright  Copyright (c) 2017-2018 Mark R. Evans <mark@glfusion.org>
*   @package    agenda
*   @version    1.0.0
*   @license    http://opensource.org/licenses/gpl-2.0.php
*               GNU Public License v2 or later
*   @filesource
*/

namespace Agenda;

/**
*   EventHandler class
*   @package Agenda
*/
class eventHandler {

    /**
    *   Save new event
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    public function saveEvent($args = array() )
    {
        global $_CONF, $_AC_CONF, $_USER, $_TABLES, $REMOTE_ADDR;

    // initialize vars
        $errorCode  = 0;
        $errors     = 0;
        $data = array();

    // set defaults
        $repeats = 0;
        $repeat_freq = 0;
        $allday = 0;
        $queued = 0;

    // parse submitted data
        $title          = $args['title'];
        $start_date     = $args['event-date'];
        $end_date       = $args['event-end-date'];
        $start_time     = isset($args['start-time']) ? $args['start-time'] : '';
        $end_time       = isset($args['end-time'])   ? $args['end-time'] : '';
        $location       = $args['location'];
        $description    = $args['description'];
        $category       = $args['category'];
        $ip_binary      = inet_pton($REMOTE_ADDR);

        if ( isset($args['freq']) && $args['freq'] != 'none' ) {
            $repeats = 1;
            $repeats     = 1;
            $repeat_freq = 0; //COM_applyFilter($args['repeat-freq'],true);
        }
        if ( isset($args['event-allday'] ) ) {
            $allday = 1;
            $end_time = '24:00:00';
        }
        if ( COM_isAnonUser() ) {
            $owner_id = 1;
        } else {
            $owner_id = $_USER['uid'];
        }

        $queued = 0;

        // check permissions and determine if this should be queued.
        if ( !$this->hasWriteAccess() ) {
            switch ($_AC_CONF['allow_entry']) {
                case 0 : // admins only
                    $errorCode = AC_ERR_NO_ACCESS;
                    break;
                case 1 : // only logged in users
                    if ( COM_isAnonUser() ) {
                        $errorCode = AC_ERR_NO_ACCESS;
                    } else {
                        if ( $_AC_CONF['submission_queue'] == 2 ) {
                            $queued = 1;
                        }
                    }
                    break;
                case 2 : // anyone
                    if ( COM_isAnonUser() && $_AC_CONF['submission_queue'] >= 1 ) {
                        $queued = 1;
                    } elseif ( $_AC_CONF['submission_queue'] == 2 ) {
                        $queued = 1;
                    }
                    break;
                default :
                    $errorCode = AC_ERR_NO_ACCESS;
                    break;
            }
            if ( $queued == 1 && SEC_hasRights('agenda.noqueue')) {
                $queued = 0;
            }
        }

        if ( $errorCode ) {
            return $errorCode;
        }

    // check / validate submitted data

        if ( $start_time == '' ) {
            $start_time = '00:00:00';
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

        if ( $title == '' ) {
            $errorCode = AC_ERR_EMPTY_TITLE;
            $error++;
        }

        if ( !agenda_validateDate($start_date, 'Y-m-d') ) {
            $errorCode = AC_ERR_INVALID_DATE;
            $errors++;
        }

        if ( !agenda_validateDate($end_date, 'Y-m-d') ) {
            $errorCode = AC_ERR_INVALID_DATE;
            $errors++;
        }

        if ( $errors ) {
            COM_errorLog("ERROR: Agenda Plugin: Valdation of title, start / end event date failed.");
            return $errorCode;
        }

        // sanitize the input

        $filter = new \sanitizer();
        $filter->setPostmode('text');

        $description = $filter->filterText($description);
        $title = $filter->filterText($title);
        $location = $filter->filterText($location);

        // initilize the date objects for start / end
        $dtStart = new \Date($start,$_USER['tzid']);
        $dtEnd   = new \Date($end,  $_USER['tzid']);

        // create the unix timestamp start / end dates
        $start = $dtStart->toUnix(false);
        $end   = $dtEnd->toUnix(false);

        if (version_compare(GVERSION,'1.7.2','>=')) {
            $spamData = array(
                'ip'    => $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']),
                'type'  => 'event'
            );
            $result = PLG_checkforSpam ('<h1>'.$title.'</h1><p>'.$filter->Linkify($description).'</p><p>'.$location.'</p>', $_CONF['spamx'],$spamData);
        } else {
            $result = PLG_checkforSpam ('<h1>'.$title.'</h1><p>'.$filter->Linkify($description).'</p><p>'.$location.'</p>', $_CONF['spamx']);
        }

        if ($result > 0) {
            $errorCode = AC_ERR_SPAM;
            return $errorCode;
        }

        $data = array(
            'allday'        => $allday,
            'start'         => $start,
            'end'           => $end,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'title'         => $title,
            'location'      => $location,
            'description'   => $description,
            'repeats'       => $repeats,
            'repeat_freq'   => $repeat_freq,
            'category'      => (int) $category,
            'queued'        => $queued,
            'owner_id'      => $owner_id,
            'ip'            => $ip_binary
        );

        $parent_id = $this->saveParent($data);

        if ( $parent_id == -1 ) {
            return 2;
        }

        $rrule = $this->buildRrule();
        if ( $rrule != '' ) {
            $repeats = 1;
            // update the parent record with the recurrence rule
            DB_query("UPDATE {$_TABLES['ac_event']} SET rrule='".DB_escapeString($rrule)."' WHERE parent_id=".$parent_id);
        }

        if ( $queued ) {
            return 3;
        }

        // now build events record
        unset($data['queued']);
        $data['exception'] = 0;
        $data['parent_id'] = $parent_id;

        $rc = $this->saveChild($data);
        if ( $rc != 0 ) {
            $errorCode = AC_ERR_DB_SAVE_CHILD;
            DB_delete($_TABLES['ac_event'],'parent_id',$parent_id);
        }

        if ( $rrule != '' ) {
            if ( $end_date === '' ) {
                $end_date = $start_date;
            }
            $orig_start_date = $start_date;
            $orig_end_date   = $end_date;

            // calculate the future dates
            $ruleArray = explode(';',$rrule);
            $rules = array();
            foreach ( $ruleArray AS $element ) {
                $rule = explode('=',$element);
                if ( $rule[0] != '' ) {
                    $rules[$rule[0]] = $rule[1];
                }
            }

            $rrule = new \RRule\RRule($rules);

            $iterations = count($rrule);
            $startDateArray = array();
            foreach ( $rrule as $occurrence ) {
                $startDateArray[] = $occurrence->format('Y-m-d');
            }

            // calculate the duration (# of days) of the event
            $startD = trim($start_date . " " . '00:00:00');
            $endD   = trim($end_date . " " . '00:00:00');
            $dtStartD = new \Date($startD,$_USER['tzid']);
            $dtEndD   = new \Date($endD,  $_USER['tzid']);
            $dtDiff = $dtEndD->toUnix() - $dtStartD->toUnix();
            $durationDays = (int) floor($dtDiff / (60 * 60 * 24));

            // in some cases rrule will return the current date as part of the iterations and in other cases it does not

            if ( $startD == $startDateArray[0].' 00:00:00') {
                $y = 1;
            } else {
                $y = 0;
            }

            for ( $x = $y; $x < $iterations; $x++ ) {

                $start_date = $startDateArray[$x];
                $end_date = date('Y-m-d', strtotime($start_date . ' +'.$durationDays.' days'));
                $start          = $start_date . " " . $start_time;
                $end            = $end_date . " " . $end_time;

                $dtStart = new \Date($start,$_USER['tzid']);
                $dtEnd   = new \Date($end,  $_USER['tzid']);

                $db_start = $dtStart->toUnix(false);
                $db_end   = $dtEnd->toUnix(false);

                // add the start_date_ad / end_date_ad
                $db_start_date = $dtStart->format('Y-m-d',true);
                $db_end_date   = $dtEnd->format('Y-m-d',true);

                // only need to update the dates for the recurring event
                $data['start'] = $db_start;
                $data['end']   = $db_end;
                $data['start_date'] = $db_start_date;
                $data['end_date'] = $db_end_date;

                $rc = $this->saveChild($data);
                if ( $rc != 0 ) {
                    $errorCode = AC_ERR_DB_SAVE_CHILD;
                    DB_delete($_TABLES['ac_event'],'parent_id',$parent_id);
                    DB_delete($_TABLES['ac_events'],'parent_id',$parent_id);
                    return $errorCode;
                }
            }
        }

        PLG_itemSaved($parent_id, 'agenda');
/*
        $c = \glFusion\Cache::getInstance();
        $c->deleteItemsByTag('agenda_sql');
*/
        CACHE_remove_instance('agenda');

        return $errorCode;
    }


    /**
    *   Approve an event in the moderation queue
    *
    *   @param  int   $args    parent id of event to approve
    *   @return boolean     0 on success, other indicates error
    */
    public function approveEvent( $parent_id )
    {
        global $_CONF, $_AC_CONF, $_USER, $_TABLES;

    // initialize vars
        $errorCode  = 0;
        $errors     = 0;
        $data = array();

    // set defaults
        $repeats = 0;
        $repeat_freq = 0;
        $allday = 0;
        $queued = 0;

        $result = DB_query("SELECT * FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $parent_id);
        if ( DB_numRows($result) != 1 ) {
            return -1;
        }
        $args = DB_fetchArray($result);

    // parse submitted data
        $title          = $args['title'];
        $start_date     = $args['start_date'];
        $end_date       = $args['end_date'];
        $location       = $args['location'];
        $description    = $args['description'];
        $category       = $args['category'];
        $start          = $args['start'];
        $end            = $args['end'];

        if ( $args['repeats'] == 1 ) {
            $repeats     = 1;
            $repeat_freq = $args['repeat_freq'];
        }
        if ( $args['allday'] == 1 ) {
            $allday = 1;
        }
        $owner_id = $args['owner_id'];
        $queued = 0;

        $dtStart = new \Date($start,$_USER['tzid']);
        $dtEnd   = new \Date($end, $_USER['tzid']);
        $start_time = $dtStart->format("H:i", true);
        $end_time   = $dtEnd->format("H:i", true);

        $data = array(
            'allday'        => $allday,
            'start'         => $start,
            'end'           => $end,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'title'         => $title,
            'location'      => $location,
            'description'   => $description,
            'repeats'       => $repeats,
            'repeat_freq'   => $repeat_freq,
            'category'      => (int) $category,
            'queued'        => $queued,
            'owner_id'      => $owner_id
        );

        // update the queued flag
        DB_query("UPDATE {$_TABLES['ac_event']} SET queued=0 WHERE parent_id=".(int) $parent_id);

        // now build events record
        unset($data['queued']);
        $data['exception'] = 0;
        $data['parent_id'] = $parent_id;

        $rc = $this->saveChild($data);
        if ( $rc != 0 ) {
            $errorCode = AC_ERR_DB_SAVE_CHILD;
            DB_delete($_TABLES['ac_event'],'parent_id',$parent_id);
        }

        $rrule = $args['rrule'];
        if ( $rrule != '' && $rrule != null ) {
            if ( $end_date === '' ) {
                $end_date = $start_date;
            }
            $orig_start_date = $start_date;
            $orig_end_date   = $end_date;

            // calculate the future dates
            $ruleArray = explode(';',$rrule);
            $rules = array();
            foreach ( $ruleArray AS $element ) {
                $rule = explode('=',$element);
                if ( $rule[0] != '' ) {
                    $rules[$rule[0]] = $rule[1];
                }
            }
            $rrule = new \RRule\RRule($rules);

            $iterations = count($rrule);
            $startDateArray = array();
            foreach ( $rrule as $occurrence ) {
                $startDateArray[] = $occurrence->format('Y-m-d');
            }

            // calculate the duration (# of days) of the event
            $startD = trim($start_date . " " . '00:00:00');
            $endD   = trim($end_date . " " . '00:00:00');
            $dtStartD = new \Date($startD,$_USER['tzid']);
            $dtEndD   = new \Date($endD,  $_USER['tzid']);
            $durationDays = (int) $dtEndD->format('d') - (int) $dtStartD->format('d');

            for ( $x = 1; $x < $iterations; $x++ ) {

                $start_date = $startDateArray[$x];
                $end_date = date('Y-m-d', strtotime($start_date . ' +'.$durationDays.' day'));

                $start          = $start_date . " " . $start_time;
                $end            = $end_date . " " . $end_time;

                $dtStart = new \Date($start,$_USER['tzid']);
                $dtEnd   = new \Date($end,  $_USER['tzid']);

                $db_start = $dtStart->toUnix(false);
                $db_end   = $dtEnd->toUnix(false);

                // add the start_date_ad / end_date_ad
                $db_start_date = $dtStart->format('Y-m-d',true);
                $db_end_date   = $dtEnd->format('Y-m-d',true);

                // only need to update the dates for the recurring event
                $data['start'] = $db_start;
                $data['end']   = $db_end;
                $data['start_date'] = $db_start_date;
                $data['end_date'] = $db_end_date;

                $rc = $this->saveChild($data);
                if ( $rc != 0 ) {
                    $errorCode = AC_ERR_DB_SAVE_CHILD;
                    DB_delete($_TABLES['ac_event'],'parent_id',$parent_id);
                    DB_delete($_TABLES['ac_events'],'parent_id',$parent_id);
                    return $errorCode;
                }
            }
        }

        PLG_itemSaved($parent_id, 'agenda');
/*
        $c = \glFusion\Cache::getInstance();
        $c->deleteItemsByTag('agenda_sql');
*/
        CACHE_remove_instance('agenda');

        return $errorCode;
    }


    /**
    *   Update event after move
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    public function moveEvent( $args = array() )
    {
        global $_CONF, $_AC_CONF, $_TABLES, $_USER;

        $retval = 0;

        if ( !isset($args['id'])) {
            return -1;
        }

        $event_id = isset($args['id']) ? (int) COM_applyFilter($args['id'],true) : -1;

        // get the parent_id

        $parent_id = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.(int) $event_id);
        if ( $parent_id == null ) {
            return -1;
        }
        $repeats = DB_getItem($_TABLES['ac_events'],'repeats','event_id='.$event_id);
        if (isset($args['allday']) && $args['allday'] == 'true') {
             $allday = 1;
        } else {
            $allday = 0;
        }

        if ( !isset($args['end'])) {
            $args['end'] = $args['date'];
        }
        if ( $allday ) {
            // add the time to the fields as all day events only pass dates
            $args['date'] = $args['date'].' 00:00:00';
            $args['end']  = $args['end'].' 24:00:00';
        }

        $start_date = substr($args['date'],0,10);
        $end_date   = substr($args['end'],0,10);

        $start_time = substr($args['date'],11);
        $end_time   = substr($args['end'],11);

        $start_time = date("H:i", strtotime($start_time));
        $end_time   = date("H:i", strtotime($end_time));
        // create the full start / end time for the event
        $start          = trim($start_date . " " . $start_time);
        $end            = trim($end_date . " " . $end_time);

        $dtStart = new \Date($start,$_USER['tzid']);
        $dtEnd   = new \Date($end,  $_USER['tzid']);
        // create the unix timestamp start / end dates
        $db_start = $dtStart->toUnix(false);
        $db_end   = $dtEnd->toUnix(false);
        // use the entered start / end date
        $db_start_date = DB_escapeString($start_date);

        if ( $allday ) {
            $db_end_date = DB_escapeString(date('Y-m-d', strtotime('-1 day', strtotime($end_date))));
        } else {
            $db_end_date = DB_escapeString($end_date);;
        }

        $sqlValues = "start='{$db_start}' ";
        $sqlValues .= ", end='{$db_end}' ";
        $sqlValues .= ",allday=".(int) $allday . " ";
        if ( $repeats == 1 ) {
            $sqlValues .= ", exception=1 ";
        }
        $sqlValues .= ", start_date='{$db_start_date}', end_date='{$db_end_date}' ";
        $sql = "UPDATE {$_TABLES['ac_events']} SET ".$sqlValues." WHERE event_id=".$event_id;

        DB_query($sql,1);
        if ( $repeats == 0 ) { // don't update parent for series
            $sql = "UPDATE {$_TABLES['ac_event']} SET ".$sqlValues." WHERE parent_id=".$parent_id;
            DB_query($sql,1);
        }

        PLG_itemSaved($parent_id, 'agenda');
/*
        $c = \glFusion\Cache::getInstance();
        $c->deleteItemsByTag('agenda_sql');
*/
        CACHE_remove_instance('agenda');

        return $retval;
    }

    /**
    *   Save an edited event
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    public function updateEvent($args = array())
    {
        global $_CONF, $_AC_CONF, $_USER, $_TABLES;

        $errorCode = 0;
        $errors = 0;

    // set defaults
        $allday = 0;

        // get the parentid and the event id

        $parent_id      = (int) COM_applyFilter($args['parent_id'],true);
        $event_id       = (int) COM_applyFilter($args['event_id'],true);

        // we can retrieve the original event and determine if it is a recurring
        // event - if it is - we are only updating the single instance, which means
        // we set exception to 1
        // we can change any other attribute about the event - even the allday or not flags

        $recurring = DB_getItem($_TABLES['ac_events'],'repeats','event_id='.(int) $event_id);

        $exception = 0;
        if ( $recurring == 1 ) {
            $exception = 1;
        }

    // parse submitted data
        $title          = $args['title'];
        $start_date     = $args['event-date'];
        $end_date       = $args['event-end-date'];
        $start_time     = isset($args['start-time']) ? $args['start-time'] : '';
        $end_time       = isset($args['end-time'])   ? $args['end-time'] : '';
        $location       = $args['location'];
        $description    = $args['description'];
        $category       = (int) $args['category'];

        if ( isset($args['event-allday'] ) ) {
            $allday = 1;
            $end_time = '24:00:00';
        }
        if ( $start_time == '' ) {
            $start_time = '00:00:00';
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
        $filter = new \sanitizer();
        $filter->setPostmode('text');
        $description = $filter->filterText($description);
        $title = $filter->filterText($title);
        $location = $filter->filterText($location);

        // calculate the dates

        $dtStart = new \Date($start,$_USER['tzid']);
        $dtEnd   = new \Date($end,  $_USER['tzid']);

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

        if ( $exception == 0 ) {
            // update parent record
            $sql = "UPDATE {$_TABLES['ac_event']} SET
                    title = '{$db_title}',
                    location = '{$db_location}',
                    description = '{$db_description}',
                    allday = {$allday},
                    category = {$category},
                    start_date = '{$db_start_date}',
                    end_date = '{$db_end_date}',
                    start = '{$db_start}',
                    end = '{$db_end}'";

            $sql .= " WHERE parent_id=".(int) $parent_id;
            DB_query($sql,1);
            if ( DB_error() ) {
                $errorCode = 1;
            }
        }
        if ( $errorCode == 0 ) {
            // updat events record
            $sql = "UPDATE {$_TABLES['ac_events']} SET
                    title = '{$db_title}',
                    location = '{$db_location}',
                    description = '{$db_description}',
                    allday = {$allday},
                    category = {$category},
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
/*
        $c = \glFusion\Cache::getInstance();
        $c->deleteItemsByTag('agenda_sql');
*/
        CACHE_remove_instance('agenda');

        return $errorCode;
    }

    /**
    *   Update an event series
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    public function updateEventSeries( $args = array() )
    {
        global $_CONF, $_AC_CONF, $_USER, $_TABLES;

        $retval = 0;

        // parsed user input
        $parent_id      = (int) COM_applyFilter($args['parent_id'],true);

        if ( isset($_POST['edit-recurrence'] ) ) {
            // delete all the old
            $retval = $this->deleteEventSeries($parent_id);
            // save new series
            $retval = $this->saveEvent($args);
        } else {
            $title          = $args['title'];
            $location       = $args['location'];
            $description    = $args['description'];
            $category       = $args['category'];

            // sanitize input
            $filter = new \sanitizer();
            $filter->setPostmode('text');

            $description = $filter->filterText($description);
            $title       = $filter->filterText($title);
            $location    = $filter->filterText($location);

            // prepare for DB
            $db_title           = DB_escapeString($title);
            $db_location        = DB_escapeString($location);
            $db_description     = DB_escapeString($description);
            $category           = (int) $category;

           // update parent record
            $sql = "UPDATE {$_TABLES['ac_event']} SET
                    title = '{$db_title}',
                    location = '{$db_location}',
                    category = {$category},
                    description = '{$db_description}'";
            $sql .= " WHERE parent_id=".(int) $parent_id;

            DB_query($sql,1);

            if ( DB_error() ) {
                $retval = 1;
            } else {
                // updat events record
                $sql = "UPDATE {$_TABLES['ac_events']} SET
                        title = '{$db_title}',
                        location = '{$db_location}',
                        category = {$category},
                        description = '{$db_description}'";
                $sql .= " WHERE parent_id=".(int) $parent_id . " AND exception = 0";
                DB_query($sql,1);

                if ( DB_error() ) {
                    $retval = 2;
                }
            }
            PLG_itemSaved($parent_id, 'agenda');
            CACHE_remove_instance('agenda');
        }
        return $retval;
    }

    /**
    *   Save an edited event in submission queue
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    public function updateQueuedEvent($args = array())
    {
        global $_CONF, $_AC_CONF, $_USER, $_TABLES;

        $errorCode = 0;
        $errors = 0;

    // set defaults
        $allday = 0;

        // get the parentid and the event id

        $parent_id      = (int) COM_applyFilter($args['parent_id'],true);

    // parse submitted data
        $title          = $args['title'];
        $start_date     = $args['event-date'];
        $end_date       = $args['event-end-date'];
        $start_time     = isset($args['start-time']) ? $args['start-time'] : '';
        $end_time       = isset($args['end-time'])   ? $args['end-time'] : '';
        $location       = $args['location'];
        $description    = $args['description'];
        $category       = $args['category'];
        $repeats        = 0;
        $repeat_freq    = 0;
        if ( isset($args['freq']) && $args['freq'] != 'none' ) {
            $repeats     = 1;
            $repeat_freq = 0;
        }
        if ( isset($args['event-allday'] ) ) {
            $allday = 1;
            $end_time = '24:00:00';
        }
        if ( COM_isAnonUser() ) {
            $owner_id = 1;
        } else {
            $owner_id = $_USER['uid'];
        }

        if ( $errorCode ) {
            return $errorCode;
        }

    // check / validate submitted data

        if ( $start_time == '' ) {
            $start_time = '00:00:00';
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
            $errorCode = AC_ERR_INVALID_DATE;
            $errors++;
        }

        if ( !agenda_validateDate($end_date, 'Y-m-d') ) {
            $errorCode = AC_ERR_INVALID_DATE;
            $errors++;
        }

        if ( $errors ) {
            COM_errorLog("ERROR: Agenda Plugin: Valdation of start / end event date failed.");
            return $errorCode;
        }

        // sanitize the input

        $filter = new \sanitizer();
        $filter->setPostmode('text');

        $description = $filter->filterText($description);
        $title = $filter->filterText($title);
        $location = $filter->filterText($location);

        // initilize the date objects for start / end
        $dtStart = new \Date($start,$_USER['tzid']);
        $dtEnd   = new \Date($end,  $_USER['tzid']);

        // create the unix timestamp start / end dates
        $start = $dtStart->toUnix(false);
        $end   = $dtEnd->toUnix(false);

        $rrule = $this->buildRrule();
        if ( $rrule != '' ) {
            $repeats = 1;
        }

        $data = array(
            'allday'        => $allday,
            'start'         => $start,
            'end'           => $end,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'title'         => $title,
            'location'      => $location,
            'description'   => $description,
            'repeats'       => $repeats,
            'repeat_freq'   => $repeat_freq,
            'rrule'         => $rrule,
            'category'      => (int) $category,
        );

        $dataSQL = '';
        $commaCount = 0;
        // build out our data structions
        foreach ($data AS $column => $value) {
            if ( $commaCount > 0 ) {
                $dataSQL .= ', ';
            }
            $dataSQL .= $column . " = '".DB_escapeString($value)."'";
            $commaCount++;
        }
        $sql = "UPDATE {$_TABLES['ac_event']} SET ";
        $sql .= $dataSQL;
        $sql .= " WHERE parent_id=".(int) $parent_id;
        DB_query($sql,1);
        if ( DB_error() ) {
            $errorCode = 1;
        }
/*
        $c = \glFusion\Cache::getInstance();
        $c->deleteItemsByTag('agenda_sql');
*/
        return $errorCode;
    }

    /**
    *   Delete a single event
    *
    *   @param  int    $parent_id    parent id of event to delete
    *   @param  int    $event_id     event id of event to delete
    *   @return boolean     0 on success, other indicates error
    */
    public function deleteEvent($parent_id, $event_id)
    {
        global $_CONF, $_AC_CONF, $_TABLES;

        $retval = 0;

        if ( $this->hasWriteAccess() ) {
            // validation check
            if ( $parent_id < 1 || $event_id < 1 ) {
                COM_errorLog("ERROR: Agenda Plugin: deleteEvent() received an invalid parent id or event id");
                return -1;
            }
            $sql = "DELETE FROM {$_TABLES['ac_events']} WHERE event_id=".(int) $event_id . " AND parent_id=".(int) $parent_id;
            DB_query($sql);

            // don't delete parent if it is a series
            $series = DB_getItem($_TABLES['ac_event'],'repeats','parent_id='.(int) $parent_id);
            if ( $series != 1 ) {
                $sql = "DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $parent_id;
                DB_query($sql);
                PLG_itemDeleted($parent_id,'agenda');
            }
/*
            $c = \glFusion\Cache::getInstance();
            $c->deleteItemsByTag('agenda_sql');
*/
            CACHE_remove_instance('agenda');
            $retval = 0;
        } else {
            COM_errorLog("ERROR: Agenda Plugin: Attempt to delete event with proper permissions");
            $retval = -1;
        }
        return $retval;
    }


    /**
    *   Delete a full series of events
    *
    *   @param  int    $parent_id    parent id of event to delete
    *   @return boolean     0 on success, other indicates error
    */
    public function deleteEventSeries($parent_id)
    {
        global $_CONF, $_AC_CONF, $_TABLES;

        $retval = 0;
        if ( $this->hasWriteAccess() ) {
            $sql = "DELETE FROM {$_TABLES['ac_events']} WHERE parent_id=".(int) $parent_id;
            DB_query($sql);
            $sql = "DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $parent_id;
            DB_query($sql);

            PLG_itemDeleted($parent_id,'agenda');
            CACHE_remove_instance('agenda');
            $retval = 0;
        } else {
            COM_errorLog("ERROR: Agenda Plugin: User did not have permissions to delete event series.");
            $retval = -1;
        }
        return $retval;
    }

    private function hasWriteAccess()
    {
        if ( SEC_hasRights('agenda.admin') ) {
            return true;
        }
        return false;

    }


    /**
    *   Save new parent record to dB
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */

    private function saveParent($data = array() )
    {
        global $_CONF, $_AC_CONF, $_TABLES;

        $columns = array();
        $values  = array();

        // build out our data structions
        foreach ($data AS $column => $value) {
            $columns[] = $column;
            $values[]  = DB_escapeString($value);
        }
        $saveColumns = implode(',',$columns);
        $saveValues  = implode("','",$values);
        $saveValues = "'".$saveValues."'";
        $sql = "INSERT INTO {$_TABLES['ac_event']} (" . $saveColumns . " ) ";
        $sql .= " VALUES ( " . $saveValues . ")";
        $result = DB_query($sql,1);

        if ( DB_error() ) {
            return -1;
        }
        $parent_id = DB_insertId($result);
        return $parent_id;
    }

    /**
    *   Save new child event to DB
    *
    *   @param  array   $args    Array of input arguments
    *   @return boolean     0 on success, other indicates error
    */
    private function saveChild($data = array() )
    {
        global $_CONF, $_AC_CONF, $_TABLES;

        $columns = array();
        $values  = array();

        // build out our data structions
        foreach ($data AS $column => $value) {
            if ( $column == 'ip' ) continue;
            $columns[] = $column;
            $values[]  = DB_escapeString($value);
        }
        $saveColumns = implode(',',$columns);
        $saveValues  = implode("','",$values);
        $saveValues = "'".$saveValues."'";
        $sql = "INSERT INTO {$_TABLES['ac_events']} (" . $saveColumns . " ) ";
        $sql .= " VALUES ( " . $saveValues . ")";

        $result = DB_query($sql,1);

        if ( DB_error() ) {
            return -1;
        }
        return 0;
    }

    /**
    *   Returns future dates for recurring event
    *
    *   @param  string   $type     type of recurrence (daily, weekly, biweekly,monthly, yearly)
    *   @param  string   $date     original start date of the event
    *   @param  int      $interval recurrence interval (future event iteration)
    *   @return string      Y-m-d string of future date
    */
    private function buildRecurring( $type, $date, $interval = 1)
    {
        $retval = '';

        $dt = new \DateTime($date);

        switch ( $type ) {
            case 'daily' :
                $addInterval = "P" . $interval . "D";
                $oldDay = $dt->format("d");
                $dt->add(new \DateInterval($addInterval));
                $retval = $dt->format("Y-m-d");
                break;

            case 'weekly' :
                $addInterval = "P" . $interval . "W";
                $oldDay = $dt->format("d");
                $dt->add(new \DateInterval($addInterval));
                $retval = $dt->format("Y-m-d");
                break;

            case 'biweekly' :
                $addInterval = "P" . $interval * 2 . "W";
                $oldDay = $dt->format("d");
                $dt->add(new \DateInterval($addInterval));
                $retval = $dt->format("Y-m-d");
                break;

            case 'monthly' :
                $addInterval = "P" . $interval . "M";
                $oldDay = $dt->format("d");
                $dt->add(new \DateInterval($addInterval));
                $newDay = $dt->format("d");
                if($oldDay != $newDay) {
                    $dt->sub(new \DateInterval("P" . $newDay . "D"));
                }
                $retval = $dt->format("Y-m-d");
                break;

            case 'yearly' :
                $addInterval = "P" . $interval . "Y";
                $oldDay = $dt->format("d");
                $dt->add(new \DateInterval($addInterval));
                $newDay = $dt->format("d");
                if($oldDay != $newDay) {
                    $dt->sub(new \DateInterval("P" . $newDay . "D"));
                }
                $retval = $dt->format("Y-m-d");
                break;

            default :
                $retval = $dt->format("Y-m-d");
                break;
        }
        return $retval;
    }

    /**
    *   Returns a well formed RRULE record
    *
    *   requires the appropriate POST fields be availble
    *
    *   @return string      Blank (no recurrence) or a RRULE string
    */
    private function buildRrule()
    {
        $rrule = '';

        if ( isset($_POST['freq']) && $_POST['freq'] != 'none' ) {
            $freq = COM_applyFilter($_POST['freq']);
            $interval = COM_applyFilter($_POST['interval']);

            switch ($freq) {
                case 'HOURLY' :
                    $rrule = 'FREQ=HOURLY;INTERVAL=' . (int) $interval.';';
                    break;

                case 'DAILY' :
                    $rrule = 'FREQ=DAILY;INTERVAL=' . (int) $interval .';';
                    break;

                case 'WEEKLY' :
                    $rrule = 'FREQ=WEEKLY;INTERVAL='. (int) $interval;

                    if ( isset($_POST['wk-byday']) && $_POST['wk-byday'] != '' ) {
                        $rrule .= ';BYDAY='. $_POST['wk-byday'];
                    }
                    $rrule .= ';';
                    break;

                case 'MONTHLY' :
                    $rrule = 'FREQ=MONTHLY;INTERVAL='.(int) $interval.';';
                    if ( $_POST['mtype'] == 0 ) {
                        $rrule .= "BYMONTHDAY=".(int) COM_applyFilter($_POST['mo-dom'],true) . ';';
                    } elseif ( $_POST['mtype'] == 1 ) {
                        $rrule .= "BYSETPOS=".COM_applyFilter($_POST['mo-setpos']);
                        if ( isset($_POST['mo-day']) && $_POST['mo-day'] != '' ) {
                            $rrule .= ';BYDAY='.$_POST['mo-day'];
                        }
                        $rrule .= ';';
                    }
                    break;

                case 'YEARLY' :
                    $rrule = 'FREQ=YEARLY;';
                    if ( $_POST['yr-type'] == 0 ) {
                        $rrule .= "BYMONTH=".(int) COM_applyFilter($_POST['yr-month'],true).';';
                        $rrule .= "BYMONTHDAY=".(int) COM_applyFilter($_POST['yr-dom'],true).';';
                    } elseif ( $_POST['yr-type'] == 1 ) {
                        $rrule .= "BYSETPOS=".(int) COM_applyFilter($_POST['yr-setpos'],true).';';
                        $rrule .= "BYDAY=".$_POST['yr-day'].';';
                        $rrule .= "BYMONTH=".(int) COM_applyFilter($_POST['yr-month2'],true).';';
                    }
                    break;
                default : // must be a frequency we do not support
                    return '';
                    break;
            }

    // validate event date is valid
            $rrule .= 'DTSTART='.COM_applyFilter($_POST['event-date']).';';

            if ($_POST['end-type'] == 0 ) {
                $rrule .= 'COUNT='.(int) COM_applyFilter($_POST['endafter'],true).';';
            } elseif ( $_POST['end-type'] == 1 ) {
                $rrule .= 'UNTIL='.COM_applyFilter($_POST['recur-end-date']).';';
            }
        }
        return $rrule;
    }
}
