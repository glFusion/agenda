<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* English Language
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2019 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$LANG_AC = array (
    'plugin'            => 'agenda',
    'plugin_name'       => 'Agenda',
    'plugin_admin'		=> 'Agenda Admin',
    'event_list'        => 'Agenda Events',
    'admin_help'        => 'Create, Edit and Delete Agenda Categories which can be used highlight events on the Agenda Calendar.<br><br>Event Administration is done in the Agenda Calendar Interface.',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'You are not authorized to view this Page. Your user name and IP have been recorded.',
    'category_list'     => 'Categories',
    'edit_category'     => 'Edit Category',

    'event_list'        => 'Events',
    'admin'		        => 'Agenda Admin',
    'cancel'			=> 'Cancel',
    'delete'			=> 'Delete',
    'save'				=> 'Save',
    'create'            => 'New Event',
    'category_new'      => 'New Category',
    'header'            => 'Agenda',
    'category_name'     => 'Category Name',
    'category_desc'     => 'Category Description',
    'no_categories'     => 'No categories',
    'bgcolor'           => 'Background Color',
    'fgcolor'           => 'Text Color',
    'color_preview'     => 'Color Preview',
    'submissions'       => 'Agenda Submissions',
    'edit'              => 'Edit',
    'title'             => 'Title',
    'owner'             => 'Owner',
    'start_date'        => 'Start Date',
    'end_date'          => 'End Date',
    'allday'            => 'All Day',
    'no_events'         => 'No Events Found',
    'delete_checked'    => 'Delete Checked',
    'delete_confirm'    => 'Are you sure you want to delete the selected category?',
    'delete_confirm_event'    => 'Are you sure you want to delete the selected events?',
    'published'         => 'Published',
    'choose'            => 'Choose',
    'more'              => 'more',
    'less'              => 'less',
    'sample_category'   => 'Sample Category',
    'submission_mod_approved' => 'Event Submission Approved',
    'series'            => 'Series',
    'event_exception'   => 'This event is part of a series but has been modified',
    'event_list'        => 'Event List',
// block strings
    'site_events'       => 'Site Events',
    'no_upcoming'       => 'No Upcoming Events',
    'today'             => 'Today',
// event forms
    'event_title'       => 'Event Title',
    'location'          => 'Event Location',
    'event_start'       => 'Event Start',
    'start_date'        => 'Start Date',
    'start_time'        => 'Start Time',
    'all_day_event'     => 'All Day Event',
    'event_end'         => 'Event End',
    'end_date'          => 'End Date',
    'end_time'          => 'End Time',
    'repeats'           => 'Repeats',
    'repeat_options'    => 'Repeat Options',
    'daily'             => 'Daily',
    'weekly'            => 'Weekly',
    'biweekly'          => 'Every 2 Weeks',
    'monthly'           => 'Monthly',
    'yearly'            => 'Yearly',
    'description'       => 'Description',
    'category'          => 'Category',
    'no_category'       => 'No Category Selected',
    'no_category_desc'  => 'Default category used when no category is selected',
    'edit_single_or_series' => 'Edit Event or Series',
    'what_to_edit'       => 'This is one event in a series. What do you want to edit?',
    'just_this_one'     => 'Just this one',
    'entire_series'     => 'The entire series',
    'series_delete_msg' => 'The Event Series has been deleted',
    'event_saved_msg'   => 'The Event  has been saved',

    // rrule

    'repeat'            => 'Repeat',
    'none'              => 'No Repeat',
    'hourly'            => 'Hourly',
    'daily'             => 'Daily',
    'weekly'            => 'Weekly',
    'monthly'           => 'Monthly',
    'yearly'            => 'Yearly',
    'every'             => 'every',
    'hours'             => 'hour(s)',
    'days'              => 'day(s)',
    'weeks'             => 'week(s)',
    'months'            => 'month(s)',
// LANG_WEEK already has these - just use them...
    'weekly_help'       => 'Select each day of the week for event to occur',
    'on_day'            => 'on day',
    'on_the'            => 'on the',
    'first'             => 'First',
    'second'            => 'Second',
    'third'             => 'Third',
    'forth'             => 'Fourth',
    'fifth'             => 'Fifth',
    'last'              => 'Last',
//LANG WEEK has weekday names too
    'day'               => 'Day',
    'weekday'           => 'Weekday',
    'weekend'           => 'Weekend',
// LANG_MONTH has month names and abbreviations
    'after'             => 'After',
    'on_date'           => 'On Date',
    'occurrences'       => 'Occurrences',
    'end_after_date'    => 'End After Date',
    'of'                => 'of',
    'end'               => 'End',
    'exception_warning' => '<strong>This event series has exception events.</strong><br>Exception events will be removed and the entire series re-created if you edit the recurrence rules.',
    'edit_recurrence'   => 'Edit Recurrence',
    'exception'         => 'Exception',
    'ip_address'        => 'IP Address',
);

