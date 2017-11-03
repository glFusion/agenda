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
var whattoeditform;
var calheight;
var calwidth;
var qTipArray=new Array();
var qTipAPI = new Array();
var dragging = 0;
var lang = new Array();
//var defaultView = 'month';

//var agendaConfig = { "allow_new":false, "allow_edit":false };

// pull setup data
url = glfusionSiteUrl + '/agenda/ajax/ajax-controller.php';
$.ajax({
	type: "POST",
	async:true,
	cache: false,
	dataType: "json",
	data: {"action" : "setup-agenda" },
	url: url,
	success: function (data) {
		var result = $.parseJSON(data["js"]);
		lang = result.lang;
		var agendaConfig = result.config;
		$(document).ready(function () {
			initializeCalendar(agendaConfig);
			initializeAgenda();
		});
	},
	error: function (e) {
		alert('Error Retrieving Agenda Configuration');
		console.log("error retrieving setup information");
	}
});

function initializeAgenda() {

	// dialog for event edits
	dialog = $( "#dialog-form-full" ).dialog({
		autoOpen: false,
		modal: true,
		resizable: true,
		classes: { "ui-dialog": "tm-agenda-dialog" },
		height: window.innerHeight * .85,
		width: window.innerWidth * .8,
		buttons: [
		{
			text: lang['save_event'],
			"class" : 'uk-button uk-button-primary',
			click: function() {
				saveevent();
			}
		},
		{
			text: lang['cancel'],
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
		classes: { "ui-dialog": "tm-agenda-dialog" },
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
			text: lang['cancel'],
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
						required: lang['err_select_option']+"<br/>"
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
}


/*
* general functions
*/

// save an event
function saveevent() {
	// ensure the form is valid
	if ( $('#event-form').valid() == false ) return false;

	// need to know if we are editing or adding or deleting

	url = glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php';
	$.ajax({
		type: "POST",
		dataType: "json",
		url: url,
		data: $('#event-form').serialize(),
		success: function (data) {
			var result = $.parseJSON(data["js"]);
			if ( result.errorCode == 0 ) {
				console.log('save event ajax returned successfully');
			}
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
	// validate we want to do this...
	UIkit.modal.confirm(lang['delete_event_confirm'], function(){
		url = glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php';
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {"action" : "delete-event", "parent_id" : event.parent_id, "event_id" : event.id },
			success: function (data) {
				var result = $.parseJSON(data["js"]);
				if ( result.errorcode == 0 ) {
					console.log('success');
				}
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
	// validate we want to do this...
	UIkit.modal.confirm(lang['delete_series_confirm'], function(){
		url = glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php';
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

	url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
	$.ajax({
		type: "POST",
		dataType: "html",
		url: url,
		data: {"action" : "edit-event", "parent_id" : event.parent_id, "event_id" : event.id },
		success: function (data) {
			$("#dialog-form-full").html(data);
		},

		error: function (e) {
			console.log("Error retrieving form");
		}
	});
	// override the dialog buttons
	var editButtons = [
	{
		text: lang['save_event'],
		"class" : 'uk-button uk-button-success',
		click: function() {
			saveevent();
		}
	},
	{
		text: lang['delete_event'],
		"class" : 'uk-button uk-button-danger',
		click: function() {
			deleteevent(event);
		}
	},
	{
		text: lang['cancel'],
		"class": 'uk-button uk-button',
		click : function () {
			dialog.dialog('close');
		}
	}
	];

	dialog.dialog("option", "buttons", editButtons); // setter
	dialog.dialog('option', 'height', window.innerHeight * .85 );
	dialog.dialog('option', 'width', window.innerWidth * .8 );

	var buttons = $('.ui-dialog-buttonset').children('button');
	buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
	//	$(".uk-button-danger").attr("formnovalidate");

	dialog.dialog("open");
}

function edit_series_event(event)
{
	$('#dialog-form-full').dialog('option', 'title', 'Edit Event Series');
	$("#dialog-form-full").html('');

	url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
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
		text: lang['save_event'],
		"class" : 'uk-button uk-button-success',
		click: function() {
			saveevent();
		}
	},
	{
		text: lang['delete_series'],
		"class" : 'uk-button uk-button-danger',
		click: function() {
			deleteeventseries(event);
		}
	},
	{
		text: lang['cancel'],
		"class": 'uk-button uk-button',
		click : function () {
			dialog.dialog('close');
		}
	}
	];

	dialog.dialog("option", "buttons", editButtons); // setter
	dialog.dialog('option', 'height', window.innerHeight * .85 );
	dialog.dialog('option', 'width', window.innerWidth * .8 );

	var buttons = $('.ui-dialog-buttonset').children('button');
	buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
	dialog.dialog("open");
}

/*
* Initializes the full calendar widget
*
* params - config - configuration data
*/
function initializeCalendar( config )
{
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	whattoeditform = $("#what-to-edit");

	// initialize full calendar
	calendar = $('#calendar').fullCalendar({
		dayClick: function(date, jsEvent, view) {
			if ( config['allow_new'] ) {
				var clickDate = date.format();
				$('#dialog-form-full').dialog('option', 'title', lang['add_event']);
				if ( window.innerWidth < 600 ) {
					$('#dialog-form-full').dialog('option', 'height', window.innerHeight);
					$('#dialog-form-full').dialog('option', 'width', window.innerWidth * .95);
				} else {
					$('#dialog-form-full').dialog('option', 'height', window.innerHeight *.85 );
					$('#dialog-form-full').dialog('option', 'width', window.innerWidth * .8 );
				}
				$("#dialog-form-full").html('');
				url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
				$.ajax({
					type: "POST",
					dataType: "html",
					url: url,
					data: {"action" : "new-event", "clickdate" : clickDate },
					success: function (data) {
						$("#dialog-form-full").html(data);
					},
					error: function (e) {
						console.log("error retrieving new-event form");
					}
				});

				// override the dialog buttons
				var editButtons = [
				{
					text: lang['save_event'],
					"class" : 'uk-button uk-button-success',
					click: function() {
						saveevent();
					}
				},
				{
					text: lang['cancel'],
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
			if ( config['allow_edit'] ) {
				$('.qtip').remove();
				qTipArray[event.id].hide();

				if ( event.repeats == 1 ) {
					$("#dialog-series").data('event-data', event).dialog('open');
				} else {
					edit_single_event(event);
				}
				$('#calendar').fullCalendar('refetchEvents');
			} else {
				alert('event click for non-admin users');
			}
		},

		// apply during the event display / render
		eventRender: function(event, element, view) {
			if ( dragging == 0 ) { // don't render tooltip if dragging in progress
				var mouseTarget = false;
				if ( view.name == 'listMonth') {
					mouseTarget = 'event';
				}
				eval("var qtip_" + event.id + '=1');
				qTipArray[event.id] = element.qtip({
					content:{
						title : event.title,
						text : '<p><b>'+lang['when']+'</b><br>'+ event.when +
						(event.location && '<p><b>'+lang['location']+'</b><br>'+event.location+'</p>' || '') +
						(event.description && '<p><b>'+lang['details']+'</b><br>'+event.description+'</p>' || '')
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
			$('.qtip').remove();
			console.log("event has been resized");
			qTipArray[event.id].hide();
			var params = "&action=move-event&id=" + event.id;
			if ( event.start != null ) {
				params = params + "&date=" + event.start.format();
			}
			if ( event.end != null ) {
				params = params + "&end=" + event.end.format();
			}
			params = params + "&allday=" + event.allDay;
			$.ajax({
				type: "POST",
				url: glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php',
				data: params,
				success: function (data) {
					var result = $.parseJSON(data["js"]);
					if ( result.errorCode == 0 ) {
						console.log("resize event successful");
					}
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
		},
		eventDragStop: function( event, jsEvent, ui, view ) {
			dragging = 0;
		},
		eventResizeStart: function( event, jsEvent, ui, view ) {
			$('.qtip').remove();
			dragging = 1;
			qTipArray[event.id].hide();
		},
		eventResizeStop: function( event, jsEvent, ui, view ) {
			dragging = 0;
		},
		eventDrop: function(event, delta, revertFunc) {
			$('.qtip').remove();
			qTipArray[event.id].hide();
			qTipArray[event.id].qtip('destroy', true);
			var params = "&action=move-event&id=" + event.id;
			if ( event.start != null ) {
				params = params + "&date=" + event.start.format();
			}
			if ( event.end != null ) {
				params = params + "&end=" + event.end.format();
			}
			params = params + "&allday=" + event.allDay;
			$.ajax({
				type: "POST",
				url: glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php',
				data: params,
				dataType: "json",
				success: function (data) {
					var result = $.parseJSON(data["js"]);
					if ( result.errorCode == 0 ) {
						console.log("move event successful");
					}
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
			left: config['header_left'],
			center: config['header_center'],
			right: config['header_right']
		},
		locale: config['locale'],
		isRTL: config['isrtl'],
		events: glfusionSiteUrl + '/agenda/ajax/json-events.php',
		editable: false, // overriden in the event object
		defaultView: defaultview,
		defaultDate:  defaultdate,
		allDayDefault: false,
		height: config['autoheight'],
		firstDay: config['first_day'],
	});
	// end of full calendar initialization
}
