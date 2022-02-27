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
*  Copyright (C) 2016-2019 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

$LANG_AC = array (
    'plugin'            => 'agenda',
    'plugin_name'       => 'Agenda',
    'plugin_admin'		=> 'Správce agendy',
    'event_list'        => 'Události agendy',
    'admin_help'        => 'Vytvořit, upravit a odstranit kategorie agendy, které mohou být použity k zvýraznění událostí v kalendáři agendy.<br><br>Správa událostí se provádí v prostředí kalendáře agendy.',
    'access_denied'     => 'Přístup odepřen',
    'access_denied_msg' => 'Nemáte oprávnění k zobrazení této stránky. Vaše uživatelské jméno a IP adresa byly zaznamenány.',
    'category_list'     => 'Kategorie',
    'edit_category'     => 'Edituj kategorii',

    'event_list'        => 'Události',
    'admin'		        => 'Správce agendy',
    'cancel'			=> 'Zrušit',
    'delete'			=> 'Vymazat',
    'save'				=> 'Uložit',
    'create'            => 'Nová událost',
    'category_new'      => 'Nová kategorie',
    'header'            => 'Agenda',
    'category_name'     => 'Název kategorie',
    'category_desc'     => 'Popis kategorie',
    'no_categories'     => 'Zatím bez kategorie',
    'bgcolor'           => 'Barva pozadí',
    'fgcolor'           => 'Barva textu',
    'color_preview'     => 'Náhled barev',
    'submissions'       => 'Vložení do Agendy',
    'edit'              => 'Upravit',
    'title'             => 'Název',
    'owner'             => 'Vlastník',
    'start_date'        => 'Datum zahájení',
    'end_date'          => 'Datum ukončení',
    'allday'            => 'Po celý den',
    'no_events'         => 'Nebyly nalezeny žádné události',
    'delete_checked'    => 'Vymaž vybrané',
    'delete_confirm'    => 'Opravdu chcete odstranit vybranou kategorii?',
    'delete_confirm_event'    => 'Jste si jisti, že chcete odstranit vybrané události?',
    'published'         => 'Publikováno',
    'choose'            => 'Zvolit',
    'more'              => 'více',
    'less'              => 'méně',
    'sample_category'   => 'Kategorie vzorová',
    'submission_mod_approved' => 'Příspěvek události schválen',
    'series'            => 'Série',
    'event_exception'   => 'Tato událost je součástí série, která ale byla upravena',
    'event_list'        => 'Seznam událostí',
// block strings
    'site_events'       => 'Události webu',
    'no_upcoming'       => 'Žádné nadcházející události',
    'today'             => 'Dnes',
// event forms
    'event_title'       => 'Název události',
    'location'          => 'Místo konání události',
    'event_start'       => 'Začátek události',
    'start_date'        => 'Datum zahájení',
    'start_time'        => 'Čas zahájení',
    'all_day_event'     => 'Celodenní událost',
    'event_end'         => 'Konec události',
    'end_date'          => 'Datum ukončení',
    'end_time'          => 'Čas ukončení',
    'repeats'           => 'Opakování',
    'repeat_options'    => 'Možnosti opakování',
    'daily'             => 'Denně',
    'weekly'            => 'Týdně',
    'biweekly'          => 'Každé 2 týdny',
    'monthly'           => 'Měsíčně',
    'yearly'            => 'Ročně',
    'description'       => 'Popis',
    'category'          => 'Kategorie',
    'no_category'       => 'Není vybrána žádná kategorie',
    'no_category_desc'  => 'Výchozí kategorie pokud není vybrána jiná kategorie',
    'edit_single_or_series' => 'Upravit událost nebo sérii',
    'what_to_edit'       => 'Toto je jediná událost ve sérii. Co chcete upravit?',
    'just_this_one'     => 'Jen tuto',
    'entire_series'     => 'Celá série',
    'series_delete_msg' => 'Serie událostí byla smazána',
    'event_saved_msg'   => 'Událost byla uložena',

    // rrule

    'repeat'            => 'Opakovat',
    'none'              => 'Bez opakování',
    'hourly'            => 'Každou hodinu',
    'daily'             => 'Každý den',
    'weekly'            => 'Týdně',
    'monthly'           => 'Měsíčně',
    'yearly'            => 'Ročně',
    'every'             => 'jednou za',
    'hours'             => 'hodin(a, y, u)',
    'days'              => 'dny(dnů)',
    'weeks'             => 'týden (týdny)',
    'months'            => 'měsíc(ů)',
// LANG_WEEK already has these - just use them...
    'weekly_help'       => 'Vyberte všechny dny v týdnu pro událost',
    'on_day'            => 'v den',
    'on_the'            => 'na',
    'first'             => 'První',
    'second'            => 'Druhý',
    'third'             => 'Třetí',
    'forth'             => 'Čtvrtý',
    'fifth'             => 'Pátý',
    'last'              => 'Poslední',
//LANG WEEK has weekday names too
    'day'               => 'Den',
    'weekday'           => 'Pracovní dny',
    'weekend'           => 'Víkend',
// LANG_MONTH has month names and abbreviations
    'after'             => 'Po',
    'on_date'           => 'K datu',
    'occurrences'       => 'Výskyt',
    'end_after_date'    => 'Konec po datu',
    'of'                => 'z',
    'end'               => 'Konec',
    'exception_warning' => '<strong>Tato série událostí obsahuje výjimky.</strong><br> výjimky budou odebrány a celá řada bude znovu vytvořena, pokud upravíte pravidla opakování.',
    'edit_recurrence'   => 'Nastavit opakování',
    'exception'         => 'Výjimka',
    'ip_address'        => 'IP adresa',
);

