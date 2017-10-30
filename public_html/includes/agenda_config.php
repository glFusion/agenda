<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Agenda Configuration
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$agendaConfig = array();

$agendaConfig = array(
    'allow_new'     => false,
    'allow_edit'    => false,
);

switch ($_AC_CONF['allow_entry'])
{
    case 0 :
        if (SEC_inGroup('Agenda Admin')) {
            $agendaConfig['allow_new'] = true;
            $agendaConfig['allow_edit'] = true;
        }
        break;
    case 1 :
        if ( !COM_isAnonUser() ) {
            $agendaConfig['allow_new']  = true;
            $agendaConfig['allow_edit'] = false;
        }
        break;
    case 2 :
        $agendaConfig['allow_new']  = true;
        $agendaConfig['allow_edit'] = false;
        break;
    default :
        if (SEC_inGroup('Agenda Admin')) {
            $agendaConfig['allow_new']  = true;
            $agendaConfig['allow_edit'] = false;
        }
        break;
}

echo json_encode($agendaConfig);
exit;
?>