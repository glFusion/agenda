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

if ( COM_isAnonUser() && $_AC_CONF['allow_anonymous_view'] == false )  {
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

/*
* Main Function
*/

$query  = '';
$eid    = 0;
$page   = 1;

$allowedViews = array('month','agendaWeek','agendaDay','listMonth');

COM_setArgNames( array('v','y','m','d') );
$view = COM_applyFilter(COM_getArgument('v'));
if ( !in_array($view,$allowedViews)) {
    $view = 'month';
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

if ( SEC_hasRights('agenda.admin') ) {
    $T->set_var('write_access',true);
} else {

// $_AC_CONF['allow_entry'] // 0 = none, 1 = logged in, 2 = anyone
    $T->unset_var('write_access');
}

$T->set_var ('header', $LANG_AC['header']);


$T->set_var('view',$view);
$T->set_var('defaultdate',$defaultDate);

$T->parse('output', 'page');
$page = $T->finish($T->get_var('output'));

$display = COM_siteHeader($_AC_CONF['menu'],$LANG_AC['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
?>