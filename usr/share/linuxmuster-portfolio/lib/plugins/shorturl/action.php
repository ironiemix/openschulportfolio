<?php
/**
 * ShortURL Plugin
 * based on redirect plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_shorturl extends DokuWiki_Action_Plugin {

    /**
     * register the eventhandlers
     */
    function register(&$controller){
        $controller->register_hook('DOKUWIKI_STARTED',
                                   'AFTER',
                                   $this,
                                   'handle_start',
                                   array());
    }

    /**
     * handle event
     */
    function handle_start(&$event, $param){
        global $ID;
        global $ACT;

        if($ACT != 'show') return;

        $redirects = confToHash($this->getsavedir().'/shorturl.conf');
        if($redirects[$ID]){
            if(preg_match('/^https?:\/\//',$redirects[$ID])){
                send_redirect($redirects[$ID]);
            }else{
                if($this->getConf('showmsg')){
                    msg(sprintf($this->getLang('redirected'),hsc($ID)));
                }
                send_redirect(wl($redirects[$ID] ,'',true));
            }
            exit;
        } else {
            if ($_GET['generateShortURL'] != "" && auth_quickaclcheck($ID) >= AUTH_READ) {
                $this->generateShortURL();
            }
        }
    }

    /**
      * get savedir
      */
    function getsavedir() {
        global $conf;
        if ( $this->getConf('saveconftocachedir') ) {
            return rtrim($conf['savedir'],"/") . "/cache";
        } else {
            return dirname(__FILE__);
        }
    }

    /**
     * generates short page id from current page id
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   string shortid
     * @url      http://www.snippetit.com/2009/04/php-short-url-algorithm-implementation/
     *
     **/
    function generateShortUrl() {
        global $ID;
        $output = array();
        $base32 = array (
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
                'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
                'y', 'z', '0', '1', '2', '3', '4', '5'
                );

        $hex = md5($ID);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;

        for ($i = 0; $i < $subHexLen; $i++) {
            $subHex = substr ($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
            $out = '';

            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }

            $output[] = $out;
        }

        // save redirect to file
        $redirects = confToHash(action_plugin_shorturl::getsavedir().'/shorturl.conf');
        // check for duplicates in database and select alternative shorty when needed
        $shorturl = $output[0];
        for ($j = 0; $j < 6; $j++) {
            if ( $redirects["$shorturl"] && $redirects["$shorturl"] != $ID ) {
                $shorturl = $output[$j+1];
            }
        }
        $redirects["$shorturl"] = $ID;
        $filecontents = "";
        foreach ( $redirects as $short => $long ) {
            $filecontents .= $short . "          " . $long . "\n";
        }
        io_saveFile(action_plugin_shorturl::getsavedir().'/shorturl.conf',$filecontents);

        return $shorturl;

    }
    

}

