<?php
/**
 * Dokuwiki Action Plugin Media-File-Rename
 * Allows renaming of mediafiles to dokuwiki-conform names
 * 
 * @author Christian Eder 
 * 
 */
 
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
 
class action_plugin_mediarename extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'STADTWERKSTATT',
      'email'  => 'target@servus.at',
      'date'   => '2009-12-02',
      'name'   => 'Media File Rename (mediarename)',
      'desc'   => 'Media Files with invalid names can be renamed to valid names when they are found in the media directory by the media-manager. Renaming is triggered by a link at media-managers left-side area. Such a filename fixing can be needed, if files were not uploaded with the media manager but copied via the servers filesystem. Take notice: recursive renaming works, but stops inside renamed directories. Re-run the action in that case.',
      'url'    => 'http://develop.servus.at/',
    );
  }

  /**
   * Register its handlers with the dokuwiki's event controller
   */
  function register(&$controller) {
    // MEDIAMANAGER_CONTENT_OUTPUT Wraps the output of the (right) content pane in the Media Manager 
    // intresting but we use MEDIAMANAGER_STARTED
    $controller->register_hook(
      'MEDIAMANAGER_CONTENT_OUTPUT', 'BEFORE', $this, 'handle_link'
    );
  }

 

  /**
   * Handle the event
   */ 
  function handle_link(&$event, $param) {
//if (!$wpcal =& plugin_load('helper', 'wpcalendarlist')) return false;
   //$_REQUEST['do']='admin';
    global $conf;
    global $lang; 
    if (isset($_REQUEST['rename'])){
	//echo $_REQUEST['rename'];
	$ns = cleanID($_REQUEST['ns']);
	$dir = utf8_encodeFN(str_replace(':','/',$ns));
    	$data = array();
        $recurse=($_REQUEST['rename']=='recv') ? true : false;
        
	//The showhiddenmedia plugin would provide a changed search function which does not exclude
	//filenames beginning with '_' (underscore), as unfortunately many digicams name their 
        //files similar to "_IMGxxxx".
  //      if ($hiddenfiles =& plugin_load('action', 'showhiddenmedia'))  {
  //  	 	$hiddenfiles->my_search($data,$conf['mediadir'],array($this,'_media_file_rename'),array('showmsg'=>true,'recurse'=>$recurse),$dir,0);}
 //       else { 
		search($data,$conf['mediadir'],array($this,'_media_file_rename'),array('showmsg'=>true,'recurse'=>$recurse),$dir,0);
//	}
	}

  }

  /**
   * Callback-function for the search call. It checks permission for the given ids.
   * If an invalid filename is encountered, rename it to a valid one generated.
   * Does the same with directries. If recursion is selected, it renames recursively. 
   * 
   * Fix-me:
   * Attention: in case that a directory had to be renamed, recursion breaks - 
   * possible invalid filenames inside that directory are not renamed.
   * Simply run the function a second time !
   * This is for the case that a directory has to be renamed exactly to the name of
   * an already exising directory - to prevent the unintendet moving of the files from the 
   * first dir to the existing dir (that would follow from the recursive process)  
   */
  function _media_file_rename(&$data,$base,$file,$type,$lvl,$opts){
	$info         = array();
  	$info['id']   = pathID($file,true);
        //quick permissioncheck
  	if(is_null($auth)) $auth = auth_quickaclcheck($info['id']);
    	if($auth < AUTH_READ){return;}
 	
	
	//check for validity of the filename
 	if($info['id'] != cleanID($info['id'])){ 
                $id   = cleanID($ns.':'.$info['id'],false,true);

                //find a target filename which is free to use (avoid overwrite) 
		$tmp=$id;$fn='';$cnt=0;
                while (!$fn){ 
                        if (!file_exists(mediaFN($tmp))){$fn   = mediaFN($tmp);} 
			else {$cnt++;$ext=strrchr($id,'.');
                                //is there an extension?
				if ($ext) $tmp=substr($id,0,strrpos($id,'.')).'_'.$cnt.$ext;
                       		else $tmp=$id.'_'.$cnt;
			}
		}
		        //and now rename
			rename($base."/".$file,$fn);
			if($opts['showmsg']) msg(hsc($info['id']).' was not a valid file name for DokuWiki - moved to new name: '.$tmp);    	 		
  				//return true if recursion is desired 	
			
	}

	//return true if recursion is desired 	
	if($type == 'd') {
  	 	if ($opts['recurse']==true) {
		return true;}
	}
	else { return false; }
		
  	
  }


}

