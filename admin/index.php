<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2021 by the following authors:
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
 * Edits an event from the submission queue
 */
function editEvent( $parent_id )
{
    global $_CONF, $_AC_CONF, $_TABLES, $_USER, $LANG_ADMIN, $LANG_AC, $LANG_AC_JS, $LANG_WEEK, $LANG_MONTH, $LANG29;

    $page = '';

    $event = new Agenda\eventForms();

    $result = DB_query("SELECT * FROM {$_TABLES['ac_event']} AS event  WHERE parent_id=" . (int) $parent_id);

    if ( DB_numRows($result) > 0 ) {
        $row = DB_fetchArray($result);

        $T = new \Template ($_CONF['path'] . 'plugins/agenda/templates');
        $T->set_file ('page','edit-event-form.thtml');

        if ( $row['allday'] == 0 ) {
            $dt = new \Date($row['start'],$_USER['tzid']);
            $row['start_date'] = $dt->format('Y-m-d',true);
            $row['start_time'] = $dt->format('H:i',true);

            $dt = new \Date($row['end'],$_USER['tzid']);
            $row['end_date'] = $dt->format('Y-m-d',true);
            $row['end_time'] = $dt->format('H:i',true);
        } else {
            $row['start_date'] = $row['start_date'];
            $row['end_date'] = $row['end_date'];
            $row['start_time'] = '00:00';
            $row['end_time'] = '23:59';
        }

        $catSelList = '';
        $catList = $event->getCategories();
        foreach ( $catList AS $id => $name ) {
            if ( $id == $row['category'] ) {
                $selected = ' selected="selected" ';
            } else {
                $selected = '';
            }
            $catSelList .= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
        }

        $T->set_var(array(
            'form_action'       => $_CONF['site_admin_url'].'/plugins/agenda/index.php',
            'cancel_value'      => 'mod',
            'src'               => 'mod',
            'title'             => $row['title'],
            'start-date'        => $row['start_date'],
            'end-date'          => $row['end_date'],
            'start-time'        => date('h:i A',strtotime($row['start_time'])),
            'end-time'          => date('h:i A',strtotime($row['end_time'])),
            'location'          => $row['location'],
            'description'       => $row['description'],
            'parent_id'         => $row['parent_id'],
            'owner_id'          => $row['owner_id'],
            'owner_name'        => COM_getDisplayName($row['owner_id']),
            'ip_address'        => $row['ip'] == NULL ? '' : inet_ntop($row['ip']),
            'category_select'   => $catSelList,
            'lang_submitted_by' => $LANG29[46],
            'lang_ip_address'   => $LANG_AC['ip_address'],
            'lang_category'     => $LANG_AC['category'],
            'lang_event_title'  => $LANG_AC['event_title'],
            'lang_location'     => $LANG_AC['location'],
            'lang_event_start'  => $LANG_AC['event_start'],
            'lang_start_date'   => $LANG_AC['start_date'],
            'lang_all_day_event'=> $LANG_AC['all_day_event'],
            'lang_event_end'    => $LANG_AC['event_end'],
            'lang_end_date'     => $LANG_AC['end_date'],
            'lang_end_time'     => $LANG_AC['end_time'],
            'lang_repeats'      => $LANG_AC['repeats'],
            'lang_repeat_options'=> $LANG_AC['repeat_options'],
            'lang_daily'        => $LANG_AC['daily'],
            'lang_weekly'       => $LANG_AC['weekly'],
            'lang_biweekly'     => $LANG_AC['biweekly'],
            'lang_monthly'      => $LANG_AC['monthly'],
            'lang_yearly'       => $LANG_AC['yearly'],
            'lang_description'  => $LANG_AC['description'],
            'lang_save'         => $LANG_AC['save'],
            'lang_delete'       => $LANG_AC['delete'],
            'lang_cancel'       => $LANG_AC['cancel'],
            'lang_err_no_title' => $LANG_AC_JS['err_enter_title'],
            'lang_err_datetime' => $LANG_AC_JS['err_end_before_start'],
            'lang_repeat'       => $LANG_AC['repeat'],
            'lang_none'         => $LANG_AC['none'],
            'lang_hourly'       => $LANG_AC['hourly'],
            'lang_daily'        => $LANG_AC['daily'],
            'lang_weekly'       => $LANG_AC['weekly'],
            'lang_monthly'      => $LANG_AC['monthly'],
            'lang_yearly'       => $LANG_AC['yearly'],
            'lang_every'        => $LANG_AC['every'],
            'lang_hours'        => $LANG_AC['hours'],
            'lang_days'         => $LANG_AC['days'],
            'lang_weeks'        => $LANG_AC['weeks'],
            'lang_months'       => $LANG_AC['months'],
            'lang_weekly_help'  => $LANG_AC['weekly_help'],
            'lang_on_day'       => $LANG_AC['on_day'],
            'lang_on_the'       => $LANG_AC['on_the'],
            'lang_first'        => $LANG_AC['first'],
            'lang_second'       => $LANG_AC['second'],
            'lang_third'        => $LANG_AC['third'],
            'lang_forth'        => $LANG_AC['forth'],
            'lang_last'         => $LANG_AC['last'],
            'lang_day'          => $LANG_AC['day'],
            'lang_weekday'      => $LANG_AC['weekday'],
            'lang_weekend'      => $LANG_AC['weekend'],
            'lang_after'        => $LANG_AC['after'],
            'lang_on_date'      => $LANG_AC['on_date'],
            'lang_occurrences'  => $LANG_AC['occurrences'],
            'lang_end_after_date' => $LANG_AC['end_after_date'],
            'lang_hours'        => $LANG_AC['hours'],
            'lang_days'         => $LANG_AC['days'],
            'lang_weeks'        => $LANG_AC['weeks'],
            'lang_months'       => $LANG_AC['months'],
            'lang_january'      => $LANG_MONTH[1],
            'lang_february'     => $LANG_MONTH[2],
            'lang_march'        => $LANG_MONTH[3],
            'lang_april'        => $LANG_MONTH[4],
            'lang_may'          => $LANG_MONTH[5],
            'lang_june'         => $LANG_MONTH[6],
            'lang_july'         => $LANG_MONTH[7],
            'lang_august'       => $LANG_MONTH[8],
            'lang_september'    => $LANG_MONTH[9],
            'lang_october'      => $LANG_MONTH[10],
            'lang_november'     => $LANG_MONTH[11],
            'lang_december'     => $LANG_MONTH[12],
            'lang_jan'          => $LANG_MONTH[13],
            'lang_feb'          => $LANG_MONTH[14],
            'lang_mar'          => $LANG_MONTH[15],
            'lang_apr'          => $LANG_MONTH[16],
            'lang_may'          => $LANG_MONTH[17],
            'lang_jun'          => $LANG_MONTH[18],
            'lang_jul'          => $LANG_MONTH[19],
            'lang_aug'          => $LANG_MONTH[20],
            'lang_sep'          => $LANG_MONTH[21],
            'lang_oct'          => $LANG_MONTH[22],
            'lang_nov'          => $LANG_MONTH[23],
            'lang_dec'          => $LANG_MONTH[24],
            'lang_sun'          => $LANG_WEEK[8],
            'lang_mon'          => $LANG_WEEK[9],
            'lang_tue'          => $LANG_WEEK[10],
            'lang_wed'          => $LANG_WEEK[11],
            'lang_thu'          => $LANG_WEEK[12],
            'lang_fri'          => $LANG_WEEK[13],
            'lang_sat'          => $LANG_WEEK[14],
            'lang_sunday'       => $LANG_WEEK[1],
            'lang_monday'       => $LANG_WEEK[2],
            'lang_tuesday'      => $LANG_WEEK[3],
            'lang_wednesday'    => $LANG_WEEK[4],
            'lang_thursday'     => $LANG_WEEK[5],
            'lang_friday'       => $LANG_WEEK[6],
            'lang_saturday'     => $LANG_WEEK[7],
            'lang_of'           => $LANG_AC['of'],
            'lang_end'          => $LANG_AC['end'],
            'locale'            => AC_getLocale(),
         ));
         $T->set_var('repeats',1);

        // parse rrule

        if ( $row['repeats'] == 1 ) {
            $ruleArray = explode(';',$row['rrule']);
            $rules = array();
            foreach ( $ruleArray AS $element ) {
                $rule = explode('=',$element);
                if ( $rule[0] != '' ) {
                    $rules[$rule[0]] = $rule[1];
                }
            }

            $T->set_var('freq_selected_'.$rules['FREQ'],' selected="selected" ');
            switch ( $rules['FREQ'] ) {
                case 'DAILY' :
                    $T->set_var('interval_value',$rules['INTERVAL']);
                    break;
                case 'WEEKLY' :
                    $T->set_var('interval_value',$rules['INTERVAL']);
                    $T->set_var('byday_value',$rules['BYDAY']);
                    $bydayArray = explode(',',$rules['BYDAY']);
                    foreach ($bydayArray AS $day) {
                        $T->set_var($day.'_selected','uk-button-success');
                    }
                    break;
                case 'MONTHLY' :
                    $T->set_var('interval_value',$rules['INTERVAL']);
                    if ( isset($rules['BYMONTHDAY'])) { // by month day - mtype = 0
                        $T->set_var('mtype_0_checked',' checked="checked" ');
                        $T->set_var('day_'.$rules['BYMONTHDAY'],' selected="selected" ');
                    } else {
                        $T->set_var('mtype_1_checked',' checked="checked" ');
                        $T->set_var('setpos_'.$rules['BYSETPOS'].'_selected',' selected="selected" ');
                        switch ($rules['BYDAY']) {
                            case 'SU':
                            case 'MO':
                            case 'TU':
                            case 'WE':
                            case 'TH':
                            case 'FR':
                            case 'SA':
                            case 'SU':
                                $T->set_var('moday_'.$rules['BYDAY'].'_selected',' selected="selected" ');
                                break;
                            case 'SU,MO,TU,WE,TH,FR,SA' :
                                $T->set_var('moday_day_selected',' selected="selected" ');
                                break;
                            case 'MO,TU,WE,TH,FR' :
                                $T->set_var('moday_weekday_selected',' selected="selected" ');
                                break;
                            case 'SA,SU' :
                                $T->set_var('moday_weekend_selected',' selected="selected" ');
                                break;
                        }
                    }
                    break;
                case 'YEARLY' :
                    if ( isset($rules['BYMONTHDAY'])) {  // yr-type = 0
                        $T->set_var('yrtype_0_checked',' checked="checked" ');
                        $T->set_var('mo_'.$rules['BYMONTH'],' selected="selected" ');
                        $T->set_var('day_'.$rules['BYMONTHDAY'],' selected="selected" ');
                    } else {
                        $T->set_var('yrtype_1_checked',' checked="checked" ');
                        $T->set_var('setpos_'.$rules['BYSETPOS'].'_selected',' selected="selected" ');
                        switch ($rules['BYDAY']) {
                            case 'SU':
                            case 'MO':
                            case 'TU':
                            case 'WE':
                            case 'TH':
                            case 'FR':
                            case 'SA':
                            case 'SU':
                                $T->set_var('moday_'.$rules['BYDAY'].'_selected',' selected="selected" ');
                                break;
                            case 'SU,MO,TU,WE,TH,FR,SA' :
                                $T->set_var('moday_day_selected',' selected="selected" ');
                                break;
                            case 'MO,TU,WE,TH,FR' :
                                $T->set_var('moday_weekday_selected',' selected="selected" ');
                                break;
                            case 'SA,SU' :
                                $T->set_var('moday_weekend_selected',' selected="selected" ');
                                break;
                        }
                        $T->set_var('mo_'.$rules['BYMONTH'],' selected="selected" ');
                    }
                    break;
                default :
                break;
            }
            if ( isset($rules['COUNT'])) {
                $T->set_var('end_type_0_selected',' selected="selected" ');
                $T->set_var('endafter_value',$rules['COUNT']);
            } else {
                $T->set_var('end_type_1_selected',' selected="selected" ');
                $T->set_var('recur-end-date',$rules['UNTIL']);
            }
        }
        // end of recurence edits

        if ( $row['allday'] == 1 ) {
            $T->set_var('allday_checked',' checked="checked" ');
        }
        $T->set_var('parent_id',$parent_id);

        $T->set_var(array(
            'admin_form' => true,
            'form_action' => $_CONF['site_admin_url'].'/plugins/agenda/index.php',
        ));

        $T->parse('output', 'page');
        $page = $T->finish($T->get_var('output'));
    }
    return $page;
}


