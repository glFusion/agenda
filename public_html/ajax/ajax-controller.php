<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Agenda AJAX Controller
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$ajaxHandler = new \ajaxHandler();

$retval = '';
$action = '';
if (isset($_POST['action'])) {
    $action = COM_applyFilter($_POST['action']);
}

switch ($action) {
    case 'get-config' :
        $retval = getConfig();
        break;

    case 'get-lang' : // save an edit
        $retval = getLang();
        break;

    case 'setup-agenda' :
        $config = getConfig();
        $lang   = getLang();

        $ajaxHandler->setResponse( 'config', $config );
        $ajaxHandler->setResponse( 'lang', $lang );
        $ajaxHandler->setErrorCode( 0 );
        $ajaxHandler->sendResponse();
        exit;

    default :
        break;
}

echo json_encode($retval);
exit;

function getConfig()
{
    global $_CONF, $_AC_CONF;

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
    return $agendaConfig;
}

function getLang()
{
    global $_CONF, $_AC_CONF, $LANG_AC_JS;

    return $LANG_AC_JS;
}


?>