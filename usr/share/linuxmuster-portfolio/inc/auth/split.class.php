<?php
/**
 * Split authentication/authorisation backend
 * 
 * Delegates authentication to one backend (login auth), authorisation to another (groups auth)
 *
 * Typical scenario is your corporate LDAP is authoritative for passwords, names, email addresses but you need to
 * manage groups independantly. (login=ldap,groups=plain|mysql)
 
 * OR You've got a large user base and you want to use one of the backends that provide for authentication only
 * and you want to use mysql for the groups (login=pam|radius|ntlm|imap,groups=mysql). 
 *
 * 
 * ** Profile integration **
 * If auth login supports external and does not implement checkPass then $conf['profileconfirm'] should be false,
 * to hide the current password check
 *  
 * **User Manager Integration**
 *
 * List of users comes from groups auth unless $conf['auth']['split']['use_login_auth_for_users'] is set true.
 * 
 * Create/modify/delete are delegated to both backends if they are capable. It is possible to modify a user that exists in
 * only one backend in which case it will be created in the other.
 *
 * Obviously this is not transactional so you may find difficulties if errors occur on one but not the other.
 * 
 * Also possible that the authoritative backend for password, name, or email does not accept updates but the non-authoritative one does, the
 * updates will be successful but you won't see the result.
 *
 * 
 *
 * ** Configuration **
  $conf['auth']['split']['login_auth']   # the auth backend for authentication
  $conf['auth']['split']['groups_auth']  # the auth backend that supplies groups
  $conf['auth']['split']['merge_groups'] = false # should groups from login auth also be included
  $conf['auth']['split']['use_login_auth_for_users'] = false # Should login auth be used for supplying the list of users for usermanager
  $conf['auth']['split']['use_login_auth_for_name'] = false # Should login auth supply user name, or only used if groups auth provides an empty name
  $conf['auth']['split']['use_login_auth_for_mail'] = false # Should login auth supply email address, or only used if groups auth provides empty email.
 *
 * TODO: should useSessionCache be implemented?
 *
 * See also "chained" auth backed, where a list of backends is searched until the user is found.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version    0.1
 * @author     Grant Gardner <grant@lastweekend.com.au>
 */

 define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/simple.class.php');

class auth_split extends auth_simple {

     var $authForLogin = null;
     var $authForGroups = null;
     
     var $useLoginAuthForUserList = false;
     var $useLoginAuthForName = false;
     var $useLoginAuthForMail = false;
     var $mergeGroups = false;
    
    /**
     * Constructor
     *
     * Carry out sanity checks to ensure the object is
     * able to operate. Set capabilities.
     *
     */
    function auth_split() {
      global $conf;
      
      // Call parent constructor
        if (method_exists($this, 'auth_simple'))
            parent::auth_simple();
       
      
      $login_authname = $conf['auth']['split']['login_auth'];
      $groups_authname = $conf['auth']['split']['groups_auth'];
      $this->authForLogin = $this->createAuth($login_authname);
      $this->authForGroups = $this->createAuth($groups_authname);


      if ($this->authForLogin === null || $this->authForGroups === null) {
        msg("Unable to create backends $login_authname, $groups_authname",-1);
	      $this->success = false;
	      return;
      }
       
      $this->useLoginAuthForName = !empty($conf['auth']['split']['use_login_auth_for_name']);
      $this->useLoginAuthForMail = !empty($conf['auth']['split']['use_login_auth_for_mail']);
      $this->mergeGroups = !empty($conf['auth']['split']['merge_groups']);
      
      $this->useLoginAuthForUserList = !empty($conf['auth']['split']['use_login_auth_for_users']);
     
/*    #Kept in case we decide to prefer this technique in favour of overriding the canDo method below.
      #Can add/delete from either... Probably a bit stupid if one backend only allows add and the other only del
      $this->cando['addUser'] = ($this->authForGroups->canDo('addUser') or $this->authForLogin->canDo('addUser'));
      $this->cando['delUser'] = ($this->authForGroups->canDo('delUser') or $this->authForLogin->canDo('delUser'));
      $this->cando['modLogin'] = ($this->authForGroups->canDo('modLogin') or $this->authForLogin->canDo('modLogin'));

      #Password, Name and Mail will be tried on both
      $this->cando['modPass'] = ($this->authForLogin->canDo('modPass') or $this->authForGroups->canDo('modPass'));
      $this->cando['modName'] = ($this->authForGroups->canDo('modName') or $this->authForLogin->canDo('modName'));
      $this->cando['modMail'] = ($this->authForGroups->canDo('modMail') or $this->authForLogin->canDo('modMail'));
      
      #External/Logoff only sensible for auth functions
      $this->cando['external'] = $this->authForLogin->canDo('external');
      $this->cando['logoff'] = $this->authForLogin->canDo('logoff');
      
      #Groups. Better come from auth groups
      $this->cando['modGroups'] = $this->authForGroups->canDo('modGroups');
      $this->cando['getGroups'] = $this->authForGroups->canDo('getGroups');
            
      $this->cando['getUsers'] = $this->authForUserList->canDo('getUsers');
      $this->cando['getUserCount'] = $this->authForUserList->canDo('getUserCount');
*/          
         
      $this->success = true;
    }

