<?php

/**
 * Htaccess Dokuwiki authentication backend
 * 
 * Can be used behind a real .htaccess basic authentication OR
 * stand alone but using the htpasswd, htgroup formatted files.
 * 
 * htaccess does not support extended user info (name, email) so
 * these are either stored in a separate file 
 * 
 * Configuration
   $conf['authtype'] = 'htaccess';
   
   # name of .htaccess file, must exist if absolute, if relative will search for this file up to the document root.
   $conf['auth']['htaccess']['htaccess'] = '.htaccess'; 
   
   # name of file to store names and emails for each user. if relative assumed same directory as "AuthUserFile" directive.
   $conf['auth']['htaccess']['htuser'] = null;
   
 *  @see also configuration for httpbasic backend.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author	   Grant Gardner <grant@lastweekend.com.au>
 * @version:    0.3
 *
 * Work based on previous authentication backends by:
 * @author     Samuele Tognini <samuele@cli.di.unipi.it>
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Chris Smith <chris@jalakai.co.uk>
 * @author     Marcel Meulemans <marcel_AT_meulemans_DOT_org>
 * Additions:  Sebastian S <Seb.S@web.expr42.net>
 * 
 */

define('DOKU_AUTH', dirname(__FILE__));
require_once (DOKU_AUTH . '/httpbasic.class.php');

class auth_htaccess extends auth_httpbasic {

	var $users = null;
	var $lockFile = null;
	var $htpasswd;
	var $htgroup;
	var $htusers;
	var $_pattern = array ();

	/**
	 * Constructor
	 * Check config, .htaccess and set capabilities
	 */
	function auth_htaccess() {
		global $conf;
		
    if (method_exists($this, 'auth_httpbasic')) {
        parent::auth_httpbasic();
    }
    
		$this->htpasswd = new htpasswd();
		$this->htuser = new htuser();
		$this->htgroup = new htgroup("",$conf['defaultGroup']);
		
		if (!$this->findHtAccess()) {
       $this->success = false;
       return;
    } 

		if (!$this->htpasswd->canRead()) {
			$this->success = false;
			return;
		}
	
		$this->cando['getUsers'] = true;
		$this->cando['getUserCount'] = true;
		
		if ($this->htpasswd->canModify() && $this->htuser->canModify()) {
		    $this->cando['addUser'] = true;
		    $this->cando['delUser'] = true;
		    $this->cando['modLogin'] = true;
	    	$this->cando['modPass'] = true;
			$this->cando['modName'] = true;
			$this->cando['modMail'] = true;		
		}
		
			
		//And groups.
		if ($this->htgroup->canRead()) {
			$this->cando['getGroups'] = true;
			if ($this->htgroup->canModify()) {
				$this->cando['modGroups'] = true;		
			}
		}
    		
	}
	

  /**
   * Check user+password, if using the login page
   * or update profile form
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   * @return  bool
   */
  function checkPassword($user,$pass){
    return $this->htpasswd->verifyUser($user,$pass);
  }
  
   /**
	* Return user info
    */
	function getUserData($user) {
		global $conf;
		
		if ($this->users === null) $this->loadUserData();

		return isset ($this->users[$user]) ? $this->users[$user] : false;
	}

	function getUserCount($filter = array ()) {
		if ($this->users === null) $this->loadUserData();

		if (!count($filter))
			return count($this->users);

		$count = 0;
		$this->constructPattern($filter);

		foreach ($this->users as $user => $info) {
			$count += $this->filter($user, $info);
		}

		return $count;
	}

	function retrieveUsers($start = 0, $limit = 0, $filter = array ()) {
		if ($this->users === null) 
			$this->loadUserData();

		ksort($this->users);

		$i = 0;
		$count = 0;
		$out = array ();
		$this->constructPattern($filter);

		foreach ($this->users as $user => $info) {
			if ($this->filter($user, $info)) {
				if ($i >= $start) {
					$out[$user] = $info;
					$count++;
					if (($limit > 0) && ($count >= $limit))
						break;
				}
				$i++;
			}
		}
		return $out;
	}
	
