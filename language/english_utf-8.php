<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* English Language - UTF-8
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

$LANG_AC = array (
    'plugin'            => 'agenda',
    'plugin_name'       => 'Agenda',
    'plugin_admin'		=> 'Agenda Admin',
    'event_list'        => 'Agenda Events',
    'admin_help'        => 'Create, Edit and Delete Agenda Categories which can be used highlight events on the Agenda Calendar.<br><br>Event Administration is done in the Agenda Calendar Interface.',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'You are not authorized to view this Page. Your user name and IP have been recorded.',
    'category_list'     => 'Categories',
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
    'published'         => 'Published',
    'choose'            => 'Choose',
    'more'              => 'more',
    'less'              => 'less',
    'sample_category'   => 'Sample Category',
    'submission_mod_approved' => 'Event Submission Approved',
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
);

// JavaScript specific translations
$LANG_AC_JS = array(
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
);

$LANG_AC_ERRORS = array(
    'invalid_title'     => 'You must enter an event title',
);

$LANG_configsections['agenda'] = array(
    'label' => 'Agenda',
    'title' => 'Agenda Plugin Configuration',
);

$LANG_confignames['agenda'] = array(
    'allow_anonymous_view'  => 'Allow Non-Logged-In users to view calendar',
    'security_exit'         => 'Security Exit',
    'allow_entry'           => 'Who Can Submit Events',
    'submission_queue'      => 'Submission Queue',
    'displayblocks'         => 'Display Blocks',
    'showupcomingevents'    => 'Show Upcoming Events Block',
    'upcomingeventsrange'   => 'Days to include in Upcoming Event Block',
    'defaultview'           => 'Default View',
    'autoheight'            => 'Auto Height',
    'header_left'           => 'Header Left',
    'header_center'         => 'Header Center',
    'header_right'          => 'Header Right',
    'first_day'             => 'First Day of Week',
);

$LANG_configsubgroups['agenda'] = array(
    'sg_main' => 'Main Settings',
);

$LANG_fs['agenda'] = array(
    'fs_main'       => 'Main Settings',
    'fs_permissions'=> 'Permission Settings',
    'fs_display'    => 'Display Settings',
    'fs_advanced'   => 'Advanced Settings',
);

$LANG_configselects['agenda'] = array(
    0  => array('True' => 1, 'False' => 0 ),
    1  => array('Not Found Page (404)' => 0, 'Login Screen' => 1),
    2  => array('Admin Only' => 0,'Logged-In-Users' => 1, 'All Users' => 2),
    3  => array('Disabled' => 0, 'Anonymous Only' => 1, 'All Users' => 2),
    4  => array('Left Blocks' => 0, 'Right Blocks' => 1, 'All Blocks' => 2, 'No Blocks' => 3),
    5  => array('Month' => 'month','Week' => 'agendaWeek', 'Day' => 'agendaDay', 'List' => 'listMonth'),
    6  => array('Auto' => 'auto', 'Fit' => 'fit'),
    7  => array(
        'Title' => 'title',
        'None'  => '',
        'Today Prev,Next' => 'today prev,next',
        'Today PrevYear,NextYear Prev,Next' => 'today prevYear,nextYear prev,next',
        'Views' => 'month,agendaWeek,agendaDay,listMonth',
    ),
    8  => array(
        'Sunday'    => 0,
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6
        ),
);

?>