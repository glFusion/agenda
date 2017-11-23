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

var calendar;
var lang = new Array();
var globalEvent;
var agendaConfig;

// fetch configuration data and language translations
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
		agendaConfig = result.config;
		$(document).ready(function () {
			initializeCalendar(agendaConfig);
			initializeAgenda();
		});
	},
	error: function (e) {
		alert(lang['err_initialize']);
		console.log("AJAX call to initialize Agenda plugin failed.");
	}
});

function initializeAgenda() {
	// binds the escape key to close qtip2
	$(window).bind('keydown', function(event) {
		if(event.keyCode === 27) {
			$('.qtip').qtip('hide', event);
		}
	});
}

/*
* general functions
*/

// save an event
function saveevent() {
	// ensure the form is valid
	if ( $('#event-form').valid() == false ) return false;
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
			if ( result.errorCode == 3 ) { // queued event
				UIkit.modal.alert(lang['event_queued']);
			}
			$('#calendar').fullCalendar('refetchEvents');
		},
		error: function (e) {
			console.log("AJAX call to save event failed.");
			$('#calendar').fullCalendar('refetchEvents');
		}
	});
	$("#event-form")[0].reset();
	$( "#dialog-form-full" ).dialog( "close" );
	return true;
};

function deleteevent () {

	event = globalEvent;

	// validate we want to do this...
	UIkit.modal.confirm(lang['delete_event_confirm'], function(){
		url = glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php';
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {"action" : "delete-event", "parent_id" : globalEvent.parent_id, "event_id" : globalEvent.id },
			success: function (data) {
				var result = $.parseJSON(data["js"]);
				if ( result.errorCode == 0 ) {
				} else {
					console.log('Error deleting event - see glFusion error.log for details');
				}
				$('#calendar').fullCalendar('refetchEvents');
			},
			error: function (e) {
				console.log("AJAX call to delete event failed.");
				$('#calendar').fullCalendar('refetchEvents');
			}
		});
		$("#event-form")[0].reset();
		$( "#dialog-form-full" ).dialog( "close" );
	});
	return true;
};

function deleteeventseries() {
	// validate we want to do this...
	UIkit.modal.confirm(lang['delete_series_confirm'], function(){
		url = glfusionSiteUrl + '/agenda/ajax/ajax-event-handler.php';
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {"action" : "delete-event-series", "parent_id" : globalEvent.parent_id, "event_id" : globalEvent.id },
			success: function (data) {
				var result = $.parseJSON(data["js"]);
				if ( result.errorCode == 0 ) {
					console.log('Deleting event series returned successfully');
				} else {
					console.log('Error deleting series - see glFusion error.log for details');
				}
				$('#calendar').fullCalendar('refetchEvents');
			},
			error: function (e) {
				console.log("AJAX call to delete event series failed.");
				$('#calendar').fullCalendar('refetchEvents');
			}
		});
		$("#event-form")[0].reset();
		$( "#dialog-form-full" ).dialog( "close" );
	});
	return true;
};

function edit_single_event( )
{
	//	event = globalEvent;
	$('.qtip').hide();

	var editDialog = $( "#dialog-form-full" ).dialog({
		autoOpen: false,
		modal: true,
		resizable: true,
		classes: { "ui-dialog": "tm-agenda-dialog" },
		position: {
			my: "left top",
			at: "left top",
			of: "#calendar",
			collision: "fit"
		},
		width: $('#calendar').width(),
		height: 'auto',
		title: lang['edit_event'],
		buttons: [
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
				deleteevent();
			}
		},
		{
			text: lang['cancel'],
			"class": 'uk-button uk-button',
			click : function () {
				editDialog.dialog('close');
			}
		}
		]
	});

	url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
	$.ajax({
		type: "POST",
		dataType: "html",
		url: url,
		data: {"action" : "edit-event", "parent_id" : globalEvent.parent_id, "event_id" : globalEvent.id },
		success: function (data) {
			editDialog.html(data);
			// override the dialog buttons
			var buttons = $('.ui-dialog-buttonset').children('button');
			buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
			editDialog.dialog("open");
		},
		error: function (e) {
			console.log("AJAX call to retrieve edit event form failed.");
		}
	});
}