// JavaScript specific translations
$LANG_AC_JS = array(
    'agenda_calendar'   => 'Kalendář Agendy',
    'add_event'         => 'Přidat událost',
    'edit_event'        => 'Upravit událost',
    'edit_event_series' => 'Upravit série událostí',
    'save_event'        => 'Uložit událost',
    'delete_event'      => 'Odstranit událost',
    'delete_series'     => 'Odstranit sérii',
    'edit'              => 'Upravit',
    'edit_series'       => 'Upravit sérii',
    'close'             => 'Zavřít',
    'delete_event_confirm' => 'Jste si jisti, že chcete odstranit tuto událost?',
    'delete_series_confirm' => 'Jste si jisti, že chcete SMAZAT CELOU SÉRII UDÁLOSTÍ?',
    'cancel'            => 'Zrušit',
    'when'              => 'Kdy',
    'location'          => 'Umístění',
    'details'           => 'Detaily',
    'err_select_option' => 'Vyberte prosím možnost',
    'err_enter_title'   => 'Zadejte název události',
    'err_end_before_start' => 'Koncové datum musí být pozdější než počáteční datum',
    'err_initialize'    => 'Chyba při inicializaci pluginu Agenda',
    'event_queued'      => 'Děkujeme za váš příspěvek. Příspěvek byl umístěn ve frontě k přezkoumání a schválení.',
    'exception_event'   => 'Vyjímečná událost',
    'print'             => 'tisk',
    'spam'              => 'Zjištěná událost je spam',
);

$LANG_AC_ERRORS = array(
    'invalid_title'     => 'Musíte zadat název události',
);

$LANG_configsections['agenda'] = array(
    'label' => 'Agenda',
    'title' => 'Nastavení pluginu Agenda',
);