	function createUser($user, $pwd, $name, $mail, $grps = null) {
		global $conf;
    $lockfp = $this->lockWrite();
		
		$this->htpasswd->reload();
		$this->htuser->reload();

		$addOK = $this->htpasswd->addUser($user,$pwd);
		$addOK = $addOK && $this->htuser->addUser($user,$name,$mail);
		
		if (isset($grps)) {
			$this->htgroup->reload();
			$addOK = $addOK && $this->htgroup->setGroupsForUser($user,$grps);
		}

		$this->lockRelease($lockfp);
		$this->loadUserData();
		return $addOK;
	}
	
	function deleteUsers($users) {
				
		$userCount = $this->getUserCount();
		
		$lockfp = $this->lockWrite();
		
		$this->htpasswd->reload();
		$deleteOK = $this->htpasswd->delete($users);
			
		if ($this->htuser) {
			$this->htuser->reload();
			$deleteOK =  $deleteOK && $this->htuser->delete($users);
		}		
	
		$this->htgroup->reload();	
		$deleteOK = $deleteOK && $this->htgroup->delete($users);
	
		$this->lockRelease($lockfp);	
				
		$this->loadUserData();		
		return ($userCount - $this->getUserCount());
	}
	
  function modifyUser($user, $changes) {
    	
  	$lockfp = $this->lockWrite();
  	
  	$modifyOK = true;
  	
  	$this->htpasswd->reload();
  	$this->htuser->reload();
  	$this->htgroup->reload();
  	
	if (!empty($changes['user'])) {
		
		$newUser = $changes['user'];
		$modifyOK = $this->htpasswd->renameUser($user,$newUser,empty($changes['pass']));
		
		if ($modifyOK) {
			$userInfo = $this->htuser->getUserInfo($user);
			if ($userInfo) {
				$modifyOK = $modifyOK && $this->htuser->delete($user,false);
				$changes = array_merge($userInfo,$changes);
			}

			$oldGroups = $this->htgroup->getGroupsForUser($user);
			if ($oldGroups) {
				$modifyOK = $modifyOK && $this->htgroup->delete($user,false);
				if (empty($changes['grps'])) {
						$changes['grps'] = $oldGroups;
				}
			}
						
						
			$user = $newUser;
		}
	}
	
	if (!empty($changes['pass'])) {
		$modifyOK = $modifyOK && $this->htpasswd->changePass($user,$changes['pass']);
	}
	
	if (!empty($changes['grps'])) {
		$modifyOK = $modifyOK && $this->htgroup->setGroupsForUser($user,$changes['grps']);
	}
	

	$modifyOK = $modifyOK && $this->htuser->modify($user,$changes);
	
	
	$this->lockRelease($lockfp);
	$this->loadUserdata();
    return $modifyOK;
  }
  
	/*private*/ function loadUserData() {
		
		$this->users = array();
		$passwords = $this->htpasswd->getUsers();
		foreach ($passwords as $user => $cryptPass) {
		
			$this->users[$user] = $this->getDefaultUser($user);
			$this->users[$user]['pass']=$cryptPass;		
		}
		
		$extendedUserInfo = $this->htuser->getUsers();
		foreach ($extendedUserInfo as $user => $userinfo) {	
			if (!isset($this->users[$user])) {
				$this->users[$user] = $this->getDefaultUser($user);
			}
			$this->users[$user] = array_merge($this->users[$user], $userinfo);
		}
	
		
		$groupsByUser = $this->htgroup->getGroupsByUser();
		foreach ($groupsByUser as $user => $groups) {
			
			if (!isset($this->users[$user])) {
				$this->users[$user] = $this->getDefaultUser($user);
			}
			$this->users[$user]['grps']=$groups;
		}


	}
	/**
		* return 1 if $user + $info match $filter criteria, 0 otherwise
		*
		* @author   Chris Smith <chris@jalakai.co.uk>
		*/
	/*private*/ function filter($user, $info) {

		foreach ($this->_pattern as $item => $pattern) {
			if ($item == 'user') {
				if (!preg_match($pattern, $user))
					return 0;
			} else
				if ($item == 'grps') {
					if (!count(preg_grep($pattern, $info['grps'])))
						return 0;
				} else {
					if (!preg_match($pattern, $info[$item]))
						return 0;
				}
		}
		return 1;
	}

