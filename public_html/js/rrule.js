/*! glFusion CMS - Agenda Plugin for glFusion - License GNU General Public License version 2 or later
 *  Copyright (C) 2016-2017 by Mark R. Evans - mark AT glfusion DOT org */
var dowMapArray = ['SU','MO','TU','WE','TH','FR','SA'];

$(document).ready(function() {
	// initialize end date
	var start_date = $("#event-date").val();
	var momentFrom = moment(start_date, 'YYYY-MM-DD');
	var updated_stop_date = moment(momentFrom).add(1, 'y');
	var newStopDate = updated_stop_date.format('YYYY-MM-DD');
	$("#recur-end-date").val(newStopDate);

	$('select[name="end-type"]').change(function(){
		switch ( $(this).val() ) {
			case "0" :
				$("#max-occurrence").show();
				$("#repeat-end-date").hide();
				break;
			default :
				$("#max-occurrence").hide();
				$("#repeat-end-date").show();
				break;
		}
	});

	$('select[name="freq"]').change(function(){
		if( $(this).val() != "none" ){
			$('#recurring-rules').show();
			switch ( $(this).val() ) {
				case 'HOURLY' :
					$('#event-interval').show();
					$('#intervals').show();
					$('#weekday-select').hide();
					$('#monthly-rules').hide();
					$('#yearly-rules').hide();
					$('#interval-prompt').html(lang_hours);
					break;

				case 'DAILY' :
					$('#event-interval').show();
					$('#intervals').show();
					$('#weekday-select').hide();
					$('#monthly-rules').hide();
					$('#yearly-rules').hide();
					$('#interval-prompt').html(lang_days);
					break;

				case 'WEEKLY' :
					$('#event-interval').show();
					$('#intervals').show();
					$('#weekday-select').show();
					$('#monthly-rules').hide();
					$('#yearly-rules').hide();
					$('#interval-prompt').html(lang_weeks);

					var sDate = $("#event-date").val();
					var sTime = moment(sDate, 'YYYY-MM-DD').day();
					$('#'+dowMapArray[sTime]).addClass('uk-button-success');
					var byday = [];
					$('#weekday-select button').each(function(){
						if ( $(this).hasClass('uk-button-success') ) {
							byday.push($(this).attr('id'));
						}
					});
					$('#wk-byday').val(byday.toString());
					break;

				case 'MONTHLY' :
					$('#event-interval').show();
					$('#intervals').show();
					$('#monthly-rules').show();
					$('#weekday-select').hide();
					$('#yearly-rules').hide();
					$('#interval-prompt').html(lang_months);

					var start_date = $("#event-date").val();
					var momentFrom = moment(start_date, 'YYYY-MM-DD');
					var selMonth = momentFrom.format('M');
					var selDay   = momentFrom.format('D');
					var dow      = momentFrom.format('e');
					$("#mo-dom").val(selDay);
					$("#mo-day").val(dowMapArray[dow]);

					var wkInMonth = Math.ceil(momentFrom.date() / 7);
					$('#mo-setpos').val(wkInMonth);

					//mo-setpos
					break;

				case 'YEARLY' :
					$('#intervals').hide();
					$('#event-interval').hide();
					$('#weekday-select').hide();
					$('#monthly-rules').hide();
					$('#yearly-rules').show();
					// calculate
					var start_date = $("#event-date").val();
					var momentFrom = moment(start_date, 'YYYY-MM-DD');
					var selMonth = momentFrom.format('M');
					var selDay   = momentFrom.format('D');
					var dow      = momentFrom.format('e');
					$("#yr-month").val(selMonth);
					$("#yr-dom").val(selDay);
					$("#yr-month2").val(selMonth);
					$("#yr-day").val(dowMapArray[dow]);

					var wkInMonth = Math.ceil(momentFrom.date() / 7);
					$('#yr-setpos').val(wkInMonth);

					// calculate an end date 5 years in advance
					var start_date = $("#event-date").val();
					var momentFrom = moment(start_date, 'YYYY-MM-DD');
					var updated_stop_date = moment(momentFrom).add(5, 'y');
					var newStopDate = updated_stop_date.format('YYYY-MM-DD');
					$("#recur-end-date").val(newStopDate);

					break;
			}
		} else {
			$('#recurring-rules').hide();
			$('#intervals').hide();
		}
	});
	$('#weekday-select button').on('click', function(){
		$(this).toggleClass('uk-button-success');
		var byday = [];
		$('#weekday-select button').each(function(){
			if ( $(this).hasClass('uk-button-success') ) {
				byday.push($(this).attr('id'));
			}
		});
		$('#wk-byday').val( byday.join(","));
	});
});