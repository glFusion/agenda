<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Auto Installer
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

global $_DB_table_prefix, $_TABLES;

// Plugin info

$_AC_CONF['pi_name']            = 'agenda';
$_AC_CONF['pi_display_name']    = 'Agenda';
$_AC_CONF['pi_version']         = '1.0.1';
$_AC_CONF['gl_version']         = '1.7.0';
$_AC_CONF['pi_url']             = 'https://www.glfusion.org/';

$_TABLES['ac_event']        = $_DB_table_prefix . 'ac_event';
$_TABLES['ac_events']       = $_DB_table_prefix . 'ac_events';
$_TABLES['ac_category']     = $_DB_table_prefix . 'ac_category';
?>