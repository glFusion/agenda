Agenda Plugin ChangeLog

## v1.0.1 (May 14, 2018)
 - Fixed issue Agenda would return invalid results when searching by author
 - Update to address issue where all day events did not always display correctly
 - Display previous 30 days of events in admin event list

## v1.0.0 (January 30, 2018)
 - First full production release
 - Additional spam checks
 - Display Submitted By in the submission queue
 - Capture the IP address of event submissions
 - Filter title, location and description through glFusion bad word filter

## v0.9.0 (December 17, 2017)
 - Automatic event maintenance (remove old events)
 - Updated FullCalendar to v3.7.0

## v0.8.0 (December 6, 2017)
 - Admin event manager

## v0.7.0 (November 27, 2017)
 - Add new configuration options to control column date/time format and title date/time format for Monthly, Weekly and Day views

## v0.6.1 (November 26, 2017)
 - Fixed incorrect reference to agenda's main JS file
 - Fixed a few spelling issues and removed extra < in recurrence forms

## v0.6.0 (November 23, 2017)
 - You can now edit all attributes of an event series
 - Use datetimepicker plugin for pop-up calendar
 - Do not allow end date to be prior to begin date
 - Code consolidations and cleanup

## v0.5.0 (November 16, 2017)
 - Add print option
 - New configuration setting to enable print button / feature
 - Resolved duplicate entries in Upcoming Events Block

## v0.4.0 (November 14, 2017)
 - Added several new configuration options to control how the calendar displays
   - display week number
   - time formats
   - start / end times on events
   - number of events per 'box'
 - Rewrote upgrade / install routine to leverage a single source for config data

## v0.3.1 (November 12, 2017)
 - Disable Edit Series button on repeating events that are marked as an exception
 - Left align buttons on both pop-up and edit strings
 - Reduced the size of the description entry field by 1 row
 - Fixed some E_ALL errors
 - Fixed some missing localized string
 - End before start validation did not work if an all day event

## v0.3.0 (November 10, 2017)
 - New recurrence rules engine
 - Migrated date / time validations to momemt JS
 - Localized submission queue message
 - Use configuration Week Start setting on date picker
 - Localized start time string in edit event template

## v0.2.1 (November 7, 2017)
 - Fixed error where delete configuration dialog would not close
 - Improved wrapping of text for All Day and Repeats checkboxes

## v0.2.0 (November 7, 2017)
 - Complete rewrite of the tooltip / dialog UI
 - Tooltips open on click
 - Add edit / edit series buttons to tooltips
 - Automatically calculate end-time when start-time is changed

## v0.1.1 (November 4, 2017)
 - Fixed error where resizing an event did not refresh the display
 - Fixed error where editing an event did not update the child event record properly

## v0.1.0 (November 3, 2017)
 - Initial Alpha Release
