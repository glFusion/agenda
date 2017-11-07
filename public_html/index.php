<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Index Page
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../lib-common.php';

if (!in_array('agenda', $_PLUGINS)) {
    COM_404();
    exit;
}

if ( COM_isAnonUser() ) {
    if ( $_AC_CONF['allow_anonymous_view'] == false && !SEC_hasRights('agenda.view')) {
        if ( $_AC_CONF['security_exit'] == 0 ) {
            COM_404();
            exit;
        } else {
            $display  = COM_siteHeader();
            $display .= SEC_loginRequiredForm();
            $display .= COM_siteFooter();
            echo $display;
            exit;
        }
    }
} elseif (!SEC_hasRights('agenda.view')) {
    COM_404();
    exit;
}

/*
* Main Function
*/

$query  = '';

$allowedViews = array('month','agendaWeek','agendaDay','listMonth');

COM_setArgNames( array('v','y','m','d') );
$view = COM_applyFilter(COM_getArgument('v'));
if ( !in_array($view,$allowedViews)) {
    $view = $_AC_CONF['defaultview'];
}

$year   = COM_getArgument('y');
$month  = COM_getArgument('m');
$day    = COM_getArgument('d');

$dt = new Date('now',$_USER['tzid']);

if ( $year == '' ) {
    $defaultDate = $dt->format('Y-m-d');
} else {
    if ( $month == '' ) {
        $month = 1;
    }
    if ( $day == '' ) {
        $day = 1;
    }
    $defaultDate = sprintf("%4d-%02d-%02d",$year,$month,$day);
}


if (isset ($_GET['query'])) {
    $query = trim(COM_applyFilter ($_GET['query']));
}

$T = new Template ($_CONF['path'] . 'plugins/agenda/templates');

$T->set_file (array (
    'page' => 'calendar.thtml',
));

$T->set_var(array(
    'lang_edit_single_or_series'    => $LANG_AC['edit_single_or_series'],
    'lang_what_to_edit'             => $LANG_AC['what_to_edit'],
    'lang_just_this_one'            => $LANG_AC['just_this_one'],
    'lang_entire_series'            => $LANG_AC['entire_series'],
    'lang_agenda'                   => $LANG_AC['plugin_name'],
    'view'                          => $view,
    'defaultdate'                   => $defaultDate,
    'version'                       => $_AC_CONF['pi_version'].'.'.AGENDA_SNAPSHOT,
));

$T->parse('output', 'page');
$page = $T->finish($T->get_var('output'));
$display = COM_siteHeader($_AC_CONF['menu'],$LANG_AC['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();
echo $display;
?>