	/*private*/ function constructPattern($filter) {
		$this->_pattern = array ();
		foreach ($filter as $item => $pattern) {
			//        $this->_pattern[$item] = '/'.preg_quote($pattern,"/").'/';          // don't allow regex characters
			$this->_pattern[$item] = '/' . str_replace('/', '\/', $pattern) . '/'; // allow regex characters
		}
	}

	/*private*/ function findHtAccess() {
		global $conf;
		
	  $htaccessFile = $conf['auth']['htaccess']['htaccess'];
 
    if (empty($htaccessFile)) {
      $htaccessFile = ".htaccess";
    }
    
    if (basename($htaccessFile) == $htaccessFile) {
 
      $baseName = $htaccessFile;
      $currentDir = realpath(DOKU_AUTH . "/../../");
      $htaccessFile = $currentDir . "/".$basename;
      
      //Stop at "/", if we knew what the doc root was we'd stop there
      while (!empty ($currentDir) && !file_exists($htaccessFile)) {

        $parentDir = dirname($currentDir);
        if ($parentDir == $currentDir) {
          break; //at root
        } 
        $htaccessFile = $parentDir."/".$basename;        
      }	  
    } 
    
    
		if (!file_exists($htaccessFile)) {
			return false;
		}
		
    $this->lockFile = $htaccessFile;
    
		$lockfp = $this->lockRead();
		
		$lines = file($htaccessFile);
		
   		
		foreach ($lines as $line) {
			$row = preg_split("/\s+/", $line,3);
			#unshift leading spaces
			if(trim($row[0])==""){ array_shift($row); }
    
			$var = strtolower(trim($row[0]));
			$value = trim($row[1]);

			if ($var == "authuserfile") {
				$this->htpasswd->init($value);
				$htUserFile = $conf['auth']['htaccess']['htuser'];
				if (empty($htUserFile)) {
					$htUserFile="htuser";
				}
				if (basename($htUserFile) == $htUserFile) {
					$htUserFile = dirname($value)."/$htUserFile";
				}
				$this->htuser->init($htUserFile);			
			}
			elseif ($var == "authgroupfile") {
				$this->htgroup->init($value);
			} elseif ($var == "authname") {
				$this->realm = $value; #Hope we can assign to superclass var
			}

		}
		
		$this->lockRelease($lockfp);

    return true;
	}


	/*private*/ function lockRead() {
		$lockfp = fopen($this->lockFile,'r');
		flock($lockfp,LOCK_SH) || die("Can't get shared lock on ".$this->lockFile);
		return $lockfp;
	}

	/*private*/ function lockWrite() {
    $lockfp = fopen($this->lockFile,'r');		
		flock($lockfp,LOCK_EX) || die("Can't get exclusive lock on ".$this->lockFile);
		return $lockfp;
	}
	
	/*private*/ function lockRelease($lockfp) {
		flock($lockfp,LOCK_UN);
	}
}

