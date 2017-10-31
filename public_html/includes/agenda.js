/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* JavaScript backend
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

// global vars

var modal;
var calendar;
var dialog;
var dialog_series;
var form;	// entry form
var whattoeditform;
var calheight;
var calwidth;
var qTipArray=new Array();
var qTipAPI = new Array();
var dragging = 0;

var agendaConfig = { "allow_new":false, "allow_edit":false };

// pull configuration data
url = glfusionSiteUrl + '/agenda/includes/agenda_config.php';
$.ajax({
	type: "POST",
	dataType: "json",
	url: url,
	success: function (data) {
		agendaConfig = data;
		if ( agendaConfig['allow_edit'] ) $allowEdit = true;
		console.log(agendaConfig);
	},
	error: function (e) {
		console.log("error retrieving configuration");
	}
});

//
// document ready function
//
$(document).ready(function() {
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	form = $( "#event-form" );
	whattoeditform = $("#what-to-edit");
	// initialize FullCalendar

	calendar = $('#calendar').fullCalendar({
// click on the day - Add Event
		dayClick: function(date, jsEvent, view) {
			if ( agendaConfig['allow_new'] ) {
				var clickDate = date.format();
				$('#dialog-form-full').dialog('option', 'title', 'Add Event');
				$("#dialog-form-full").html('');
				url = glfusionSiteUrl + '/agenda/includes/ajax-form-manager.php';
				$.ajax({
					type: "POST",
					dataType: "html",
					url: url,
					data: {"action" : "new-event", "clickdate" : clickDate },
					success: function (data) {
						$("#dialog-form-full").html(data);
						form = $( "#event-form" );
						form.validate();
					},
					error: function (e) {
						console.log("error retrieving new-event form");
					}
				});

				// override the dialog buttons
				var editButtons = [
				{
					text: "Save Event",
					"class" : 'uk-button uk-button-success',
					click: function() {
						saveevent();
					}
				},
				{
					text: "Cancel",
					"class": 'uk-button uk-button',
					click : function () {
						dialog.dialog('close');
					}
				}
				];
				dialog.dialog("option", "buttons", editButtons);
				var buttons = $('.ui-dialog-buttonset').children('button');
				buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
				dialog.dialog("open");
			}
		},

// click on event - make this edit
		eventClick: function(event, element) {

			if ( agendaConfig['allow_edit'] ) {
				qTipArray[event.id].hide();
				qTipArray[event.id].qtip('destroy', true);

				if ( event.repeats == 1 ) {
					$("#dialog-series").data('event-data', event).dialog('open');
				} else {
					edit_single_event(event);
				}
				$('#calendar').fullCalendar('refetchEvents');
			}
		},

// apply during the event display / render
		eventRender: function(event, element, view) {
			if ( dragging == 0 ) { // don't render tooltip if dragging in progress
				var mouseTarget = false;
				if ( view.name == 'listMonth') {
					mouseTarget = 'mouse';
				}
				eval("var qtip_" + event.id + '=1');
				qTipArray[event.id] = element.qtip({
					content:{
						title : event.title,
						text : '<p><b>When</b><br>'+ event.when +
						(event.location && '<p><b>Location</b><br>'+event.location+'</p>' || '') +
						(event.description && '<p><b>Details</b><br>'+event.description+'</p>' || '')
					},
					show: {
						delay: 500
					},
					style: {
						classes: 'qtip-bootstrap qtip-shadow',
						width: 	 '1000px'
					},
					position: {
						my: 'top center',
						at: 'bottom center',
						target: mouseTarget, // Defaults to target element
						container: $('#calendar'), // Defaults to $(document.body)
						viewport: $(window), // Requires Viewport plugin
						adjust: {
							x: 0, y: 0, // Minor x/y adjustments
							mouse: true, // Follow mouse when using target:'mouse'
							resize: true, // Reposition on resize by default
							method: 'flip flip' // Requires Viewport plugin
						},
						effect: function(api, pos, viewport) {
							$(this).animate(pos, {
								duration: 200,
								queue: false
							});
						}
					},
				}); // end of qtip
				qTipAPI[event.id] = qTipArray[event.id].qtip('api');
			}
		},

		eventResize: function(event, delta, revertFunc) {
			console.log("event has been resized");
			qTipArray[event.id].hide();
			var params = "&id=" + event.id;
			if ( event.start != null ) {
				params = params + "&date=" + event.start.format();
			}
			if ( event.end != null ) {
				params = params + "&end=" + event.end.format();
			}
			params = params + "&allday=" + event.allDay;
			$.ajax({
				type: "POST",
				url: glfusionSiteUrl + '/agenda/includes/move_event.php',
				data: params,
				success: function (data) {
					console.log("resize event successful");
					$('#calendar').fullCalendar('refetchEvents');
					$('#calendar').fullCalendar( 'rerenderEvents' );
				},
				error: function (e) {
					console.log("Error resizing event");
					revertFunc();
				}
			});
		},

// moved an event
		eventDragStart: function( event, jsEvent, ui, view ) {
			dragging = 1;
			console.log("starting drag");
		},
		eventDragStop: function( event, jsEvent, ui, view ) {
			dragging = 0;
		},
		eventResizeStart: function( event, jsEvent, ui, view ) {
			dragging = 1;
			console.log("starting drag");
			qTipArray[event.id].hide();
		},
		eventResizeStop: function( event, jsEvent, ui, view ) {
			dragging = 0;
		},
		eventDrop: function(event, delta, revertFunc) {
			console.log("event has been moved");
			qTipArray[event.id].hide();
			qTipArray[event.id].qtip('destroy', true);
			var params = "&id=" + event.id;
			if ( event.start != null ) {
				params = params + "&date=" + event.start.format();
			}
			if ( event.end != null ) {
				params = params + "&end=" + event.end.format();
			}
			params = params + "&allday=" + event.allDay;
			$.ajax({
				type: "POST",
				url: glfusionSiteUrl + '/agenda/includes/move_event.php',
				data: params,
				success: function (data) {
					console.log("move event successful");
					$('#calendar').fullCalendar('refetchEvents');
				},
				error: function (e) {
					console.log("Error moving event");
					revertFunc();
				}
			});

		},
// config options
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listMonth'
		},
		events: glfusionSiteUrl + '/agenda/includes/json-events.php',
		editable: false, // overriden in the event object
		defaultView: 'month',
		allDayDefault: false,
	});
// end of full calendar initialization

// dialog for event edits
	dialog = $( "#dialog-form-full" ).dialog({
		autoOpen: false,
		height: window.innerHeight * .75,
		width: window.innerWidth * .7,
		modal: true,
		buttons: [
		{
			text: "Save Event",
			"class" : 'uk-button uk-button-primary',
			click: function() {
				saveevent();
			}
		},
		{
			text: "Cancel",
			"class": 'uk-button uk-button-danger',
			click : function () {
				dialog.dialog('close');
			}
		}
		],
		create: function () {
			var buttons = $('.ui-dialog-buttonset').children('button');
			buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
		},
		close: function() {
			var openForm = $("event-form");
			if ( typeof $("#event-form")[ 0 ] !== 'undefined' ) {
				$("#event-form")[ 0 ].reset();
			}
		}
	});
// end of dialog

//
// recurring event edit dialog to
// determine if editing the event
// or the series
	dialog_series = $( "#dialog-series" ).dialog({
		autoOpen: false,
		buttons: [
		{
			text: "OK",
			"class" : 'uk-button uk-button-primary',
			click: function() {
				// restore the event object
				event = $(this).data('event-data');

				// check if form is valid
				if ( whattoeditform.valid() == false ) return false;
				var answer = $('input[name="edit-series"]:checked').val();
				dialog_series.dialog('close');
				// 0 = edit single event
				// 1 = edit the series
				if ( answer == 0 ) {
					edit_single_event( event );
				} else {
					edit_series_event( event );
				}
			}
		},
		{
			text: "Cancel",
			"class": 'uk-button uk-button-danger',
			click : function () {
				dialog_series.dialog('close');
			}
		}
		],
		close: function() {
			$("#what-to-edit")[0].reset();

		},
		open: function() {
			//				whattoeditform.validate();
			var vform = whattoeditform.validate({
				rules: {
					"edit-series": {
						required:true
					}
				},
				messages:
				{
					"edit-series":
					{
						required:"Please select an option<br/>"
					}
				},
				errorPlacement: function(error, element)
				{
					console.log(error);
					if ( element.is(":radio") ) {

						error.replaceAll($("#validate-error"));

						//error.appendTo( element.parents('.container') );
						// error.insertAfter( element );
					} else { // This is the default behavior
						error.insertAfter( element );
					}
				}
			});

		}
	});
	// end of dialog series
}); // end of document ready


