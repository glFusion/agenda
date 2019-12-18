# Agenda Plugin ChangeLog

## v1.0.3

### Fixed
  - Fixed issue where the 'fifth' option did not display for repeating event entry
  - In some cases, repeating events would skip the first occurence of the future events
  - Events that run between two months do not always display correctly #3
  - Fixed issue where C&C icon showed for any user with access to C&C regardless if they had permission to view

### Updated
  - Updated rlanvin/php-rrule (v1.6.2 => v1.6.3)

## v1.0.2

### Added
 - iCal Content Syndication support (glFusion v1.7.6+ or newer only)

### Changed
 - Improved warning message when editing series with exception events
 - Updated RRULE library to 1.6.2
 - Updated Full Calendar to v3.9.0
 - Updated Moment JS to v2.22.2
 - Form consistency - use standard colors for buttons - move delete to right alignment

### Fixed
 - Fixed a long standing timezone bug that prevented some all day events from displaying properly
 - Fixed incorrect function call in creating excerpt in getItemInfo()
 - Fixed incorrect date return in getItemInfo()
 - Fixed incorrect status return in getItemInfo()
 - Fixed error in monthly maintenance routes that prevented recurring events with purged parents from being deleted

## v1.0.1

### Added
 - Display previous 30 days of events in admin event list

### Changed
 - Update to address issue where all day events did not always display correctly

### Fixed
 - Fixed issue Agenda would return invalid results when searching by author

## v1.0.0
 - First full production release
 - Additional spam checks
 - Display Submitted By in the submission queue
 - Capture the IP address of event submissions
 - Filter title, location and description through glFusion bad word filter

## v0.9.0
 - Automatic event maintenance (remove old events)
 - Updated FullCalendar to v3.7.0

## v0.8.0
 - Admin event manager

## v0.7.0
 - Add new configuration options to control column date/time format and title date/time format for Monthly, Weekly and Day views

## v0.6.1
 - Fixed incorrect reference to agenda's main JS file
 - Fixed a few spelling issues and removed extra < in recurrence forms

## v0.6.0
 - You can now edit all attributes of an event series
 - Use datetimepicker plugin for pop-up calendar
 - Do not allow end date to be prior to begin date
 - Code consolidations and cleanup

## v0.5.0
 - Add print option
 - New configuration setting to enable print button / feature
 - Resolved duplicate entries in Upcoming Events Block

## v0.4.0
 - Added several new configuration options to control how the calendar displays
   - display week number
   - time formats
   - start / end times on events
   - number of events per 'box'
 - Rewrote upgrade / install routine to leverage a single source for config data

## v0.3.1
 - Disable Edit Series button on repeating events that are marked as an exception
 - Left align buttons on both pop-up and edit strings
 - Reduced the size of the description entry field by 1 row
 - Fixed some E_ALL errors
 - Fixed some missing localized string
 - End before start validation did not work if an all day event

## v0.3.0
 - New recurrence rules engine
 - Migrated date / time validations to momemt JS
 - Localized submission queue message
 - Use configuration Week Start setting on date picker
 - Localized start time string in edit event template

## v0.2.1
 - Fixed error where delete configuration dialog would not close
 - Improved wrapping of text for All Day and Repeats checkboxes

## v0.2.0
 - Complete rewrite of the tooltip / dialog UI
 - Tooltips open on click
 - Add edit / edit series buttons to tooltips
 - Automatically calculate end-time when start-time is changed

## v0.1.1
 - Fixed error where resizing an event did not refresh the display
 - Fixed error where editing an event did not update the child event record properly

## v0.1.0
 - Initial Alpha Release
