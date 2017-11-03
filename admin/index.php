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
    $display .= COM_startBlock($LANG_AC['access_denied']);
    $display .= $LANG_AC['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

USES_lib_admin();

/*
 * Display admin list of categories
*/
function listCategory()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC, $_IMAGE_TYPE;

    $retval = "";

    $header_arr = array(      # display 'text' and use table field 'field'
            array('text' => $LANG_AC['edit'],   'field' => 'category_id', 'sort' => false, 'align' => 'center'),
            array('text' => $LANG_AC['category_name'], 'field' => 'cat_name', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['category_desc'], 'field' => 'cat_desc', 'sort' => true, 'align' => 'left'),
            array('text' => $LANG_AC['bgcolor'], 'field' => 'bgcolor', 'sort' => true, 'align' => 'center'),
            array('text' => $LANG_AC['fgcolor'], 'field' => 'fgcolor', 'sort' => true, 'align' => 'center'),
            array('text' => $LANG_AC['delete'], 'field' => 'catdelid', 'sort' => true, 'align' => 'center'),
    );
    $defsort_arr = array('field'     => 'cat_name',
                         'direction' => 'ASC');
    $text_arr = array(
            'form_url'      => $_CONF['site_admin_url'] . '/plugins/agenda/index.php?catlist=x',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
            'no_data'       => $LANG_AC['no_categories'],
    );

    $sql = "SELECT *,category_id AS catdelid "
            . "FROM {$_TABLES['ac_category']} ";

    $query_arr = array('table' => 'ac_category',
                        'sql' => $sql,
                        'query_fields' => array('category_id','cat_name'),
                        'default_filter' => " WHERE 1=1 ",
                        'group_by' => "");

    $filter = '';

    $actions = '<input name="delsel" type="image" src="'
            . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
            . '" style="vertical-align:bottom;" title="' . $LANG_AC['delete_checked']
            . '" onclick="return confirm(\'' . $LANG_AC['delete_confirm'] . '\');"'
            . ' value="x" '
            . '/>&nbsp;' . $LANG_AC['delete_checked'];

    $option_arr = array('chkselect' => false,
            'chkfield'      => 'catdelid',
            'chkname'       => 'category_ids',
            'chkminimum'    => 0,
            'chkall'        => true,
            'chkactions'    => $actions
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
    global $_CONF, $_USER, $_TABLES, $LANG_AC, $LANG_ADMIN, $LANG04, $LANG28, $_IMAGE_TYPE;

    $retval = '';

    switch ($fieldname) {

// category list items

        case 'category_id' :
            $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?editcat=x&id='.$fieldvalue;
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'bgcolor' :
            $retval = '<div style="margin:0 auto; text-align:center;border:1px solid black;height:30px;width:30px;background-color:'.$fieldvalue.';">&nbsp;</div>';
            break;

        case 'fgcolor' :
            $retval = '<div style="margin:0 auto;text-align:center;border:1px solid black;height:30px;width:30px;background-color:'.$fieldvalue.';">&nbsp;</div>';
            break;

        case 'catdelid' :
            if ( $fieldvalue != 1 ) {
                $attr['title'] = $LANG_ADMIN['delete'];
                $attr['onclick'] = "return confirm('" . $LANG_AC['delete_confirm'] . "');";
                $retval .= COM_createLink($icon_arr['delete'],
                    $_CONF['site_admin_url'] . '/plugins/agenda/index.php'
                    . '?delcat=x&amp;cid=' . $fieldvalue . '&amp;' . CSRF_TOKEN . '=' . $token, $attr);
            }
            break;
        case 'cat_name' :
            if ( $A['category_id'] == 1 ) {
                $retval = $LANG_AC['no_category'];
            } else {
                $retval = $fieldvalue;
            }
            break;
        case 'cat_desc' :
            if ( $A['category_id'] == 1 ) {
                $retval = $LANG_AC['no_category_desc'];
            } else {
                $retval = $fieldvalue;
            }
            break;


        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;
}

function agenda_new_category()
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC;

    $page = '';

    $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
    $T->set_file ('page','category-form.thtml');

    $T->set_var('form_action',$_CONF['site_admin_url'].'/plugins/agenda/index.php');

    $T->set_var(array(
        'category_id'   => 0,
        'cat_name'      => '',
        'cat_desc'      => '',
        'fgcolor'       => '#ffffff',
        'bgcolor'       => '#3a87ad',
        'action'        => 'save-new-category',
        'lang_save'     => $LANG_AC['save'],
        'lang_cancel'   => $LANG_AC['cancel'],
        'lang_category_name' => $LANG_AC['category_name'],
        'lang_category_desc' => $LANG_AC['category_desc'],
        'lang_color_preview' => $LANG_AC['color_preview'],
        'lang_bg_color'      => $LANG_AC['bgcolor'],
        'lang_text_color'    => $LANG_AC['fgcolor'],
        'lang_delete_confirm'=> $LANG_AC['delete_confirm'],
        'lang_more'          => $LANG_AC['more'],
        'lang_choose'        => $LANG_AC['choose'],
        'lang_less'          => $LANG_AC['less'],
        'lang_sample_category' => $LANG_AC['sample_category'],
    ));

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    return $page;
}

function saveNewCategory()
{
    global $_CONF, $_AC_CONF, $_TABLES;

    // check permissions

    if ( !SEC_hasRights('agenda.admin') ) {
        return 'invalid permissions';
    }

    $cat_name    = $_POST['cat_name'];
    $cat_desc    = $_POST['cat_desc'];
    $fgcolor     = $_POST['fgcolor'];
    $bgcolor     = $_POST['bgcolor'];

    $filter = new \sanitizer();
    $filter->setPostmode('text');

    $cat_name    = $filter->filterText($cat_name);
    $cat_desc    = $filter->filterText($cat_desc);

    // escape
    $db_cat_name = DB_escapeString($cat_name);
    $db_cat_desc = DB_escapeString($cat_desc);
    $db_fgcolor  = DB_escapeString($fgcolor);
    $db_bgcolor  = DB_escapeString($bgcolor);

    $sql  = "INSERT INTO {$_TABLES['ac_category']} (cat_name,cat_desc,fgcolor,bgcolor) ";
    $sql .= "VALUES ('{$db_cat_name}','{$db_cat_desc}','{$db_fgcolor}','{$db_bgcolor}')";

    $result = DB_query($sql);

    return listCategory();
}

function agenda_edit_category( $category_id = 0 )
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC;

    $page = '';

    if ( $category_id == 0 ) {
        return 'Invalid Category';
    }
    $result = DB_query("SELECT * FROM {$_TABLES['ac_category']} WHERE category_id=".(int) $category_id);
    if ( DB_numRows($result) == 0 ) {
        return 'Invalid Category';
    }
    $row = DB_fetchArray($result);

    $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
    $T->set_file ('page','category-form.thtml');

    $T->set_var('form_action',$_CONF['site_admin_url'].'/plugins/agenda/index.php');

    $T->set_var(array(
        'category_id'   => $row['category_id'],
        'cat_name'      => $row['cat_name'],
        'cat_desc'      => $row['cat_desc'],
        'fgcolor'       => $row['fgcolor'],
        'bgcolor'       => $row['bgcolor'],
        'action'        => 'edit-category',
        'lang_save'     => $LANG_AC['save'],
        'lang_cancel'   => $LANG_AC['cancel'],
        'lang_save'     => $LANG_AC['save'],
        'lang_cancel'   => $LANG_AC['cancel'],
        'lang_category_name' => $LANG_AC['category_name'],
        'lang_category_desc' => $LANG_AC['category_desc'],
        'lang_color_preview' => $LANG_AC['color_preview'],
        'lang_bg_color'      => $LANG_AC['bgcolor'],
        'lang_text_color'    => $LANG_AC['fgcolor'],
        'lang_delete_confirm'=> $LANG_AC['delete_confirm'],
        'lang_more'          => $LANG_AC['more'],
        'lang_choose'        => $LANG_AC['choose'],
        'lang_less'          => $LANG_AC['less'],
        'lang_sample_category' => $LANG_AC['sample_category'],
    ));
    if ( $category_id != 1 ) {
        $T->set_var('lang_delete',$LANG_AC['delete']);
    } else {
        $T->unset_var('lang_delete');
        $T->set_var('name_disabled',' disabled="disabled" ');
        $T->set_var('desc_disabled',' disabled="disabled" ');
        $T->set_var('cat_name', $LANG_AC['no_category']);
        $T->set_var('cat_desc', $LANG_AC['no_category_desc']);
    }

    $T->parse('output', 'page');
    $page = $T->finish($T->get_var('output'));

    return $page;
}

