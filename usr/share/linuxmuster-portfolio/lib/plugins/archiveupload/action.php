<?php
/**
 * Action Plugin ArchiveUpload
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Klier chi@chimeric.de
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

/**
 * DokuWiki Action Plugin Archive Upload
 *
 * @author Michael Klier <chi@chimeric.de>
 */
class action_plugin_archiveupload extends DokuWiki_Action_Plugin {

    var $tmpdir = '';

    /**
     * return some info
     */
    function getInfo() {
        return array(
                'author' => 'Michael Klier',
                'email'  => 'chi@chimeric.de',
                'date'   => @file_get_contents(DOKU_PLUGIN.'archiveupload/VERSION'),
                'name'   => 'ArchiveUpload',
                'desc'   => 'Allows you to unpack uploaded archives.',
                'url'    => 'http://dokuwiki.org/plugin:archiveupload'
            );
    }

     /**
      * Registers our callback functions
      */
    function register(&$controller) {
        $controller->register_hook('HTML_UPLOADFORM_OUTPUT', 'BEFORE', $this, 'handle_form_output');
        $controller->register_hook('MEDIA_UPLOAD_FINISH', 'BEFORE', $this, 'handle_media_upload');
    }

    /**
     * Adds a checkbox
     * 
     * @author Michael Klier <chi@chimeric.de>
     */
    function handle_form_output(&$event, $param) {
        global $INFO;
        if($this->getConf('manageronly')) {
            if(!$INFO['isadmin'] && !$INFO['ismanager']) return;
        }
        $event->data->addElement(form_makeCheckboxField('unpack', 0, $this->getLang('unpack')));
    }

    /**
     * MEDIA_UPLOAD_FINISH handler
     * 
     * @author Michael Klier <chi@chimeric.de>
     */
    function handle_media_upload(&$event, $param) {
        global $INFO;

        // nothing todo
        if(!isset($_REQUEST['unpack'])) return;

        if($this->getConf('manageronly')) {
            if(!$INFO['isadmin'] && !$INFO['ismanager']) return;
        }

        // our turn - prevent default action
        $event->preventDefault();

        call_user_func_array(array($this,'extract_archive'), $event->data);
    }

    /**
     * Uploads an extracts an archive
     * FIXME add bz and bz2 support
     *
     * @author Michael Klier <chi@chimeric.de>
     */
    function extract_archive($fn_tmp, $fn, $id, $imime) {
        global $lang;
        global $conf;

        $dir = io_mktmpdir();
        if($dir) {
            $this->tmpdir = $dir;
        } else {
            msg('Failed to create tmp dir, check permissions of cache/ directory', -1);
            return false;
        }

        // failed to create tmp dir stop here
        if(!$this->tmpdir) return false;

        $ext = substr($fn, strrpos($fn,'.')+1);

        if(in_array($ext, array('tar','gz','tgz','zip'))) {

            //prepare directory
            //FIXME needed? do it later?
            io_createNamespace($id, 'media');

            if(move_uploaded_file($fn_tmp, $fn)) {

                chmod($fn, $conf['fmode']);

                if($this->decompress($fn, dirname($fn))) {
                    msg($this->getLang('decompr_succ'), 1);
                } else {
                    msg($this->getLang('decompr_err'), -1);
                }

                // delete archive after decompression
                // fixme check for success?
                unlink($fn);

            } else {
                msg($lang['uploadfail'], -1);
            }

        } else {
            msg($this->getLang('unsupported_ftype'), -1);
            return false;
        }

        // remove tmpdir in any case
        rmdir($this->tmpdir);

        // fixme do a sweepNS here, just in case?
    }

