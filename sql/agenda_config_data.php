<?php
/**
* glFusion CMS
*
* Agenda Plugin Configuration
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$agendaConfigData = array(
    array(
    	'name' => 'sg_main',
    	'default_value' => NULL,
    	'type' => 'subgroup',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'fs_general',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'allow_anonymous_view',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 0,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'security_exit',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 1,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'allow_entry',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 2,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'submission_queue',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 3,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),

    array(
    	'name' => 'displayblocks',
    	'default_value' => 3,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 4,
    	'sort' => 50,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'showupcomingevents',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => 0,
    	'sort' => 60,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'upcomingeventsrang',
    	'default_value' => 14,
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 0,
    	'selection_array' => NULL,
    	'sort' => 70,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
// Global Calendar Settings
    array(
    	'name' => 'fs_global',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'defaultview',
    	'default_value' => 'month',
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 5,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'autoheight',
    	'default_value' => 'fit',
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 6,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'header_left',
    	'default_value' => 'today prev,next',
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 7,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'header_center',
    	'default_value' => 'title',
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 7,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'header_right',
    	'default_value' => 'month,agendaWeek,agendaDay,listMonth',
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 7,
    	'sort' => 50,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'first_day',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 8,
    	'sort' => 60,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'weeknumbers',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 1,
    	'selection_array' => 0,
    	'sort' => 70,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
// Month View
    array(
    	'name' => 'fs_month',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'month_eventlimit',
    	'default_value' => 5,
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => NULL,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'month_timeformat',
    	'default_value' => 'hh:mm a',
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => NULL,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'month_displayeventtime',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => 0,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'month_displayeventend',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 2,
    	'selection_array' => 0,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),

// week view
    array(
    	'name' => 'fs_week',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 3,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'week_eventlimit',
    	'default_value' => 5,
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 3,
    	'selection_array' => NULL,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'week_timeformat',
    	'default_value' => 'hh:mm a',
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 3,
    	'selection_array' => NULL,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'week_displayeventtime',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 3,
    	'selection_array' => 0,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'week_displayeventend',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 3,
    	'selection_array' => 0,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
// day view
    array(
    	'name' => 'fs_day',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 4,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'day_eventlimit',
    	'default_value' => 5,
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 4,
    	'selection_array' => NULL,
    	'sort' => 10,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'day_timeformat',
    	'default_value' => 'hh:mm a',
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 4,
    	'selection_array' => NULL,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'day_displayeventtime',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 4,
    	'selection_array' => 0,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'day_displayeventend',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 4,
    	'selection_array' => 0,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),

// list view
    array(
    	'name' => 'fs_list',
    	'default_value' => NULL,
    	'type' => 'fieldset',
    	'subgroup' => 0,
    	'fieldset' => 5,
    	'selection_array' => NULL,
    	'sort' => 0,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'list_timeformat',
    	'default_value' => 'hh:mm a',
    	'type' => 'text',
    	'subgroup' => 0,
    	'fieldset' => 5,
    	'selection_array' => NULL,
    	'sort' => 20,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'list_displayeventtime',
    	'default_value' => 1,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 5,
    	'selection_array' => 0,
    	'sort' => 30,
    	'set' => TRUE,
    	'group' => 'agenda'
    ),
    array(
    	'name' => 'list_displayeventend',
    	'default_value' => 0,
    	'type' => 'select',
    	'subgroup' => 0,
    	'fieldset' => 5,
    	'selection_array' => 0,
    	'sort' => 40,
    	'set' => TRUE,
    	'group' => 'agenda'
    )
);
?>