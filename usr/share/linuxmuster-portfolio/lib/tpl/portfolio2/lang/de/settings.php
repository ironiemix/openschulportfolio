<?php

/**
 * Default options for the "portfolio2" DokuWiki template
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

// portfolio title
$lang["sitetitle"]    = "Titel des Portfoliowikis"; //TRUE: use/show user pages
$lang["schoolname"]    = "Untertitel des Portfoliowikis, z.B. der Schulname"; //TRUE: use/show user pages

//styling
$lang["sitetitle_css"]  = "CSS Regeln zur Formatierung des Wikititels";
$lang["schoolname_css"]  = "CSS Regeln zur Formatierunbg des Untertitels";
$lang["barcolor_css"]  = "CSS Regeln zur Formatierung der akzetuierten Elemente (Topbar, Sidebarheadings)";
$lang["pageid_css"]  = "CSS Regeln zur Formatierung der PageID (Kleiner Seitenname oben rechts)";
// ns search
$lang["searchnamespaces"] = "Namensräume, die zur Suche ausgewählt werden können. Schreibweise: namensraum>Anzuzeigende Bezeichnung. Durch Kommata getrennt.";

//user pages
$lang["userpage"]    = "Benutzerseiten verwenden?";
$lang["userpage_ns"] = "Namensraum, in dem die Benutzerseiten angelegt werden.";
//show infomail button?
$lang["infomail"]    = "Infomailfunktion verwenden?";

//discussion pages
$lang["discuss"]    = "Diskussionsseiten verwenden?"; //TRUE: use/show discussion pages
$lang["discuss_ns"] = "Namensraum, in dem die Diskussionsseiten angelegt werden."; //namespace to use for discussion page storage

//topmenu
$lang["topmenu"]          = "Topmenü anzeigen?";
$lang["topmenu_page"] = "Seite, die als Topmenü verwendet wird.";

//default sidebar
$lang["sidebar"]          = "Sidebar anzeigen?"; //TRUE: use/show navigation
$lang["sidebar_page"] = "Seite, die als Sidebar verwendet wird."; //page/article used to store the navigation

//exportbox ("print/export")
$lang["exportbox"]          = "Exportfunktionen in der Sidebar anzeigen?"; //TRUE: use/show exportbox

//toolbox
$lang["toolbox"]          = "Werkzeugfunktionen in der Sidebar anzeigen?"; //TRUE: use/show toolbox

// Winmuster
$lang["winML_logout"]   = ""; //Logout link according to WinMl SSO?
$lang["winML_logout_argument"] = ""; // String to attach to url for logging out
$lang["winML_hide_loginlogout"] = ""; // Hide login/logout functions
$lang["winML_hide_loginlogout_subnet"] = ""; // wehn hiding, for wicht subnets?

