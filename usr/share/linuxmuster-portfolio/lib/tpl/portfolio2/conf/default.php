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
$conf["exportbox_default"]  = true; //TRUE: use default exportbox (if exportbox is enabled at all)
$conf["exportbox_location"] = ":wiki:exportbox"; //page/article used to store a custom exportbox

//toolbox
$conf["toolbox"]          = true; //TRUE: use/show toolbox
$conf["toolbox_default"]  = true; //TRUE: use default toolbox (if toolbox is enabled at all)
$conf["toolbox_location"] = ":wiki:toolbox"; //page/article used to store a custom toolbox

//custom copyright notice
$conf["copyright"]          = true; //TRUE: use/show copyright notice
$conf["copyright_default"]  = true; //TRUE: use default copyright notice (if copyright notice is enabled at all)
$conf["copyright_location"] = ":wiki:copyright"; //page/article used to store a custom copyright notice

//donation link/button
$conf["donate"]          = false; //TRUE: use/show donation link/button
$conf["donate_default"]  = true; //TRUE: use default donation link/button (if donation link is enabled at all)
$conf["donate_url"]      = "http://andreas-haerter.com/donate/vector/paypal"; //custom donation URL instead of the default one

//TOC
$conf["toc_position"] = "article"; //article: show TOC embedded within the article; "sidebar": show TOC near the navigation, left column

//other stuff
$conf["mediamanager_embedded"] =  false; //TRUE: Show media manager surrounded by the common navigation/tabs and stuff
$conf["breadcrumbs_position"]  = "bottom"; //position of breadcrumbs navigation ("top" or "bottom")
$conf["youarehere_position"]   = "top"; //position of "you are here" navigation ("top" or "bottom")
// Winmuster
$conf["winML_logout"]   = false; //Logout link according to WinMl SSO?
$conf["winML_logout_argument"] = "CMD=logoff"; // String to attach to url for logging out
$conf["winML_hide_loginlogout"] = false; // Hide login/logout functions
$conf["winML_hide_loginlogout_subnet"] = "10.1.x.x"; // wehn hiding, for wicht subnets?
if (!empty($_SERVER["HTTP_HOST"])){
  $conf["cite_author"] = "Contributors of ".hsc($_SERVER["HTTP_HOST"]); //name to use for the author on the citation page (hostname included)
} else {
  $conf["cite_author"] = "Anonymous Contributors"; //name to use for the author on the citation page
}
$conf["loaduserjs"]            = false; //TRUE: vector/user/user.js will be loaded

