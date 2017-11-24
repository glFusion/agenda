/*!
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* JavaScript Event Repeat Initialization
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/
$(document).ready(function() {
	var repeatOption = $('select[name="freq"]').val();
	if ( repeatOption != 'none' ) {
		$('#recurring-rules').show();
		switch ( repeatOption ) {
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
					break;

				case 'MONTHLY' :
					$('#event-interval').show();
					$('#intervals').show();
					$('#monthly-rules').show();
					$('#weekday-select').hide();
					$('#yearly-rules').hide();
					$('#interval-prompt').html(lang_months);
					break;

				case 'YEARLY' :
					$('#intervals').hide();
					$('#event-interval').hide();
					$('#weekday-select').hide();
					$('#monthly-rules').hide();
					$('#yearly-rules').show();
					break;
		}
	}
	var endType = $('select[name="end-type"]').val();
	switch ( endType ) {
		case "0" :
			$("#max-occurrence").show();
			$("#repeat-end-date").hide();
			break;

		default :
			$("#max-occurrence").hide();
			$("#repeat-end-date").show();

			break;
	}
}); // end of document ready