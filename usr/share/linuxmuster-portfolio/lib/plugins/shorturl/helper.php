<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");


class helper_plugin_shorturl extends DokuWiki_Plugin {

    var $configtocache = '';
    var $savedir       = '';

    /**
     * Constructor gets default preferences and language strings
     */
    function helper_plugin_shorturl() {
        global $ID, $conf;

        $this->configtocache = $this->getConf('saveconftocachedir');

        if ( $this->configtocache ) {
            $this->savedir = rtrim($conf['savedir'],"/") . "/cache";
        } else {
            $this->savedir = dirname(__FILE__);
        }

    }


    function getInfo() {
        return array(

                );
    }

    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'autoGenerateShortUrl',
                'desc'   => 'returns the short url if exists, otherwise create the short url',
                'return' => array('shortID' => 'string'),
                );
        $result[] = array(
                'name'   => 'shorturlPrintLink',
                'desc'   => 'returns a link to the short url if it exists, otherwise a link to create the short url',
                'return' => array('html' => 'string'),
                );
        return $result;
    }

   /**
    * returns shortID for pageID
    * creates and saves forwarding to shortID if not
    */
    function autoGenerateShortUrl ($pageID) {
        $redirects = confToHash($this->savedir.'/shorturl.conf');
        if (in_array($pageID, $redirects)) {
            $shortID = array_search($pageID, $redirects);
        } else {
            $shortID = $this->generateShortUrl($pageID);
        }
        return $shortID;
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
    function generateShortUrl($pageID) {
        $output = array();
        $base32 = array (
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
                'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
                'y', 'z', '0', '1', '2', '3', '4', '5'
                );

        $hex = md5($pageID);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;

        for ($i = 0; $i < $subHexLen; $i++) {
            $subHex = substr ($hex, $i * 8, 8);
            $int = hexdec('0x'.$subHex);
            $out = '';

            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }

            $output[] = $out;
        }

        // save redirect to file
        $redirects = confToHash($this->savedir.'/shorturl.conf');
        // check for duplicates in database and select alternative shorty when needed
        $shorturl = $output[0];
        for ($j = 0; $j < 6; $j++) {
            if ( $redirects["$shorturl"] && $redirects["$shorturl"] != $pageID ) {
                $shorturl = $output[$j+1];
            }
        }
        $redirects["$shorturl"] = $pageID;
        $filecontents = "";
        foreach ( $redirects as $short => $long ) {
            $filecontents .= $short . "          " . $long . "\n";
        }
        io_saveFile($this->savedir.'/shorturl.conf',$filecontents);

        return $shorturl;

    }

    /**
     * if a short id exists in db: get it
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   string regular id
     *
     */
    function shorturlPrintLink ($pageID) {
        
        if (file_exists($this->savedir.'/shorturl.conf') ) {
            $redirects = confToHash($this->savedir.'/shorturl.conf');
        } else {
            $redirects = array();
        }

        if (in_array($pageID, $redirects)) {
            $shortID = array_search($pageID, $redirects);
            $linktext = $this->getLang('shortlinktext');
            return '<a href="' . wl($shortID, "", true) . '"> ' . $linktext . '</a>' ;
        } else {
            $linktext = $this->getLang('generateshortlink');
            return'<a href="' . wl($pageID, array(generateShortURL=>yes), true) . '"> ' . $linktext . '</a>' ;
        }
    }


}
// vim:ts=4:sw=4:et:enc=utf-8:
