/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* JavaScript Event Form Validations
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/
$(document).ready(function () {
	$.validator.addMethod("enddate", function(value, element) {
		var from_time = $("#start-time").val();
		var to_time = $("#end-time").val();
		var start_date = $("#event-date").val();
		var end_date = $("#event-end-date").val();
		var from = Date.parse(start_date +' '+ from_time);
		var to = Date.parse(end_date + ' '+ to_time);
		if (from > to){
			return false;
		} else {
			return true;
		}
	}, " * " + lang['err_end_before_start']);

	$.validator.addMethod("allday", function(value, element) {
		if ($('#event-allday').is(':checked') == false) {
			return true;
		}
		var from_time = '12:00 AM';
		var to_time = '11:59 PM';
		var start_date = $("#event-date").val();
		var end_date = $("#event-end-date").val();
		var from = Date.parse(start_date +' '+ from_time);
		var to = Date.parse(end_date + ' '+ to_time);
		if (from > to){
			return false;
		} else {
			return true;
		}
	}, " * " + lang['err_end_before_start']);

	$( "#event-form" ).validate({
		errorElement: 'span',
		errorClass: 'uk-text-danger uk-text-bold',
		rules: {
			"title": { required:true },
			"end-time": { enddate: true },
			"event-allday": { allday: true },
		},
		messages: {
			"title":{	required: ' * ' + lang['err_enter_title']	},
		},
		errorPlacement: function(error, element) {
			if (element.attr("name") == "end-time" || element.attr("name") == "event-allday" ) {
				error.insertAfter('#date-errors');
			} else {
				error.insertAfter( element );
			}
		},
	});
});