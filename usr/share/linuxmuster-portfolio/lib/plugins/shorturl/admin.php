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
require_once(DOKU_PLUGIN.'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_shorturl extends DokuWiki_Admin_Plugin {

    /**
     * Access for managers allowed
     */
    function forAdminOnly(){
        return false;
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 140;
    }

    /**
     * return prompt for admin menu
     */
    function getMenuText($language) {
        return $this->getLang('name');
    }

    /**
     * handle user request
     */
    function handle() {
        if($_POST['redirdata']){
            if(io_saveFile($this->getsavedir().'/shorturl.conf',$_POST['redirdata'])){
                msg($this->getLang('saved'),1);
            }
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        global $lang;
        echo $this->locale_xhtml('intro');
        echo '<form action="" method="post" >';
        echo '<input type="hidden" name="do" value="admin" />';
        echo '<input type="hidden" name="page" value="shorturl" />';
        echo '<textarea class="edit" rows="15" cols="80" style="height: 300px" name="redirdata">';
        echo formtext(io_readFile($this->getsavedir().'/shorturl.conf'));
        echo '</textarea><br />';
        echo '<input type="submit" value="'.$lang['btn_save'].'" class="button" />';
        echo '</form>';
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

}
//Setup VIM: ex: et ts=4 enc=utf-8 :
