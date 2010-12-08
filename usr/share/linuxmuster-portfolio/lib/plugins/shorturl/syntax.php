<?php
/**
 * DokuWiki Plugin shorturl (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frank Schiebel <frank@linuxmuster.net>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'syntax.php');
require_once(dirname(__FILE__) . "/action.php");

class syntax_plugin_shorturl extends DokuWiki_Syntax_Plugin {

    function syntax_plugin_shorturl() { }

    function getType() {
        return 'substition';
    }

    function getPType() {
        return 'block';
    }

    function getSort() {
        return 302;
    }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\~\~SHORTURL\~\~',$mode,'plugin_shorturl');
    }

    function handle($match, $state, $pos, &$handler){

        $data['todo'] = "print";
        return $data;
    }

    function render($mode, &$renderer, $data) {
        global $ID;

        if($mode != 'xhtml') return false;

        if ( $data['todo'] == "print" ) {
            $shorturl =& plugin_load('helper', 'shorturl');
            if ($shorturl) {
                $shortID = $shorturl->autoGenerateShortUrl($ID);
                $renderer->doc .= "<a href=". wl($shortID, "", true) ." class=\"shortlinkinpage\" >" . $this->getLang('shortlinktext')  . "</a>\n";
            }
        }

        return true;
    }
}

// vim:ts=4:sw=4:et:enc=utf-8:
