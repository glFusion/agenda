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

/*
* Main Function
*/

$query  = '';
$eid    = 0;
$page   = 1;

COM_setArgNames( array('id') );
$tid = (int) COM_applyFilter(COM_getArgument( 'id' ),true);

if (isset ($_GET['query'])) {
    $query = trim(COM_applyFilter ($_GET['query']));
}

$T = new Template ($_CONF['path'] . 'plugins/agenda/templates');

$T->set_file (array (
    'page' => 'calendar.thtml',
));

$T->set_var('write_access',true);

$T->set_var ('header', $LANG_AC['header']);


$T->parse('output', 'page');
$page = $T->finish($T->get_var('output'));

$display = COM_siteHeader('none',$LANG_AC['plugin_name']);
$display .= $page;
$display .= COM_siteFooter();

echo $display;
?>