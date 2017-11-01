<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Form Manager
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

if ( !COM_isAjax() ) die('invalid request');

$action = '';
if ( isset($_POST['action'])) {
    $action = COM_applyFilter($_POST['action']);
}

$page = '';

switch ($action) {

    case 'edit-event' :
        if ( !isset($_POST['parent_id']) || !isset($_POST['event_id'] ) ) {
            return $page;
        }
        $parent_id = COM_applyFilter($_POST['parent_id'],true);
        $event_id  = COM_applyFilter($_POST['event_id'],true);
        $form = new Agenda\eventForms();
        $page = $form->editEvent($parent_id, $event_id);
        break;

    case 'edit-event-series' :
        if ( !isset($_POST['parent_id']) || !isset($_POST['event_id'] ) ) {
            return $page;
        }
        $parent_id = COM_applyFilter($_POST['parent_id'],true);
        $event_id  = COM_applyFilter($_POST['event_id'],true);
        $form = new Agenda\eventForms();
        $page = $form->editSeries($event_id);
        break;

    case 'new-event' :
        if ( !isset($_POST['clickdate']) ) {
            return $page;
        }
        $clickDate = COM_applyFilter($_POST['clickdate']);
        $form = new Agenda\eventForms();
        $page = $form->newEvent($clickDate);
        break;

    default :
        $page = '';
        break;

}

echo $page;

?>