<?php
/**
* glFusion CMS
*
* Calendar - Calendar Plugin for glFusion
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

namespace fullcalendar;

class fcEvent {

    public var $event_id;
    public var $title;
    public var $weekday;
    public var $start_date;
    public var $start_time;
    public var $end_date;
    public var $end_time;
    public var $repeats;
    public var $repeat_freq;

    public function __constructor()
    {

    }

    public function getId()
    {
        return $this->event_id;
    }

    public function addEvent($data = array())
    {
        global $_CONF, $_FC_CONF, $_TABLES;

        $title          = $data['title'];
        $start_date     = $data['event-date'];
        $weekday        = date('N', strtotime($start_date));
        $start_time     = $data['start-time'];
        $end_time       = $data['end-time'];
        $start          = $start_date . " " . $start_time;
        $end            = $start_date . " " . $end_time;

        // validations
        if (!$this->validateDate($start_date,'Y-m-d')) {
            return false;
        }

        if (!data($_data['repeats'])) {

            $repeats = 0;
            $repeat_freq = 0;

            // prepare vars for DB
            $db_title       = DB_escapeString($title);
            $db_start_date  = DB_escapeString($start_date);
            $db_weekday     = DB_escapeString($weekday);
            $db_start_time  = DB_escapeString($start_time);
            $db_end_time    = DB_escapeString($end_time);
            $db_start       = DB_escapeString($start);
            $db_end         = DB_escapeString($end);

            // save parent event
            $sql = "INSERT INTO {$_TABLES['fc_event']} ( title,start_date,start_time,end_time,weekday,repeats,repeat_freq ) ";
            $sql .= "VALUES ('{$db_title}','{$db_start_date}','{$db_start_time}','{$db_end_time}','{$db_weekday}',$repeats,$repeat_freq)";
            $result = DB_query($sql);
            $parent_id = DB_insertId($result);

            // save child events
            $sql =  "INSERT INTO {$_TABLES['fc_events']} (title,start,end,parent_id) ";
            $sql .= "VALUES ('{$db_title}','{$db_start}','{$db_end}',$parent_id)";

            DB_query($sql);
        } else {
            $repeats = $data['repeats'];
            $repeat_freq = $data['repeat-freq'];

            $future = 365;
            if ( $repeat_freq == 365 ) $future = 3650;

            $until = ($future/$repeat_freq);
            if ($repeat_freq == 1) {
                $weekday = 0;
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
            $sql = "INSERT INTO {$_TABLES['fc_event']} ( title,start_date,start_time,end_time,weekday,repeats,repeat_freq ) ";
            $sql .= "VALUES ('{$db_title}','{$db_start_date}','{$db_start_time}','{$db_end_time}','{$db_weekday}',$repeats,$repeat_freq)";
            $result = DB_query($sql);
            $parent_id = DB_insertId($result);

            // insert the initial event
            $sql =  "INSERT INTO {$_TABLES['fc_events']} (title,start,end,parent_id) ";
            $sql .= "VALUES ('{$db_title}','{$db_start}','{$db_end}',$parent_id)";
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

                $sql =  "INSERT INTO {$_TABLES['fc_events']} (title,start,end,parent_id) ";
                $sql .= "VALUES ('{$db_title}','{$db_start}','{$db_end}',$parent_id)";
                DB_query($sql);
            }
        }
        return true;
    }

    public function delEvent()
    {

    }

    public function chgEvent()
    {

    }

    public function moveEvent()
    {

    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}