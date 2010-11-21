<?php
/**
 * Easy confiiguration of openschulportfolios design
 * only works with template "portfolio"
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_INCLUDE')) define('DOKU_INCLUDE',DOKU_INC.'inc/');
require_once(DOKU_PLUGIN . 'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_ospdocimport extends DokuWiki_Admin_Plugin
{
var $state = 0;
var $backup = '';

	/**
	 * Constructor
	 */
	function admin_plugin_ospdocimport()
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
			'name'   => 'Configure design elements of openSchulportfolio',
			'desc'   => 'Allows to change logos an link-colors of openSchulportfolio without file access to the webspace.',
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
	function handle()
    {
     if (!isset($_REQUEST['ospcmd'])) return;
     if ($_REQUEST['ospcmd'] == "create_upload_dir") $this->create_upload_dir();
	}

	/**
	 * output appropriate html
	 */
	function html()
	{
      global $conf;
      

      ptln("<h1>Importassistent für bestehende Dokumentensammlungen</h1>");
      $helptext = "<p>Dieser Assistent soll Ihnen ermöglichen, eine vorhanden Sammlung von Office-Dokumenten (Word, Powerpoint, PDF u.ä.) in das Wiki zu importieren.</p>";
      $helptext .= "<p>Der Vorgang besteht aus vier Schritten:</p>";
      $helptext .= "<ol><li>Zunächst wird der gesamte vorhandene Dokumentenbestand mit allen Unterverzeichnissen auf den Server kopiert, auf dem openSchulportftolio installiert ist.";
      $helptext .= "Dazu wird vom Assistenten vorübergehend ein Verzeichnis angelegt, in welches die Dateien transferiert werden müssen.</li>";
      $helptext .= "<li>In einem weiteren Schritt werden die Dateien in den eigentlichen Dokumentenbaum des Wikis kopiert. Dabei werden die Dateien wenn nötig umbenannt";
      $helptext .= "(Umlaute, Leerzeichen und ähnliches sind für die Verwendung in Online-Systemen nicht geeignet). Außerdem wird bei diesem Vorgang im Wiki eine";
      $helptext .= "Seitenstruktur erzeugt, über die alle kopierten Dokumente anschließend erreichbar sind. Diese Wikiseiten können nach erfolgreichem Import beliebig";
      $helptext .= "angepasst werden.</li>";
      $helptext .= "<li>Wenn alles geklappt hat, können die zuvor auf den Server geladenen Dokumente gelöscht werden.</li>";
      $helptext .= "</ol>";
      ptln($helptext);

      $warning = "<div class='notewarning'>Bei der Ausführung des Assistenten werden alle Dokumente im Namensraum <tt>portfolio:dokumente</tt> ";
      $warning .= "<strong>unwiderruflich durch den importierten Dokumentenstamm ersetzt</strong>!<br />";
      $warning .= "Außerdem wird die Startseite im Namensraum <tt>portfolio</tt> durch eine Vorlage ersetzt, die einen Verweis auf die importierte ";
      $warning .= "Seitenstruktur enthält. Ältere Versionen dieser Wiki-Seite können wie gewohnt wiederhergestellt werden.</div>";
      ptln($warning);


        // TOCONF
        $file_upload = $conf['savedir'] . "media/incoming/";
        if ($_REQUEST['ospcmd'] == "importit") {
         $html = $this->import_docs();
         ptln($html);
        }

        

        # check if input dir exists
        if( is_dir($file_upload) ) {
         ptln("<span class=\"ospok\">OK </span> Quellverzeichnis existiert: <tt>$file_upload</tt>");
         ptln("<div class=\"ospnext\"><div>Laden Sie nun den gesamten Verzeichnisbaum ihrer");
         ptln("Dokumentensammlung in das Verzeichnis</div><tt>$file_upload</tt>");
         ptln("<div>auf dem Server hoch. Anschließend können Sie Ihren Dokumentenstamm durch betätigen der Schaltfläche ins Wiki importieren.</div>");
         ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
         ptln('	<input type="hidden" name="do"   value="admin" />');
         ptln('	<input type="hidden" name="ospcmd"   value="importit" />');
         ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
         ptln('  <input type="submit" value="Dateien importieren und Seitenstruktur anlegen"> ');
         ptln(' </form></div>');
        } else {
         ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
         ptln('	<input type="hidden" name="do"   value="admin" />');
         ptln('	<input type="hidden" name="ospcmd"   value="create_upload_dir" />');
         ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
         ptln('  <input type="submit" value="Importverzeichnis anlegen"> ');
         ptln(' </form>');
        }


        
    }


    function create_upload_dir() {
        global $conf;
        $file_upload = $conf['savedir'] . "media/incoming/";
        mkdir($file_upload);
    }

    /**
     * Kopiert den Verzeichnisbaum mit dem bisherigen Dokumentenbestand an die 
     * konfigurierte Stelle im media-Baum. 
     *
     *
     *
     *
     *
    **/
    function import_docs() {
        global $conf;
        $mediapath = $conf['savedir'] . "media/";
        $pagespath = $conf['savedir'] . "pages/";

        $file_upload    = $mediapath."incoming/";
        $pfdir          = $mediapath."portfolio/";
        $dest           = $mediapath."portfolio/dokumente/";
        $pagesdir       = $pagespath."portfolio/dokumente/";

        if (!is_dir($pfdir)) { mkdir($pfdir); }

        // delete old media an pages 
        if (is_dir($dest)) { $this->recursive_del($dest); }
        if (is_dir($pagesdir)) { $this->recursive_del($pagesdir); }
        if (!is_dir($pagesdir)) { mkdir($pagesdir); }

        // copy files recursively
        $this->recursive_copy($file_upload, $dest);
        // create startpages
        $this->create_startpages($dest);

        $pfstartfile_in  = realpath(dirname(__FILE__))."/start.txt";
        $pfstartfile_out = $pagespath."portfolio/start.txt";
        copy($pfstartfile_in, $pfstartfile_out);
    }

    /**
     * Loescht ein Verzeichnis rekursiv
    **/
    function recursive_del($dest) {
        $list = array_diff(scandir($dest), array('.', '..'));
            foreach ($list as $value) {
                    $file = $dest.'/'.$value;
                    if (is_dir($file)) { $this->recursive_del($file); } else { unlink($file); }
            }
            return rmdir($dest);
    }


    /**
     * Copy a file, or recursively copy a folder and its contents
     * 
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     *
     **/
    function recursive_copy($source, $dest) {
      global $conf;

      $dest = $this->get_clean_filename($dest);
      //  Check for symlinks
      if (is_link($source)) {
        return symlink(readlink($source), $dest);
      }

      // Simple copy for a file
      if (is_file($source)) {
        return copy($source, $dest);
      }

      $dest = $this->get_clean_filename($dest);
      // Make destination directory
      if (!is_dir($dest)) {

        $mediapath = $conf['savedir'] . "media/";
        $pagespath = $conf['savedir'] . "pages/";

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
        $this->recursive_copy("$source/$entry", "$dest/$entry");
      }

      // Clean up
      $dir->close();
      return true;
    }

    /** Gets utf8 encoded wiki filename (experimantal, has to be tested!)
     *
    **/
    function get_clean_filename($dest) {
      global $conf;
      $fixpath = $conf['savedir'] . "media/";

      $dest = iconv("CP437", "UTF-8", $dest);

      $fixpath = $conf['savedir'] . "media/";

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
      $media = $conf['savedir'] . "media/";
      $pages = $conf['savedir'] . "pages/";

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

      // sort subdirs an create wiki-markup 
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



}
