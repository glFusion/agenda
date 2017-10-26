<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* glFusion Plugin Interface
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

$ac_config = \config::get_instance();
$_AC_CONF = $ac_config->get_config('agenda');

if ( !isset($_AC_CONF['disable_submissions'] ) ) $_AC_CONF['disable_submissions'] = false;
if ( !isset($_AC_CONF['centerblock_where'] ) ) $_AC_CONF['centerblock_where'] = -1;

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/agenda/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once $langfile;
} else {
    include_once $_CONF['path'] . 'plugins/agenda/language/english_utf-8.php';
}

/*
* Include agenda config file
*/
require_once $_CONF['path'] . 'plugins/agenda/agenda.php';

if ( !isset($_AC_CONF['displayblocks'])) $_AC_CONF['displayblocks'] = 'none';

switch( $_AC_CONF['displayblocks'] ) {
    case 0 : // left only
    case 2 :
        $_AC_CONF['menu'] = 'menu';
        break;
    case 1 : // right only
    case 3 :
        $_AC_CONF['menu'] = 'none';
        break;
    default :
        $_AC_CONF['menu'] = 'menu';
        break;
}

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* Universal Plugin:  Uncomment and fill in variables;
*
* @return    array   Associative array of menu items to be added to menu
*
*/
function plugin_getmenuitems_agenda()
{
    global $_CONF, $_TABLES, $LANG_AC;

    $menulabel = $LANG_AC['plugin_name'];
    $menurul = $_CONF['site_url'] . "/agenda/index.php";

    $menuitems = array();
  	$menuitems[$menulabel] = $menurul;

    return $menuitems;

}

/**
* Check to see if this Plugin uses comments
*
* glFusion is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how glFusion
* does comments.
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_agenda()
{
    return false;
}

/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_agenda()
{
    global $_CONF, $LANG_AC;

    return array(
        $LANG_AC['plugin_name'],
        $_CONF['site_admin_url'] . '/plugins/agenda/index.php',
        $_CONF['site_url'] . '/agenda/images/agenda.png'
    );
}

/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify/Uncomment use it
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
function plugin_getadminoption_agenda()
{
    global $_CONF, $_TABLES, $LANG_AC;

    if (SEC_hasRights('agenda.admin')) {
        return array(
            $LANG_AC['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/agenda/index.php',
            0
        );
    }
}

/**
* Counts the items that are submitted
*
* @return   int     number of items in submission queue
*
*/
function plugin_submissioncount_agenda()
{
    global $_TABLES;

    $retval = 0;

    $retval = 0;

    return $retval;
}


/**
* returns list of moderation values
*
* The array returned contains (in order): the row 'id' label, main plugin
* table, moderation fields (comma seperated), and plugin submission table
*
* @return       array        Returns array of useful moderation values
*
*/
function plugin_moderationvalues_agenda()
{
    global $_TABLES;

    return array (
        'testid',
        $_TABLES['fc_event'],
        "id,queued",
        ''
    );
}

/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from mediagallery submission
* to mediagallery tables, within the function we handle all other approval
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationapprove_agenda($id)
{
    global $_CONF, $_TABLES, $LANG_AC;

    if ( (int) $id <= 0 ) return '';

    $sql = "UPDATE {$_TABLES['fc_event']} SET queued=0 WHERE testid=".(int) $id;
    DB_query($sql);

    PLG_itemSaved($id,'agenda');
    CACHE_remove_instance('menu');
    COM_setMsg($LANG_AC['submission_mod_approved'],'warning');

	$email = DB_getItem($_TABLES['fc_event'],'email','testid='.(int) $id);
	FC_notifyUser($email);

    return '';
}

/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from mediagallery submission
* table, within this function we handle all other deletion
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationdelete_agenda($id)
{
    global $_CONF, $_TABLES;

    if ( (int) $id <= 0 ) return '';

    $sql = "DELETE FROM {$_TABLES['fc_event']} WHERE testid=".(int) $id . " AND queued=1";
    DB_query($sql);
    PLG_itemDeleted($id, 'agenda');
    CACHE_remove_instance('menu');
    return;
}