$LANG_confignames['agenda'] = array(
// General Settings
    'allow_anonymous_view'  => 'Povolit nepřihlášeným uživatelům zobrazit kalendář',
    'security_exit'         => 'Bezpečné odhlášení',
    'allow_entry'           => 'Kdo může odeslat události',
    'submission_queue'      => 'Fronta příspěvkú',
    'displayblocks'         => 'Zobrazit bloky',
    'showupcomingevents'    => 'Zobrazit blok nadcházejících událostí',
    'upcomingeventsrange'   => 'Dny zahrnuté do bloku nadcházejících událostí',
    'maintenance_check_freq' => 'Jak často smazat staré události ( ve dnech)',
    'maintenance_max_age'   => 'Jak dlouho ponechat staré události (roky)',

// Global Calendar Settings

    'defaultview'           => 'Výchozí zobrazení',
    'autoheight'            => 'Automatická výška',
    'header_left'           => 'Záhlaví vlevo',
    'header_center'         => 'Záhlaví na střed',
    'header_right'          => 'Záhlaví vpravo',
    'first_day'             => 'První den týdne',
    'weeknumbers'           => 'Zobrazit pořadí týdne',
    'printenabled'          => 'Povolit tisk',

// View Settings - Month View

    'month_eventlimit'          => 'Limit události',
    'month_timeformat'          => 'Formát času',
    'month_displayeventtime'    => 'Zobrazit čas zahájení',
    'month_displayeventend'     => 'Zobrazit čas ukončení',
    'month_columnformat'        => 'Formát data a času sloupce',
    'month_titleformat'         => 'Název záhlaví Datum / formát času',

// Week View

    'week_eventlimit'           => 'Limit události',
    'week_timeformat'           => 'Formát času',
    'week_displayeventtime'     => 'Zobrazit počáteční čas',
    'week_displayeventend'      => 'Zobrazit čas ukončení',
    'week_columnformat'         => 'Formát data a času sloupce týdne',
    'week_titleformat'          => 'Název záhlaví Datum / formát času',

// Day View
    'day_eventlimit'            => 'Limit události',
    'day_timeformat'            => 'Formát času',
    'day_displayeventtime'      => 'Zobrazit čas zahájení',
    'day_displayeventend'       => 'Zobrazit čas ukončení',
    'day_columnformat'          => 'Formát data a času sloupce',
    'day_titleformat'           => 'Název záhlaví Datum / formát času',

// List View
    'list_timeformat'           => 'Formát času',
    'list_displayeventtime'     => 'Zobrazit čas zahájení',
    'list_displayeventend'      => 'Zobrazit čas ukončení',

);

$LANG_configsubgroups['agenda'] = array(
    'sg_main' => 'Hlavní nastavení',
);

$LANG_fs['agenda'] = array(
    'fs_main'       => 'Hlavní nastavení',
    'fs_general'    => 'Obecná nastavení',
    'fs_global'     => 'Nastavení globálního zobrazení',
    'fs_month'      => 'Nastavení zobrazení měsíce',
    'fs_week'       => 'Nastavení zobrazení týdne',
    'fs_day'        => 'Nastavení zobrazení dne',
    'fs_list'       => 'Nastavení zobrazení seznamu',
);

$LANG_configSelect['agenda'] = array(
    0  => array(1=>'Ano', 0 => 'Ne'),
    1  => array(0=>'Stránka nebyla nalezena (404)', 1=>'Obrazovka přihlášení'),
    2  => array(0=>'Pouze administrátor',1=>'Přihlášení uživatelé', 2=>'Všichni uživatelé'),
    3  => array(0=>'Vypnuto', 1=>'Pouze pro anonymní hosty', 2=>'Všichni uživatelé'),
    4  => array(0=>'Bloky vlevo', 1=>'Bloky vpravo', 2=>'Všechny bloky', 3=>'Bez bloků'),
    5  => array('month'=>'Měsíc','agendaWeek'=>'Týden', 'agendaDay'=>'Den', 'listMonth'=>'Seznam'),
    6  => array('auto'=>'Automaticky', 'fit'=>'Přizpůsobit'),
    7  => array(
        'title' =>'Název',
        'none'  => 'Žádné',
        'today prev,next'=>'Včerejšek, předchozí',
        'today prevYear,nextYear prev,next' => 'Předešlý rok, Další',
        'month,agendaWeek,agendaDay,listMonth' => 'Zobrazení',
    ),
    8  => array(
        0 =>'Neděle',
        1 =>'Pondělí',
        2 =>'Úterý',
        3 =>'Středa',
        4 =>'Čtvrtek',
        5 =>'Pátek',
        6 =>'Sobota'
    ),
);

?>