/*abstract*/ class htbase {


	/*private*/ var $htFile= "";
	
	function htbase ($htFile = "")
	{
		if(!empty($htFile))
		{
			$this->init($htFile);
		}
		return;
	}

	function init ($htFile)
	{
		$this->htFile	= $htFile;

		if(empty($htFile))
		{		
			$this->error("Empty file passed to init",1);
		}
		
		if ($this->canRead()) {
			$this->loadFile();
		}
	}

	function reload() {
		$this->loadFile();
	}		
	
	function canRead ($filename = "")
	{		
	
		if (empty($filename)) {
			$filename = $this->htFile;
		}
		
		if (!(file_exists($filename))) {
			//empty file is OK.		
			return true;
		}
		
		if (!(is_readable($filename)))
		{
			$this->error("File [$filename] not readable",0);
			return false;
		}
		
		if(is_dir($filename))
		{
			$this->error("File [$filename] is a directory",0);
			return false;
		}		

		return true;
	}
	
    function canModify() {    	
    	
    	if (!file_exists($this->htFile)) {
    		return is_writable(dirname($this->htFile));	
    	}
    	
    	if(is_link($this->htFile))
		{
			$this->error("File [$this->htFile] is a symlink",0);
			return false;
		}
    
    	return is_writable($this->htFile);
    }
    
    /*protected*/ function htFile() {
    	return $this->htFile;
    }
    
    /*protected*/ /*abstract*/ function loadFile() {
    }
    
    /*protected*/ /*abstract*/ function writeFile() { 
    }
    
	  /*protected*/ function error($text,$level=0) {
		  msg($text,$level);	
	  }
	
	
}
 
class htgroup extends htbase {

	//Maintain both users in group and groups for user.
	var $groups=array();
	var $users=array();
	var $defGrp;
	
	function htgroup($file="",$defGrp = null) {
		if (isset($defGrp)) $this->defGrp = trim($defGrp);	
		htbase::htbase($file);
	}
	 			
    function getGroupsByUser() {
    	return $this->users;
    }
    
    function getGroupsForUser($user) {
    	return isset ($this->users[$user]) ? $this->users[$user] : false;
    }    
    
    function setGroupsForUser($user,$groups) {
    	
    	if (isset($this->defGrp) && !in_array($this->defGrp,$groups)) {
    		$groups = array_merge(array($this->defGrp),$groups);
    	}
    	
    	$this->users[$user]=$groups;
		$this->resetGroups();
    	
    	return $this->writeFile();    		
    }
    
    function delete($user,$writeFile = true) {
    	if (!is_array($user)) {
    		if (isset($this->users[$user])) {
    			unset($this->users[$user]);
    		}
    	} else {
    		foreach ($user as $aUser) {
    			$this->delete($aUser,false);
    		}
    	}
    	
    	if ($writeFile) {
    	    $this->resetGroups();
    		return $this->writeFile();
    	}
    	
    	return true;
    
    }

    //reset groups array from users array. Will delete group 
    /*private*/ function resetGroups() {
    	$this->groups = array();
    	foreach ($this->users as $user => $groups) {
    		foreach ($groups as $group) {
    			$this->groups[$group][]=$user;
    		}
    	}
    }
	/*protected*/ function loadFile ()
	{
		$this->groups = array();
		$this->users = array();
		
		if (!file_exists($this->htFile())) return;
		
		$lines = file($this->htFile());
		foreach ($lines as $line) {
			$line = preg_replace('/#.*$/', '', $line); //ignore comments
			$line = trim($line);
			if (empty ($line)) continue;
			$row = split(":", $line, 2);
			$group = trim($row[0]);
			if (empty ($group)) continue;
			
			if ($group == $this->defGrp) continue;
			
			$users_in_group = preg_split("'\s'", $row[1]);
			foreach ($users_in_group as $user) {
				if (empty ($user))
					continue;
				
				if (isset($this->defGrp) && !array_key_exists($user,$this->users)) {
					$this->users[$user][] = $this->defGrp;					
				}
				
				$this->groups[$group][] = $user;	
				$this->users[$user][] = $group;
			}

		}
		
	}
	
