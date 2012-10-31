<?php

/**
 * Default options for the "portfolio2" DokuWiki template
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

// portfolio title
$conf["sitetitle"]    = "Schulportfolio"; //TRUE: use/show user pages
$conf["schoolname"]    = "Schulname hier eintragen"; //TRUE: use/show user pages

//styling
$conf["sitetitle_css"]  = "color:#999; text-shadow: 2px 2px 0 #FFFFFF; font-size: 1.5em; font-weight: bold;";
$conf["schoolname_css"]  = "color: #333; font-size: 1em;";
$conf["barcolor_css"]  = "background-color: #ffe4ae; color: #555; text-shadow: 1px 1px 0 #FFFFFF;";
$conf["pageid_css"]  = "background-color: #ffe4ae; color: #555;";

//ns search
$conf["searchnamespaces"] = ">Alles,portfolio>Portfolio,hilfe>Hilfe";


//user pages
$conf["userpage"]    = false; //TRUE: use/show user pages
$conf["userpage_ns"] = ":wiki:userpages:"; //namespace to use for user page storage
//show infomail button?
$conf["infomail"]    = true;

//discussion pages
$conf["discuss"]    = false; //TRUE: use/show discussion pages
$conf["discuss_ns"] = ":wiki:discussion:"; //namespace to use for discussion page storage

//topmenu
$conf["topmenu"]          = true; //TRUE: use/show sitenotice
$conf["topmenu_page"] = ":wiki:topmenu"; //page/article used to store the sitenotice

//default sidebar
$conf["sidebar"]          = true; //TRUE: use/show navigation
$conf["sidebar_page"] = ":wiki:sidebar"; //page/article used to store the navigation

//exportbox ("print/export")
$conf["exportbox"]          = true; //TRUE: use/show exportbox

//toolbox
$conf["toolbox"]          = true; //TRUE: use/show toolbox

// Winmuster
$conf["winML_logout"]   = false; //Logout link according to WinMl SSO?
$conf["winML_logout_argument"] = "CMD=logoff"; // String to attach to url for logging out
$conf["winML_hide_loginlogout"] = false; // Hide login/logout functions
$conf["winML_hide_loginlogout_subnet"] = "10.1.x.x"; // wehn hiding, for wicht subnets?

