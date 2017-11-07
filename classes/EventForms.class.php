<?php
/**
*   Agenda Plugin for glFusion CMS
*
*   Event Forms Management
*
*   @author     Mark R. Evans <mark@lglfusion.org>
*   @copyright  Copyright (c) 2017 Mark R. Evans <mark@glfusion.org>
*   @package    agenda
*   @version    1.0.0
*   @license    http://opensource.org/licenses/gpl-2.0.php
*               GNU Public License v2 or later
*   @filesource
*/

namespace Agenda;

define('AC_ERR_INVALID_DATE',1);
define('AC_ERR_DB_SAVE_PARENT',2);
define('AC_ERR_DB_SAVE_CHILD',3);
define('AC_ERR_NO_ACCESS',4);

/**
*   EventForm class
*   @package Agenda
*/
class eventForms {

    /*
     * Calendar form or admin form
     *
    */

    protected $adminForm = 0;

    public function __construct( $type = 0 )
    {
        $this->adminForm = $type;
    }

    /**
    *   New Event Entry Form
    *
    *   @param  string   $date    Date to use to initially populate the form
    *   @return string     HTML of the new event form
    */
    public function newEvent( $date = '')
    {
        global $_CONF, $_AC_CONF, $_TABLES, $_USER, $LANG_AC, $LANG_AC_JS;

        $dt = new \Date('now',$_USER['tzid']);
        if ( $date == '' ) {
            $date = $dt->toISO8601(true);
        }

        if ( strstr($date,"T") !== false ) {
            $allday = 0;
        } else {
            $allday = 1;
        }

        $time = strtotime($date);
        $start_date = date('Y-m-d', $time);
        $start_time = date('h:i:A', $time);
        $end_date = $start_date;
        $end_time = $start_time;

        $catSelList = '';
        $catList = $this->getCategories();
        foreach ( $catList AS $id => $name ) {
            if ( $id == 1 ) {
                $selected = ' selected="selected" ';
            } else {
                $selected = '';
            }
            $catSelList .= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
        }

        $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
        $T->set_file ('page','new-event-form.thtml');

        $T->set_var(array(
            'allday_checked'    => $allday ? ' checked="checked" ' : '',
            'start-date'        => trim($start_date),
            'end-date'          => trim($end_date),
            'start-time'        => trim($start_time),
            'end-time'          => trim($end_time),
            'weekstart'         => $_AC_CONF['first_day'],
            'category_select'   => $catSelList,
            'lang_category'     => $LANG_AC['category'],
            'lang_event_title'  => $LANG_AC['event_title'],
            'lang_location'     => $LANG_AC['location'],
            'lang_event_start'  => $LANG_AC['event_start'],
            'lang_start_date'   => $LANG_AC['start_date'],
            'lang_all_day_event'=> $LANG_AC['all_day_event'],
            'lang_event_end'    => $LANG_AC['event_end'],
            'lang_end_date'     => $LANG_AC['end_date'],
            'lang_end_time'     => $LANG_AC['end_time'],
            'lang_repeats'      => $LANG_AC['repeats'],
            'lang_repeat_options'=> $LANG_AC['repeat_options'],
            'lang_daily'        => $LANG_AC['daily'],
            'lang_weekly'       => $LANG_AC['weekly'],
            'lang_biweekly'     => $LANG_AC['biweekly'],
            'lang_monthly'      => $LANG_AC['monthly'],
            'lang_yearly'       => $LANG_AC['yearly'],
            'lang_description'  => $LANG_AC['description'],
            'lang_err_no_title' => $LANG_AC_JS['err_enter_title'],
            'lang_err_datetime' => $LANG_AC_JS['err_end_before_start'],
        ));

        $T->parse('output', 'page');
        $page = $T->finish($T->get_var('output'));

        return $page;
    }