	/*protected*/ function writeFile ()
	{	
		if (!$this->htFile()) return false;
			
		$fd = fopen( $this->htFile(), "w" );

		foreach ($this->groups as $group => $users) {
			
			if ($group == $this->defGrp) continue;
			
			fwrite($fd,"$group:");
			foreach($users as $user) {
				fwrite($fd," $user");
			}
			fwrite($fd,"\n");
		}

		fclose( $fd );
		return true;
	}
}

class htpasswd extends htbase {
	
	/*private*/ var $users = null;	//Array of [$userId]=cryptpass

	function getUsers() {
		return $this->users;	
	}

    function isUser($user) {
    	return array_key_exists($user,$this->users);
    }
    
	function verifyUser ($UserID,$clearPass)
	{
		$pass = "";
		$match = false;
		$salt = "";

		if (empty($UserID))				{ return false; }
		if (empty($clearPass))				{ return false; }

		$pass = $this->users[$UserID];
		$salt = substr($pass,0,2);
		$cryptPass =	$this->cryptPass($clearPass,$salt); 

		if ($pass == $cryptPass)
		{
			$match = true;
		}

		return $match;

    } 

	
	function changePass ($UserID, $newPass, $oldPass = "")
	{
	
		if (empty($UserID))				{ return false; }

		if (!($this->isUser($UserID)))
		{
			return false;
		}

		if(empty($newPass))
		{
			$this->error("changePass failure - no new password submitted",0);
			return false;
		}

		$checkname = strtolower($UserID);
		$checkpass = strtolower($newPass);

		if($checkname == $checkpass)
		{
			$this->error("changePass failure: UserID and password cannot be the same",0);
			return false;
		}


		if(!(empty($oldPass)))
		{
			if (!($this->verifyUser($UserID,$oldPass)))
			{
				$this->error("changePass failure for [$UserID] : Authentication Failed",0);
				return false;
			}

			if($newPass == $oldPass)
			{
				// Passwords are the same, no sense wasting time here			
				return true;
			}
		}

		$this->users[$UserID] = $this->cryptPass($newPass);

		return $this->writeFile();

    } 

	function renameUser ($OldID, $NewID, $writeFile = true)
	{
		if(!$this->isUser($OldID)) {
			$this->error("Cannot change userid, [$OldID] does not exist",0);
		}
		
		if($this->isUser($NewID))
		{
			$this->error("Cannot change UserID, [$NewID] already exists",0);
			return false;
		}	
		$oldCrypt = $this->users[$OldID];
		unset($this->users[$OldID]);
		$this->users[$NewID]=$oldCrypt;
		
		if ($writeFile) {
			return $this->writeFile();
		}
		
		return true;
			
    }

	function addUser ($UserID, $newPass, $writeFile=true)
	{

		if(empty($UserID))
		{
			$this->error("addUser fail. No UserID",0);
			return false;
		}
		if(empty($newPass))
		{
			$this->error("addUser fail. No password",0);
			return false;
		}

		if($this->isUser($UserID))
		{
			$this->error("addUser fail. UserID already exists",0);
			return false;
        }

		$this->users[$UserID] = $this->cryptPass($newPass);

		if ($writeFile) {
		if(!($this->writeFile()))
			{
				$this->error("FATAL could not add user due to file error! [$php_errormsg]",1);
				exit;	// Just in case
			}
		}
		// Successfully added user

		return true;

    } 
    
	function delete($users, $writeFile=true)
	{
		if (!is_array($users)) {
			$users = array($users);
		}

		if (empty($users)) {
			return false;
		}
		
		$oldUsers = $this->users;
		$this->users=array();
		foreach ($oldUsers as $user => $userinfo) {
			if (!in_array($user,$users)) {
				$this->users[$user]=$userinfo;
			}
		}
		
		return $this->writeFile();
		
    } 

