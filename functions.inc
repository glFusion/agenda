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
*  Copyright (C) 2016-2022 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

define('AGENDA_SNAPSHOT','##builddatetime##');

$ac_config = \config::get_instance();
$_AC_CONF = $ac_config->get_config('agenda');

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/agenda/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once $langfile;
} else {
    include_once $_CONF['path'] . 'plugins/agenda/language/english_utf-8.php';
}
if (version_compare(GVERSION,'2.0.0','lt')) {
    if (isset($LANG_configSelect['agenda']) && !isset($LANG_configselects['agenda'])) {
        foreach ($LANG_configSelect['agenda'] AS $cfgItem) {
            $LANG_configselects['agenda'][] = array_flip($cfgItem);
        }
    }
}

/*
* Include agenda config file
*/
require_once $_CONF['path'] . 'plugins/agenda/agenda.php';

if ( !isset($_AC_CONF['displayblocks'])) $_AC_CONF['displayblocks'] = 'none';
if ( !isset($_AC_CONF['showupcomingevents']) ) $_AC_CONF['showupcomingevents'] = 1;
if ( !isset($_AC_CONF['upcomingeventsrange'] ) ) $_AC_CONF['upcomingeventsrange'] = 14;

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

// defaults for maintenance

if ( !isset($_AC_CONF['maintenance_check_freq']) ) {
    $_AC_CONF['maintenance_check_freq'] = 30;
}
$_AC_CONF['maintenance_check_frequency'] = 86400 * $_AC_CONF['maintenance_check_freq'];
if ( !isset($_AC_CONF['maintenance_max_age']) ) {
    $_AC_CONF['maintenance_max_age'] = 1;
}

define('AC_ERR_INVALID_DATE',1);
define('AC_ERR_DB_SAVE_PARENT',2);
define('AC_ERR_DB_SAVE_CHILD',3);
define('AC_ERR_NO_ACCESS',4);
define('AC_ERR_EMPTY_TITLE',5);
define('AC_ERR_SPAM',6);

require_once $_CONF['path'].'plugins/agenda/vendor/autoload.php';

spl_autoload_register('agenda_autoloader');

function agenda_autoloader($class) {
    global $_CONF;
    if (strpos($class, 'Agenda\\') === 0) {
        $class = str_replace('Agenda\\', '', $class);
        $class = ucfirst($class);
        $path = $_CONF['path'].'plugins/agenda/classes/' . $class . '.class.php';
        if (file_exists($path)) {
            include $path;
        }
    }
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
    global $_CONF, $_AC_CONF, $_TABLES, $LANG_AC;

    if ( COM_isAnonUser() && $_AC_CONF['allow_anonymous_view'] == false )  {
        if ( $_AC_CONF['security_exit'] == 0 ) {
            return array();
            exit;
        }
    }

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

    if (SEC_hasRights('agenda.admin')) {
        return array(
            $LANG_AC['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/agenda/index.php',
            $_CONF['site_url'] . '/agenda/images/agenda.png'
        );
    }
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

    $retval = DB_count($_TABLES['ac_event'],'queued',1);

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
        'parent_id',
        $_TABLES['ac_event'],
        "parent_id,queued",
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

    $event = new Agenda\eventHandler();
    $rc = $event->approveEvent($id);

    PLG_itemSaved($id,'agenda');
    CACHE_remove_instance('menu');
    COM_setMsg($LANG_AC['submission_mod_approved'],'warning');

// no email support at this time.
//	$email = DB_getItem($_TABLES['ac_event'],'email','parent_id='.(int) $id);
//	AC_notifyUser($email);

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

    $sql = "DELETE FROM {$_TABLES['ac_event']} WHERE parent_id=".(int) $id . " AND queued=1";
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

    $sql = "SELECT * FROM {$_TABLES['ac_event']} WHERE queued = 1";

    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    if ( $nrows == 0 ) return;

    $data_arr = array();
    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);
        $A['edit'] = 'edit';
        $A['_type_']  = 'agenda';
        $A['_key_']   = 'parent_id';      // name of key/id field
        $data_arr[$i] = $A;     // push row data into array
    }

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'parent_id', 'align' => 'center', 'width' => '25px'),
        array('text' => $LANG_AC['title'], 'field' => 'title','align' => 'left', ),
        array('text' => $LANG_AC['start_date'], 'field' => 'start_date','align' => 'center'),
        array('text' => $LANG_AC['repeats'], 'field' => 'repeats', 'align' => 'center'),
        array('text' => $LANG29[46], 'field' => 'owner_id', 'align' => 'left'),
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
                     'chkfield' => 'parent_id',
                     'chkname' => 'selitem',
                     'chkminimum' => 0,
                     'chkall' => true,
                     'chkactions' => $actions,
                     );

    $form_arr['bottom'] = '<input type="hidden" name="type" value="agenda"/>' . LB
            . '<input type="hidden" name="' . CSRF_TOKEN . '" value="' . $token . '"/>' . LB
            . '<input type="hidden" name="moderation" value="x"/>' . LB
            . '<input type="hidden" name="count" value="' . $nrows . '"/>';

    $retval .= ADMIN_simpleList('_ac_getListField', $header_arr,
                              $text_arr, $data_arr, $options, $form_arr, $token);
    return $retval;
}

