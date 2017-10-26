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
    'admin_help'        => 'Administer events',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'You are not authorized to view this Page.  Your user name and IP have been recorded.',
    'category_list'     => 'Categories',
    'admin'		        => 'Agenda Admin',
    'cancel'			=> 'Cancel',
    'delete'			=> 'Delete',
    'save'				=> 'Save',
    'create'            => 'New Event',
    'header'            => 'Agenda',
);

$LANG_AC_ERRORS = array(
    'invalid_title'     => 'You must enter an event title',
);

$LANG_configsections['agenda'] = array(
    'label' => 'Agenda',
    'title' => 'Agenda Plugin Configuration',
);

$LANG_confignames['agenda'] = array(
    'displayblocks'         => 'Display Blocks',
);

$LANG_configsubgroups['agenda'] = array(
    'sg_main' => 'Main Settings',
);

$LANG_fs['agenda'] = array(
    'fs_main' => 'Main Settings',
);

$LANG_configselects['agenda'] = array(
    0  => array('True' => 1, 'False' => 0 ),
    1  => array('Left Blocks' => 0, 'Right Blocks' => 1, 'All Blocks' => 2, 'No Blocks' => 3),
    2  => array('Yes' => 1, 'No' => 0 ),
    3  => array('No Centerblock' => -1, 'Top of Page' => 1, 'After Featured Story' => 2, 'Bottom of Page' => 3),
);

?>