function edit_series_event()
{
	event = globalEvent;
	$('.qtip').hide();

	var editDialog = $( "#dialog-form-full" ).dialog({
		autoOpen: false,
		modal: true,
		resizable: true,
		classes: { "ui-dialog": "tm-agenda-dialog" },
		position: {
			my: "left top",
			at: "left top",
			of: "#calendar",
			collision: "fit"
		},
		width: $('#calendar').width(),
		height: 'auto',
		title: lang['edit_event_series'],
		buttons: [
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
				deleteeventseries();
			}
		},
		{
			text: lang['cancel'],
			"class": 'uk-button uk-button',
			click : function () {
				editDialog.dialog('close');
			}
		}
		]
	});
	url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
	$.ajax({
		type: "POST",
		dataType: "html",
		url: url,
		data: {"action" : "edit-event-series", "parent_id" : event.parent_id, "event_id" : event.id },
		success: function (data) {
			editDialog.html(data);
			form = $( "#event-form" );
			form.validate();
			var buttons = $('.ui-dialog-buttonset').children('button');
			buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
			editDialog.dialog("open");
		},
		error: function (e) {
			console.log("AJAX call to retrieve edit event series form failed.");
		}
	});
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

	// create the tooltip we will use on event click
	var tooltip = $('<div/>').qtip({
		id: 'calendar',
		prerender: true,
		content: {
			text: ' ',
			title: {
				button: true
			}
		},
		position: {
			viewport: $(window),
			my: 'top center',
			at: 'bottom center',
			target: 'event',
			adjust: {
				method: 'flip flip',
				scroll: false
			}
		},
		show: {
			solo: true,
			event: false,
		},
		hide: {
			event: 'unfocus'
		},
		style: {
			classes: 'qtip-bootstrap qtip-shadow'
		},
	}).qtip('api');

	// initialize full calendar
	calendar = $('#calendar').fullCalendar({
		dayClick: function(date, jsEvent, view) {
			if ( $('.qtip').is(':visible') ) {
				$('.qtip').hide();
			} else {
				if ( config['allow_new'] ) {
					var clickDate = date.format();

					var createDialog = $( "#dialog-form-full" ).dialog({
						autoOpen: false,
						modal: true,
						resizable: true,
						classes: { "ui-dialog": "tm-agenda-dialog" },
						position: {
							my: "left top",
							at: "left top",
							of: "#calendar",
							collision: "fit"
						},
						width: $('#calendar').width(),
						height: 'auto',
						title: lang['add_event'],
						buttons: [
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
								createDialog.dialog('close');
							}
						}
						]
					});
					url = glfusionSiteUrl + '/agenda/ajax/ajax-form-manager.php';
					$.ajax({
						type: "POST",
						dataType: "html",
						url: url,
						data: {"action" : "new-event", "clickdate" : clickDate },
						success: function (data) {
							createDialog.html(data);
							var buttons = $('.ui-dialog-buttonset').children('button');
							buttons.removeClass("ui-button ui-widget ui-state-default ui-state-active ui-state-focus");
							createDialog.dialog("open");
						},
						error: function (e) {
							console.log("AJAX call to retrieve new-event form failed.");
						}
					});
				}
			}
		},

		// click on event - make this edit
		eventClick: function(data, event, view) {
			globalEvent = data;

			var content = '<p><b>'+lang['when']+'</b><br>'+ data.when +
			(data.location && '<p><b>'+lang['location']+'</b><br>'+data.location+'</p>' || '') +
			(data.description && '<p><b>'+lang['details']+'</b><br>'+data.description+'</p>' || '');

			if ( config['allow_edit'] ) {
				content = content + '<hr>' +
				'<div class="uk-align-left">'+
				'<button onclick="edit_single_event();" id="edit-button" class="uk-margin-small-right uk-button uk-button-small uk-button-success" type="button">'+lang['edit']+'</button>';
				if ( data.repeats == 1 ) {
					content = content +
					'<button onclick="edit_series_event();" class="uk-margin-small-right uk-button uk-button-small uk-button-primary"';
					if ( data.exception == 1 ) {
						content = content + ' disabled="disabled" ';
					}
					content = content + '>' + lang['edit_series'] + '</button>';
					if ( data.exception == 1 ) {
						content = content + '&nbsp;' + lang['exception_event'];
					}
				}
				content = content +
				'</div>';
			}
			tooltip.set({
				'content.text': content,
				'content.title' : '<b>'+data.title+'</b>',
				'position.target' : 'event',
				'position.adjust.method' : 'flip',
			})
			.reposition(event).show(event);
		},

		eventResize: function(event, delta, revertFunc) {
			$('.qtip').hide();
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
						console.log("resize event successful");
					}
					$('#calendar').fullCalendar('refetchEvents');
					$('#calendar').fullCalendar( 'rerenderEvents' );
				},
				error: function (e) {
					console.log("AJAX call to resize an event failed.");
					revertFunc();
				}
			});
		},

		eventResizeStart: function( event, jsEvent, ui, view ) {
			$('.qtip').hide();
		},

		eventDrop: function(event, delta, revertFunc) {
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
					console.log("AJAX call to move an event failed.");
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
		customButtons: {
			print: {
				text: lang['print'],
				click: function() {
					printPreview();
				}
			}
		},
		locale: config['iso_lang'],
		isRTL: config['isrtl'],
		events: glfusionSiteUrl + '/agenda/ajax/json-events.php',
		editable: false, // overriden in the event object
		defaultView: defaultview,
		defaultDate:  defaultdate,
		allDayDefault: false,
		height: config['autoheight'],
		firstDay: config['first_day'],
		weekNumbers : config['weeknumbers'],
		views: {
			month: {
				eventLimit: config['month_eventlimit'] + 1,
				timeFormat: config['month_timeformat'],
				displayEventTime: config['month_displayeventtime'],
				displayEventEnd: config['month_displayeventend'],
			},
			week : {
				eventLimit: config['week_eventlimit'] + 1,
				timeFormat: config['week_timeformat'],
				displayEventTime: config['week_displayeventtime'],
				displayEventEnd: config['week_displayeventend'],
			},
			day: {
				eventLimit: config['day_eventlimit'] + 1,
				timeFormat: config['day_timeformat'],
				displayEventTime: config['day_displayeventtime'],
				displayEventEnd: config['day_displayeventend'],
			},
			listMonth: {
				timeFormat: config['list_timeformat'],
				displayEventTime: config['list_displayeventtime'],
				displayEventEnd: config['list_displayeventend'],
			}
		}
	});
	// end of full calendar initialization
}

