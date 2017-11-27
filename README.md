## Agenda Plugin for glFusion

### Overview

This plugin offers a full featured calendar for your glFusion site.

### System Requirements

The Agenda Plugin has the following system requirements:

* PHP 5.6.0 and higher.
* glFusion v1.7.0 or newer
* Must be using a UIKIT based theme - will not work with Vintage or Nouveau themes

### Features
Agenda is a full featured event management system that supports the following features:
* Dynamic, JavaScript driven calendar view (provided by Full Calendar)
* Recurring event support
* Localized - supports all glFusion supported languages
* Full RTL support
* Upcoming Events block


### Installation

The Agenda Plugin uses the glFusion automated plugin installer. Simply upload the distribution using the glFusion plugin installer located in the Plugin Administration page.

### Upgrading

The upgrade process is identical to the installation process, simply upload the distribution from the Plugin Administration page.

### Usage Notes

#### Recurring Events

Agenda support the iCal RRULE standard for recurring events (RFC 2445).

Some examples of types of recurring events you can create:

* Weekly on Tuesday and Thursday for 5 weeks
* Monthly on the 1st Friday for ten occurrences
* Yearly, every forth Thursday in November (Thanksgiving in the US)

### Special Notes on Recurrence

Per RFC section 3.3.10, recurrence instances falling on invalid dates and times are ignored rather than corrected:

> Recurrence rules may generate recurrence instances with an invalid date (e.g., February 30) or nonexistent local time (e.g., 1:30 AM on a day where the local time is moved forward by an hour at 1:00 AM). Such recurrence instances MUST be ignored and MUST NOT be counted as part of the recurrence set.

This means recurring events that have an event that would occur on an invalid date is simply ignored. For example, every month on the 29th would skip February 29 on years that are not leap years.

### Recurring Event Rules

When editing a specific event that is part of a series, you have the option to do the following:

* Edit the Individual Event
* Edit the entire series

#### Editing an Individual Event

When editing a single event in a series, you can change any attribute about the event, including dates and times. The event will marked as an exception, meaning any series edits will not apply this single event in the series.

Dragging a single event in a series to a new date or resizing the time window will mark the event as an exception, making it exempt from series edits.

If you delete the entire series, this event **will** be deleted as well.

##### Editing a Series

When editing an Event Series, there are two methods to edit the series - updating without changing the dates / times or recurrence rules and updating the recurrence rules (Edit Recurrence checked in the edit form).

When updating just the Location, Description and/or Category (Edit Recurrence checkbox **not** checked), when saved, the entire event series will be updated **except** any exception events.

When **Edit Recurrence** is checked, allowing you to edit all attributes of the event series, when saved, the **entire** event series (including exception events) are deleted and the series is re-created using the new rules. It is important to remember when **Edit Recurrence** is checked, all exception events are removed on save. Agenda will also place a warning on the screen to remind you of this as well.


### Permissions

The following controls are implemented to control access:

#### Configuration Settings

#### Anonymous User Access
You can enable or disable anonymous user access to the calendar.  Logged-in users will have access to the calendar.

#### Event Submission
Agenda supports limiting who can add new events to the calendar. You can restrict to Admin users only, allow Admins and Logged-In users, or allow anyone (including non-logged in users) to add events.


#### glFusion Groups and Rights

#### Agenda Admin Group

Members of the Agenda Admin group have **full** read / write capabilities.

#### agenda.view Right

Anyone, with the **agenda.view** feature assigned to a group they belong to will be able to view the Agenda Calendar. Note, you can override the Configuration Setting of Allow Anonymous View by assigning the **agenda.view** feature to the Non-Logged-in Users group.

#### agenda.noqueue Right

Anyone with the **agenda.noqueue** feature assigned to a group they belong to will be able to submit new events and **bypass** the submission queue (if enabled).

