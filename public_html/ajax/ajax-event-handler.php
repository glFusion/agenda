<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* Full Calendar Event Callback Handler
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

require_once '../../lib-common.php';

$ajaxHandler = new \ajaxHandler();

$errorCode = 0;
$errors    = 0;
$retval = array();

$action = '';
if (isset($_POST['action'])) {
    $action = COM_applyFilter($_POST['action']);
}

switch ($action) {
    case 'new-event' :
        $event = new Agenda\eventHandler();
        $rc = $event->saveEvent($_POST);
        break;

    case 'edit-event' : // save an edit
        $event = new Agenda\eventHandler();
        $rc = $event->updateEvent($_POST);
        break;

    case 'edit-event-series' : // save an event series edit
        $event = new Agenda\eventHandler();
        $rc = $event->updateEventSeries($_POST);
        break;

    case 'delete-event' : // delete a SINGLE event
        $parent_id = isset($_POST['parent_id']) ? (int) COM_applyFilter($_POST['parent_id'],true) : -1;
        $event_id  = isset($_POST['event_id'])  ? (int) COM_applyFilter($_POST['event_id'],true) : -1;

        $event = new Agenda\eventHandler();
        $rc = $event->deleteEvent($parent_id, $event_id);
        break;

    case 'delete-event-series' :
        $parent_id = isset($_POST['parent_id']) ? (int) COM_applyFilter($_POST['parent_id'],true) : -1;
        $event = new Agenda\eventHandler();
        $rc = $event->deleteEventSeries($parent_id);
        break;

    case 'move-event' :
        $event = new Agenda\eventHandler();
        $rc = $event->moveEvent($_POST);
        break;

    default :
        $rc = -1;
        break;
}
$ajaxHandler->setErrorCode( $rc );
$ajaxHandler->sendResponse();

?>