function printPreview() {
	var waitToPrint = "<script>window.onload = function () { window.setTimeout(function () { window.print(); window.close(); }, 500); }</script>";
	var headerElements = document.getElementsByClassName('fc-header');
	for(var i = 0, length = headerElements.length; i < length; i++) {
		headerElements[i].style.display = 'none';
	}
	var toPrint = document.getElementById('calendar-area').cloneNode(true);
	for(var i = 0, length = headerElements.length; i < length; i++) {
		headerElements[i].style.display = '';
	}
	var linkElements = document.getElementsByTagName('link');
	var link = '';
	for(var i = 0, length = linkElements.length; i < length; i++) {
		link = link + linkElements[i].outerHTML;
	}
	var styleElements = document.getElementsByTagName('style');
	var styles = '';
	for(var i = 0, length = styleElements.length; i < length; i++) {
		styles = styles + styleElements[i].innerHTML;
	}
	link = link + '<link rel="stylesheet" type="text/css" href="'+glfusionSiteUrl+'/agenda/fc/fullcalendar.print.min.css">';
	var popupWin = window.open('', '_blank');
	popupWin.document.open();
	popupWin.document.write('<html><title>'+lang['agenda_calendar']+'</title>'+ link +'<style>'+styles+'</style>'+waitToPrint+'</head><body">');
	popupWin.document.write(toPrint.innerHTML);
	popupWin.document.write('</html>');
	popupWin.document.close();
//	popupWin.print();
//	setTimeout(function(){popupWin.close();}, 1);
}