function saveEditCategory()
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_AC;

    // check permissions

    if ( !SEC_hasRights('agenda.admin') ) {
        return 'invalid permissions';
    }

    $category_id = COM_applyFilter($_POST['category_id'],true);

    $cat_name    = isset($_POST['cat_name']) ? $_POST['cat_name'] : $LANG_AC['no_category'];
    $cat_desc    = isset($_POST['cat_desc']) ? $_POST['cat_desc'] : $LANG_AC['no_category_desc'];

    $fgcolor     = $_POST['fgcolor'];
    $bgcolor     = $_POST['bgcolor'];

    $filter = new \sanitizer();
    $filter->setPostmode('text');

    $cat_name    = $filter->filterText($cat_name);
    $cat_desc    = $filter->filterText($cat_desc);

    // escape
    $db_cat_name = DB_escapeString($cat_name);
    $db_cat_desc = DB_escapeString($cat_desc);
    $db_fgcolor  = DB_escapeString($fgcolor);
    $db_bgcolor  = DB_escapeString($bgcolor);

    $sql  = "UPDATE {$_TABLES['ac_category']} SET ";
    $sql .= "cat_name = '{$db_cat_name}',cat_desc='{$db_cat_desc}',fgcolor='{$db_fgcolor}',bgcolor='{$db_bgcolor}' ";
    $sql .= " WHERE category_id=".(int) $category_id;

    $result = DB_query($sql);

    return listCategory();
}

