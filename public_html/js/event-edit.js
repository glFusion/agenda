/*! glFusion CMS - Agenda Plugin for glFusion - License GNU General Public License version 2 or later
 *  Copyright (C) 2016-2017 by Mark R. Evans - mark AT glfusion DOT org */

$(document).ready(function() {
	// check if all day is checked and set what is shown / hidden
	if ($('#event-allday').is(':checked') == true){
		$('#start-time').prop('disabled', true);
		$('#end-time').prop('disabled', true);
	}
	// update end time
	$('#start-time').change(function() {
		var from_time = $("#start-time").val();
		var to_time 	= $("#end-time").val();
		var start_date = $("#event-date").val();
		var end_date 	= $("#event-end-date").val();
		var momentFrom = moment(start_date + ' ' + from_time, 'YYYY-MM-DD hh:mm A');
		var momentTo   = moment(end_date + ' ' + to_time, 'YYYY-MM-DD hh:mm A');
		if (momentFrom > momentTo) {
			var updated_end_date_time = moment(momentFrom).add(1, 'h');
			var newToHours = updated_end_date_time.format('hh:mm A');
			$("#end-time").val(newToHours);
		}
	});
	// updated end date if needed
	$('#event-date').change(function(){
		var start_date = $("#event-date").val();
		var end_date 	= $("#event-end-date").val();
		var momentFrom = moment(start_date, 'YYYY-MM-DD');
		var momentTo   = moment(end_date, 'YYYY-MM-DD');
		if (momentFrom > momentTo) {
			$("#event-end-date").val(start_date);
		}
	});
	// show / hide stuff based on all day checked...
	$('#event-allday').change(function(){
		if ($('#event-allday').is(':checked') == true){
			$('#start-time').prop('disabled', true);
			$('#end-time').prop('disabled', true);
		} else {
			$('#start-time').prop('disabled', false);
			$('#end-time').prop('disabled', false);
		}
	});
}); // end of document ready

$.datetimepicker.setLocale(agendaConfig['iso_lang']);
var setMinimum = function( currentDateTime ){
	var start_date = $("#event-date").val();
	this.setOptions({
		minDate: start_date
	});
};
$('#event-date').datetimepicker({
	format:'Y-m-d',
	timepicker:false,
	mask:'9999-19-39',

});
$('#event-end-date').datetimepicker({
	format:'Y-m-d',
	timepicker:false,
	mask:'9999-19-39',
	onShow:setMinimum
});
$('#recur-end-date').datetimepicker({
	format:'Y-m-d',
	timepicker:false,
	mask:'9999-19-39',
});