/*
* general functions
*/

// save an event
function saveevent() {
	// ensure the form is valid
	if ( form.valid() == false ) return false;

	// need to know if we are editing or adding or deleting

	//  url = $( '#dbbackupform' ).attr( 'action' );
	url = '/agenda/includes/ajax-save-event.php';
	$.ajax({
		type: "POST",
		dataType: "json",
		url: url,
		data: $('#event-form').serialize(),
		success: function (data) {
			console.log('save event ajax returned successfully');
			$('#calendar').fullCalendar('refetchEvents');
		},
		error: function (e) {
			console.log("Error saving event");
			$('#calendar').fullCalendar('refetchEvents');
		}
	});

	$("#event-form")[0].reset();
	$('#start-time').val('00:00').prop('disabled', false);
	$('#end-time').val('01:00').prop('disabled', false);
	dialog.dialog( "close" );
	return true;
};

function deleteevent ( event ) {
	// ensure the form is valid
	if ( form.valid() == false ) return false;

	// validate we want to do this...
	UIkit.modal.confirm("Are you sure you want to delete this event?", function(){
		url = '/agenda/includes/ajax-save-event.php';
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {"action" : "delete-event", "parent_id" : event.parent_id, "event_id" : event.id },
			success: function (data) {
				console.log('Deleting an event returned successfully');
				$('#calendar').fullCalendar('refetchEvents');
			},
			error: function (e) {
				console.log("Error deleting event");
				$('#calendar').fullCalendar('refetchEvents');
			}
		});

		$("#event-form")[0].reset();
		$('#start-time').val('00:00').prop('disabled', false);
		$('#end-time').val('01:00').prop('disabled', false);
		dialog.dialog( "close" );
	});
	return true;
};

