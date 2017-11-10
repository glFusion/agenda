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
    allday      tinyint(1) unsigned default '0',
    start       int(11) unsigned default 0,
    end         int(11) unsigned default 0,
    start_date  DATE default NULL,
    end_date    DATE default NULL,
    title       varchar(120) default NULL,
    location    varchar(120) default NULL,
    description text default NULL,
    repeats     tinyint(1)  default '0',
    repeat_freq	int(30) default NULL,
    rrule       varchar(128) default NULL,
    category    int(30) default '1',
    queued      tinyint(1) unsigned default 0,
    owner_id mediumint(8) unsigned NOT NULL default '1',
    PRIMARY KEY  (parent_id)
) ENGINE=MyISAM
";

$_SQL['ac_events'] = "CREATE TABLE {$_TABLES['ac_events']} (
    event_id    int(30) unsigned auto_increment,
    parent_id   int(30) unsigned default NULL,
    allday      tinyint(1) unsigned default '0',
    start       int(11) unsigned default 0,
    end         int(11) unsigned default 0,
    start_date  DATE default NULL,
    end_date    DATE default NULL,
    title       varchar(120) default NULL,
    location    varchar(120) default NULL,
    description text default NULL,
    repeats     tinyint(1)  default '0',
    repeat_freq	int(30) default NULL,
    category    int(30) default '1',
    exception   tinyint(1) unsigned default '0',
    owner_id mediumint(8) unsigned NOT NULL default '1',
    PRIMARY KEY  (event_id)
) ENGINE=MyISAM
";

$_SQL['ac_category'] = "CREATE TABLE {$_TABLES['ac_category']} (
    category_id int(30) unsigned auto_increment,
    cat_name    varchar(120) default NULL,
    cat_desc    varchar(120) default NULL,
    fgcolor     varchar(28) default '#fff',
    bgcolor     varchar(28) default '#3a87ad',
    PRIMARY KEY  (category_id)
) ENGINE=MyISAM
";

$_DATA['default_category'] = "
    INSERT INTO {$_TABLES['ac_category']} (category_id,cat_name,cat_desc) VALUES (1,'No category','Default Category')
    ";

?>