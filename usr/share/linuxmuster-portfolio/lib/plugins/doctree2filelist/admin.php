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
var $state = 0;
var $backup = '';

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
            'date'   => '2010-05-25',
            'name'   => 'doctree2filelist: Imports document tree into dokuwiki',
            'desc'   => '...',
            'url'    => 'http://openschulportfolio.de/',
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
        return 'Importassistent (OSP)';
    }

    /**
     * handle user request
     */
    function handle() {
        if (!isset($_REQUEST['ospcmd'])) return;

        if ($_REQUEST['ospcmd'] == "create_upload_dir") {
            $this->create_upload_dir();
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

        # print out explanation and warning
        print "<h1>" . $this->getLang('headline') ."</h1>\n";
        print "<p>" . $this->getLang('description') ."</p>\n";
        print $this->getLang('detaildesc') ."\n";
        print $this->getLang('warning_osp') ."\n";

        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');


        # check if input dir exists, if not display button to create it
        if( is_dir($file_upload) ) {
            print "<span class=\"ospok\">OK </span>" . $this->getLang('sourcedir_exists') . " <tt> " . $file_upload . " </tt></span>\n";
            ptln("<div class=\"ospnext\"><div>Laden Sie nun den gesamten Verzeichnisbaum ihrer");
            ptln("Dokumentensammlung in das Verzeichnis</div><tt>$file_upload</tt>");
            ptln("<div>auf dem Server hoch. Anschließend können Sie Ihren Dokumentenstamm durch betätigen der Schaltfläche ins Wiki importieren.</div>");
            ptln('<form action="'.wl($ID).'" method="post" /> ');
            ptln(' <input type="hidden" name="do"   value="admin" />');
            ptln(' <input type="hidden" name="ospcmd"   value="importit" />');
            ptln(' <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            print ' <input type="submit" value="'. $this->getLang('btn_import') . '"> ' . "\n";
            ptln('</form></div>');
        } else {
            ptln("<div class=\"ospnext\">");
            print "<p>" . $this->getLang('sourcedir_does_not_exist') . " <tt> " . $file_upload . " </tt></p>\n ";
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
     * Creates upload dir according to config
     *
     * @author   Frank Schiebel <frank@linuxmuster.net>
     * @param    none
     * @returns  none
     *
     **/
    function create_upload_dir() {
        global $conf;
        # determine upload dir from conf
        $file_upload = $this->_strip_doubleslashes($conf['savedir'] . '/media/' . $this->getConf('sourcetree') . '/');
        mkdir($file_upload);
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
        print $media_dest . $pagesdir;

        # create fresh namespacedirs for media an pages
        io_createNamespace($this->getConf('destination_namespace').":xx");
        io_createNamespace($this->getConf('destination_namespace').":xx", 'media');

        // copy files recursively
        $this->_copytree($file_upload, $media_dest);
        // create startpages
        $this->create_startpages($media_dest);

        $pfstartfile_in  = realpath(dirname(__FILE__))."/start.txt";
        $pfstartfile_out = $pagespath."portfolio/start.txt";
        copy($pfstartfile_in, $pfstartfile_out);
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

      $dest = $this->_get_clean_filename($dest);

      //  Check for symlinks
      if (is_link($source)) {
        return symlink(readlink($source), $dest);
      }
      // Simple copy for a file
      if (is_file($source)) {
        return copy($source, $dest);
      }

      $dest = $this->_get_clean_filename($dest);

      // Make destination directory
      if (!is_dir($dest)) {

        $mediapath = $this->_strip_doubleslashes($conf['savedir'] . '/media/');
        $pagespath = $this->_strip_doubleslashes($conf['savedir'] . '/pages/');


        $pages_dir = str_replace("$mediapath", "$pagespath", $dest);
        mkdir($dest);
        if (!is_dir($pages_dir)) {
          mkdir($pages_dir);
        }
      }

      // Loop through the folder
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
      $dest = iconv("CP437", "UTF-8", $dest);
      $dest = str_replace("$fixpath", "", $dest);
      $dest = str_replace("//", "/", $dest);
      $dest = str_replace("/", ":", $dest);
      $dest = preg_replace("/:$/", "",  $dest);
      $dest = mediaFN($dest);
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
        $subdir_out = "===== Untergeordnete Namensräume ===== \n\n";
        foreach ($subdirs as $subdir) {
          $subdir_out .= "  * [[.$subdir:start|$subdir]]\n";
        }
      }

      // write start.txt
      $handle = fopen ("$startpage", "w");
      fwrite($handle, "[[".DOKU_URL."/lib/exe/mediamanager.php?ns=$filelist_namespace|Dateien bearbeiten]] | [[..start|In den übergeordneten Namensraum wechseln]].\n\n");
      fwrite($handle, "====== Dokumente für: $header_namespace======\n\n");
      fwrite($handle, "Hier sind alle Dateien des Namensraums ''$filelist_namespace'' aufgelistet.\n\n");
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



}
