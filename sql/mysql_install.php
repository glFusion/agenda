<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* SQL Table Schema
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2017 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$_SQL['ac_event'] = "CREATE TABLE {$_TABLES['ac_event']} (
    parent_id   int(30) unsigned auto_increment,
    title	    varchar(120)    default NULL,
    location    varchar(120)    default NULL,
    description text            default NULL,
    allday      tinyint(1) unsigned default '0',
    start_date	date		    default NULL,
    start_time	time		    default '00:00:00',
    end_date    date            default NULL,
    end_time	time		    default '00:00:00',
    repeats	    tinyint(1)      default NULL,
    repeat_freq	int(30)		    default NULL,

    queued      tinyint(1)      default '0',

    owner_id mediumint(8) unsigned NOT NULL default '1',

    PRIMARY KEY  (parent_id)
) ENGINE=MyISAM
";

$_SQL['ac_events'] = "CREATE TABLE {$_TABLES['ac_events']} (
    event_id    int(30) unsigned auto_increment,
    parent_id   int(30) unsigned default NULL,
    allday      tinyint(1) unsigned default '0',
    start       datetime default NULL,
    end	        datetime default NULL,
    title       varchar(120) default NULL,
    location    varchar(120) default NULL,
    description text default NULL,

    owner_id mediumint(8) unsigned NOT NULL default '1',

    PRIMARY KEY  (event_id)
) ENGINE=MyISAM
";

?>