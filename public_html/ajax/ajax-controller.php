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

    // locale mapping $LANG_LOCALE
    // Set the ISO 2 digit code for language
    switch ($_CONF['language']) {
        case 'afrikaans' :
        case 'afrikaans_utf-8' :
            $_AC_CONF['iso_lang'] = 'af';
            break;
// bosnian not support by FC
        case 'bosnian' :
        case 'bosnian_utf-8' :
            $_AC_CONF['iso_lang'] = 'en';
            break;
        case 'bulgarian' :
        case 'bulgarian_utf-8' :
            $_AC_CONF['iso_lang'] = 'bg';
            break;
        case 'catalan' :
        case 'catalan_utf-8' :
            $_AC_CONF['iso_lang'] = 'ca';
            break;
        case 'chinese_traditional' :
        case 'chinese_traditional_utf-8' :
        case 'chinese_simplified' :
        case 'chinese_simplified_utf-8' :
            $_AC_CONF['iso_lang'] = 'zh-cn';
            break;
        case 'croatian' :
        case 'croatian_utf-8' :
            $_AC_CONF['iso_lang'] = 'hr';
            break;
        case 'czech' :
        case 'czech_utf-8' :
            $_AC_CONF['iso_lang'] = 'cs';
            break;
        case 'danish' :
        case 'danish_utf-8' :
            $_AC_CONF['iso_lang'] = 'da';
            break;
        case 'dutch' :
        case 'dutch_utf-8' :
            $_AC_CONF['iso_lang'] = 'nl';
            break;
        case 'english' :
        case 'english_utf-8' :
            $_AC_CONF['iso_lang'] = 'en';
            break;
        case 'estonian' :
        case 'estonian_utf-8' :
            $_AC_CONF['iso_lang'] = 'et';
            break;
        case 'farsi' :
        case 'farsi_utf-8' :
            $_AC_CONF['iso_lang'] = 'fa';
            break;
        case 'finnish' :
        case 'finnish_utf-8' :
            $_AC_CONF['iso_lang'] = 'fi';
            break;
        case 'french_canada' :
        case 'french_canada_utf-8' :
            $_AC_CONF['iso_lang'] = 'fr-ca';
            break;
        case 'french_france' :
        case 'french_france_utf-8' :
            $_AC_CONF['iso_lang'] = 'fr';
            break;
        case 'german' :
        case 'german_utf-8' :
        case 'german_formal' :
        case 'german_formal_utf-8' :
            $_AC_CONF['iso_lang'] = 'de';
            break;
        case 'hebrew' :
        case 'hebrew_utf-8' :
            $_AC_CONF['iso_lang'] = 'he';
            break;
        case 'hellenic' :
        case 'hellenic_utf-8' :
            $_AC_CONF['iso_lang'] = 'el';
            break;
        case 'indonesian' :
        case 'indonesian_utf-8' :
            $_AC_CONF['iso_lang'] = 'id';
            break;
        case 'italian' :
        case 'italian_utf-8' :
            $_AC_CONF['iso_lang'] = 'it';
            break;
        case 'japanese' :
        case 'japanese_utf-8' :
            $_AC_CONF['iso_lang'] = 'ja';
            break;
        case 'korean' :
        case 'korean_utf-8' :
            $_AC_CONF['iso_lang'] = 'ko';
            break;
        case 'norwegian' :
        case 'norwegian_utf-8' :
            $_AC_CONF['iso_lang'] = 'nb';
            break;
        case 'polish' :
        case 'polish_utf-8' :
            $_AC_CONF['iso_lang'] = 'pl';
            break;
        case 'portuguese_brazil' :
        case 'portuguese_brazil_utf-8' :
            $_AC_CONF['iso_lang'] = 'pt-br';
            break;
        case 'portuguese' :
        case 'portuguese_utf-8' :
            $_AC_CONF['iso_lang'] = 'pt';
            break;
        case 'romanian' :
        case 'romanian_utf-8' :
            $_AC_CONF['iso_lang'] = 'ro';
            break;
        case 'russian' :
        case 'russian_utf-8' :
            $_AC_CONF['iso_lang'] = 'ru';
            break;
        case 'slovak' :
        case 'slovak_utf-8' :
            $_AC_CONF['iso_lang'] = 'sk';
            break;
        case 'slovenian' :
        case 'slovenian_utf-8' :
            $_AC_CONF['iso_lang'] = 'sl';
            break;
        case 'spanish' :
        case 'spanish_utf-8' :
            $_AC_CONF['iso_lang'] = 'es';
            break;
        case 'swedish' :
        case 'swedish_utf-8' :
            $_AC_CONF['iso_lang'] = 'sv';
            break;
        case 'turkish' :
        case 'turkish_utf-8' :
            $_AC_CONF['iso_lang'] = 'tr';
            break;
        case 'ukrainian' :
        case 'ukrainian_utf-8' :
            $_AC_CONF['iso_lang'] = 'uk';
            break;
        default :
            $_AC_CONF['iso_lang'] = 'en';
            break;
    }

    if ( isset($LANG_DIRECTION) && $LANG_DIRECTION == 'rtl' ) {
        $_AC_CONF['isRTL'] = true;
    } else {
        $_AC_CONF['isRTL'] = false;
    }

    $agendaConfig = array(
        'allow_new'     => false,
        'allow_edit'    => false,
        'defaultview'   => $_AC_CONF['defaultview'],
        'autoheight'    => $_AC_CONF['autoheight'],
        'header_left'   => $_AC_CONF['header_left'],
        'header_center' => $_AC_CONF['header_center'],
        'header_right'  => $_AC_CONF['header_right'],
        'first_day'     => $_AC_CONF['first_day'],
        'locale'        => $_AC_CONF['iso_lang'],
        'isrtl'         => $_AC_CONF['isRTL'],
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