  /**
   * Capability check. 
   *
   * Not supposed to override this but what we canDo depends entirely on what our delegated auths canDo. 
   *
   *
   * @return  bool
   */
  function canDo($cap) {
    switch($cap){
      case 'Profile':
        // can at least one of the user's properties be changed?
        //Not we recurse into this method rather than checking the capability array. If superclass did this
        //we could delegate to that method.
        return ( $this->canDo('modPass')  ||
                 $this->cando('modName')  ||
                 $this->cando('modMail') );
        break;
      case 'UserMod':
        // can at least anything be changed?
        return ( $this->canDo('modPass')   ||
                 $this->canDo('modName')   ||
                 $this->canDo('modMail')   ||
                 $this->canDo('modLogin')  ||
                 $this->canDo('modGroups') ||
                 $this->canDo('modMail') );
        break;
        
      case 'modLogin':
      case 'modPass':
      case 'modName':
      case 'modMail':
      case 'addUser':
      case 'delUser':
          //OK if supported by one or other delegates
        return ($this->authForLogin->canDo($cap) || $this->authForGroups->canDo($cap));
          
        break;
            
      case 'external':
      case 'logoff':
        //delegated to login auth
        return ($this->authForLogin->canDo($cap));
        break; #surely not necessary if we have returned?
      case 'modGroups':
      case 'getGroups':
        //delegated to groups auth
        return ($this->authForGroups->canDo($cap));
        break;
          
      case 'getUsers':
      case 'getUserCount':
        //delegation depends on config set in constructor
        return ($this->authForUserList()->canDo($cap));
        break;
        
      default:
          msg("Check for unsupported capability '$cap' - Maybe dokuwiki has been upgraded?",-1);
          return false;
      }
    }
  
    function createAuth($auth_name)  {
      $auth_classfile=DOKU_INC.'inc/auth/'.$auth_name.'.class.php';
      
      if (file_exists($auth_classfile)) {
        require_once($auth_classfile);
      } else { 
        msg("$auth_classfile does not exist",-1);
        return null;
      }
      
      
      $auth_class = "auth_".$auth_name;
      
      if (class_exists($auth_class)) {
         return new $auth_class();
      } else {
        msg("Class $auth_class does not exist",-1);
        return null;
      }
    }
     
  /**
    this will set global variables (really auth.php should be changed so that
    trustExternal returns true/false and then $USERINFO is set from getUserData
      
  **/
  function trustExternal($user,$pass,$sticky=false){
    global $USERINFO;
    
    $result = $this->authForLogin->trustExternal($user,$pass,$sticky);
    
    if ($result) {
      //Get user data, assume the login has set all the other appropriate globals
      
      $userInfo = $this->getUserData($user);
      if (!empty($userInfo)) {
        $USERINFO=$userInfo;
      }
      
      //msg(print_r($USERINFO,true),0);
    }
    return $result;
  }
  
  
  /**
   * Get user data from both Login and Groups, then merge together
   * Possible to exist in one but not the other.
   */
  function getUserData($user) {
    $login_user = $this->authForLogin->getUserData($user);
    $groups_user = $this->authForGroups->getUserData($user);
    
      
    if (empty($login_user)) {
      //If login user supports "external", it doesn't have to return user data, so we use the groups data instead
      //but also possible for user not to exist in the groups auth
      return $groups_user;
    }
    
    if (!empty($groups_user)) {
      
      if (empty($login_user['pass'])) {
        $login_user['pass'] = $groups_user['pass'];
      }
      
      if (!$this->useLoginAuthForMail and !empty($groups_user['mail'])) {
        $login_user['mail'] = $groups_user['mail'];
      }
      
      if (!$this->useLoginAuthForName and !empty($groups_user['name'])) {
          $login_user['name'] = $groups_user['name'];
      }
      
      if ($this->mergeGroups and !empty($login_user['grps'])) {
        $login_user['grps'] = array_unique(array_merge((array) $login_user['grps'], (array) $groups_user['grps']));
      } else {
        $login_user['grps'] = $groups_user['grps'];
      }
       
    }
    
	  return $login_user;	
  }

