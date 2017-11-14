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

/**
*   Initialize agenda plugin configuration
*
*   @return boolean             true: success; false: an error occurred
*/
function plugin_initconfig_agenda()
{
    global $_CONF;

    $c = config::get_instance();

    if (!$c->group_exists('agenda')) {
        require_once $_CONF['path'].'plugins/agenda/sql/agenda_config_data.php';

        foreach ( $agendaConfigData AS $cfgItem ) {
            $c->add(
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
     return true;
}

?>
