<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");


class helper_plugin_headercss extends DokuWiki_Plugin {

    var $configtocache = '';
    var $savedir       = '';

    /**
     * Constructor gets default preferences and language strings
     */
    function helper_plugin_headercss() {
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
                'name'   => 'outputCSS',
                'desc'   => '',
                'return' => array('shortID' => 'string'),
                );
        return $result;
    }

   /**
    * returns shortID for pageID
    * creates and saves forwarding to shortID if not
    */

    function outputCSS () {

        if (! file_exists($this->savedir.'/headercss.css')) {
            if (file_exists(dirname(__FILE__).'/headercss.css')) {
                copy(dirname(__FILE__).'/headercss.css', $this->savedir.'/headercss.css');
            }
        }
        if (file_exists($this->savedir.'/headercss.css')) {
            $css = $this->css_compress(io_readFile($this->savedir.'/headercss.css'));
            print "\n";
            print "<style type=\"text/css\">\n";
            print "<!--\n";
            print $css;
            print"\n-->\n";
            print"</style>\n";
        }
    }

 /**
  * Very simple CSS optimizer
  *
  * @author Andreas Gohr <andi@splitbrain.org>
  */
  function css_compress($css){
      //strip comments through a callback
      $css = preg_replace_callback('#(/\*)(.*?)(\*/)#s',array( &$this, 'css_comment_cb'),$css);

      //strip (incorrect but common) one line comments
      $css = preg_replace('/(?<!:)\/\/.*$/m','',$css);

      // strip whitespaces
      $css = preg_replace('![\r\n\t ]+!',' ',$css);
      $css = preg_replace('/ ?([:;,{}\/]) ?/','\\1',$css);

      // shorten colors
      $css = preg_replace("/#([0-9a-fA-F]{1})\\1([0-9a-fA-F]{1})\\2([0-9a-fA-F]{1})\\3/", "#\\1\\2\\3",$css);

      return $css;
  }

  /**
  * Callback for css_compress()
  *
  * Keeps short comments (< 5 chars) to maintain typical browser hacks
  *
  * @author Andreas Gohr <andi@splitbrain.org>
  */
function css_comment_cb($matches){
    if(strlen($matches[2]) > 4) return '';
        return $matches[0];
    }

}
// vim:ts=4:sw=4:et:enc=utf-8:
