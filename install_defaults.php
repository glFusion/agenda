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

/** Utility plugin configuration data
*   @global array */
global $_AC_CONF;
if (!isset($_AC_CONF) || empty($_AC_CONF)) {
    $_AC_CONF = array();
    require_once dirname(__FILE__) . '/agenda.php';
}

/** Utility plugin default configurations
*   @global array */
global $_AC_DEFAULTS;
$_AC_DEFAULTS = array(
    'displayblocks'         => 0,
    'disable_submissions'   => false,
    'anonymous_submit'      => false,
    'queue_submissions'     => true,
    'speedlimit'            => 300,
    'per_page'              => 15,
    'centerblock_where'     => -1,
    'centerblock_rotate'    => false,
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
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true,'agenda');
        $c->add('displayblocks', $_AC_DEFAULTS['displayblocks'],'select', 0, 0, 1, 5, true, 'agenda');
        $c->add('disable_submissions', $_AC_DEFAULTS['disable_submissions'],'select', 0, 0, 0, 10, true, 'agenda');
        $c->add('anonymous_submit', $_AC_DEFAULTS['anonymous_submit'],'select', 0, 0, 2, 15, true, 'agenda');
        $c->add('queue_submissions', $_AC_DEFAULTS['queue_submissions'],'select', 0, 0, 0, 20, true, 'agenda');
        $c->add('speedlimit', $_AC_DEFAULTS['speedlimit'],'text', 0, 0, NULL, 25, true, 'agenda');
        $c->add('per_page', $_AC_DEFAULTS['per_page'],'text', 0, 0, NULL, 30, true, 'agenda');
        $c->add('centerblock_where', $_AC_DEFAULTS['per_page'],'select', 0, 0, 3, 40, true, 'agenda');
        $c->add('centerblock_rotate', $_AC_DEFAULTS['centerblock_rotate'],'select', 0, 0, 0, 45, true, 'agenda');
     }
     return true;
}

?>