/*
 * Saves an event that has been edited from the
 * submission queue
 */
function saveEditEvent($args = array())
{
    global $_CONF, $_AC_CONF, $_USER, $_TABLES;

    $event = new Agenda\eventHandler();
    $rc = $event->updateQueuedEvent($args);
    return $rc;
}


/*
 * Display admin list of categories
*/
function listEvents()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_AC, $LANG_AC_JS, $_IMAGE_TYPE;

    $retval = "";

    $header_arr = array(
        array('text' => $LANG_ADMIN['edit'], 'field' => 'pid', 'align' => 'center', 'width' => '25px'),
        array('text' => $LANG_AC['title'], 'field' => 'title','align' => 'left', ),
        array('text' => $LANG_AC_JS['details'], 'field' => 'start', 'align' => 'center', 'sort'=>false,'width' => '25px'),
        array('text' => $LANG_AC['start_date'], 'field' => 'start_date','nowrap'=>true,'sort' => true, 'align' => 'center'),
        array('text' => $LANG_AC['end_date'], 'field' => 'end_date','nowrap'=>true,'sort' => true, 'align' => 'center'),
        array('text' => $LANG_AC['series'], 'field' => 'repeats', 'align' => 'center'),
        array('text' => $LANG_AC['allday'], 'field' => 'allday', 'align' => 'center'),
        array('text' => $LANG_AC['exception'], 'field' => 'exception', 'sort' => false, 'align' => 'center'),
    );

    $defsort_arr = array('field'     => 'start_date',
                         'direction' => 'ASC');
    $text_arr = array(
            'form_url'      => $_CONF['site_admin_url'] . '/plugins/agenda/index.php?eventlist=x',
            'help_url'      => '',
            'has_search'    => true,
            'has_limit'     => true,
            'has_paging'    => true,
            'no_data'       => $LANG_AC['no_events'],
    );

    $sql = "SELECT *, parent_id AS pid, event_id AS delid FROM {$_TABLES['ac_events']} ";

    $query_arr = array( 'table' => 'ac_events',
                        'sql' => $sql,
                        'query_fields' => array('parent_id','title'),
                        'default_filter' => " WHERE start_date >= (curdate() - 2678400) ", // 31 days previous
                        'group_by' => "");

    $filter = '';

    $actions = '<input name="delsel" type="image" src="'
            . $_CONF['layout_url'] . '/images/admin/delete.' . $_IMAGE_TYPE
            . '" style="vertical-align:bottom;" title="' . $LANG_AC['delete_checked']
            . '" onclick="return confirm(\'' . $LANG_AC['delete_confirm_event'] . '\');"'
            . ' value="x" '
            . '/>&nbsp;' . $LANG_AC['delete_checked'];

    $option_arr = array(
            'chkselect'     => false,
            'chkfield'      => 'delid',
            'chkname'       => 'event_ids',
            'chkminimum'    => 0,
            'chkall'        => true,
            'chkactions'    => $actions
    );

    $token = SEC_createToken();

    $formfields = '
        <input name="action" type="hidden" value="delevt">
        <input type="hidden" name="' . CSRF_TOKEN . '" value="'. $token .'">
    ';

    $form_arr = array(
        'top' => $formfields
    );

    $retval .= ADMIN_list('eventlist', 'AC_getListField', $header_arr,
                          $text_arr, $query_arr, $defsort_arr, $filter,
                          $token, $option_arr, $form_arr);

    return $retval;
}

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

    $retval .= ADMIN_list('categorylist', 'AC_getListField', $header_arr,
    $text_arr, $query_arr, $defsort_arr, $filter, $token, $option_arr, $form_arr);

    return $retval;
}

