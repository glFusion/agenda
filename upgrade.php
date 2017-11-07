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
        case '0.1.0' :
            $c = config::get_instance();
            // no changes

        case '0.1.1' :
            // added new features

            $sql = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) "
                 . "VALUES (NULL, 'agenda.view', 'Allows access to the Agenda Calendar', 1)";

            $result = DB_query($sql);
            $agendaViewFeatureId = DB_insertId();

            $sql = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) "
                 . "VALUES (NULL, 'agenda.noqueue', 'Bypasses the Agenda Submission Queue', 1)";

            $result = DB_query($sql);
            $agendaNoqueueFeatureId = DB_insertId();

            $agendaAdminGroup = DB_getItem($_TABLES['groups'],'grp_id','grp_name="Agenda Admin"');

            // assign new features to admin group..
            $sql = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ({$agendaViewFeatureId}, {$agendaAdminGroup})";
            DB_query($sql);
            $sql = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ({$agendaNoqueueFeatureId}, {$agendaAdminGroup})";
            DB_query($sql);
            $sql = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ({$agendaViewFeatureId}, 13)";
            DB_query($sql);

        case '0.2.0' :
            // no changes

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