  function checkPass($user,$pass) {
    ///turn off $conf['profileConfirm'] if auth supports external and does not implement checkPass
     return $this->authForLogin->checkPass($user,$pass);
  }
 
  function logOff(){
    return $this->authForLogin->logOff();
  }
  
  /**
   * We sort the changes and pass any to either backend if they indicate support, except for groups which only goes to the groups backend.
   * It is therefore possible to configure so that the changes are not written to the BE that provides the value.
   * 
   * It is possible that the user does not exist in one or the other backends and needs to be created
   *
   * This method is also called from create user
   */
  function modifyUser($user, $changes, $create=false) {
   
    $cap_map = array( "user" => "modLogin", "pass" => "modPass", "mail" => "modMail", "name" => "modName");
    $old_userdata = $this->getUserData($user);
    $login_userdata = $this->authForLogin->getUserData($user);
    $groups_userdata = $this->authForGroups->getUserData($user);
    
    //msg("user=".$user.", changes=".print_r($changes,true).", create=".$create,0);
    
    if ($create and !empty($login_userdata) and !empty($groups_userdata)) {
      //This is actually a create, and user already exists in both backends.
      return false;
    }
    
    foreach(array_keys($cap_map) as $chg) {
      if (
          ( empty($login_userdata) and $this->authForLogin->canDo('addUser') )
          or 
          ( !empty($login_userdata) and !empty($changes[$chg]) and $this->authForLogin->canDo($cap_map[$chg]) ) 
         ){
      
          $login_changes[$chg] = empty($changes[$chg]) ? $old_userdata[$chg] : $changes[$chg];
          
      }
      
      if (
          ( empty($groups_userdata) and $this->authForGroups->canDo('addUser') )
          or 
          (!empty($groups_userdata) and !empty($changes[$chg]) and $this->authForGroups->canDo($cap_map[$chg]))
         ){
         
        $groups_changes[$chg] = empty($changes[$chg]) ? $old_userdata[$chg] : $changes[$chg];
       
      }
    }
    
    if (!empty($changes['grps'])) {
        $groups_changes['grps'] = $changes['grps'];
    }
    
        
    $login_result = true;
    if (isset($login_changes)) {
        
        if (empty($login_userdata)) {
          $login_result =  $this->authForLogin->createUser($user,
                $login_changes['pass'], $login_changes['name'], $login_changes['mail'],null);
          msg("Login user added ".$user,0);
          
        } else {
          //msg("Updating login user".$user." ".print_r($login_changes,true),0);
          $login_result = $this->authForLogin->modifyUser($user,$login_changes);
        }
    }
    
    $groups_result = true;
    if (isset($groups_changes)) {
        if (empty($groups_userdata)) {
          $groups_result = $this->authForGroups->createUser($user,
                $groups_changes['pass'], $groups_changes['name'], $groups_changes['mail'], $groups_changes['grps']);
          msg("Groups user added ".$user,0);
          
        } else {
          $groups_result = $this->authForGroups->modifyUser($user,$groups_changes);
        }
    }
    
    if ($create and ($groups_result == null or $login_result == null)) {
      //Error in create
      return null;
    } else {
      return ($groups_result and $login_result);
    }
  }
  

  /**
   * All the logic for creating users exists in modify above so we do all the hard work there.
   */
  function createUser($user,$pass,$name,$mail,$grps=null) {

    $changes = compact("user","pass","name","mail","grps");
    
    return $this->modifyUser($user,$changes,true);
  }

  function deleteUsers($users) {
    $result = true;
    if ($this->authForLogin->canDo('delUser')) {
      $result = ($result and $this->authForLogin->deleteUsers($users));
    }
    
    if ($this->authForGroups->canDo('delUser')) {
      $result = ($result and $this->authForGroups->deleteUsers($users));
    }
    
    return $result;
  }

  function authForUserList() {
    return $this->useLoginAuthForUserList ? $this->authForLogin : $this->authForGroups; 
  }
  
  function getUserCount($filter=array()) {
    
    return $this->authForUserList()->getUserCount($filter);  
  }

  function retrieveUsers($start=0,$limit=-1,$filter=null) {
    return  $this->authForUserList()->retrieveUsers($start,$limit,$filter);
  }

  function addGroup($group) {
    return  $this->authForGroups->addGroup($group);
  }

  function retrieveGroups($start=0,$limit=0) {
    return  $this->authForGroups->retrieveGroups($start,$limit);
  }

}

