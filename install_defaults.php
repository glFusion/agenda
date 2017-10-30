<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Configuration Settings
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

global $_AC_CONF;

if (!isset($_AC_CONF) || empty($_AC_CONF)) {
    $_AC_CONF = array();
    require_once dirname(__FILE__) . '/agenda.php';
}

global $_AC_DEFAULTS;

$_AC_DEFAULTS = array(

// permission related field set
    'allow_anonymous_view'  => 1,       // 0 = no, 1 = yes
    'security_exit'         => 0,       // 0 = not found (404) - 1 = login screen
    'allow_entry'           => 0,       // 0 = Admin Only, 1 = logged in, 2 = anyone
    'submission_queue'      => 0,       // 0 none - 1 anonymous only 2 = Logged in / Anonymous

// display items fieldset

    'displayblocks'         => 3,       // 'Left Blocks' => 0, 'Right Blocks' => 1, 'All Blocks' => 2, 'No Blocks' => 3
);

/**
*   Initialize agenda plugin configuration
*
*   @return boolean             true: success; false: an error occurred
*/
function plugin_initconfig_agenda()
{
    global $_CONF, $_AC_CONF, $_AC_DEFAULTS;

    $c = config::get_instance();

    if (!$c->group_exists('agenda')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true,'agenda');
        $c->add('fs_permissions', NULL, 'fieldset', 0, 0, NULL, 0, true,'agenda');
//add($param_name, $default_value, $type, $subgroup, $fieldset,
//         $selection_array=null, $sort=0, $set=true, $group='Core')
        $c->add('allow_anonymous_view', $_AC_DEFAULTS['allow_anonymous_view'],'select',0,0,0,10,true,'agenda');
        $c->add('security_exit', $_AC_DEFAULTS['security_exit'],'select',0,0,1,20,true,'agenda');
        $c->add('allow_entry', $_AC_DEFAULTS['allow_entry'],'select',0,0,2,30,true,'agenda');
        $c->add('submission_queue', $_AC_DEFAULTS['submission_queue'],'select',0,0,3,40,true,'agenda');

// display fieldset
        $c->add('fs_display', NULL, 'fieldset', 0, 1, NULL, 0, true,'agenda');
        $c->add('displayblocks', $_AC_DEFAULTS['displayblocks'],'select', 0, 1, 4, 10, true, 'agenda');

     }
     return true;
}

?>
