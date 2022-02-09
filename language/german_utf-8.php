<?php
/**
* glFusion CMS
*
* Agenda - Agenda Plugin for glFusion
*
* English Language - UTF-8
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2016-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$LANG_AC = array (
    'plugin'            => 'Terminkalender',
    'plugin_name'       => 'Terminkalender',
    'plugin_admin'		=> 'Termin-Verwaltung',
    'event_list'        => 'Termin-Übersicht',
    'admin_help'        => 'Erstelle, lösche und bearbeite hier die Termine und Kategorien des Terminkalenders.<br><br>Benutze Kategorien um Termine farblich im Kalender zu hinterlegen.',
    'access_denied'     => 'Zugriff verweigert',
    'access_denied_msg' => 'Sie sind nicht berechtigt, diese Seite zu sehen. Ihr Benutzername und Ihre IP wurden aufgezeichnet.',
    'category_list'     => 'Kategorien',
    'edit_category'     => 'Kategorie bearbeiten',

    'event_list'        => 'Termine',
    'admin'		        => 'Termin-Verwaltung',
    'cancel'			=> 'Abbrechen',
    'delete'			=> 'Löschen',
    'save'				=> 'Speichern',
    'create'            => 'Neuer Termin',
    'category_new'      => 'Neue Kategorie',
    'header'            => 'Terminkalender',
    'category_name'     => 'Kategorie-Name',
    'category_desc'     => 'Kategorie-Beschreibung',
    'no_categories'     => 'Keine Kategorien',
    'bgcolor'           => 'Hintergrundfarbe',
    'fgcolor'           => 'Schriftfarbe',
    'color_preview'     => 'Farbvorschau',
    'submissions'       => 'Termin-Einsendungen',
    'edit'              => 'Bearbeiten',
    'title'             => 'Titel',
    'owner'             => 'Ersteller',
    'start_date'        => 'Datum-Beginn',
    'end_date'          => 'Datum-Ende',
    'allday'            => 'Ganztägig',
    'no_events'         => 'Keine Termine gefunden',
    'delete_checked'    => 'Ausgewählte löschen',
    'delete_confirm'    => 'Sind Sie sicher, dass Sie die ausgewählten Kategorien löschen möchten?',
    'delete_confirm_event'    => 'Sind Sie sicher, dass Sie diese ausgewählten Termine löschen möchten?',
    'published'         => 'Veröffentlicht',
    'choose'            => 'Wähle',
    'more'              => 'Mehr',
    'less'              => 'Weniger',
    'sample_category'   => 'Beispielkategorie',
    'submission_mod_approved' => 'Termin genehmigt',
    'series'            => 'Serie',
    'event_exception'   => 'Dieserv Termin ist Teil einer Serie, wurde aber geändert',
    'event_list'        => 'Termin-Liste',
// block strings
    'site_events'       => 'Allgemeine Termine',
    'no_upcoming'       => 'Keine anstehenden Termine',
    'today'             => 'Heute',
// event forms
    'event_title'       => 'Titel des Termins',
    'location'          => 'Ort des Termins',
    'event_start'       => 'Termin-Beginn',
    'start_date'        => 'Datum-Beginn',
    'start_time'        => 'Uhrzeit-Beginn',
    'all_day_event'     => 'Ganztägiger Termin',
    'event_end'         => 'Ende des Termins',
    'end_date'          => 'Datum-Ende',
    'end_time'          => 'Uhrzeit-Ende',
    'repeats'           => 'Wiederholungen',
    'repeat_options'    => 'Wiederholungsoptionen',
    'daily'             => 'Täglich',
    'weekly'            => 'Wöchentlich',
    'biweekly'          => 'Alle 2 Wochen',
    'monthly'           => 'Monatlich',
    'yearly'            => 'Jährlich',
    'description'       => 'Beschreibung',
    'category'          => 'Kategorie',
    'no_category'       => 'Keine Kategorie ausgewählt',
    'no_category_desc'  => 'Standard-Kategorie verwenden, wenn keine Kategorie ausgewählt ist',
    'edit_single_or_series' => 'Termin oder Serie bearbeiten',
    'what_to_edit'       => 'Dies ist ein Tdermin in einer Serie. Was möchten Sie bearbeiten?',
    'just_this_one'     => 'Nur diesen',
    'entire_series'     => 'Die ganze Serie',
    'series_delete_msg' => 'Die Termin-Serie wurde gelöscht',
    'event_saved_msg'   => 'Der Termin wurde gespeichert',

    // rrule

    'repeat'            => 'Wiederholen',
    'none'              => 'Keine Wiederholung',
    'hourly'            => 'Stündlich',
    'daily'             => 'Täglich',
    'weekly'            => 'Wöchentlich',
    'monthly'           => 'Monatlich',
    'yearly'            => 'Jährlich',
    'every'             => 'jede',
    'hours'             => 'Stunde(n)',
    'days'              => 'Tag(e)',
    'weeks'             => 'Woche(n)',
    'months'            => 'Monat(e)',
// LANG_WEEK already has these - just use them...
    'weekly_help'       => 'Wählen Sie den/die Wochentag(e) für den Termin',
    'on_day'            => 'am Tag',
    'on_the'            => 'am',
    'first'             => 'Ersten',
    'second'            => 'Zweiten',
    'third'             => 'Dritten',
    'forth'             => 'Vierten',
    'last'              => 'Letzten',
//LANG WEEK has weekday names too
    'day'               => 'Tag',
    'weekday'           => 'Wochentag',
    'weekend'           => 'Wochenende',
// LANG_MONTH has month names and abbreviations
    'after'             => 'Nach',
    'on_date'           => 'Am',
    'occurrences'       => 'Auftreten',
    'end_after_date'    => 'Ende nach Datum',
    'of'                => 'von',
    'end'               => 'Ende',
    'exception_warning' => '<strong>Diese Termin-Serie hat Ausnahmeereignisse.</strong><br>Ausnahmeereignisse werden entfernt und die gesamte Serie wird neu erstellt, wenn Sie die Wiederholungsregeln bearbeiten.',
    'edit_recurrence'   => 'Serien-Termine bearbeiten',
    'exception'         => 'Ausnahmen',
    'ip_address'        => 'IP-Adresse',
);

// JavaScript specific translations
$LANG_AC_JS = array(
    'agenda_calendar'   => 'Termin-Kalender',
    'add_event'         => 'Neuer Termin',
    'edit_event'        => 'Termin bearbeiten',
    'edit_event_series' => 'Termi-Serie bearbeiten',
    'save_event'        => 'Termin speichern',
    'delete_event'      => 'Termin löschen',
    'delete_series'     => 'Termin-Serie löschen',
    'edit'              => 'Bearbeiten',
    'edit_series'       => 'Termin-Serie bearbeiten',
    'close'             => 'Schließen',
    'delete_event_confirm' => 'Sind Sie sicher, dass Sie dieses Termin löschen möchten?',
    'delete_series_confirm' => 'Sind Sie sicher, dass Sie diese Termin-Serie löschen möchten?',
    'cancel'            => 'Abbrechen',
    'when'              => 'Wann',
    'location'          => 'Ort',
    'details'           => 'Details',
    'err_select_option' => 'Bitte wähle eine Möglichkeit aus',
    'err_enter_title'   => 'Bitte geben Sie einen Titel ein',
    'err_end_before_start' => 'Datum/Uhrzeit-Ende muss nach Datum/Uhrzeit-Beginn sein',
    'err_initialize'    => 'Fehler beim Initialisieren des Terminkalender-Plugins',
    'event_queued'      => 'Vielen Dank für Ihren Termin. Ihre Einsendung befindet sich in der Warteschlange und wird demnächst überprüft.',
    'exception_event'   => 'Ausnahmeereignis',
    'print'             => 'drucken',
    'spam'              => 'Termin wurde als Spam eingestuft',
);

$LANG_AC_ERRORS = array(
    'invalid_title'     => 'Sie müssen einen Titel eintragen',
);

$LANG_configsections['agenda'] = array(
    'label' => 'Terminkalender',
    'title' => 'Terminkalender-Konfiguration',
);

$LANG_confignames['agenda'] = array(
// General Settings
    'allow_anonymous_view'  => 'Einsicht auch für Gäste',
    'security_exit'         => 'Gäste Weiterleitung',
    'allow_entry'           => 'Wer kann Termine eintragen',
    'submission_queue'      => 'Termin-Einsendungen',
    'displayblocks'         => 'Blöcke anzeigen',
    'showupcomingevents'    => 'Termin-Block anzeigen?',
    'upcomingeventsrange'   => 'Tage im Termin-Block',
    'maintenance_check_freq' => 'Alte Termine entfernen (Tage)',
    'maintenance_max_age'   => 'Alte Temine behalten (Jahre)',

// Global Calendar Settings

    'defaultview'           => 'Standard-Ansicht',
    'autoheight'            => 'Auto-Höhe',
    'header_left'           => 'Kopfzeile-Links',
    'header_center'         => 'Kopfzeile-Mitte',
    'header_right'          => 'Kopfzeile-Rechts',
    'first_day'             => 'Erster Tag der Woche',
    'weeknumbers'           => 'Wochennummern anzeigen',
    'printenabled'          => 'Drucken erlauben',

// View Settings - Month View

    'month_eventlimit'          => 'Termin-Lmit',
    'month_timeformat'          => 'Zeitformat',
    'month_displayeventtime'    => 'Uhrzeit-Start anzeigen',
    'month_displayeventend'     => 'Uhrzeit-Ende anzeigen',
    'month_columnformat'        => 'Datums-/Zeitformat (Termin)',
    'month_titleformat'         => 'Datums-/Zeitformat (Kalender)',

// Week View

    'week_eventlimit'           => 'Termin-Limit',
    'week_timeformat'           => 'Zeitformat',
    'week_displayeventtime'     => 'Uhrzeit-Start anzeigen',
    'week_displayeventend'      => 'Uhrzeit-Ende anzeigen',
    'week_columnformat'         => 'Datums-/Zeitformat (Termin)',
    'week_titleformat'          => 'Datums-/Zeitformat (Kalender)',

// Day View
    'day_eventlimit'            => 'Termin-Limit',
    'day_timeformat'            => 'Zeitformat',
    'day_displayeventtime'      => 'Uhrzeit-Start anzeigen',
    'day_displayeventend'       => 'Uhrzeit-Ende anzeigen',
    'day_columnformat'          => 'Datums-/Zeitformat (Termin)',
    'day_titleformat'           => 'Datums-/Zeitformat (Kalender)',

// List View
    'list_timeformat'           => 'Zeitformat',
    'list_displayeventtime'     => 'Uhrzeit-Start anzeigen',
    'list_displayeventend'      => 'Uhrzeit-Ende anzeigen',

);

$LANG_configsubgroups['agenda'] = array(
    'sg_main' => 'Haupteinstellungen',
);

$LANG_fs['agenda'] = array(
    'fs_main'       => 'Haupteinstellungen',
    'fs_general'    => 'Allgemeine-Einstellungen',
    'fs_global'     => 'Allgemeine-Ansicht',
    'fs_month'      => 'Monats-Ansicht',
    'fs_week'       => 'Wochen-Ansicht',
    'fs_day'        => 'Tages-Ansicht',
    'fs_list'       => 'Listen-Ansicht',
);

$LANG_configSelect['agenda'] = array(
    0  => array(1=>'Ja', 0 => 'Nein'),
    1  => array(0=>'Seite nicht gefunden (404)', 1=>'Anmeldebildschirm'),
    2  => array(0=>'Nur Admins',1=>'Eingeloggte Benutzer', 2=>'Alle Benutzer'),
    3  => array(0=>'Deaktiviert', 1=>'Nur für Gäste', 2=>'Alle Benutzer'),
    4  => array(0=>'Linke Blöcke', 1=>'Rechte Blöcke', 2=>'Alle Blöcke', 3=>'Keine Blöcke'),
    5  => array('month'=>'Monat','agendaWeek'=>'Woche', 'agendaDay'=>'Tag', 'listMonth'=>'Liste'),
    6  => array('auto'=>'Auto', 'fit'=>'Anpassen'),
    7  => array(
        'title' =>'Titel',
        'none'  => 'Keine',
        'today prev,next'=>'Heute Zurück,Weiter',
        'today prevYear,nextYear prev,next' => 'Heute Jahr-,Jahr+ Zurück,Weiter',
        'month,agendaWeek,agendaDay,listMonth' => 'Aufrufe',
    ),
    8  => array(
        0 =>'Sonntag',
        1 =>'Montag',
        2 =>'Dienstag',
        3 =>'Mittwoch',
        4 =>'Donnerstag',
        5 =>'Freitag',
        6 =>'Samstag'
    ),
);

?>