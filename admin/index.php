<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

// Only let admin users access this page
if (!SEC_hasRights('agenda.admin')) {
    COM_errorLog("Someone has tried to access the Agenda Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_TSTM01['access_denied']);
    $display .= $LANG_TSTM01['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

USES_lib_admin();

/*
 * Display admin list of all testimonials
*/
function listEvents()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC, $_IMAGE_TYPE;

    $retval = "";

    $header_arr = array(      # display 'text' and use table field 'field'
            array('text' => $LANG_AC['edit'],   'field' => 'parent_id', 'sort' => false, 'align' => 'center'),
            array('text' => $LANG_AC['title'], 'field' => 'title', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['owner'], 'field' => 'owner_id', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['start_date'], 'field' => 'start_date', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['end_date'], 'field' => 'end_date', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['allday'], 'field' => 'allday', 'sort' => true, 'align' => 'center'),
            array('text' => $LANG_AC['published'], 'field' => 'queued', 'sort' => true, 'align' => 'center'),
            array('text' => $LANG_AC['delete'], 'field' => 'id1', 'sort' => true, 'align' => 'center'),
    );
    $defsort_arr = array('field'     => 'start_date',
                         'direction' => 'DESC');
    $text_arr = array(
            'form_url'      => $_CONF['site_admin_url'] . '/plugins/agenda/index.php',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
            'no_data'       => $LANG_AC['no_events'],
    );

    $sql = "SELECT *,parent_id AS id1 "
            . "FROM {$_TABLES['ac_event']} ";

    $query_arr = array('table' => 'ac_event',
                        'sql' => $sql,
                        'query_fields' => array('parent_id','title'),
                        'default_filter' => " WHERE 1=1 ",
                        'group_by' => "");

    $filter = '';

    $actions = '<input name="delsel" type="image" src="'
            . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
            . '" style="vertical-align:bottom;" title="' . $LANG_AC['delete_checked']
            . '" onclick="return confirm(\'' . $LANG_AC['delete_confirm'] . '\');"'
            . ' value="x" '
            . '/>&nbsp;' . $LANG_AC['delete_checked'];

    $option_arr = array('chkselect' => true,
            'chkfield' => 'id1',
            'chkname' => 'parent_ids',
            'chkminimum' => 0,
            'chkall' => true,
            'chkactions' => $actions
    );

    $token = SEC_createToken();

    $formfields = '
        <input name="action" type="hidden" value="delete">
        <input type="hidden" name="' . CSRF_TOKEN . '" value="'. $token .'">
    ';

    $form_arr = array(
        'top' => $formfields
    );

    $retval .= ADMIN_list('taglist', 'AC_getListField', $header_arr,
    $text_arr, $query_arr, $defsort_arr, $filter, "", $option_arr, $form_arr);

    return $retval;
}

function AC_getListField($fieldname, $fieldvalue, $A, $icon_arr, $token = "")
{
    global $_CONF, $_USER, $_TABLES, $LANG_ADMIN, $LANG04, $LANG28, $_IMAGE_TYPE;

    $retval = '';

    switch ($fieldname) {
        case 'company' :
            $retval = $fieldvalue;
            break;

        case 'owner_id' :
            $retval = COM_getDisplayName($fieldvalue);
            break;

        case 'allday' :
            if ( $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-check"></i>';
            } else {
                $retval = '';
            }
            break;

        case 'parent_id' :
            $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?edit=x&id='.$A['parent_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'text_full' :
            $retval = '<a class="'.COM_getToolTipStyle().'" title="' . htmlspecialchars($A['text_full']).'"><i class="uk-icon uk-icon-info-circle"></i></a>';
            break;

        case 'queued' :
            if ( $fieldvalue != 0 ) {
                $retval = '<i class="uk-icon uk-icon-times uk-text-danger"></i>';
            } else {
                $retval = '<i class="uk-icon uk-icon-check-circle uk-text-success"></i>';
            }
            break;
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;
}

function agenda_edit_event( $event_id = 0 )
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC;

    $page = '';

    $form = new Agenda\eventForms(true);

    if ( $event_id != 0 ) {
        $parent_id = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.(int) $event_id);
        if ( $parent_id == null ) {
            return 'Invalid Event ID';
        }
    } else {
        return 'Invalid Event ID';
    }
    $page = $form->editEvent($parent_id,$event_id);

    return $page;

}



function agenda_admin_menu($action)
{
    global $_CONF, $_AC_CONF, $LANG_ADMIN,$LANG_AC;

    $retval = '';

    $menu_arr = array(
        array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?list=x','text' => $LANG_AC['event_list'],'active' => ($action == 'list' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?edit=x','text'=> ($action == 'edit_existing' ? $LANG_AC['edit'] : $LANG_AC['create']), 'active'=> ($action == 'edit' || $action == 'edit_existing' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?catlist=x','text' => $LANG_AC['category_list'],'active' => ($action == 'catlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'], 'text' => $LANG_ADMIN['admin_home'])
    );

    $retval = '<h2>'.$LANG_AC['plugin_name'].'</h2>';

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_AC['admin_help'],
        $_CONF['site_url'] . '/agenda/images/agenda.png'
    );

    return $retval;
}

$page = '';
$display = '';
$cmd ='list';

// actions:
// list, edit-event, edit-series, new-event, del-event, del-series,delsel_x,save-event,save-edit,save-edit-series,

// list
// edit - edit a single event
// edit-series


$expectedActions = array('list','edit','delete','save','delsel_x');
foreach ( $expectedActions AS $action ) {
    if ( isset($_POST[$action])) {
        $cmd = $action;
    } elseif ( isset($_GET[$action])) {
        $cmd = $action;
    }
}
if ( isset($_POST['cancel'])) {
    $src = COM_applyFilter($_POST['cancel']);
    if ( $src == 'mod' ) COM_refresh($_CONF['site_admin_url'].'/moderation.php');
    $cmd = 'list';
}

switch ( $cmd ) {
    case 'edit' :
        if (empty ($_GET['id'])) {
            $page = agenda_edit_event();
        } else {
            $page = agenda_edit_event((int) COM_applyFilter ($_GET['id']));
            $cmd = 'edit_existing';
        }
        break;
    case 'save' :
        if (SEC_checkToken()) {
            $page = saveEntry();
        } else {
            $page = listEntries();
        }
        break;

    case  'delsel_x':
        if (SEC_checkToken()) {
            delEntry();
        }
        $page = listEntries();
        break;

    case 'delete' :
        $page = 'Not implemented yet';
        break;

    case 'list' :
    default :
        $page = listEvents();
        break;
}

$display  = COM_siteHeader ('menu', $LANG_AC['admin']);
$display .= agenda_admin_menu($cmd);
$display .= $page;
$display .= COM_siteFooter (false);
echo $display;

?>