    /**
    *   Edit Event Form
    *
    *   @param  int   $parent_id    Parent id of event
    *   @param  int   $event_id     event id of event
    *   @return string     HTML of the new event form
    */
    public function editEvent($parent_id, $event_id)
    {
        global $_CONF, $_AC_CONF, $_TABLES, $_USER, $LANG_AC, $LANG_AC_JS;

        $page = '';

        $result = DB_query("SELECT * FROM {$_TABLES['ac_events']} AS events  WHERE event_id=" . (int) $event_id);

        if ( DB_numRows($result) > 0 ) {
            $row = DB_fetchArray($result);

            $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
            $T->set_file ('page','edit-event-form.thtml');

            if ( $row['allday'] == 0 ) {
                $dt = new \Date($row['start'],$_USER['tzid']);
                $row['start_date'] = $dt->format('Y-m-d',true);
                $row['start_time'] = $dt->format('H:i',true);

                $dt = new \Date($row['end'],$_USER['tzid']);
                $row['end_date'] = $dt->format('Y-m-d',true);
                $row['end_time'] = $dt->format('H:i',true);
            } else {
                $row['start_date'] = $row['start_date'];
                $row['end_date'] = $row['end_date'];
                $row['start_time'] = '00:00';
                $row['end_time'] = '23:59';
            }

            $catSelList = '';
            $catList = $this->getCategories();
            foreach ( $catList AS $id => $name ) {
                if ( $id == $row['category'] ) {
                    $selected = ' selected="selected" ';
                } else {
                    $selected = '';
                }
                $catSelList .= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
            }

            $T->set_var(array(
                'title'       => $row['title'],
                'start-date'        => $row['start_date'],
                'end-date'          => $row['end_date'],
                'start-time'        => date('h:i A',strtotime($row['start_time'])),
                'end-time'          => date('h:i A',strtotime($row['end_time'])),
                'location'          => $row['location'],
                'description'       => $row['description'],
                'parent_id'         => $row['parent_id'],
                'event_id'          => $event_id,
                'category_select'   => $catSelList,
                'weekstart'         => $_AC_CONF['first_day'],
                'lang_category'     => $LANG_AC['category'],
                'lang_event_title'  => $LANG_AC['event_title'],
                'lang_location'     => $LANG_AC['location'],
                'lang_event_start'  => $LANG_AC['event_start'],
                'lang_start_date'   => $LANG_AC['start_date'],
                'lang_start_time'   => $LANG_AC['start_time'],
                'lang_all_day_event'=> $LANG_AC['all_day_event'],
                'lang_event_end'    => $LANG_AC['event_end'],
                'lang_end_date'     => $LANG_AC['end_date'],
                'lang_end_time'     => $LANG_AC['end_time'],
                'lang_repeats'      => $LANG_AC['repeats'],
                'lang_repeat_options'=> $LANG_AC['repeat_options'],
                'lang_daily'        => $LANG_AC['daily'],
                'lang_weekly'       => $LANG_AC['weekly'],
                'lang_biweekly'     => $LANG_AC['biweekly'],
                'lang_monthly'      => $LANG_AC['monthly'],
                'lang_yearly'       => $LANG_AC['yearly'],
                'lang_description'  => $LANG_AC['description'],
                'lang_save'         => $LANG_AC['save'],
                'lang_delete'       => $LANG_AC['delete'],
                'lang_cancel'       => $LANG_AC['cancel'],

                'lang_err_no_title' => $LANG_AC_JS['err_enter_title'],
                'lang_err_datetime' => $LANG_AC_JS['err_end_before_start'],


             ));
             if ( $row['repeats'] == 1 ) {
                $T->set_var('repeats_checked',' checked="checked" ');
                switch ( $row['repeat_freq'] ) {
                    case 1 :
                        $T->set_var('daily_checked',' checked="checked" ');
                        break;
                    case 7 :
                        $T->set_var('weekly_checked',' checked="checked" ');
                        break;
                    case 14 :
                        $T->set_var('biweekly_checked',' checked="checked" ');
                        break;
                    case 30 :
                        $T->set_var('monthly_checked', ' checked="checked" ');
                        break;
                    case 365 :
                        $T->set_var('yearly_checked',' checked="checked" ');
                        break;
                }
            } else {
                $T->set_var('repeats_checked','');
            }

            if ( $row['allday'] == 1 ) {
                $T->set_var('allday_checked',' checked="checked" ');
            }
            $T->set_var('parent_id',$parent_id);

            if ( $this->adminForm) {
                $T->set_var(array(
                    'admin_form' => true,
                    'form_action' => $_CONF['site_admin_url'].'/plugins/agenda/index.php',
                ));
            }

            $T->parse('output', 'page');
            $page = $T->finish($T->get_var('output'));
        }
        return $page;

    }

    /**
    *   Edit Event Series Form
    *
    *   @param  int   $event_id     event id of event
    *   @return string     HTML of the new event form
    */
    public function editSeries($parent_id)
    {
        global $_CONF, $_AC_CONF, $_TABLES, $_USER, $LANG_AC;

        $result = DB_query("SELECT * FROM {$_TABLES['ac_event']} AS event WHERE parent_id=" . (int) $parent_id);

        if ( DB_numRows($result) > 0 ) {
            $row = DB_fetchArray($result);

            $catSelList = '';
            $catList = $this->getCategories();
            foreach ( $catList AS $id => $name ) {
                if ( $id == $row['category'] ) {
                    $selected = ' selected="selected" ';
                } else {
                    $selected = '';
                }
                $catSelList .= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
            }

            $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
            $T->set_file ('page','edit-event-series-form.thtml');

            $T->set_var(array(
                'title'             => $row['title'],
                'location'          => $row['location'],
                'description'       => $row['description'],
                'parent_id'         => $parent_id,
                'category_select'   => $catSelList,
                'lang_category'     => $LANG_AC['category'],
                'lang_event_title'  => $LANG_AC['event_title'],
                'lang_location'     => $LANG_AC['location'],
                'lang_description'  => $LANG_AC['description'],
                'lang_save'         => $LANG_AC['save'],
                'lang_delete'       => $LANG_AC['delete'],
                'lang_cancel'       => $LANG_AC['cancel'],
             ));

            $T->parse('output', 'page');
            $page = $T->finish($T->get_var('output'));
        }
        return $page;
    }

    public function getCategories()
    {
        global $_CONF, $_AC_CONF, $_TABLES, $LANG_AC;

        $retval = array();

        $result = DB_query("SELECT * FROM {$_TABLES['ac_category']} WHERE category_id > 1 ORDER BY cat_name ASC");
        $retval[1] = $LANG_AC['no_category'];
        while ( ( $row = DB_fetchArray($result)) != null  ) {
            $retval[$row['category_id']] = $row['cat_name'];
        }
        return $retval;
    }
}