    /**
     * Decompress an archive (adopted from plugin manager)
     *
     * @author Christopher Smith <chris@jalakai.co.uk>
     * @author Michael Klier <chi@chimeric.de>
     */
    function decompress($file, $target) {

        // need to source plugin manager because otherwise the ZipLib doesn't work
        // FIXME fix ZipLib.class.php
        require_once(DOKU_INC.'lib/plugins/plugin/admin.php');

        // decompression library doesn't like target folders ending in "/"
        if(substr($target, -1) == "/") $target = substr($target, 0, -1);

        $ext = substr($file, strrpos($file,'.')+1);

        if(in_array($ext, array('tar','gz','tgz'))) {

            require_once(DOKU_INC."inc/TarLib.class.php");

            if(strpos($ext, 'gz') !== false) $compress_type = COMPRESS_GZIP;
            //else if (strpos($ext,'bz') !== false) $compress_type = COMPRESS_BZIP; // FIXME bz2 support
            else $compress_type = COMPRESS_NONE;

            $tar = new TarLib($file, $compress_type);
            $ok  = $tar->Extract(FULL_ARCHIVE, $this->tmpdir, '', 0777);

            if($ok) {
                $files = $tar->ListContents();
                $this->postProcessFiles($target, $files);
                return true;
            } else {
               return false;
            }

        } else if ($ext == 'zip') {

            require_once(DOKU_INC."inc/ZipLib.class.php");

            $zip = new ZipLib();
            $ok  = $zip->Extract($file, $this->tmpdir);

            if($ok) {
                $files = $zip->get_List($file);
                $this->postProcessFiles($target, $files);
                return true;
            } else {
                return false;
            }

        }

        // unsupported file type
        return false;
    }

    /**
     * Checks the mime type and fixes the permission and filenames of the
     * extracted files and sends a notification email per uploaded file
     *
     * @author Michael Klier <chi@chimeric.de>
     */
    function postProcessFiles($dir, $files) {
        global $conf;
        global $lang;

        require_once(DOKU_INC.'inc/media.php');
        $reldir = preg_replace("#".$conf['mediadir']."#", '/', $dir) . '/';

        // get filetype regexp
        $types = array_keys(getMimeTypes());
        $types = array_map(create_function('$q','return preg_quote($q,"/");'),$types);
        $regex = join('|',$types);

        $dirs     = array();
        $tmp_dirs = array();

        foreach($files as $file) {
            $fn_old = $file['filename'];                                // original filename
            $fn_new = str_replace('/',':',$fn_old);                     // target filename
            $fn_new = str_replace(':', '/', cleanID($fn_new));

            if(substr($fn_old, -1) == '/') { 
                // given file is a directory
                io_mkdir_p($dir.'/'.$fn_new);
                chmod($dir.'/'.$fn_new, $conf['dmode']);
                array_push($dirs, $dir.'/'.$fn_new);
                array_push($tmp_dirs, $this->tmpdir.'/'.$fn_old);
            } else {
                list($ext, $imime) = mimetype($this->tmpdir.'/'.$fn_old);
                
                if(preg_match('/\.('.$regex.')$/i',$fn_old)){
                    // check for overwrite
                    if(@file_exists($dir.'/'.$fn_new) && (!$_POST['ow'] || $auth < AUTH_DELETE)){
                        msg($lang['uploadexist'],0);
                        continue;
                    }

                    // check for valid content
                    $ok = media_contentcheck($this->tmpdir.'/'.$fn_old,$imime);
                    if($ok == -1){
                        msg(sprintf($lang['uploadbadcontent'],".$ext"),-1);
                        unlink($this->tmpdir.'/'.$fn_old);
                        continue;
                    }elseif($ok == -2){
                        msg($lang['uploadspam'],-1);
                        unlink($this->tmpdir.'/'.$fn_old);
                        continue;
                    }elseif($ok == -3){
                        msg($lang['uploadxss'],-1);
                        unlink($this->tmpdir.'/'.$fn_old);
                        continue;
                    }

                    // everything's ok - lets move the file
                    // FIXME check for success ??
                    rename($this->tmpdir.'/'.$fn_old, $dir.'/'.$fn_new);
                    chmod($dir.'/'.$fn_new, $conf['fmode']);

                    // send notification mail
                    $id = cleanID(str_replace('/',':',$reldir.'/'.$fn_new));
                    media_notify($id, $dir.'/'.$fn_new, $imime); 
                    msg($lang['uploadsucc'], 1);

                } else {
                    msg($lang['uploadwrong'],-1);
                    @unlink($this->tmpdir.'/'.$fn_old);
                    continue;
                }
            }
        }

        // done - remove eventually left over empty dirs in destination directory 
        natsort($dirs);
        $dirs = array_reverse($dirs);
        foreach($dirs as $dir) {
            @rmdir($dir);
        }

        // do the same for the tmp dir
        natsort($tmp_dirs);
        $dirs = array_reverse($tmp_dirs);
        foreach($dirs as $dir) {
            @rmdir($dir);
        }
    }
}
// vim:ts=4:sw=4:et:enc=utf8:
