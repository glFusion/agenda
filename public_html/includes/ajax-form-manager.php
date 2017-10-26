<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Form Manager
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$action = COM_applyFilter($_POST['action']);

switch ($action) {

    case 'edit-event' :
        $parent_id = COM_applyFilter($_POST['parent_id'],true);

        $result = DB_query("SELECT * FROM {$_TABLES['ac_event']} WHERE parent_id=" . (int) $parent_id);
        if ( DB_numRows($result) > 0 ) {
            $row = DB_fetchArray($result);

            $T = new Template ($_CONF['path'] . 'plugins/agenda/templates');
            $T->set_file ('page','edit-event-form.thtml');

            $T->set_var(array(
                'event_title'       => $row['title'],
                'start_date'        => $row['start_date'],
                'end_date'          => $row['end_date'],
                'start_time'        => $row['start_time'],
                'end_time'          => $row['end_time'],
                'location'          => $row['location'],
                'description'       => $row['description'],
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
            $T->parse('output', 'page');
            $page = $T->finish($T->get_var('output'));
        }


        break;


    case 'new-event' :
        $T = new Template ($_CONF['path'] . 'plugins/agenda/templates');
        $T->set_file ('page','new-event-form.thtml');

        $T->parse('output', 'page');
        $page = $T->finish($T->get_var('output'));
        break;
}

echo $page;

?>