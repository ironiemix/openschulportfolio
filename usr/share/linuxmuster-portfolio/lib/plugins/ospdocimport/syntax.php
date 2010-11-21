<?php
/**
 * Untis-plugin: reads teacher substitution-tables exported by
 * GP-Untis and displays the tables in a nice way in DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/confutils.php');
require_once(DOKU_INC.'inc/pageutils.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_openschulportfolio extends DokuWiki_Syntax_Plugin {

    var $mediadir;
    function syntax_plugin_openschulportfolio() {

        global $conf;

    }

    /**
     * return some info about this plugin
     */
    function getInfo() {
        return array(
            'author' => 'Frank Schiebel',
            'email'  => 'frank@linuxmuster.net',
            'date'   => '2010-05-02',
            'name'   => 'OSP Plugin',
            'desc'   => '',
            'url'    => 'http://www.openschulportfolio.de/',
        );
    }

    function getType(){ return 'substition'; }
    function getPType(){ return 'block'; }
    function getSort(){ return 222; }

    function connectTo($mode) {
    $this->Lexer->addSpecialPattern('\{\{xxxuntis>.+?\}\}',$mode,'plugin_untis');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {

        $match = substr($match, 2, -2);
        list($type, $match) = split('>', $match, 2);
        list($days, $flags) = split('&', $match, 2);

        return array($type, $days, $flags);

    }
    
    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        global $conf;
        // disable caching
        $renderer->info['cache'] = false;
          
    }
    


}