function AC_getListField($fieldname, $fieldvalue, &$A, $icon_arr, $token = "")
{
    global $_CONF, $_USER, $_TABLES, $LANG_AC, $LANG_AC_JS, $LANG_ADMIN, $LANG04, $LANG28, $_IMAGE_TYPE;

    $retval = '';

    $toolTipStyle = COM_getToolTipStyle();

    switch ($fieldname) {
        case 'start' :
            $dt = new Date('now',$_USER['tzid']);
            if ( $A['allday'] ) {
                $acStartDate = $A['start_date'];
                $acStartTime = '00:00:00';
                $acEndDate = $A['end_date'];
                $acEndTime = '24:00:00';
                $dt->setTimestamp(strtotime($acStartDate.' '.$acStartTime));
                $when =  $dt->format('l d-M-Y', false);
                $dt->setTimestamp(strtotime($acEndDate. ' ' . '23:00:00'));
                if ( $A['start_date'] != $A['end_date']) {
                    $when .= ' to ' . $dt->format('l d-M-Y', false);
                }
            } else {
                $dt->setTimestamp($A['start']);
                $cmpStart = $dt->format('Ymd',true);
                $tStartDate = $dt->format("l   d-M-Y", true);
                $tStartTime = $dt->format("h:i a", true);
                $dt->setTimestamp($A['end']);
                $cmpEnd = $dt->format('Ymd',true);
                $tEndDateFormat = "h:i a";
                if ( $cmpStart != $cmpEnd ) {
                    $tEndDateFormat = "l d-M-Y h:i a";
                }
                $tEndDate = $dt->format($tEndDateFormat, true);
                $when = $tStartDate .'<br>' . $tStartTime .' to ' . $tEndDate;
            }
            $retval = '<i class="uk-icon uk-icon-calendar '.$toolTipStyle.'" title="';
            $retval .= '<p><b>'.$LANG_AC_JS['when'].'</b><br>'. $when;
            if ( $A['location'] != '' ) {
                $retval .= '<p><b>'.$LANG_AC_JS['location'].'</b><br>'.$A['location'].'</p>';
            }
            if ( $A['description'] != '' ) {
                $retval .= '<p><b>'.$LANG_AC_JS['details'].'</b><br>'.$A['description'].'</p>';
            }
            $retval .= '"></i>';
            break;

        case 'pid' :
            $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?editevent=x&event_id='.$A['event_id'];
            $retval = '<a href="'.$url.'" title="'.$LANG_AC_JS['edit_event'].'"><i class="uk-icon uk-icon-pencil"></i></a>';
            break;

        case 'parent_id' :
            if ( $A['repeats'] == 1 ) {
                $langConfirm = $LANG_AC_JS['delete_series_confirm'];
            } else {
                $langConfirm = $LANG_AC_JS['delete_event_confirm'];
            }

            $attr['title'] = $LANG_ADMIN['delete'];
            $attr['onclick'] = "return confirm('" . $langConfirm . "');";
            $retval .= COM_createLink($icon_arr['delete'],
                $_CONF['site_admin_url'] . '/plugins/agenda/index.php'
                . '?delevent=x&amp;pid=' . $fieldvalue . '&amp;' . CSRF_TOKEN . '=' . $token, $attr);
            break;

        case 'exception' :
            if ( $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-circle uk-text-danger" title="'.$LANG_AC['event_exception'].'"></i>';
            } else {
                $retval = '';
            }
            break;

        case 'repeats' :
            if ( $fieldvalue == 1 && $A['exception'] == 0 ) {
                $url = $_CONF['site_admin_url'].'/plugins/agenda/index.php?editpid=x&parent_id='.$A['parent_id'];
                $retval = '<a href="'.$url.'" title="'.$LANG_AC_JS['edit_series'].'"><i class="uk-icon uk-icon-pencil-square-o"></i></a>';
                break;
            } else {
                $retval = '';
            }
            break;

        case 'allday' :
            if ( $fieldvalue == 1 ) {
                $retval = '<i class="uk-icon uk-icon-circle uk-text-success"></i>';
            } else {
                $retval = '';
            }
            break;

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

function agenda_admin_menu($action,$title)
{
    global $_CONF, $_AC_CONF, $LANG_ADMIN, $LANG_AC, $LANG_AC_JS, $LANG01;

    $retval = '';

    if ( $action == 'edit' ) {
        $menu_arr = array(
            array( 'url' => $_CONF['site_admin_url'].'/moderation.php','text' => $LANG01[10],'active' =>false),
            array( 'url' => $_CONF['site_admin_url'].'/moderation.php','text' => $LANG_AC_JS['edit_event'],'active' => true),
            array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?catlist=x','text' => $LANG_AC['category_list'],'active' => ($action == 'catlist' ? true : false)),
            array( 'url' => $_CONF['site_admin_url'].'/index.php', 'text' => $LANG_ADMIN['admin_home'])
        );
    } else {
        $menu_arr = array(
            array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php','text' => $LANG_AC['event_list'],'active' => ($action == 'list' ? true : false)),
            array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?newevent=x','text' => $LANG_AC['create'],'active' => ($action == 'newevent' ? true : false)),
            array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?catlist=x','text' => $LANG_AC['category_list'],'active' => ($action == 'catlist' ? true : false)),
            array( 'url' => $_CONF['site_admin_url'].'/plugins/agenda/index.php?newcat=x','text' => $LANG_AC['category_new'],'active' => ($action == 'newcat' ? true : false)),
            array( 'url' => $_CONF['site_admin_url'].'/index.php', 'text' => $LANG_ADMIN['admin_home'])
        );
    }

    $retval = '<h2>'.$title.'</h2>';

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_AC['admin_help'],
        $_CONF['site_url'] . '/agenda/images/agenda.png'
    );

    return $retval;
}

$page = '';
$display = '';
$cmd = 'list';
//var_dump($_POST);exit;
$title = $LANG_AC['admin'];

if ( isset($_POST['action']) ) {
    $action = COM_applyFilter($_POST['action']);
    $_POST[$action] = 'x';
}

$expectedActions = array('newevent','new-event','edit','editpid','editevent','edit-event','edit-event-series','catlist','editcat','edit-category','newcat','save-new-category','delcat');
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

// load needed JS
$outputHandler = \outputHandler::getInstance();
$outputHandler->addLinkScript($_CONF['site_url'].'/agenda/fc/moment.min.js',HEADER_PRIO_NORMAL);
$outputHandler->addLinkStyle($_CONF['site_url'].'/javascript/addons/datetime/jquery.datetimepicker.min.css',HEADER_PRIO_NORMAL);
$outputHandler->addLinkScript($_CONF['site_url'].'/javascript/addons/datetime/jquery.datetimepicker.full.min.js',HEADER_PRIO_NORMAL);

switch ( $cmd ) {

    case 'newevent' :

        $form = new Agenda\eventForms(true);
        $page = $form->newEvent();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC_JS['add_event'];

        break;
// saves a new event
    case 'new-event' :
        if (isset($_POST['submit']) && SEC_checkToken() ) {
            // save the event
            $eventHandler = new Agenda\eventHandler();
            $eventHandler->saveEvent($_POST);
            COM_setMsg($LANG_AC['event_saved_msg'],'info',false);
        }
        $page = listEvents();
        break;

// edit from mod queue
    case 'edit' :
        if ( isset($_GET['parent_id'])) {
            $parent_id = COM_applyFilter($_GET['parent_id'],true);
            $page = editEvent($parent_id);
        } else {
            $page = 'invalid input';
        }
        break;

// edit event from admin list
    case 'editevent' :
        $event_id = COM_applyFilter($_GET['event_id'],true);
        $parent_id = DB_getItem($_TABLES['ac_events'],'parent_id','event_id='.(int) $event_id);
        $form = new Agenda\eventForms(true);
        $page = $form->editEvent($parent_id,$event_id);
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC_JS['edit_event'];
        break;

// edit series from admin list
    case 'editpid' :
        $parent_id = COM_applyFilter($_GET['parent_id'],true);
        $form = new Agenda\eventForms(true);
        $page = $form->editSeries($parent_id);
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC_JS['edit_event_series'];
        break;

// save edit event series from admin list
    case 'edit-event-series' :
        if ( isset($_POST['submit']) && SEC_checkToken() ) {
            $eventHandler = new Agenda\eventHandler();
            $eventHandler->updateEventSeries($_POST);
            COM_setMsg($LANG_AC['event_saved_msg'],'info',false);
        } elseif ( isset($_POST['delete-series']) && SEC_checkToken() ) {
            if ( isset($_POST['parent_id'] ) ) {
                $parent_id = COM_applyFilter($_POST['parent_id'],true);
                $eventHandler = new Agenda\eventHandler();
                $eventHandler->deleteEventSeries($parent_id);
                COM_setMsg($LANG_AC['series_delete_msg'],'info',false);
            }
        }
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['event_list'];
        $page = listEvents();

        break;

// edit event from moderator queue or admin list
    case 'edit-event' :
        // called from mod queue - or when saving from both mod queue or admin list
        if ( isset($_POST['src']) && $_POST['src'] == 'mod' ) {
            $rc = saveEditEvent($_POST);
            if ( $rc == 0 ) {
                COM_setMsg($LANG_AC['event_saved_msg'],'info',false);
                echo COM_refresh($_CONF['site_admin_url'].'/moderation.php');
                exit;
            } else {
                $parent_id = COM_applyFilter($_POST['parent_id'],true);
                $page = editEvent($parent_id);
            }
        } elseif ( isset($_POST['submit'] ) && SEC_checkToken() ) {
            // admin list save
            $eventHandler = new Agenda\eventHandler();
            $eventHandler->updateEvent($_POST);
            COM_setMsg($LANG_AC['event_saved_msg'],'info',false);
        } elseif ( isset($_POST['delete-event'] ) && SEC_checkToken() ) {
            if ( isset($_POST['parent_id']) && isset($_POST['event_id'] ) ) {
                $parent_id = COM_applyFilter($_POST['parent_id'],true);
                $event_id  = COM_applyFilter($_POST['event_id'],true);
                $eventHandler = new Agenda\eventHandler();
                $eventHandler->deleteEvent($parent_id, $event_id);
                COM_setMsg('Event deleted','info',false);
            }
        }
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['event_list'];
        $page = listEvents();
        break;

    case 'catlist' :
        $page = listCategory();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['category_list'];
        break;

    case 'editcat' :
        $category_id = isset($_GET['id']) ? (int) COM_applyFilter($_GET['id'],true) : 0;
        $page = agenda_edit_category($category_id);
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['edit_category'];
        break;

    case 'edit-category' :
        // save an edited category
        $page = saveEditCategory();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['edit_category'];
        break;

    case 'newcat' :
        $page = agenda_new_category();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['category_new'];
        break;

    case 'save-new-category' :
        $page = saveNewCategory();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['category_list'];
        break;

    case 'delcat' :
        $page =  deleteCategory();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['category_list'];
        break;

    case 'list' :
    default :
        $page = listEvents();
        $title = $LANG_AC['plugin_admin'] . ' :: ' . $LANG_AC['event_list'];
        break;
}

$display  = COM_siteHeader ('menu', $LANG_AC['admin']);
$display .= agenda_admin_menu($cmd,$title);
$display .= $page;
$display .= COM_siteFooter (false);
echo $display;

?>