For more information on glFusion Groups / Rights - please see the [Permissions Overview Wiki Page](https://www.glfusion.org/wiki/glfusion:permissions).


### Configuration

#### General

**Allow Anonymous View**

If set to TRUE - anonymous (non-logged-in-users) will be able to view the calendar. If set to FALSE, anonymous users will not be able to access the calendar. See the Security Exit option below to control how glFusion responds to a user without access.

**Security Exit**

If Allow Anonymous View is FALSE, this option controls how glFusion responds to an anonymous user. The user can receive a Page Not Found error or be redirected to the login to access the calendar.

**Who can enter Events**

This setting controls who can enter new events on the calendar. Options include No One (only admins), Logged-in-users or anyone. See the Submission Queue setting to enable / disable submission queuing of user submitted events.

**Submission Queue**

Controls what events are queued for review prior to publishing. Options include Disabled - all submissions are immediately published, Anonymous Only - only non-logged-in users will have their submissions queued, All - all user submitted events will be queued. Note: Admin users are exempt from the submission queue.

**Display Blocks**

Which glFusion blocks to display when viewing the Agenda Calendar.

**Show Upcoming Events Block**

If set to TRUE - the Upcoming Events block will be enabled.

**Days to Include in Upcoming Events Block**

Number of days into the future to list upcoming events.

#### Global View Options

**Default View**

Select the default calendar view when calendar is initially displayed. Options are Month, Week, Day or List view.

**Auto Height**

Determines how the calendar sizes in the browser window. Auto will use as much 'height' as needed to display the select view without adding scroll bars to the view. Auto also compresses the month view to take only the space needed. Fit will take as much space as available and add scroll bars to the view if additional vertical space is needed.

**Header Left Items**

Select what to display in the Left header area of the calendar.

**Header Center Items**

Select what to display in the Center header area of the calendar.

**Header Right Items**

Select what to display in the Right header area of the calendar.

**First Day of Week**

Select the first day of the week for your location.

**Display Week Numbers**

If TRUE, the week number will be displayed on the Month, Week and Day views.

**Print Enabled**

If TRUE, a print button will be displayed in the Agenda header to allow printing the current view.


#### Month View Options

**Month View :: Event Limit**

Limits the number of events displayed on a day. When there are too many events, a link that looks like "+2 more" is displayed.

**Month View :: Time Format**

Determines the time-text that will be displayed on each event. For example, 'hh:mm a' will display 12:25 am. See PHP date() formats for details on different formats.

**Month View :: Column Format**

Determines the text that will be displayed on the calendar's column headings. For example, 'ddd' will display 'Mon', 'ddd M/D' will display 'Mon 9/7'.


**Month View :: Title Format**

Determines the text that will be displayed in the header's title. For example, MMMM YYYY' will display 'September 2009', 'MMM D YYYY' will display 'Sep 13 2009', 'MMMM D YYYY' will display 'September 8 2009'.


**Month View :: Display Event Time**

If TRUE, the Event Start Time will display next to the event (All Day events DO NOT display a start time).

**Month View :: Display Event End Time**

If TRUE, the Event End Time will also display next to the event (All Day events DO NOT display a end time).

#### Week View Options

**Week View :: Event Limit**

Limits the number of events displayed on a day. When there are too many events, a link that looks like "+2 more" is displayed.

**Week View :: Time Format**

Determines the time-text that will be displayed on each event. For example, 'hh:mm a' will display 12:25 am. See PHP date() formats for details on different formats.

**Week View :: Column Format**

Determines the text that will be displayed on the calendar's column headings. For example, 'ddd' will display 'Mon', 'ddd M/D' will display 'Mon 9/7'.


**Week View :: Title Format**

Determines the text that will be displayed in the header's title. For example, MMMM YYYY' will display 'September 2009', 'MMM D YYYY' will display 'Sep 13 2009', 'MMMM D YYYY' will display 'September 8 2009'.


**Week View :: Display Event Time**

If TRUE, the Event Start Time will display next to the event (All Day events DO NOT display a start time).

**Week View :: Display Event End Time**
If TRUE, the Event End Time will also d
isplay next to the event (All Day events DO NOT display a end time).


#### Day View Options

**Day View :: Event Limit**

Limits the number of events displayed on a day. When there are too many events, a link that looks like "+2 more" is displayed.

**Day View :: Time Format**

Determines the time-text that will be displayed on each event. For example, 'hh:mm a' will display 12:25 am. See PHP date() formats for details on different formats.formats.

**Day View :: Column Format**

Determines the text that will be displayed on the calendar's column headings. For example, 'ddd' will display 'Mon', 'ddd M/D' will display 'Mon 9/7'.


**Day View :: Title Format**

Determines the text that will be displayed in the header's title. For example, MMMM YYYY' will display 'September 2009', 'MMM D YYYY' will display 'Sep 13 2009', 'MMMM D YYYY' will display 'September 8 2009'.


**Day View :: Display Event Time**

If TRUE, the Event Start Time will display next to the event (All Day events DO NOT display a start time).

**Day View :: Display Event End Time**

If TRUE, the Event End Time will also display next to the event (All Day events DO NOT display a end time).

#### List View Options

**List View :: Time Format**

Determines the time-text that will be displayed on each event. For example, 'hh:mm a' will display 12:25 am. See PHP date() formats for details on different formats.

**List View :: Display Event Time**

If TRUE, the Event Start Time will display next to the event (All Day events DO NOT display a start time).

**List View :: Display Event End Time**

If TRUE, the Event End Time will also display next to the event (All Day events DO NOT display a end time).


### License

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