	/*protected*/ function loadFile ()
	{

		$this->users = array();
		
		if (!file_exists($this->htFile())) return;
		
		$lines = file($this->htFile());
		
		if (!$lines) {
			return;
		}
		
		foreach ($lines as $line) {
			$line = preg_replace('/#.*$/','',$line); //ignore comments		
			list($user,$pass) = split(":",$line,2);
			$user=trim($user);
			$pass=trim($pass);
			if (!empty($user)) {
				$this->users[$user]=$pass;
			}
		}

		
	}

	/*protected*/ function writeFile ()
	{
		
		if (!$this->htFile()) return false;
		
		$fd = fopen( $this->htFile(), "w" );

		foreach ($this->users as $user => $cryptPass) {
			
			fwrite($fd, "$user:$cryptPass\n");
		}

		fclose( $fd );

		return true;
	}
	
	/*private*/ function cryptPass ($passwd, $salt = "")
	{
		if (!($passwd))
		{
			return "";
		}

		if (!empty($salt))
		{
			$salt = substr ($salt, 0, 2);
		}
		else
		{
			$salt = $this->genSalt();
		}

		return (crypt($passwd, $salt));

	}	
	/*private*/ function genSalt ()
	{
		$random = 0;
		$rand64 = "";
		$salt = "";

		$random=rand();	
		
		$rand64= "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$salt=substr($rand64,$random  %  64,1).substr($rand64,($random/64) % 64,1);
		$salt=substr($salt,0,2); // Just in case

		return($salt);

	}


} 

class htuser extends htbase {

	/*private*/ var $users = array(); //Array = [$user][name|mail] = value
		
	function getUsers() {
			
		return $this->users;
	}
	
	function getUserInfo($user) {
		return isset ($this->users[$user]) ? $this->users[$user] : false;
	}
	
    function isUser($user) {
    	return isset($this->users[$user]);
    }

	function addUser ($UserID, $name, $mail, $writeFile=true)
	{

		if(empty($UserID))
		{
			$this->error("add htUser fail. No UserID",0);
			return false;
		}
		
		if($this->isUser($UserID))
		{
			$this->error("add htUser fail. UserID already exists",0);
			return false;
        }

		$this->users[$UserID]['name'] = $name;
		$this->users[$UserID]['mail'] = $mail;
		
		if ($writeFile) {
			return $this->writeFile();
		}
    } 

	
	function delete($users, $writeFile=true)
	{
		if (!is_array($users)) {
			$users = array($users);
		}

		if (empty($users)) {
			return false;
		}
		
		$oldUsers = $this->users;
		$this->users=array();
		foreach ($oldUsers as $user => $userinfo) {
			if (!in_array($user,$users)) {
				$this->users[$user]=$userinfo;
			}
		}
		
		if ($writeFile) {
			return $this->writeFile();
		}
		
		return true;
    } 

    function modify($user,$changes,$writeFile=true) {
    	
    	if ($this->isUser($user)) {
    		$changes = array_merge($this->users[$user],$changes);
    	}
    	
  		$this->users[$user] = $changes; 
  		
  		if ($writeFile) {
  			return $this->writeFile();
  		}
  		
  		return true;    	    
    }
    
	/*protected*/ function loadFile ()
	{

		$this->users = array();
		
		if (!file_exists($this->htFile())) return;
		
		$lines = file($this->htFile());
		
		foreach ($lines as $line) {
			$line = preg_replace('/#.*$/','',$line); //ignore comments		
			list($user,$name,$mail) = split(":",$line,3);
			$user=trim($user);
			$name=trim($name);
			$mail=trim($mail);
			if (!empty($user)) {
				$this->users[$user]['name']=$name;
				$this->users[$user]['mail']=$mail;
			}
		}
		
	}
	    	
    /*protected*/ function writeFile ()
	{
	
		if (!$this->htFile()) return false;
		
		$fd = fopen( $this->htFile(), "w" );

		foreach ($this->users as $user => $userInfo) {
			$name = $userInfo['name'];
			$mail = $userInfo['mail'];
			fwrite($fd, "$user:$name:$mail\n");
		}

		fclose( $fd );
		return true;
	}

    
}

