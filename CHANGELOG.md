Agenda Plugin ChangeLog

## v0.5.0 (unreleased)
 - Add print option

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
