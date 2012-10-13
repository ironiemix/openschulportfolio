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

    if (tpl_getConf('closedwiki') &&
        !isset($_SERVER["REMOTE_USER"])) {
            echo "<br />";
            return;
    }
    if(!isset($_SERVER['REMOTE_USER'])) {
            return;
    }

    if ( $ACT == "media" ) {
        return;
    }
    print "<div id=\"topbar\">";
    print "<ul class=\"topbar-left\">";
    print "</ul>";
    print "<ul class=\"topbar-right\">";
    tpl_action('edit',      1, 'li', 0, '', '');
    tpl_action('revisions', 1, 'li', 0, '', '');

    if(isset($_SERVER['REMOTE_USER'])) {
        $lang['btn_infomail'] = 'Infomail';
        echo "<li class=\"infomail\">" . html_btn('infomail',$ID,null,array('do' => 'infomail', 'id' => $ID)) . "</li>";
    }

    print "</ul>";
    print "</div>";

    if ( page_exists(tpl_getConf('topmenu_page'))) {
        $topMenu = tpl_getConf('topmenu_page');
    }
    echo "<div class=\"topmenu content\">";
    tpl_flush();
    tpl_include_page($topMenu, 1, 1);
    echo "</div>";

    if($conf['breadcrumbs'] || $conf['youarehere']) {
        print "<div class=\"breadcrumbs\">";
            if($conf['youarehere']) {
                print "<div class=\"youarehere\">";
                tpl_youarehere();
                print "</div>";
            }
        print "</div>";
    }
    html_msgarea();
}


function tpl_portfolio2_css() {

    $accentcolor = tpl_getConf('accentcolor');
    echo "<style type=\"text/css\">";
    echo "<!--";
    echo "div#topbar {background-color:$accentcolor;}";
    echo "#dokuwiki__aside h1 {background-color:$accentcolor;}";
    echo ".dokuwiki div.pageId span {background-color:$accentcolor;}";
    echo "-->";
    echo "</style>";
}
function tpl_portfolio2_tools() {

}

?>
