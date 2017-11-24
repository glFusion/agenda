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
    global $_CONF, $_AC_CONF, $LANG_DIRECTION;

    $agendaConfig = array();

    if ( isset($_AC_CONF['printenabled']) && $_AC_CONF['printenabled'] == 1 ) {
        if ( $_AC_CONF['header_left'] == 'today prev,next' || $_AC_CONF['header_left'] == 'today prevYear,nextYear prev,next' ) {
            $_AC_CONF['header_left'] = $_AC_CONF['header_left'] . ' print';
        } elseif ( $_AC_CONF['header_center'] == 'today prev,next' || $_AC_CONF['header_center'] == 'today prevYear,nextYear prev,next' ) {
            $_AC_CONF['header_center'] = $_AC_CONF['header_center'] . ' print';
        } elseif ( $_AC_CONF['header_right'] == 'today prev,next' || $_AC_CONF['header_right'] == 'today prevYear,nextYear prev,next' ) {
            $_AC_CONF['header_right'] = $_AC_CONF['header_right'] . ' print';
        }
    }

    $_AC_CONF['iso_lang'] = AC_getLocale();

    if ( isset($LANG_DIRECTION) && $LANG_DIRECTION == 'rtl' ) {
        $_AC_CONF['isRTL'] = true;
    } else {
        $_AC_CONF['isRTL'] = false;
    }

    $agendaConfig = $_AC_CONF;
    $agendaConfig['allow_new']  = false;
    $agendaConfig['allow_edit'] = false;

    if (SEC_hasRights('agenda.admin')) {
        $agendaConfig['allow_new'] = true;
        $agendaConfig['allow_edit'] = true;
    } else {
        switch ($_AC_CONF['allow_entry']) {
            case 0 :
                if (SEC_hasRights('agenda.admin')) {
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
                if (SEC_hasRights('agenda.admin')) {
                    $agendaConfig['allow_new']  = true;
                    $agendaConfig['allow_edit'] = false;
                }
            break;
        }
    }
    return $agendaConfig;
}

function getLang()
{
    global $_CONF, $_AC_CONF, $LANG_AC_JS;

    return $LANG_AC_JS;
}


?>