<?php
/*
 *
 */

function tpl_portfolio2_topbar() {
    if ( page_exists(tpl_getConf('topmenu_page'))) {
        $topMenu = tpl_getConf('topmenu_page');
    }
    echo "<div class=\"topmenu content\">";
    tpl_flush();
    tpl_include_page($topMenu, 1, 1);
    echo "</div>";
}

?>
