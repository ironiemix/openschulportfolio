<?
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(dirname(__FILE__) . "/syntax.php");

/**
  * if a short id exists in db: get it
  *
  * @author   Frank Schiebel <frank@linuxmuster.net>
  * @param    none
  * @return   string regular id
  *
  */

function plugin_shorturl_printlink () {
    global $conf;

    $psyntax = new syntax_plugin_shorturl();

    if ( $psyntax->getConf('saveconftocachedir') ) {
        $savedir = rtrim($conf['savedir'],"/") . "/cache";
    } else {
        $savedir =  dirname(__FILE__);
    }

    $redirects = confToHash($savedir.'/shorturl.conf');
    $pageID = $psyntax->idToTpl();

    if (in_array($pageID, $redirects)) {
        $shorturl = array_search($pageID, $redirects);
        $linktext = $psyntax->getLang('shortlinktext');
        return '<a href="' . wl($shorturl, "", true) . '"> ' . $linktext . '</a>' ;
    } else {
        $linktext = $psyntax->getLang('generateshortlink');
        return'<a href="' . wl($pageID, array(generateShortURL=>yes), true) . '"> ' . $linktext . '</a>' ;
    }
}
