<?php
/**
 * Backup Tool for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Terence J. Grant<tjgrant@tatewake.com>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_INCLUDE')) define('DOKU_INCLUDE',DOKU_INC.'inc/');
require_once(DOKU_PLUGIN . 'admin.php');

include_once(DOKU_PLUGIN.'backup/pref_code.php');

@include_once("Archive/Tar.php");   //PEAR Archive/Tar

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_backup extends DokuWiki_Admin_Plugin
{
var $state = 0;
var $backup = '';

    /**
     * Constructor
     */
    function admin_plugin_backup()
    {
        $this->setupLocale();
    }

    /**
     * return some info
     */
    function getInfo()
    {
        return array(
            'author' => 'Terence J. Grant, Andreas Wagner',
            'email'  => 'tjgrant@tatewake.com, andreas.wagner@em.uni-frankfurt.de',
            'date'   => '2008-08-24',
            'name'   => 'BackupTool for DokuWiki',
            'desc'   => 'A tool to backup your data and configuration.',
            'url'    => 'http://tatewake.com/wiki/projects:backuptool_for_dokuwiki',
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
        return 'BackupTool for DokuWiki';
    }

    /**
     * handle user request
     */
    function handle()
    {
        $this->state = 0;

        if (!isset($_REQUEST['cmd'])) return;   // first time - nothing to do

        if (!is_array($_REQUEST['cmd'])) return;

        $this->backup = $_REQUEST['backup'];

        if (is_array($this->backup))
        {
            $this->state = 1;
        }
    }

    function runPearBackup($files, $finalfile, $tarfilename, $compress_type)
    {
        //Create archive object, add files, compile and compress.
        $tar = new Archive_Tar($finalfile,$compress_type);
        $result = $tar->createModify($files,'',DOKU_INC);
        $tar->_Archive_Tar();

        return ($result) ? $tarfilename.'.'.$compress_type : '';    //return filename on success...
    }

    function runExecBackup($files, $tarfilename, $basename)
    {
        $result = false;
        $i = 0; //mark for first file
        $rval = 0;

        //For each item, add it to the file.
        foreach($files as $item)
        {
        //  print("tar ". (($i != 0) ? "-rf " : "-cf ") .$tarfilename." "._getRelativePath($item).'<br/>');
            if (!bt_exec("tar ". (($i != 0) ? "-rf " : "-cf ") .$tarfilename." "._getRelativePath($item)))
            {
                return ''; //tar failed (possibly out of memory)
            }
            $i = 1;
        }

        if (bt_exec('bzip2 --version'))
            if (bt_exec('bzip2 -9 '.$tarfilename)) return $basename.'.bz2'; //Bzip2 compression available.
        if (bt_exec('gzip --version'))
            if (bt_exec('gzip -9 '.$tarfilename)) return $basename.'.gz';   //Gzip compression available.
        return $basename;                   //No compression available, but tar succeeded
    }

    /**
     * output appropriate html
     */
    function html()
    {
        global $conf;
        global $bt_loaded, $bt_settings;

        $bt_pearWorks = (class_exists("Archive_Tar")) ? true : false;
        $bt_execWorks = bt_exec("tar --version");

        if (!($bt_pearWorks || $bt_execWorks))  //if neither works, display the error message.
        {
            print $this->plugin_locale_xhtml('error');
        }
        else
        {
            if ($this->state == 0)
            {
                //Print Backup introduction page
                print $this->plugin_locale_xhtml('intro');

                ptln('<form action="'.wl($ID).'" method="post">');
                ptln('  <input type="hidden" name="do"   value="admin" />');
                ptln('  <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
                ptln('  <input type="hidden" name="cmd[backup]" value="true" />');
                print '<center>';

//              ptln('bt_settings[type] = '.$bt_settings['type'].'<br/>');
                ptln('  Backup method: <select name="backup[type]">');
                if ($bt_pearWorks == true) ptln('       <option value="PEAR" '.(strcmp($bt_settings['type'], 'PEAR') == 0 ? 'selected' : '').'>PEAR Archive Library</option>');
                if ($bt_execWorks == true) ptln('       <option value="exec" '.(strcmp($bt_settings['type'], 'exec') == 0 ? 'selected' : '').'>GNU Tar</option>');
                if ($bt_execWorks == true) ptln('       <option value="lazy" '.(strcmp($bt_settings['type'], 'lazy') == 0 ? 'selected' : '').'>Lazy and Quick Method</option>');
                ptln('  </select><br/><br/>');

                print '<table class="inline">';
                print ' <tr><th> '.$this->getLang('bt_item_type').' </th><th> '.$this->getLang('bt_add_to_archive').' </th></tr>';
                print ' <tr><td> '.$this->getLang('bt_pages').' </td><td><input type="checkbox" name="backup[pages]" '.$bt_settings['pages'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_revisions').' </td><td><input type="checkbox" name="backup[revisions]" '.$bt_settings['revisions'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_subscriptions').'</td><td><input type="checkbox" name="backup[subscriptions]" '.$bt_settings['subscriptions'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_media').' </td><td><input type="checkbox" name="backup[media]" '.$bt_settings['media'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_config').' </td><td><input type="checkbox" name="backup[config]" '.$bt_settings['config'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_templates').'</td><td><input type="checkbox" name="backup[templates]" '.$bt_settings['templates'].'/></td></tr>';
                print ' <tr><td> '.$this->getLang('bt_plugins').'</td><td><input type="checkbox" name="backup[plugins]" '.$bt_settings['plugins'].'/></td></tr>';
                print '</table>';

                print '<br />';
                print '<p><input type="submit" value="'.$this->getLang('bt_create_backup').'"></p></center>';
                print '</form>';
            }
            else
            {
                //Save settings...
                $bt_settings['type']                    = strcmp($this->backup['type'], 'PEAR') == 0 ? 'PEAR' :
                                                                                strcmp($this->backup['type'], 'exec') == 0 ? 'exec' : 'lazy';
                $bt_settings['pages']                   = strcmp($this->backup['pages'], 'on') == 0 ? 'checked' : '';
                $bt_settings['revisions']           = strcmp($this->backup['revisions'], 'on') == 0 ? 'checked' : '';
                $bt_settings['subscriptions']   = strcmp($this->backup['subscriptions'], 'on') == 0 ? 'checked' : '';
                $bt_settings['media']                   = strcmp($this->backup['media'], 'on') == 0 ? 'checked' : '';
                $bt_settings['config']              = strcmp($this->backup['config'], 'on') == 0 ? 'checked' : '';
                $bt_settings['templates']           = strcmp($this->backup['templates'], 'on') == 0 ? 'checked' : '';
                $bt_settings['plugins']             = strcmp($this->backup['plugins'], 'on') == 0 ? 'checked' : '';
                bt_save();

                //Print outgoing message...
                print $this->plugin_locale_xhtml('outro');

                //Generate file names
                $tarfilename = 'dw-backup-'.date('Ymd-His').".tar";
                $compress_type = (extension_loaded('bz2') ? 'bz2' : (extension_loaded('zlib') ? 'gz' : ''));
                $finalfile = $tarfilename.'.'.$compress_type;

                //Retrieve the savedir
                if (isset($conf["savedir"])) {
                    $savedir = $conf["savedir"].'/media/';
                } else {
                    $savedir = DOKU_INC.'/data/media';
                }
                $savedir = preg_replace('/\/\//','/', $savedir);

                // retrieve config directory
                if (is_dir("/etc/linuxmuster-portfolio")) {
                    $config_dir = "/etc/linuxmuster-portfolio";
                } else {
                    $config_dir = DOKU_INC . "/conf";
                    $config_dir = preg_replace('/\/\//','/', $config_dir);
                }

                //Generate array of files
                $files = array($linuxmusterconf. "/smileys.conf");

                if (strcmp($this->backup['type'], 'lazy') == 0) //Use lazy method
                {
                    if ($this->backup['pages'])                 $files = array_merge($files, array($conf['datadir']));
                    if ($this->backup['revisions'])         $files = array_merge($files, array($conf['olddir']));
                    if ($this->backup['subscriptions']) $files = array_merge($files, array($conf['metadir']));
                    if ($this->backup['config'])                $files = array_merge($files, directoryToArray($config_dir));
                    if ($this->backup['templates'])         $files = array_merge($files, array(DOKU_INC . "lib/tpl"));
                    if ($this->backup['plugins'])               $files = array_merge($files, array(DOKU_INC . "lib/plugins"));
                    if ($this->backup['media'])                 $files = array_merge($files, array($conf['mediadir']));
                }
                else    //Use filtered files method
                {
                    if ($this->backup['pages'])                 $files = array_merge($files, directoryToArray($conf['datadir']));
                    if ($this->backup['revisions'])         $files = array_merge($files, directoryToArray($conf['olddir']));
                    if ($this->backup['subscriptions']) $files = array_merge($files, directoryToArray($conf['metadir']));
                    if ($this->backup['config'])                $files = array_merge($files, directoryToArray($config_dir));
                    if ($this->backup['templates'])         $files = array_merge($files, directoryToArray(DOKU_INC . "lib/tpl"));
                    if ($this->backup['plugins'])               $files = array_merge($files, directoryToArray(DOKU_INC . "lib/plugins"));
                    if ($this->backup['media'])
                    {
                        $files = array_merge($files, directoryToArray($conf['mediadir']));
                        $files = array_filter($files, "filterBackups");
                    }
                }



                //Run the backup method
                if (strcmp($this->backup['type'], 'PEAR') == 0)
                    $finalfile = $this->runPearBackup($files, $savedir.$finalfile, $tarfilename, $compress_type);
                else    //exec and lazy both use the exec method
                    $finalfile = $this->runExecBackup($files, $savedir.$tarfilename, $tarfilename);

                if ($finalfile == '')
                {
                    print $this->plugin_locale_xhtml('memory');
                }
                else
                {
                    print $this->plugin_locale_xhtml('download');
                    print $this->plugin_render('{{:'.$finalfile.'}}');
                }
                ob_flush(); flush();
            }
        }

        print $this->plugin_locale_xhtml('donate');
    }
}

function bt_exec($cmd)
{
    $oval = array();
    $rval = 0;
    exec($cmd, $oval, $rval);

    return (($rval == 0) ? true : false);
}

//From Uwe Koloska
function _getRelativePath($path)
{
    global $conf;

    $pattern = '/'.preg_quote(DOKU_INC, '/').'/';
    $pname = preg_replace($pattern,'',$path);
    return $pname;
}

// from http://snippets.dzone.com/posts/show/155 :
function directoryToArray($directory) {
    $array_items = array();
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && $file != "_dummy" && $file != "disabled") {
                $file = $directory . "/" . $file;
                if (is_dir($file)) {
                    $array_items = array_merge($array_items, directoryToArray($file));
                } else {
                    if(filesize($file) !== 0) $array_items[] = preg_replace("/\/\//si", "/", $file);
                }
            }
        }
        closedir($handle);
    }
    return $array_items;
}

function filterBackups($InputElement) {
    $result = !preg_match("/" . str_replace("/", "\/", $conf['mediadir'] . "/dw-backup-\d{8}-\d{6}\.tar[\.gz|\.bz2]?") . "/", $InputElement);
  $result &= !preg_match("/" . str_replace("/", "\/", $conf['mediadir'] . "/\.DS_Store?") . "/", $InputElement);
  $result &= !preg_match("/" . str_replace("/", "\/", $conf['mediadir'] . "/\._.?") . "/", $InputElement);
    return $result;
}
