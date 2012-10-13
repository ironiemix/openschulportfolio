<?php
/**
 * Types of the different option values for the "portfolio2" DokuWiki template
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}
// portfolio title
$meta["sitetitle"]    = array("string"); 
$meta["schoolname"]    = array("string"); 

$meta["accentcolor"]    = array("string"); 


//user pages
$meta["userpage"]    = array("onoff");
$meta["userpage_ns"] = array("string", "_pattern" => "/^:.{1,}:$/");

//infomail button?
$meta["infomail"]    = array("onoff");

//discussion pages
$meta["discuss"]    = array("onoff");
$meta["discuss_ns"] = array("string", "_pattern" => "/^:.{1,}:$/");

//topmenu
$meta["topmenu"]          = array("onoff");
$meta["topmenu_page"] = array("string");

//navigation
$meta["sidebar"]          = array("onoff");
$meta["sidebar_page"] = array("string");

//exportbox ("print/export")
$meta["exportbox"]          = array("onoff");

//toolbox
$meta["toolbox"]          = array("onoff");

$meta["winML_logout"]          = array("onoff"); 
$meta["winML_logout_argument"] = array("string");
$meta["winML_hide_loginlogout"] = array("onoff");
$meta["winML_hide_loginlogout_subnet"] = array("string");
$meta["cite_author"]           = array("string");
$meta["loaduserjs"]            = array("onoff");
$meta["closedwiki"]            = array("onoff");

