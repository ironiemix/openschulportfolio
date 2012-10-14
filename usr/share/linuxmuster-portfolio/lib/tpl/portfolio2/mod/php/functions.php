<?php
/*
 *  Functions for OSP modifications to dokuwikis default toolbars
 */

/*
 * Prints topbar with configured sitenotice page
 */
function tpl_portfolio2_topbar() {

    global $lang;
    global $conf;
    global $ACT;
    global $ID;

    if (tpl_getConf('closedwiki') &&
        !isset($_SERVER["REMOTE_USER"])) {
            echo "<br />";
            return;
    }
    if(!isset($_SERVER['REMOTE_USER'])) {
            return;
    }
    if (!empty($conf["useacl"]) && auth_quickaclcheck($sitenotice_page_id) < AUTH_READ) {
        return;
    }


    // topbar
    print "<div id=\"topbar\">";
    print "<ul class=\"topbar-left\">";
    print "<li><a href=\"".wl($ID)."\" title=\"". $lang['readpage_tooltip']. "\">".$lang['readpage']."</a></li>";
    if(isset($_SERVER['REMOTE_USER']) && tpl_getConf('infomail')) {
        $lang['btn_infomail'] = 'Infomail';
        echo "<li class=\"infomail\">" . html_btn('infomail',$ID,null,array('do' => 'infomail', 'id' => $ID)) . "</li>";
    }
    print "</ul>";
    print "<ul class=\"topbar-right\">";
    tpl_action('edit',      1, 'li', 0, '', '');
    tpl_action('revisions', 1, 'li', 0, '', '');
    print "</ul>";
    print "</div>";

    if ( $ACT == "media" ) {
        return;
    }

    // include topmenu page
    if ( page_exists(tpl_getConf('topmenu_page'))) {
        $topMenu = tpl_getConf('topmenu_page');
    }
    echo "<div class=\"topmenu content\">";
    echo "<div class=\"include_edit\">";
    tpl_flush();
    tpl_include_page($topMenu, 1, 1);

    if (auth_quickaclcheck($sitenotice_page_id) > AUTH_READ) {
        $link = wl($topMenu, array("do"=>"edit"));
        echo "<a href=\"". $link . "\" class=\"editlink\">". $lang["edit_include"] . "</a>\n";
    }

    echo "</div>";
    echo "</div>";

    // breadcrumbs
    if($conf['breadcrumbs'] || $conf['youarehere']) {
        print "<div class=\"breadcrumbs\">";
            if($conf['youarehere']) {
                print "<div class=\"youarehere\">";
                tpl_youarehere();
                print "</div>";
            }
        print "</div>";
    }

    // errors and messages
    html_msgarea();
}

function tpl_portfolio2_css() {

    $accentcolor = tpl_getConf('accentcolor');
    echo "<style type=\"text/css\">\n";
    echo "<!--\n";
    echo "#dokuwiki__header h1 a span#sitetitle { ". tpl_getConf('sitetitle_css') ."}\n";
    echo "#dokuwiki__header h1 a span#schoolname { ". tpl_getConf('schoolname_css') ."}\n";
    echo "div#topbar ul li a { ". tpl_getConf('barcolor_css') ."}\n";
    echo "div#topbar form input.button { ". tpl_getConf('barcolor_css') ."}\n";
    echo "div#topbar { ". tpl_getConf('barcolor_css') ."}\n";
    echo "#dokuwiki__aside h1 {  ". tpl_getConf('barcolor_css') ."}\n";
    echo ".dokuwiki div.pageId span  { ". tpl_getConf('pageid_css') ."}\n";
    echo "\n-->\n";
    echo "</style>";
}

function tpl_portfolio2_login() {


$showloginlogout = true;
// determine if the request comes from outside or inside the local net
    if (tpl_getConf('winML_hide_loginlogout')) {
        $winml_subnet = preg_replace("/(.*?)x.*/i", "$1" ,tpl_getConf('winML_hide_loginlogout_subnet'));
        $remote_subnet = substr($_SERVER['REMOTE_ADDR'],0,strlen($winml_subnet));
        if ( $winml_subnet === $remote_subnet ) {
            $showloginlogout = false;
        }
    }

    if ( $showloginlogout ) {
        if (tpl_getConf('winML_logout') ) {
            if (isset($_SERVER["REMOTE_USER"])) {
                $btn_text="Abmelden";
            } else {
                $btn_text="Anmelden";
            }
            list($arg, $value) = split("=", tpl_getConf(winML_logout_argument),2);
            echo  "<li><a class=\"action logout\" href=\"".wl("", array($arg => $value))."\" rel=\"nofollow\">".$btn_text."</a></li>\n";
            } else {
                tpl_action('login', 1, 'li');
            }
    }
}

function tpl_portfolio2_boxes($sidebar) {

    //get boxes config
    include DOKU_TPLINC."/conf/boxes.php"; //default

    //include siedbar page
    echo "<div class= \"include_edit\">";
    tpl_include_page($sidebar, 1, 1);
    if (auth_quickaclcheck($sidebar) > AUTH_READ) {
        $link = wl($sidebar, array("do"=>"edit"));
        echo "<a href=\"". $link . "\" class=\"editlink\">". $lang["edit_include"] . "</a>\n";
    }
    echo "</div>";
    // checking argument
    if (empty($boxes) || !is_array($boxes)) {
        return false;
    }

    //array to store the created boxes into
    $rendered_boxes = array();

    //handle the box data
    foreach($boxes as $div_id => $contents){
        //basic check
        if (empty($contents) ||
            !is_array($contents) ||
            !isset($contents["xhtml"])){
            continue; //ignore invalid stuff and go on
        }
        $interim  = "  <div id=\"".hsc($div_id)."\">\n";
        if (isset($contents["headline"])
            && $contents["headline"] !== ""){
            $interim .= "<h1>".hsc($contents["headline"])."</h1>\n";
        }
        $interim .= "      <div class=\"dokuwiki\">\n" //dokuwiki CSS class needed cause we might have to show rendered page content
                   .$contents["xhtml"]."\n"
                   ."      </div>\n"
                   ."    </div>\n";
        //store it
        $rendered_boxes[] = $interim;
 }
    //show everything created
    if (!empty($rendered_boxes)){
        echo  "\n";
        foreach ($rendered_boxes as $box){
            echo $box;
        }
        echo  "\n";
    }
    echo "<div id=\"ospversionpic\"></div>\n<div id=\"ospversion\">(oSP ";
    if (file_exists(DOKU_TPLINC."/ospversion.php")){
         include DOKU_TPLINC."/ospversion.php";
    }
    echo ")</div>";


    return true;
}


?>