function deleteCategory()
{
    global $_CONF, $_AC_CONF, $_TABLES;

    if ( !SEC_hasRights('agenda.admin') ) {
        return 'invalid permissions';
    }

    if ( isset($_POST['category_id'] ) ) {
        $category_id = COM_applyFilter($_POST['category_id'],true);
    } elseif ( isset($_GET['cid'] ) ) {
        $category_id = COM_applyFilter($_GET['cid'],true);
    } else {
        return "Invalid input";
    }

    if ( $category_id < 2 ) {
        return "invalid input";
    }
    // remove from the category table
    DB_query("DELETE FROM {$_TABLES['ac_category']} WHERE category_id=" . (int) $category_id);
    // remove from event table
    DB_query("UPDATE {$_TABLES['ac_event']} SET category=1 WHERE category=".(int)$category_id);
    DB_query("UPDATE {$_TABLES['ac_events']} SET category=1 WHERE category=".(int)$category_id);

    return listCategory();
}

function agenda_admin_menu($action)
{
    global $_CONF, $_AC_CONF, $LANG_ADMIN,$LANG_AC;

    $retval = '';

    $menu_arr = array(
        array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?catlist=x','text' => $LANG_AC['category_list'],'active' => ($action == 'catlist' ? true : false)),
        array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?newcat=x','text' => $LANG_AC['category_new'],'active' => ($action == 'newcat' ? true : false)),
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
$cmd ='catlist';

$expectedActions = array('catlist','editcat','edit-category','newcat','save-new-category','delcat');
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
    $cmd = 'catlist';
}

switch ( $cmd ) {
    case 'catlist' :
        $page = listCategory();
        break;

    case 'editcat' :
        $category_id = isset($_GET['id']) ? (int) COM_applyFilter($_GET['id'],true) : 0;
        $page = agenda_edit_category($category_id);
        break;

    case 'edit-category' :
        // save an edited category
        $page = saveEditCategory();
        break;

    case 'newcat' :
        $page = agenda_new_category();
        break;

    case 'save-new-category' :
        $page = saveNewCategory();
        break;

    case 'delcat' :
        $page =  deleteCategory();
        break;

    case 'list' :
    default :
        $page = listCategory();
        break;
}

$display  = COM_siteHeader ('menu', $LANG_AC['admin']);
$display .= agenda_admin_menu($cmd);
$display .= $page;
$display .= COM_siteFooter (false);
echo $display;

?>