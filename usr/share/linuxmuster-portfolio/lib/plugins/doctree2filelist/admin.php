<?php
/**
 * Imports filetrees into DokuWikis media directory
 * and creating a wiki pagetree with filelists linking to the
 * imported documents
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_INCLUDE')) define('DOKU_INCLUDE',DOKU_INC.'inc/');
require_once(DOKU_PLUGIN . 'admin.php');
require_once(DOKU_INCLUDE . 'io.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_doctree2filelist extends DokuWiki_Admin_Plugin
{
    /**
     * Constructor
     */
    function admin_plugin_doctree2filelist()
    {
        $this->setupLocale();
    }

    /**
     * return some info
     */
    function getInfo()
    {
        return array(
            'author' => 'Frank Schiebel',
            'email'  => 'frank@linuxmuster.net',
            'date'   => '2011-12-03',
            'name'   => 'doctree2filelist: Imports document tree into dokuwiki',
            'desc'   => 'This plugin is for importing a whole tree with (office-)documents to a wiki page-structure. It has been written for openschulportfolio, a dokuwiki based portfolio-system for schools.',
            'url'    => 'http://www.openschulportfolio.de/',
        );
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort()
    {
        return 999;
    }

    /**
     *  return a menu prompt for the admin menu
     *  NOT REQUIRED - its better to place $lang['menu'] string in localised string file
     *  only use this function when you need to vary the string returned
     */
    function getMenuText()
    {
        $menu_base = $this->getLang('plugname'); 
        return $menu_base;
    }

    /**
     * handle user request
     */
    function handle() {
        if (!isset($_REQUEST['ospcmd'])) return;

        if ($_REQUEST['ospcmd'] == "create_upload_dir") {
            $this->_create_upload_dir();
        }
        if ($_REQUEST['ospcmd'] == "docsuploaded") {
            $this->_save_status("DOCSUPLOADED");
        }
        if ($_REQUEST['ospcmd'] == "delete_upload_dir") {
            $this->_delete_upload_dir();
        }
        if ($_REQUEST['ospcmd'] == "start_over") {
            $this->_reset_wizard();
        }
        if ($_REQUEST['ospcmd'] == "importit") {
            $this->_import_docs();
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        global $conf;

        # check for filelist plugin
        if (!file_exists(DOKU_PLUGIN . "filelist/syntax.php")) {
            print $this->_div_warning("start");
            print $this->getLang('filelist_plugin_required');
            print '<a href="http://www.dokuwiki.org/plugin:filelist">The filelist-plugin can be found here.</a>';
            return;
        } 

        # print out explanation and warning
        print "<h1>" . $this->getLang('headline') ."</h1>\n";
        print "<p>" . $this->getLang('description') ."</p>\n";
        print $this->getLang('detaildesc') ."\n";
        print $this->_div_warning("start");
        print $this->getLang('warning_osp') ."\n";
        print $this->_div_warning("end");

        # determine upload dir from 
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        print "<h2>" . $this->getLang('wizard') . "</h2>\n";
        print '<div class="settingsbox"><strong>' . $this->getLang('settings') . "</strong><br />";
        print  $this->getLang('importdir') .": <tt>". $file_upload . "</tt><br />\n";
        print  $this->getLang('targetns') .": <tt>". $this->getConf('destination_namespace') . "</tt>\n";
        print "</div>\n";

        $status = $this->_read_status();

        if ($status == "IMPORTED") {
            $statusline = $this->_read_status("all");
            print "<p><span class=\"ospok\">OK </span>" . $this->getLang('lastimport') . " " . $statusline . "</p>\n";
            print $this->_create_reset_form();
        }
        if ($status == "DOCSUPLOADED") {
            print "<p><span class=\"ospok\">OK </span>" . $this->getLang('docsuploaded') . "</p>\n";
            print "<div class=\"ospnext\">" . $this->getLang('importnow') . "\n";
            ptln('<form action="'.wl($ID).'" method="post" /> ');
            ptln(' <input type="hidden" name="do"   value="admin" />');
            ptln(' <input type="hidden" name="ospcmd"   value="importit" />');
            ptln(' <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            print ' <input type="submit" value="'. $this->getLang('btn_import') . '"> ' . "\n";
            ptln('</form></div>');
        }
        if (is_dir($file_upload) && ($status != "IMPORTED") && ($status != "DOCSUPLOADED") )  {
            print "<span class=\"ospok\">OK </span>" . $this->getLang('sourcedir_exists') . " <tt> " . $file_upload . " </tt></span>\n";
            print "<div class=\"ospnext\">" . $this->getLang('docuploadnow') . "\n";
            ptln('<form action="'.wl($ID).'" method="post" /> ');
            ptln(' <input type="hidden" name="do"   value="admin" />');
            ptln(' <input type="hidden" name="ospcmd"   value="docsuploaded" />');
            ptln(' <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            print ' <input type="submit" value="'. $this->getLang('btn_confirm_upload') . '"> ' . "\n";
            ptln('</form></div>');
        }
        if (!is_dir($file_upload) && $status == "START") { 
            ptln("<div class=\"ospnext\">");
            print $this->getLang('sourcedir_does_not_exist') . "\n ";
            ptln(' <form action="'.wl($ID).'" method="post" /> ');
            ptln(' <input type="hidden" name="do" value="admin" />');
            ptln(' <input type="hidden" name="ospcmd" value="create_upload_dir" />');
            ptln(' <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            print ' <input type="submit" value="' . $this->getLang('btn_create_upload_dir') . '"> ' . "\n";
            ptln(' </form>');
            ptln('</div>');
        }

    }
    
    /**
     * Creates creates reset form 
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   none
     *
     **/
    function _create_reset_form() {
        global $conf;
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        if (@is_dir($file_upload) && @is_writable($file_upload)) {
            $html =  '<form action="'.wl($ID).'" method="post" /> '."\n";
            $html .= '  <input type="hidden" name="do" value="admin" />'."\n";
            $html .= '  <input type="hidden" name="ospcmd" value="delete_upload_dir" />'."\n";
            $html .= '  <input type="hidden" name="page" value="'.$this->getPluginName().'" />'."\n";
            $html .= ' <input type="submit" value="' . $this->getLang('btn_delete_upload_dir') . '"> ' . "\n";
            $html .= '</form>'."\n";
            $html .=  '<form action="'.wl($ID).'" method="post" /> '."\n";
            $html .= '  <input type="hidden" name="do" value="admin" />'."\n";
            $html .= '  <input type="hidden" name="ospcmd" value="importit" />'."\n";
            $html .= '  <input type="hidden" name="page" value="'.$this->getPluginName().'" />'."\n";
            $html .= ' <input type="submit" value="' . $this->getLang('btn_reimport') . '"> ' . "\n";
            $html .= '</form>'."\n";
        }
        $html .=  '<form action="'.wl($ID).'" method="post" /> '."\n";
        $html .= '  <input type="hidden" name="do" value="admin" />'."\n";
        $html .= '  <input type="hidden" name="ospcmd" value="start_over" />'."\n";
        $html .= '  <input type="hidden" name="page" value="'.$this->getPluginName().'" />'."\n";
        $html .= ' <input type="submit" value="' . $this->getLang('btn_start_over') . '"> ' . "\n";
        $html .= '</form>'."\n";
        return $html;
    }

    /**
     * Creates upload dir according to config
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   none
     *
     **/
    function _create_upload_dir() {
        global $conf;
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        mkdir($file_upload);
        $this->_save_status("UPLOADDIRCREATED");
    }
    
    /**
     * Resets wizard
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   none
     *
     **/
    function _reset_wizard() {
        global $conf;
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        if (@is_dir($file_upload) && @is_writable($file_upload)) {
            $this->_deltree($file_upload);
        }
        $this->_save_status("START");
    }
    
    /**
     * Deletes upload dir and all containing docs
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   none
     *
     **/
    function _delete_upload_dir() {
        global $conf;
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        if (@is_dir($file_upload) && @is_writable($file_upload)) {
            $this->_deltree($file_upload);
        }
    }

    /**
     * Import document tree to media dir
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @returns  none
     *
     **/
    function _import_docs() {
        global $conf;
        $mediapath = $this->_strip_doubleslashes($conf['savedir'] . '/media/');
        $pagespath = $this->_strip_doubleslashes($conf['savedir'] . '/pages/');
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        $subpath = str_replace(":", "/", $this->getConf('destination_namespace'));
        $media_dest     = $this->_strip_doubleslashes($mediapath."/".$subpath."/");
        $pagesdir       = $this->_strip_doubleslashes($pagespath."/".$subpath."/");

        # delete old media and pages dir
        if (is_dir($media_dest)) {
            $this->_deltree($media_dest);
        }
        if (is_dir($pagesdir)) {
            $this->_deltree($pagesdir);
        }

        # create fresh namespacedirs for media an pages
        io_createNamespace($this->getConf('destination_namespace').":xx");
        io_createNamespace($this->getConf('destination_namespace').":xx", 'media');

        // copy files recursively
        $this->_copytree($file_upload, $media_dest);
        // create startpages
        $this->create_startpages($media_dest);

        # determine if we are running under openschulportfolio
        if (file_exists(DOKU_INC . "/lib/tpl/portfolio2/ospversion.php")) {
            $pfstartfile_in  = realpath(dirname(__FILE__))."/start.txt";
            $pfstartfile_out = $pagespath."portfolio/start.txt";
            copy($pfstartfile_in, $pfstartfile_out);
        }
        $this->_save_status("IMPORTED");

    }

    /**
     * deletes directory tree recursively
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    string     directory to delete
     * @returns  boolean    status of rmdir operation
     *
     **/
    function _deltree($dest) {
        $list = array_diff(scandir($dest), array('.', '..'));
            foreach ($list as $value) {
                    $file = $dest.'/'.$value;
                    if (is_dir($file)) { $this->_deltree($file); } else { unlink($file); }
            }
            return rmdir($dest);
    }


    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @author      Frank Schiebel <frank@linuxmuster.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     *
     **/
    function _copytree($source, $dest) {
      global $conf;

      $source = $this->_strip_doubleslashes($source);
      $dest = $this->_get_clean_filename($dest);
      // Simple copy for a file
      if (is_file($source)) {  
        return @copy($source, $dest);
      }
      $dest = $this->_get_clean_filename($dest);

      // Make destination directory
      if (!is_dir($dest)) {

        $mediapath = $this->_strip_doubleslashes($conf['savedir'] . '/media/');
        $pagespath = $this->_strip_doubleslashes($conf['savedir'] . '/pages/');


        $pages_dir = str_replace("$mediapath", "$pagespath", $dest);
        mkdir($dest);
        if (!is_dir($pages_dir)) {
          @mkdir($pages_dir);
        }
      }

      // Loop through the folder
      if (!is_dir($source)) {
        return false;
      }
        
      $dir = dir($source);
      while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
          continue;
        }

        // Deep copy directories
        $this->_copytree("$source/$entry", "$dest/$entry");
      }

      // Clean up
      $dir->close();
      return true;
    }

    /** Gets utf8 encoded wiki filename (experimantal, has to be tested!)
     *
    **/
    function _get_clean_filename($dest) {
      global $conf;

      $fixpath = $this->_strip_doubleslashes($conf['savedir'] . "/media/");
      $dest = str_replace("$fixpath", "", $dest);
      # Fix windows encoding: this is really bad, but i could not figure out how to
      # change the filename to utf8 from wathever encoding comes in...
      #$dest = urlencode($dest);
      #$dest = str_replace('%2F','/', $dest);
      #$dest = str_replace('%25','%', $dest);
      #$dest = str_replace('%C2','', $dest);
      #$dest = str_replace('%C3','', $dest);
      #$dest = str_replace('%81','ue', $dest);
      #$dest = str_replace('%84','ae', $dest);
      #$dest = str_replace('%94','oe', $dest);
      #$dest = str_replace('%A1','ss', $dest);
      #$dest = str_replace('+','_', $dest);
      #$dest = str_replace('-','_', $dest);

      $dest = str_replace('__','_', $dest);
      $dest = str_replace("//", "/", $dest);
      $dest = str_replace("/", ":", $dest);
      $dest = preg_replace("/:$/", "",  $dest);

      $dest = mediaFN($dest);
      $dest = $this->_strip_doubleslashes($fixpath . str_replace($conf['mediadir'], '', $dest));

      return $dest;
    }

    /** recursively creates startpages for imported document structure
     *
     **/
    function create_startpages($dest) {
      global $conf;
      $subdirs = array();

      // namespace for filelist
      $media = $this->_strip_doubleslashes($conf['savedir'] . "/media/");
      $pages = $this->_strip_doubleslashes($conf['savedir'] . "/pages/");

      // change dest to pages
      $dest = str_replace($media, $pages, $dest);
      $startpage = $dest ."start.txt";

      // get filelist namespace
      $ns_dir = str_replace($pages, "", $dest);
      $filelist_namespace = str_replace("/",":", $ns_dir);
      $header_namespace   = preg_replace("|.*/(.*)/$|","$1", $ns_dir);

      // get all subdirs of actual ns
      $dir = dir($dest);
      while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
          continue;
        }
        if (is_dir($dest.$entry)) {
          $subdirs[] = $entry;
          // recursively call function
          $this->create_startpages($dest.$entry."/");
        }
      }

      // sort subdirs and create wiki-markup
      $subdir_out = "";
      if (count($subdirs) > 0 ) {
        sort($subdirs);
        $subdir_out = "===== " . $this->getLang('subnamespaces') ." ===== \n\n";
        foreach ($subdirs as $subdir) {
          $subdir_out .= "  * [[.$subdir:start|$subdir]]\n";
        }
      }

      // write start.txt
      $handle = fopen ("$startpage", "w");
      fwrite($handle, "[[".DOKU_URL."/lib/exe/mediamanager.php?ns=$filelist_namespace|" . $this->getLang('edit_files') . "]] | [[..start|" . $this->getLang('ns_up') . "]] \n\n");
      fwrite($handle, "====== " . $this->getLang('documents_for') .  $header_namespace . " ======\n\n");
      fwrite($handle, $this->getLang('docslisted') . "\\\\ ''$filelist_namespace'' \n\n");
      fwrite($handle, "{{filelist>:$filelist_namespace*&style=table&tableheader=1&tableshowdate=1&tableshowsize=1}}\n\n");
      fwrite($handle, "$subdir_out\n");
      fclose($handle);
    }

    /**
     * Strip double slashes from path names
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    string     path in
     * @returns  string     path out
     *
     **/
    function  _strip_doubleslashes($path) {
        return preg_replace('/\/\//','/', $path);
    }

    /**
     * Save status of import procedure to status file
     * $conf['cachedir'].'/doctree2filelist.status'
     * creates the file if it does not exist.
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    string     statusstring
     * @return   true on success
     *
     **/
    function _save_status($status) {
        global $conf;
        // build status line
        $t = time();
        $statusline = $t."\t".strftime($conf['dformat'],$t)."\t".$_SERVER['REMOTE_ADDR']."\t".$_SERVER['REMOTE_USER']."\t".$status."\n";
        // write status to file
        io_saveFile($conf['cachedir'].'/doctree2filelist.status',$statusline, false);
        return true;
    }

    /**
     * reads status of import procedure from status file
     * $conf['cachedir'].'/doctree2filelist.status'
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @return   string statusstring
     *
     **/
    function _read_status($mode = "statonly") {
        global $conf;
        $status = "START";
        // read status from file
        $statusfile = $conf['cachedir'].'/doctree2filelist.status';
        if(@file_exists($statusfile)) {
            $statusline = file($statusfile);
            $statusline = $statusline[0];  
            $status = explode("\t", $statusline);
            if ($mode == "statonly" ) {
                $status = rtrim($status[4]);
            } else {
                $status = $status[1] . " " . $this->getLang('fromuser') . " " . $status[3]; 
            }
        }

        return $status;
    }

    /**
     * prints a warning div. Uses the note plugin if available
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    string (start|end)
     * @return   string html
     *
     **/
    function _div_warning($mode) {
        if($mode == "start") {
            if (file_exists(DOKU_PLUGIN . "note/syntax.php")) {
                return '<div class="notewarning">';
            } else {
                return '<div class="docimpwarning">';
            }
        } else {
            return '</div>';
        }
    }


}
