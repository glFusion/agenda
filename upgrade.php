<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Upgrade
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

// this function is called by lib-plugin whenever the 'Upgrade' option is
// selected in the Plugin Administration screen for this plugin

function agenda_upgrade()
{
    global $_TABLES, $_CONF, $_AC_CONF, $_DB_table_prefix;

    $currentVersion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='agenda'");

    switch ($currentVersion) {
        case '0.2.0' :
            $c = config::get_instance();
            $c->add('disable_submissions', 0,'select', 0, 0, 0, 7, true, 'agenda');

        case '0.3.0' :
            DB_query("ALTER TABLE {$_TABLES['agenda']} ADD email VARCHAR(96) NULL DEFAULT NULL AFTER owner_id",1);

        case '0.4.0' :
        case '0.4.5' :
        case '0.5.0' :
            // no changes in DB / config structure
        case '0.6.0' :
            $c = config::get_instance();
            $c->add('centerblock_where', -1,'select', 0, 0, 3, 40, true, 'agenda');

        case '0.7.0' :
            // no changes

        case '1.0.0' :
            $c = config::get_instance();
            $c->add('centerblock_rotate', false,'select', 0, 0, 0, 45, true, 'agenda');

        default:
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version='".$_AC_CONF['pi_version']."',pi_gl_version='".$_AC_CONF['gl_version']."' WHERE pi_name='agenda' LIMIT 1");
            break;
    }

    CTL_clearCache();

    if ( DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='agenda'") == $_AC_CONF['pi_version']) {
        return true;
    } else {
        return false;
    }
}
?>