/**
* show files for moderation on submissions page
*
* Uses the Plugin class to return data required by moderation.php to list
* plugin objects that need to be moderated.
*
* @param        string token The
* @return       Plugin       return HTML
*
*/
function plugin_itemlist_agenda($token)
{
    global $_CONF, $_TABLES, $_USER, $LANG_AC;
    global $LANG01, $LANG24, $LANG29, $LANG_ADMIN, $_IMAGE_TYPE;

    $retval = '';
    $key='media_id';

    if ( COM_isAnonUser() ) {
        $uid = 1;
    } else {
        $uid = $_USER['uid'];
    }

    $sql = "SELECT * FROM {$_TABLES['fc_event']} WHERE queued = 1";

    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    if ( $nrows == 0 ) return;

    $data_arr = array();
    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);
        $A['edit'] = 'edit';
        $A['_type_']  = 'agenda';
        $A['_key_']   = 'testid';      // name of key/id field
        $data_arr[$i] = $A;     // push row data into array
    }

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'testid', 'align' => 'center', 'width' => '25px'),
        array('text' => $LANG_AC['client'], 'field' => 'clientname'),
        array('text' => $LANG_AC['company'], 'field' => 'company'),
        array('text' => $LANG_AC['tstdate'], 'field' => 'tst_date', 'align' => 'center', 'width' => '15%'),
        array('text' => $LANG_AC['testimonial'],'field'=>'text_full','align' => 'center'),
        array('text' => $LANG29[1], 'field' => 'approve', 'align' => 'center', 'width' => '35px'),
        array('text' => $LANG_ADMIN['delete'], 'field' => 'delete', 'align' => 'center', 'width' => '35px')
    );

    $text_arr = array('has_menu'    => false,
                      'title'       => $LANG_AC['submissions'],
                      'help_url'    => '',
                      'no_data'     => $LANG29[39],
                      'form_url'    => "{$_CONF['site_admin_url']}/moderation.php"
    );

    $actions = '<input name="approve" type="image" src="'
        . $_CONF['layout_url'] . '/images/admin/accept.' . $_IMAGE_TYPE
        . '" style="vertical-align:bottom;" title="' . $LANG29[44]
        . '" onclick="return confirm(\'' . $LANG29[45] . '\');"'
        . '/>&nbsp;' . $LANG29[1];
    $actions .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    $actions .= '<input name="delbutton" type="image" src="'
        . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
        . '" style="vertical-align:text-bottom;" title="' . $LANG01[124]
        . '" onclick="return confirm(\'' . $LANG01[125] . '\');"'
        . '/>&nbsp;' . $LANG_ADMIN['delete'];

    $options = array('chkselect' => true,
                     'chkfield' => 'testid',
                     'chkname' => 'selitem',
                     'chkminimum' => 0,
                     'chkall' => true,
                     'chkactions' => $actions,
                     );

    $form_arr['bottom'] = '<input type="hidden" name="type" value="agenda"/>' . LB
            . '<input type="hidden" name="' . CSRF_TOKEN . '" value="' . $token . '"/>' . LB
            . '<input type="hidden" name="moderation" value="x"/>' . LB
            . '<input type="hidden" name="count" value="' . $nrows . '"/>';

    $retval .= ADMIN_simpleList('_fc_getListField', $header_arr,
                              $text_arr, $data_arr, $options, $form_arr, $token);
    return $retval;
}

function plugin_getheadercss_agenda() {
    global $_CONF;

    $styles = array();

return ( $styles );

    if ( @file_exists($_CONF['path_layout'].'plugins/agenda/style.css') ) {
        $styles[] = $_CONF['path_layout'].'plugins/agenda/style.css';
    } else if ( @file_exists($_CONF['path'] . 'plugins/agenda/custom/style.css') ) {
        $styles[] = $_CONF['path'] . 'plugins/agenda/css/custom/style.css';
    } else {
        $styles[] = $_CONF['path'] . 'plugins/agenda/style.css';
    }
    return($styles);
}


/**
*   Get the URL to the configuration help file.
*
*   @param  string  $option     Config option selected
*   @param  string  $doclang    Document language
*   @return string              URL to document
*/
function plugin_getconfigelementhelp_agenda($option, $doclang='english')
{
    global $_CONF, $_AC_CONF;

    $file = 'config.html';

    $doc_url = $_CONF['site_url'] . '/agenda/docs/'.$doclang.'/config.html';

     if (empty($doc_url))
        return false;

    if (!empty($option)) {
        $doc_url .= '#desc_' . $option;
    }

    return array($doc_url, 0);
}

