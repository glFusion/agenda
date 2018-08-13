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
*  Copyright (C) 2016-2018 by the following authors:
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
        case '0.2.1' :
        case '0.2.2' :
            $sql = "ALTER TABLE {$_TABLES['ac_event']} ADD COLUMN `rrule` VARCHAR(128) NULL AFTER `repeat_freq`;";
            DB_query($sql,1);

        case '0.3.0' :
        case '0.3.1' :
        case '0.3.2' :
        case '0.4.0' :
        case '0.5.0' :
        case '0.6.0' :
        case '0.6.1' :
        case '0.7.0' :
            $sql = "ALTER TABLE {$_TABLES['ac_events']} ADD INDEX `start` (`start`);";
            $sql = "ALTER TABLE {$_TABLES['ac_events']} ADD INDEX `end` (`end`);";
            DB_query($sql,1);

        case '0.8.0' :

        case '0.9.0' :
            $sql = "ALTER TABLE {$_TABLES['ac_event']} ADD COLUMN `ip` VARBINARY(16) NULL AFTER `owner_id`;";
            DB_query($sql,1);

        case '1.0.0' :
            // no changes

        case '1.0.1' :
            // no changes

        default:
            agenda_update_config();
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

function agenda_update_config()
{
    global $_CONF, $_AC_CONF, $_TABLES;

    $c = config::get_instance();

    require_once $_CONF['path'].'plugins/agenda/sql/agenda_config_data.php';

    // remove stray items
    $result = DB_query("SELECT * FROM {$_TABLES['conf_values']} WHERE group_name='agenda'");
    while ( $row = DB_fetchArray($result) ) {
        $item = $row['name'];
        if ( ($key = _searchForIdKey($item,$agendaConfigData)) === NULL ) {
            DB_query("DELETE FROM {$_TABLES['conf_values']} WHERE name='".DB_escapeString($item)."' AND group_name='agenda'");
        } else {
            $agendaConfigData[$key]['indb'] = 1;
        }
    }
    // add any missing items
    foreach ($agendaConfigData AS $cfgItem ) {
        if (!isset($cfgItem['indb']) ) {
            _addConfigItem( $cfgItem );
        }
    }
    $c = config::get_instance();
    $c->initConfig();
    $tcnf = $c->get_config('agenda');
    // sync up sequence, etc.
    foreach ( $agendaConfigData AS $cfgItem ) {
        $c->sync(
            $cfgItem['name'],
            $cfgItem['default_value'],
            $cfgItem['type'],
            $cfgItem['subgroup'],
            $cfgItem['fieldset'],
            $cfgItem['selection_array'],
            $cfgItem['sort'],
            $cfgItem['set'],
            $cfgItem['group']
        );
    }
}

if ( !function_exists('_searchForId')) {
    function _searchForId($id, $array) {
       foreach ($array as $key => $val) {
           if ($val['name'] === $id) {
               return $array[$key];
           }
       }
       return null;
    }
}

if ( !function_exists('_searchForIdKey')) {
    function _searchForIdKey($id, $array) {
       foreach ($array as $key => $val) {
           if ($val['name'] === $id) {
               return $key;
           }
       }
       return null;
    }
}

if ( !function_exists('_addConfigItem')) {
    function _addConfigItem($data = array() )
    {
        global $_TABLES;

        $Qargs = array(
                       $data['name'],
                       $data['set'] ? serialize($data['default_value']) : 'unset',
                       $data['type'],
                       $data['subgroup'],
                       $data['group'],
                       $data['fieldset'],
                       ($data['selection_array'] === null) ?
                        -1 : $data['selection_array'],
                       $data['sort'],
                       $data['set'],
                       serialize($data['default_value']));
        $Qargs = array_map('DB_escapeString', $Qargs);

        $sql = "INSERT INTO {$_TABLES['conf_values']} (name, value, type, " .
            "subgroup, group_name, selectionArray, sort_order,".
            " fieldset, default_value) VALUES ("
            ."'{$Qargs[0]}',"   // name
            ."'{$Qargs[1]}',"   // value
            ."'{$Qargs[2]}',"   // type
            ."{$Qargs[3]},"     // subgroup
            ."'{$Qargs[4]}',"   // groupname
            ."{$Qargs[6]},"     // selection array
            ."{$Qargs[7]},"     // sort order
            ."{$Qargs[5]},"     // fieldset
            ."'{$Qargs[9]}')";  // default value

        DB_query($sql);
    }
}
?>