// JavaScript specific translations
$LANG_AC_JS = array(
    'agenda_calendar'   => 'Agenda Calendar',
    'add_event'         => 'Add Event',
    'edit_event'        => 'Edit Event',
    'edit_event_series' => 'Edit Event Series',
    'save_event'        => 'Save Event',
    'delete_event'      => 'Delete Event',
    'delete_series'     => 'Delete Series',
    'edit'              => 'Edit',
    'edit_series'       => 'Edit Series',
    'close'             => 'Close',
    'delete_event_confirm' => 'Are you positive you want to delete this event?',
    'delete_series_confirm' => 'Are you positive you want to DELETE THE ENTIRE SERIES OF EVENTS?',
    'cancel'            => 'Cancel',
    'when'              => 'When',
    'location'          => 'Location',
    'details'           => 'Details',
    'err_select_option' => 'Please select an option',
    'err_enter_title'   => 'Please enter an event title',
    'err_end_before_start' => 'End date/time must be greater than start date/time',
    'err_initialize'    => 'Error Initializing Agenda Plugin',
    'event_queued'      => 'Thank you for your submission. Your event submission has been placed in the queue for review and approval.',
    'exception_event'   => 'Exception Event',
    'print'             => 'print',
    'spam'              => 'Event detected to be Spam',
);

$LANG_AC_ERRORS = array(
    'invalid_title'     => 'You must enter an event title',
);

$LANG_configsections['agenda'] = array(
    'label' => 'Agenda',
    'title' => 'Agenda Plugin Configuration',
);

$LANG_confignames['agenda'] = array(
// General Settings
    'allow_anonymous_view'  => 'Allow Non-Logged-In users to view calendar',
    'security_exit'         => 'Security Exit',
    'allow_entry'           => 'Who Can Submit Events',
    'submission_queue'      => 'Submission Queue',
    'displayblocks'         => 'Display Blocks',
    'showupcomingevents'    => 'Show Upcoming Events Block',
    'upcomingeventsrange'   => 'Days to include in Upcoming Event Block',
    'maintenance_check_freq' => 'How Often to Purge Old Events (Days)',
    'maintenance_max_age'   => 'How Many Years of Old Events to Keep (Years)',

// Global Calendar Settings

    'defaultview'           => 'Default View',
    'autoheight'            => 'Auto Height',
    'header_left'           => 'Header Left',
    'header_center'         => 'Header Center',
    'header_right'          => 'Header Right',
    'first_day'             => 'First Day of Week',
    'weeknumbers'           => 'Display Week Numbers',
    'printenabled'          => 'Enable Print',

// View Settings - Month View

    'month_eventlimit'          => 'Event Limit',
    'month_timeformat'          => 'Time Format',
    'month_displayeventtime'    => 'Display Start Time',
    'month_displayeventend'     => 'Display End Time',
    'month_columnformat'        => 'Column Date / Time Format',
    'month_titleformat'         => 'Header Title Date / Time Format',

// Week View

    'week_eventlimit'           => 'Event Limit',
    'week_timeformat'           => 'Time Format',
    'week_displayeventtime'     => 'Display Start Time',
    'week_displayeventend'      => 'Display End Time',
    'week_columnformat'         => 'Week Column Date / Time Format',
    'week_titleformat'          => 'Header Title Date / Time Format',

// Day View
    'day_eventlimit'            => 'Event Limit',
    'day_timeformat'            => 'Time Format',
    'day_displayeventtime'      => 'Display Start Time',
    'day_displayeventend'       => 'Display End Time',
    'day_columnformat'          => 'Column Date / Time Format',
    'day_titleformat'           => 'Header Title Date / Time Format',

// List View
    'list_timeformat'           => 'Time Format',
    'list_displayeventtime'     => 'Display Start Time',
    'list_displayeventend'      => 'Display End Time',

);

$LANG_configsubgroups['agenda'] = array(
    'sg_main' => 'Main Settings',
);

$LANG_fs['agenda'] = array(
    'fs_main'       => 'Main Settings',
    'fs_general'    => 'General Settings',
    'fs_global'     => 'Global View Settings',
    'fs_month'      => 'Month View Settings',
    'fs_week'       => 'Week View Settings',
    'fs_day'        => 'Day View Settings',
    'fs_list'       => 'List View Settings',
);

$LANG_configSelect['agenda'] = array(
    0  => array(1=>'True', 0 => 'False'),
    1  => array(0=>'Not Found Page (404)', 1=>'Login Screen'),
    2  => array(0=>'Admin Only',1=>'Logged-In-Users', 2=>'All Users'),
    3  => array(0=>'Disabled', 1=>'Anonymous Only', 2=>'All Users'),
    4  => array(0=>'Left Blocks', 1=>'Right Blocks', 2=>'All Blocks', 3=>'No Blocks'),
    5  => array('month'=>'Month','agendaWeek'=>'Week', 'agendaDay'=>'Day', 'listMonth'=>'List'),
    6  => array('auto'=>'Auto', 'fit'=>'Fit'),
    7  => array(
        'title' =>'Title',
        'none'  => 'None',
        'today prev,next'=>'Today Prev,Next',
        'today prevYear,nextYear prev,next' => 'Today PrevYear,NextYear Prev,Next',
        'month,agendaWeek,agendaDay,listMonth' => 'Views',
    ),
    8  => array(
        0 =>'Sunday',
        1 =>'Monday',
        2 =>'Tuesday',
        3 =>'Wednesday',
        4 =>'Thursday',
        5 =>'Friday',
        6 =>'Saturday'
    ),
);

?>