function deleteeventseries( event ) {
	// ensure the form is valid
	if ( form.valid() == false ) return false;

	// validate we want to do this...
	UIkit.modal.confirm("Are you sure you want to delete THE ENTIRE SERIES?", function(){
		url = '/agenda/includes/ajax-save-event.php';
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {"action" : "delete-event-series", "parent_id" : event.parent_id, "event_id" : event.id },
			success: function (data) {
				console.log('Deleting an event returned successfully');
				$('#calendar').fullCalendar('refetchEvents');
			},
			error: function (e) {
				console.log("Error deleting event");
				$('#calendar').fullCalendar('refetchEvents');
			}
		});

		$("#event-form")[0].reset();
		$('#start-time').val('00:00').prop('disabled', false);
		$('#end-time').val('01:00').prop('disabled', false);
		dialog.dialog( "close" );
	});
	return true;
};

function edit_single_event( event )
{
	$('#dialog-form-full').dialog('option', 'title', 'Edit Event');
	$("#dialog-form-full").html('');

	url = '/agenda/includes/ajax-form-manager.php';
	$.ajax({
		type: "POST",
		dataType: "html",
		url: url,
		data: {"action" : "edit-event", "parent_id" : event.parent_id, "event_id" : event.id },
		success: function (data) {
			$("#dialog-form-full").html(data);
			form = $( "#event-form" );
			form.validate();
		},
		error: function (e) {
			console.log("Error retrieving form");
		}
	});
	// override the dialog buttons
	var editButtons = [
	{
		text: "Save Event",
		"class" : 'uk-button uk-button-success',
		click: function() {
			saveevent();
		}
	},
	{
		text: "Delete Event",
		"class" : 'uk-button uk-button-danger',
		click: function() {
			deleteevent(event);
		}
	},
	{
		text: "Cancel",
		"class": 'uk-button uk-button',
		click : function () {
			dialog.dialog('close');
		}
	}
	];

	dialog.dialog("option", "buttons", editButtons); // setter
	var buttons = $('.ui-dialog-buttonset').children('button');
	buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
	dialog.dialog("open");


}

function edit_series_event(event)
{
	$('#dialog-form-full').dialog('option', 'title', 'Edit Event Series');
	$("#dialog-form-full").html('');

	url = '/agenda/includes/ajax-form-manager.php';
	$.ajax({
		type: "POST",
		dataType: "html",
		url: url,
		data: {"action" : "edit-event-series", "parent_id" : event.parent_id, "event_id" : event.id },
		success: function (data) {
			$("#dialog-form-full").html(data);
			form = $( "#event-form" );
			form.validate();
		},
		error: function (e) {
			console.log("Error retrieving form");
		}
	});
	// override the dialog buttons
	var editButtons = [
	{
		text: "Save Event",
		"class" : 'uk-button uk-button-success',
		click: function() {
			saveevent();
		}
	},
	{
		text: "Delete Series",
		"class" : 'uk-button uk-button-danger',
		click: function() {
			deleteeventseries(event);
		}
	},
	{
		text: "Cancel",
		"class": 'uk-button uk-button',
		click : function () {
			dialog.dialog('close');
		}
	}
	];

	dialog.dialog("option", "buttons", editButtons); // setter
	var buttons = $('.ui-dialog-buttonset').children('button');
	buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
	dialog.dialog("open");
}

// end of file