function plugin_getheadercss_agenda() {
    global $_CONF;

    $styles = array();

    if ( @file_exists($_CONF['path_layout'].'plugins/agenda/style.css') ) {
        $styles[] = $_CONF['path_layout'].'plugins/agenda/style.css';
    } else if ( @file_exists($_CONF['path'] . 'plugins/agenda/custom/style.css') ) {
        $styles[] = $_CONF['path'] . 'plugins/agenda/css/custom/style.css';
    } else {
        $styles[] = $_CONF['path'] . 'plugins/agenda/css/style.css';
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

     if (empty($doc_url) || !file_exists($doc_url))
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
* Return information for an event
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
    global $_CONF, $_AC_CONF, $_USER, $_TABLES, $LANG_AC;

    if ($uid == 0) {
        $uid = $_USER['uid'];
    }

    $properties = explode(',', $what);
    $fields = array();
    foreach ($properties as $p) {
        switch ($p) {
            case 'date' :
            case 'date-modified':
            case 'date-created' :
                $fields[] = 'UNIX_TIMESTAMP(start_date) AS unixdate';
                break;
            case 'description':
                $fields[] = 'description';
                break;
            case 'excerpt':
                $fields[] = 'description';
                break;
            case 'searchidx' :
                $fields[] = 'description';
                $fields[] = "title";
                break;
            case 'raw-description' :
                $fields[] = 'description';
                break;
            case 'id':
                $fields[] = 'parent_id';
                break;
            case 'title':
                $fields[] = 'title';
                break;
            case 'label':
            case 'url':
            case 'status' :
                $fields[] = 'parent_id';
                $fields[] = 'queued';
                $fields[] = 'DATE_FORMAT(start_date,"%Y") as year';
                $fields[] = 'DATE_FORMAT(start_date,"%m") as month';
                $fields[] = 'DATE_FORMAT(start_date,"%d") as day';
                break;
            case 'author' :
            case 'author_name' :
                $fields[] = 'owner_id';
                break;
            case 'hits' :
//                $fields[] = 'views';
                break;
            case 'perms' :
                $fields[] = 'owner_id';
                $group_id = DB_getItem($_TABLES['groups'],'grp_id','grp_name="Agenda Admin"');
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
        $where = " WHERE parent_id = '" . DB_escapeString($id) . "' ";
        $permOp = 'AND';
    }

    $sql = "SELECT " . implode(',', $fields)
            . " FROM {$_TABLES['ac_event']} "
            . " " . $where;
    if ($id != '*') {
        $sql .= ' LIMIT 1';
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $retval = array();
    for ($i = 0; $i < $numRows; $i++) {
        $A = DB_fetchArray($result,false);

        $props = array();
        foreach ($properties as $p) {
            switch ($p) {
                case 'date' :
                case 'date-created' :
                case 'date-modified':
                    $props[$p] = $A['unixdate'];
                    break;
                case 'description':
                    $props[$p] = nl2br($A['description']);
                    break;
                case 'excerpt':
                    $props[$p] = nl2br(AC_truncate($A['description'],300,'...'));
                    break;
                case 'searchidx' :
                    $props[$p] = $A['title'].' '.$A['description'];
                    break;
                case 'raw-description':
                    $props[$p] = $A['description'];
                    break;
                case 'id':
                    $props['id'] = $A['parent_id'];
                    break;
                case 'title':
                    $props['title'] = $A['title'];
                    break;
                case 'url':
                    $view = 'agendaDay';
                    $year =
                    $props['url'] = COM_buildURL($_CONF['site_url'].'/agenda/index.php/agendaDay/' . sprintf("%02d/%02d/%02d",$A['year'],$A['month'],$A['day']));
                    break;
                case 'label':
                    $props['label'] = $LANG_AC['plugin_name'];
                    break;
                case 'status':
                    $props['status'] = $A['queued'] == 0 ? 1 : 0;
                    break;
                case 'author' :
                    $props['author'] = $A['owner_id'];
                    break;
                case 'author_name' :
                    $props['author_name'] = COM_getDisplayName($A['owner_id']);
                    break;
                case 'hits' :
 //                   $props['hits'] = 0;
                    break;
                case 'perms' :
                    $p_anon   = 2;
                    $p_member = 2;
                    if ( COM_isAnonUser($uid) && $_AC_CONF['allow_anonymous_view'] == false )  {
                        $p_anon = 0;
                    }
                    $props['perms'] = array(
                        'owner_id'      => $A['owner_id'],
                        'group_id'      => $group_id,
                        'perm_owner'    => 3,
                        'perm_group'    => 2,
                        'perm_members'  => 2,
                        'perm_anon'     => $p_anon,
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




function _ac_getListField($fieldname, $fieldvalue, $A, $icon_arr, $token = "")
{
    global $_CONF, $LANG_AC, $LANG_ADMIN, $LANG29;

    $retval = '';

    switch ( $fieldname ) {
        case 'parent_id' :
            $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?edit=x&src=mod&parent_id='.$A['parent_id'];
            $retval = '<a href="'.$url.'"><i class="uk-icon uk-icon-pencil"></i></a>';
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

        case 'repeats' :
            if ( $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-circle uk-text-success"></i>';
            } else {
                $retval = '';
            }
            break;

        case 'owner_id' :
            $retval = COM_getDisplayName($fieldvalue);
            break;

        case 'ip' :
            if ( $fieldvalue != NULL ) {
                $retval = inet_ntop($fieldvalue);
            } else {
                $retval = 'n/a';
            }
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



function plugin_enablestatechange_agenda( $enable )
{
    global $_TABLES;

    if ( $enable == FALSE ) {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=0 WHERE phpblockfn='phpblock_agenda'",1);
    } else {
        DB_query("UPDATE {$_TABLES['blocks']} SET is_enabled=1 WHERE phpblockfn='phpblock_agenda'",1);
    }

    return 1;
}

/*
* Do we support feeds?
*/
function plugin_getfeednames_agenda()
{
    global $LANG_AC;

    $feeds = array ();

    if (version_compare(GVERSION,'1.7.6','ge')) {
        $feeds[] = array ('id' => 'agenda', 'name' => $LANG_AC['plugin_name']);
    }

    return $feeds;

}

/**
* Get content for a feed that holds all events.
*
* @param    string   $feed     feed ID
* @param    string   $link     link to homepage
* @param    string   $update   list of story ids
* @return   array              content of the feed
*
*/
function plugin_getfeedcontent_agenda( $feed, &$link, &$update, $feedType, $feedVersion )
{
    global $_CONF, $_TABLES;

    if ($feedType != 'ICS') return;

    $sql = "SELECT * FROM {$_TABLES['ac_event']} "
          ."WHERE queued = 0 "
          ."ORDER BY start";
    $result = DB_query($sql);

    $content = array();
    $eids = array();
    $nrows = DB_numRows( $result );
    for($i = 1; $i <= $nrows; $i++) {
        $row = DB_fetchArray( $result );
        $eids[] = $row['parent_id'];

        $eventtitle = $row['title'];

        $eventtext = SYND_truncateSummary( $row['description'], MBYTE_strlen($row['description']));

        $dtLink = str_replace('-','/',$row['start_date']);
        $dtLink = explode('-',$row['start_date']);

        if (isset($dtLink[0]) && isset($dtLink[1]) && isset($dtLink[2])) {
            $link = $_CONF['site_url'].'/agenda/index.php?v=agendaDay&y='.$dtLink[0].'&m='.$dtLink[1].'&d='.$dtLink[2];
        } else {
            continue;
        }
        $eventlink = COM_buildURL($link);
        $extensionTags = array();

        $content[] = array( 'title'      => $eventtitle,
                            'summary'    => $eventtext,
                            'link'       => $eventlink,
                            'uid'        => $row['owner_id'],
                            'author'     => COM_getDisplayName( $row['owner_id'] ),
                            'date'       => date("Ymd\THi00",$row['start']),
                            'dtstart'    => date("Ymd\THi00",$row['start']),
                            'dtend'      => date("Ymd\THi00",$row['end']),
                            'location'   => $row['location'],
                            'format'     => 'plaintext',
                            'allday'     => $row['allday'],
                            'rrule'      => $row['rrule'],
                            'guid'       => $row['parent_id'],
                            'extensions' => $extensionTags
                          );
    }

    $link = $_CONF['site_url'] . '/agenda/index.php';
    $update = implode( ',', $eids );

    $update = trim($update);
    return $content;
}

/**
* Checking if calendar feeds are up to date
*
* @param    int     $feed           id of feed to be checked
* @param    string  $topic          topic (actually: category)
* @param    string  $update_data    data describing current feed contents
* @param    string  $limit          number of entries or number of hours
* @param    string  $updated_type   (optional) type of feed to be updated
* @param    string  $updated_topic  (optional) feed's "topic" to be updated
* @param    string  $updated_id     (optional) id of entry that has changed
*
*/
function plugin_feedupdatecheck_agenda ($feed, $topic, $update_data, $limit,
                    $updated_type = '', $updated_topic = '', $updated_id = '')
{
    global $_CONF, $_TABLES, $_SYND_DEBUG;

    $where = '';
    if ( !empty( $limit )) {
        if( substr( $limit, -1 ) == 'h' ) { // next xx hours
            $limitsql = '';
            $hours = substr( $limit, 0, -1 );
            $where = " AND (start <= DATE_ADD('".$_CONF['_now']->toMySQL(true)."', INTERVAL $hours HOUR))";
        } else {
            $limitsql = ' LIMIT ' . intval($limit);
        }
    } else {
        $limitsql = ' LIMIT 10';
    }

    $result = DB_query( "SELECT parent_id FROM {$_TABLES['ac_event']} WHERE queued = 0 AND end_date >= '".$_CONF['_now']->toMySQL(true)."'$where ORDER BY start $limitsql" );
    $nrows = DB_numRows( $result );

    $eids = array();
    for( $i = 0; $i < $nrows; $i++ ) {
        $A = DB_fetchArray( $result );

        if ( $A['parent_id'] == $updated_id ) {
            // no need to look any further - this feed has to be updated
            return false;
        }

        $eids[] = $A['parent_id'];
    }
    $current = trim(implode( ',', $eids ));

    if ($_SYND_DEBUG) {
        COM_errorLog ("Agenda: Update check for events: comparing new list ($current) with old list ($update_info)", 1);
    }

    $rc = ( $current != $update_data ) ? false : true;

    return $rc;
}

function plugin_centerblock_agenda( $where=1, $page=1, $topic ='' )
{
    global $_CONF, $_AC_CONF;


}

/**
* Returns the upcoming events block
*
* Returns the HTML for any upcoming events in the agenda
*
  Code adapted from the Calendar Plugin by Tony Bibbs - tony AT tonybibbs DOT com
*
* @param        string      $help       Help file for block
* @param        string      $title      Title to be used in block header
* @return       string  HTML formatted block containing events.
*/
function phpblock_agenda( $help='', $title='' )
{
    global $_CONF, $_SYSTEM, $_TABLES, $_USER, $_AC_CONF, $LANG_AC;

    $retval = '';

    if ( COM_isAnonUser() && $_AC_CONF['allow_anonymous_view'] == false )  {
        return $retval;
    }

    if ( $_AC_CONF['showupcomingevents'] ) {

        $filter = new sanitizer();

        $dt = new Date('now',$_USER['tzid']);
        $tzabbr = strtolower($dt->format('T',true));

        $cacheInstance = 'agenda__'.$tzabbr.'__'.$_USER['theme'];
        $retval = '';
        $retval = CACHE_check_instance($cacheInstance, 0);

        if ( $retval ) {
            // cache for up to 1 hour
            if ( CACHE_get_instance_update($cacheInstance, 0) > time() - 3600 ) {
                return $retval;
            } else {
                $retval = '';
            }
        }

        $range = $_AC_CONF['upcomingeventsrange'];
        if ( $range == 0 ) {
            $range = 14; // fallback: 14 days
        }

        $dateonly = $_CONF['dateonly'];
        if ( empty( $dateonly )) {
            $dateonly = 'd-M'; // fallback: day - abbrev. month name
        }

        if ( empty( $title )) {
            $title = DB_getItem( $_TABLES['blocks'], 'title',"name = 'block_agenda'" );
        }

        $filterStartDate = $range * 86400;
        $dtNow = new Date('now',$_USER['tzid']);
        $todayOnly = $dtNow->format('Y-m-d',true);
        $todayOnly = $todayOnly .'T' . '00:00:00';
        $unixNow = strtotime($todayOnly);
        $unixEnd = (int) $unixNow + (int) $filterStartDate;

        $sql  = "SELECT * FROM {$_TABLES['ac_events']} ";
        $sql .= " WHERE start >= ".$unixNow." AND start < ". $unixEnd;
        $sql .= " ORDER by start, parent_id DESC";

        $allEvents  = DB_query( $sql );
        $numRows    = DB_numRows( $allEvents );
        $totalrows  = $numRows;

        $numDays = 0;          // Without limits, I'll force them.
        $theRow = 1;           // Start with today!
        $oldDate1 = 'no_day';  // Invalid Date!
        $oldDate2 = 'last_d';  // Invalid Date!

        $eventsFound = 0;
        $skipFirstBreak = false;

        $classname = 'agenda-upcoming uk-text-truncate';

        $skipFirstBreak = true;

        while ( $theRow <= $numRows && $numDays < $range ) {
            // Retreive the next event, and format the start date.
            $theEvent = DB_fetchArray( $allEvents );

            $eventsFound++;

            if ( $theEvent['allday'] == 1 ) {
                $dtStart = new Date( $theEvent['start_date'].' 00:00:00',$_USER['tzid'] );
                $dtEnd   = new Date( $theEvent['end_date'].' 23:59:00',$_USER['tzid'] );
            } else {
                $dtStart = new Date($theEvent['start'],$_USER['tzid']);
                $dtEnd   = new Date($theEvent['end'],$_USER['tzid']);
            }
            // Start Date strings...
            $dayName1 = $dtStart->format('l',true);
            $abbrDate1 = $dtStart->format($dateonly,true);
            // End Date strings...
            $dayName2 = $dtEnd->format('l',true);
            $abbrDate2 = $dtEnd->format($dateonly,true);
            $todaysEvent = false;
            if ( $dtStart->format('Ymd',true) == $dtNow->format('Ymd',true) ) {
                $todaysEvent = true;
                $todaysClassName = 'site-event-today';
            }

            // If either of the dates [start/end] change, then display a new header.
            if ( $oldDate1 != $abbrDate1 || $oldDate2 != $abbrDate2 ) {

                if ( !empty( $newevents ) ) {
                    $retval .= COM_makeList( $newevents, $classname );
                    $newevents = array();
                }

                $oldDate1 = $abbrDate1;
                $oldDate2 = $abbrDate2;
                $numDays ++;

                if ( $numDays < $range ) {
                    if ( $skipFirstBreak ) {
                        $skipFirstBreak = false;
                    }
                    if ( $todaysEvent ) {
                        $retval .= '<b>'.$LANG_AC['today'].'</b>';
                    } else {
                        $retval .= '<b>' . $dayName1 . '</b>&nbsp;<small>'
                                . $abbrDate1 . '</small>';
                    }
                    // If different start and end dates, then display end date:
                    if ( $abbrDate1 != $abbrDate2 ) {
                        $retval .= ' - <b>' . $dayName2 . '</b>&nbsp;<small>' . $abbrDate2 . '</small>';
                    }
                }
                $newevents = array();
            }

            // Now display this event record.
            if ( $numDays < $range ) {
                $dt->setTimeStamp($theEvent['start']);
                $newevent_url = $_CONF['site_url'].'/agenda/index.php?v=agendaDay';
                $newevent_url .= '&y='.$dt->format('Y',true);
                $newevent_url .= '&m='.$dt->format('m',true);
                $newevent_url .= '&d='.$dt->format('d',true);
                $newevent_url = COM_buildURL( $newevent_url );

                $attr = array();
                if ( $todaysEvent ) {
                    $attr = array('class' => $todaysClassName, 'title' => $filter->censor(strip_tags(htmlspecialchars_decode($theEvent['title']))));
                }
                $newevent = COM_createLink(
                    $filter->censor(strip_tags(htmlspecialchars_decode($theEvent['title']))),
                    $newevent_url,
                    $attr
                );
                $newevents[] = $newevent;
            }
            $theRow++;
        } // end of while

        if ( !empty( $newevents )) {
            $retval .= COM_makeList( $newevents, $classname );
            $newevents = array();
        }

        if ( $eventsFound == 0 ) {
            // There aren't any upcoming events, show a nice message
            $retval .= $LANG_AC['no_upcoming'];
        }
        CACHE_create_instance($cacheInstance, $retval, 0);
    }
    return $retval;
}

function AC_getLocale()
{
    global $_CONF;

    $retval = 'en';

    // locale mapping $LANG_LOCALE
    // Set the ISO 2 digit code for language
    switch ($_CONF['language']) {
        case 'afrikaans' :
        case 'afrikaans_utf-8' :
            $retval = 'af';
            break;
// bosnian no support by FC
        case 'bosnian' :
        case 'bosnian_utf-8' :
            $retval = 'en';
            break;
        case 'bulgarian' :
        case 'bulgarian_utf-8' :
            $retval = 'bg';
            break;
        case 'catalan' :
        case 'catalan_utf-8' :
            $retval = 'ca';
            break;
        case 'chinese_traditional' :
        case 'chinese_traditional_utf-8' :
        case 'chinese_simplified' :
        case 'chinese_simplified_utf-8' :
            $retval = 'zh-cn';
            break;
        case 'croatian' :
        case 'croatian_utf-8' :
            $retval = 'hr';
            break;
        case 'czech' :
        case 'czech_utf-8' :
            $retval = 'cs';
            break;
        case 'danish' :
        case 'danish_utf-8' :
            $retval = 'da';
            break;
        case 'dutch' :
        case 'dutch_utf-8' :
            $retval = 'nl';
            break;
        case 'english' :
        case 'english_utf-8' :
            $retval = 'en';
            break;
        case 'estonian' :
        case 'estonian_utf-8' :
            $retval = 'et';
            break;
        case 'farsi' :
        case 'farsi_utf-8' :
            $retval = 'fa';
            break;
        case 'finnish' :
        case 'finnish_utf-8' :
            $retval = 'fi';
            break;
        case 'french_canada' :
        case 'french_canada_utf-8' :
            $retval = 'fr-ca';
            break;
        case 'french_france' :
        case 'french_france_utf-8' :
            $retval = 'fr';
            break;
        case 'german' :
        case 'german_utf-8' :
        case 'german_formal' :
        case 'german_formal_utf-8' :
            $retval = 'de';
            break;
        case 'hebrew' :
        case 'hebrew_utf-8' :
            $retval = 'he';
            break;
        case 'hellenic' :
        case 'hellenic_utf-8' :
            $retval = 'el';
            break;
        case 'indonesian' :
        case 'indonesian_utf-8' :
            $retval = 'id';
            break;
        case 'italian' :
        case 'italian_utf-8' :
            $retval = 'it';
            break;
        case 'japanese' :
        case 'japanese_utf-8' :
            $retval = 'ja';
            break;
        case 'korean' :
        case 'korean_utf-8' :
            $retval = 'ko';
            break;
        case 'norwegian' :
        case 'norwegian_utf-8' :
            $retval = 'nb';
            break;
        case 'polish' :
        case 'polish_utf-8' :
            $retval = 'pl';
            break;
        case 'portuguese_brazil' :
        case 'portuguese_brazil_utf-8' :
            $retval = 'pt-br';
            break;
        case 'portuguese' :
        case 'portuguese_utf-8' :
            $retval = 'pt';
            break;
        case 'romanian' :
        case 'romanian_utf-8' :
            $retval = 'ro';
            break;
        case 'russian' :
        case 'russian_utf-8' :
            $retval = 'ru';
            break;
        case 'slovak' :
        case 'slovak_utf-8' :
            $retval = 'sk';
            break;
        case 'slovenian' :
        case 'slovenian_utf-8' :
            $retval = 'sl';
            break;
        case 'spanish' :
        case 'spanish_utf-8' :
            $retval = 'es';
            break;
        case 'swedish' :
        case 'swedish_utf-8' :
            $retval = 'sv';
            break;
        case 'turkish' :
        case 'turkish_utf-8' :
            $retval = 'tr';
            break;
        case 'ukrainian' :
        case 'ukrainian_utf-8' :
            $retval = 'uk';
            break;
        default :
            $retval = 'en';
            break;
    }
    return $retval;
}

/*
	Truncated text to the nearest word based on a character count - substr()
	http://www.beliefmedia.com/php-truncate-functions
	preg-match()
	http://php.net/manual/en/function.preg-match.php
*/
function AC_truncate($string, $length, $trimmarker = '') {
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

function AC_notifyUser($email)
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

function AC_eventMaintenance()
{
    global $_CONF, $_AC_CONF, $_TABLES, $_VARS;

    if ( !isset($_VARS['agenda_maintenance']) || $_VARS['agenda_maintenance'] < (time() - $_AC_CONF['maintenance_check_frequency']) ) {
        DB_query("DELETE FROM {$_TABLES['ac_events']} where end_date < NOW() - INTERVAL ".$_AC_CONF['maintenance_max_age']." YEAR",1);
        DB_query("DELETE `f` FROM {$_TABLES['ac_event']} b LEFT JOIN {$_TABLES['ac_events']} f ON f.parent_id = b.parent_id WHERE b.queued = 0 AND f.parent_id IS NULL",1);
        DB_query("REPLACE INTO {$_TABLES['vars']} (name,value) VALUES ('agenda_maintenance',UNIX_TIMESTAMP())");
    }
}

function agenda_validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>