function plugin_searchtypes_agenda()
{
    global $_CONF, $_AC_CONF, $_USER, $LANG_AC;

    $tmp['agenda'] = $LANG_AC['plugin_name'];

    return $tmp;
}


/**
* this searches for agenda matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
*
*/
function plugin_dopluginsearch_agenda($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_USER, $_AC_CONF, $LANG_AC, $_TABLES, $_CONF;

    $query = trim(DB_escapeString(htmlspecialchars($query)));

    $sql = " SELECT testid AS id, text_full AS description,text_full,UNIX_TIMESTAMP(tst_date) AS date, views AS hits, CONCAT(clientname,' ',company) AS title, CONCAT('/agenda/index.php?id=',testid) AS url ";
    $sql .= "FROM {$_TABLES['fc_event']} ";
    $sql .= "WHERE queued = 0 ";

    $search = new SearchCriteria('agenda', 'agenda');
    $columns = array('text_full');
    $sql .= $search->getDateRangeSQL('AND', '(UNIX_TIMESTAMP(tst_date))', $datestart, $dateend);
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/**
* Return information for a testimonial
*
* @param    string  $id         file ID or '*'
* @param    string  $what       comma-separated list of properties
* @param    int     $uid        user ID or 0 = current user
* @param    array   $options    (reserved for future extensions)
* @return   mixed               string or array of strings with the information
*
*/
function plugin_getiteminfo_agenda($id, $what, $uid = 0, $options = array())
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_AC;

    $properties = explode(',', $what);
    $fields = array();
    foreach ($properties as $p) {
        switch ($p) {
            case 'date' :
            case 'date-modified':
            case 'date-created' :
                $fields[] = 'UNIX_TIMESTAMP(tst_date) AS unixdate';
                break;
            case 'description':
                $fields[] = 'text_full';
                break;
            case 'excerpt':
                $fields[] = 'text_full';
                break;
            case 'searchidx' :
                $fields[] = 'text_full';
                $fields[] = "company";
                $fields[] = "clientname";
                break;
            case 'raw-description' :
                $fields[] = 'text_full';
                break;
            case 'id':
                $fields[] = 'testid';
                break;
            case 'title':
                $fields[] = 'clientname';
                $fields[] = 'company';
                break;
            case 'label':
            case 'url':
            case 'status' :
                $fields[] = 'testid';
                $fields[] = 'queued';
                break;
            case 'author' :
            case 'author_name' :
                $fields[] = 'clientname';
                break;
            case 'hits' :
                $fields[] = 'views';
                break;
            case 'perms' :

                break;
            default:
                break;
        }
    }

    $fields = array_unique($fields);

    if (count($fields) == 0) {
        $retval = array();

        return $retval;
    }

    if ($id == '*') {
        $where = '';
        $permOp = 'WHERE queued=0 ';
    } else {
        $where = " WHERE testid = '" . DB_escapeString($id) . "' ";
        $permOp = 'AND';
    }

    $sql = "SELECT " . implode(',', $fields)
            . " FROM {$_TABLES['fc_event']} "
            . " " . $where;
    if ($id != '*') {
        $sql .= ' LIMIT 1';
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $retval = array();
    for ($i = 0; $i < $numRows; $i++) {
        $A = DB_fetchArray($result);

        $props = array();
        foreach ($properties as $p) {
            switch ($p) {
                case 'date' :
                case 'date-created' :
                case 'date-modified':
                    $props[$p] = $A['unixdate'];
                    break;
                case 'description':
                    $props[$p] = nl2br($A['text_full']);
                    break;
                case 'excerpt':
                    $props[$p] = nl2br(FC_truncate($A['text_full'],300,'...'));
                    break;
                case 'searchidx' :
                    $props[$p] = $A['text_full'].' '.$A['company'].' '.$A['clientname'];
                    break;
                case 'raw-description':
                    $props[$p] = $A['text_full'];
                    break;
                case 'id':
                    $props['id'] = $A['testid'];
                    break;
                case 'title':
                    $props['title'] = $A['clientname'];
                    if ( $A['company'] != "" ) {
                        $props['title'] .= ' - ' . $A['company'];
                    }
                    break;
                case 'url':
                    if (empty($A['testid'])) {
                        $props['url'] = COM_buildURL($_CONF['site_url'].'/agenda/index.php?id=' . $id);
                    } else {
                        $props['url'] = COM_buildURL($_CONF['site_url'].'/agenda/index.php?id=' . $A['testid']);
                    }
                    break;
                case 'label':
                    $props['label'] = $LANG_AC['plugin_name'];
                    break;
                case 'status':
                    $props['status'] = $A['queued'];
                    break;
                case 'author' :
                    $props['author'] = $A['clientname'];
                    break;
                case 'author_name' :
                    $props['author_name'] = $A['clientname'];
                    break;
                case 'hits' :
                    $props['hits'] = $A['views'];
                    break;
                case 'perms' :
                    $p_anon = 0;
                    $p_member = 0;
                    $props['perms'] = array(
                        'owner_id' => 2,
                        'group_id' => 3,
                        'perm_owner' => 3,
                        'perm_group' => 2,
                        'perm_members' => 2,
                        'perm_anon' => 2,
                    );
                    break;
                default:
                    $props[$p] = '';
                    break;
            }
        }

        $mapped = array();

        foreach ($props as $key => $value) {
            if ($id == '*') {
                if ($value != '') {
                    $mapped[$key] = $value;
                }
            } else {
                $mapped[$key] = $value;
            }
        }

        if ($id == '*') {
            $retval[] = $mapped;
        } else {
            $retval = $mapped;
            break;
        }
    }

    if (($id != '*') && (count($retval) == 1)) {
        $tRet = array_values($retval);
        $retval = $tRet[0];
    }

    return $retval;
}

/**
* Called by the plugin Editor to run the SQL Update for a plugin update
*/
function plugin_upgrade_agenda() {
    global $_CONF, $_TABLES,$_AC_CONF;

    require_once $_CONF['path'] . 'plugins/agenda/upgrade.php';

    return agenda_upgrade();
}


/**
* Display agenda Center block
*
* @param   where   int      where the block will be displayed (0..2)
* @param   page    int      page number
* @param   topic   string   topic ID
* @return          string   HTML for the center blcok (can be empty)
*/
function plugin_centerblock_agenda ($where = 1, $page = 1, $topic = '')
{
    global $_CONF, $_USER, $_TABLES, $_AC_CONF, $LANG_AC;

    if ( $_AC_CONF['centerblock_where'] != $where && $where != CENTERBLOCK_FORCE) {
        return;
    }
    if ( $page > 1 OR !empty ($topic) ) return;

    $limit = 1;
    $template = 'centerblock.thtml';

    if ( isset($_AC_CONF['centerblock_rotate']) AND $_AC_CONF['centerblock_rotate'] == true ) {
        $limit = 20;
    }

    $retval = '';

    $sql = "SELECT testid,clientname,company,homepage,text_full,tst_date
					FROM {$_TABLES['fc_event']} WHERE queued=0
					ORDER BY RAND(NOW()) LIMIT " . $limit;

	$result = DB_query ($sql);
	$num = DB_numRows ($result);

	if ( $num === 0 ) return;

    $T = new Template ($_CONF['path'] . 'plugins/agenda/templates');
    $T->set_file ('page',$template);

	$T->set_block('page','agenda','tm');

	for ($i = 0; $i < $num; $i++) {
        $A = DB_fetchArray($result);

        $T->unset_var(array(
            'testimonial_text',
            'client_name',
            'submit_date',
            'testimonial_link',
            'lang_read_full',
            'truncated',
            'company_name',
            'company_website'
        ));

        $testimonialText = FC_truncate($A['text_full'],300,'');
        $remaining       = trim(utf8_substr($A['text_full'],utf8_strlen($testimonialText)));

        $T->set_var(array(
            'testimonial_text'  => nl2br(FC_truncate($A['text_full'],500,'...')),
            'client_name'       => $A['clientname'],
            'submit_date'       => $A['tst_date'],
            'testimonial_link'  => COM_buildURL($_CONF['site_url'].'/agenda/index.php?id='.$A['testid']),
            'lang_read_full'    => $LANG_AC['read_full_testimonial'],
            'lang_customers_saying' => $LANG_AC['customers_saying'],
        ));
        if ( $A['company'] != '' ) $T->set_var('company_name',$A['company']);
        if ( $A['homepage'] != '' ) $T->set_var('company_website',$A['homepage']);
        if ( utf8_strlen($remaining) > 0 ) $T->set_var('truncated',true);
        $T->parse('tm','agenda',true);
	}
    $T->parse('output', 'page');
    $retval .= $T->finish($T->get_var('output'));

    return $retval;
}


function _fc_getListField($fieldname, $fieldvalue, $A, $icon_arr, $token = "")
{
    global $_CONF, $LANG_AC, $LANG_ADMIN, $LANG29;

    $retval = '';

    switch ( $fieldname ) {
        case 'testid' :
            $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?edit=x&src=mod&testid='.$A['testid'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';

            break;
        case 'text_full' :
            $retval = '
            <a href="#tstid'.$A['testid'].'" rel="modal:open">'.$LANG_ADMIN['preview'].'</a>
            <div id="tstid'.$A['testid'].'" style="display:none;">
                <div class=" testimonial" style="padding-top:10px;">
                    <blockquote>
                    '.nl2br($A['text_full']).'
                    </blockquote>
                    <footer>
                    <cite>
                    '.$A['clientname'].'<br>'.$A['company'].'<br>'.$A['homepage'].'
                    </cite>
                    </footer>
                </div>
            </div>
            ';
            break;

        case 'approve':
            $retval = '';
            $attr['title'] = $LANG29[1];
            $attr['onclick'] = 'return confirm(\'' . $LANG29[48] . '\');';
            $retval .= COM_createLink($icon_arr['accept'],
                $_CONF['site_admin_url'] . '/moderation.php'
                . '?approve=x'
                . '&amp;type=' . $A['_type_']
                . '&amp;id=' . $A[0]
                . '&amp;' . CSRF_TOKEN . '=' . $token, $attr);
            break;

        case 'delete':
            $retval = '';
            $attr['title'] = $LANG_ADMIN['delete'];
            $attr['onclick'] = 'return confirm(\'' . $LANG29[49] . '\');';
            $retval .= COM_createLink($icon_arr['delete'],
                $_CONF['site_admin_url'] . '/moderation.php'
                . '?delete=x'
                . '&amp;type=' . $A['_type_']
                . '&amp;id=' . $A[0]
                . '&amp;' . CSRF_TOKEN . '=' . $token, $attr);
            break;

        default :
            $retval = $fieldvalue;
            break;
    }

    return $retval;
}

/**
*   Get the currently installed version.
*
*   @return string  version number
*/
function plugin_chkVersion_agenda()
{
    global $_AC_CONF;
    return $_AC_CONF['pi_version'];
}

/**
*   Determine if current user is a moderator
*
*   @return string  version number
*/
function plugin_ismoderator_agenda()
{
    global $_CONF, $_USER;

    if ( SEC_hasRights('agenda.admin') ) return true;
    return false;
}

/**
* Returns data for the random
*
* block for the agenda plugin
*
*/
function phpblock_agenda($attributes = array(), $rotate = 0)
{
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_AC;

    $retval = '';

    $limit = 1;

    if ( $rotate != 0 ) $limit = 20;

    $sql = "SELECT testid,clientname,company,homepage,text_full,tst_date
					FROM {$_TABLES['fc_event']} WHERE queued=0
					ORDER BY RAND(NOW()) LIMIT " . (int) $limit;

    $T = new Template ($_CONF['path'] . 'plugins/agenda/templates');
    $T->set_file ('form','random_block.thtml');

    $T->set_block('form','agenda','tm');

    $result = DB_query($sql);
    $numRows = DB_numRows($result);
    if ( $numRows == 0 ) return '';

    while ( $A = DB_fetchArray($result) ) {
        $T->unset_var(array(
            'testimonial_text',
            'client_name',
            'submit_date',
            'testimonial_link',
            'lang_read_full',
            'truncated',
            'company_name',
            'company_website'
        ));

        $testimonialText = FC_truncate($A['text_full'],300,'');
        $remaining       = trim(utf8_substr($A['text_full'],utf8_strlen($testimonialText)));

        $T->set_var(array(
            'testimonial_text'  => nl2br(FC_truncate($A['text_full'],300,'...')),
            'client_name'       => $A['clientname'],
            'submit_date'       => $A['tst_date'],
            'testimonial_link'  => COM_buildURL($_CONF['site_url'].'/agenda/index.php?id='.$A['testid']),
            'lang_read_full'    => $LANG_AC['read_full_testimonial'],
        ));
        if ( $A['company'] != '' ) $T->set_var('company_name',$A['company']);
        if ( $A['homepage'] != '' ) $T->set_var('company_website',$A['homepage']);
        if ( utf8_strlen($remaining) > 0 ) $T->set_var('truncated',true);

        $T->parse('tm','agenda',true);
	}

    $T->parse('output', 'form');
    $retval .= $T->finish($T->get_var('output'));


    return $retval;
}

function plugin_enablestatechange_agenda( $enable )
{
    global $_TABLES;

    if ( $enable == FALSE ) {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=0 WHERE name='block_agenda'",1);
    } else {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=1 WHERE name='block_agenda'",1);
    }

    return 1;

}

/*
	Truncated text to the nearest word based on a character count - substr()
	http://www.beliefmedia.com/php-truncate-functions
	preg-match()
	http://php.net/manual/en/function.preg-match.php
*/
function FC_truncate($string, $length, $trimmarker = '') {
    $strlen = strlen($string);
    /* mb_substr forces a break at $length if no word (space) boundary */
    $string = trim(utf8_substr($string, 0, $strlen));
    if ($strlen > $length) {
        preg_match('/^.{1,' . ($length - strlen($trimmarker)) . '}\b/su', $string, $match);
        $string = trim($match['0']) . $trimmarker;
    } else {
        $string = trim($string);
    }
    return $string;
}

function FC_notifyUser($email)
{
	global $_CONF, $_AC_CONF, $LANG_AC;

	if ( !COM_isEmail($email)) return;

	$mailbody  = sprintf($LANG_AC['submission_approved'],$_CONF['site_name']);
	$msgData['htmlmessage'] 	= $mailbody;
	$msgData['subject'] 		= $LANG_AC['submission_approved_subject'];
	$msgData['from']['email'] 	= $_CONF['noreply_mail'];
	$msgData['from']['name'] 	= $_CONF['site_name'];
	$msgData['to'] = array($email);
	COM_emailNotification( $msgData );

	return;
}

function agenda_slider()
{
	global $_CONF, $_AC_CONF, $_TABLES;

	$slideCounter = 0;

	$T = new Template ($_CONF['path'] . 'plugins/agenda/templates');

	$T->set_file (array (
		'page' => 'testimonial_slider.thtml',
	));

	$sql = "SELECT testid,clientname,company,text_full,homepage "
		   ."FROM {$_TABLES['fc_event']} WHERE queued=0 "
		   ."ORDER BY tst_date, testid DESC "
		   ."LIMIT 20";

	$result = DB_query ($sql);
	$num = DB_numRows ($result);

	if ( $num === 0 ) return;

	$T->set_block('page','agenda','tm');
	$T->set_block('page','dotnav','dn');

	for ($i = 0; $i < $num; $i++) {
		$A = DB_fetchArray ($result);

		$truncated = FC_truncate($A['text_full'], 700,'...');

		$T->set_var(array(
			'testid'            => $A['testid'],
			'client'            => $A['clientname'],
			'text_truncated'    => nl2br($truncated),
			'company_name'      => $A['company'],
		));
		if ( $A['homepage'] != '' ) {
			$T->set_var('company_url',$A['homepage']);
		} else {
			$T->unset_var('company_url');
		}
		$T->set_var('slidecounter',$slideCounter);
		$T->parse('tm','agenda',true);
		$T->parse('dn','dotnav',true);
		$slideCounter++;
	}

	$T->parse('output', 'page');
	$page = $T->finish($T->get_var('output'));

	return $page;
}

function agenda_validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>