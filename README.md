## Agenda Plugin for glFusion

For the latest, and more detailed, documentation, please see the [Agenda Plugin Wiki Page](https://www.glfusion.org/wiki/glfusion:plugins:agenda:start)

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
* Upcoming Events block


### Installation

The Agenda Plugin uses the glFusion automated plugin installer. Simply upload the distribution using the glFusion plugin installer located in the Plugin Administration page.

### Upgrading

The upgrade process is identical to the installation process, simply upload the distribution from the Plugin Administration page.

### Usage Notes

#### Recurring Events
Agenda supports the following recurrence patterns:
* Daily
* Weekly
* Every 2 weeks
* Monthly
* Yearly

##### Special Notes

###### Monthly

If a recurring event is created on a day that does not appear in all months - for example, October 31 and is set to repeat each month - it will repeat on the last day of each month.


### Permissions

Agenda is designed to be open - as a result, events do not have any permissions associated with them. If you can view the calendar, you can view an event. The following controls are implemented to restrict access:

#### Anonymous User Access
You can enable or disable anonymous user access to the calendar.  Logged-in users will have access to the calendar.

#### Event Submission
Agenda supports limiting who can add new events to the calendar. You can restrict to Admin users only, allow Admins and Logged-In users, or allow anyone (including non-logged in users) to add events.

Events can be queued for review prior to publishing. Queuing can be applied all events (except those entered by Admin users), or just anonymous users who submit an event.


### Configuration

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


### License

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.
