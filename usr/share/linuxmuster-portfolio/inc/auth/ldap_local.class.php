<?php
/**
 * LDAP authentication backend with local ACL
 *
 * @license   GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author    Andreas Gohr <andi@splitbrain.org>
 * @author    Chris Smith <chris@jalakaic.co.uk>
 * @author    Klaus Vormweg <klaus.vormweg@gmx.de>
 */
define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/ldap.class.php');

define('AUTH_USERFILE',DOKU_CONF.'users.auth.php');

class auth_ldap_local extends auth_ldap {
    var $cnf = null;
    var $con = null;
    var $bound = 0; // 0: anonymous, 1: user, 2: superuser

    /**
     * Constructor
     *
     * Carry out sanity checks to ensure the object is
     * able to operate. Set capabilities.
     *
     * @author  Christopher Smith <chris@jalakai.co.uk>
     */
    function auth_ldap_local(){
        global $conf;
        $this->cnf = $conf['auth']['ldap'];
        if (!@is_readable(AUTH_USERFILE)){
          $this->success = false;
        }else{
          if(@is_writable(AUTH_USERFILE)){
            $this->cando['addUser']      = true;
            $this->cando['delUser']      = true;
            $this->cando['modLogin']     = true;
            $this->cando['modPass']      = true;
            $this->cando['modName']      = true;
            $this->cando['modGroups']    = true;
            $this->cando['modMail']      = true;
          }
          $this->cando['getUsers']     = true;
          $this->cando['getUserCount'] = true;
       }
        // ldap extension is needed
        if(!function_exists('ldap_connect')) {
            if ($this->cnf['debug'])
                msg("LDAP err: PHP LDAP extension not found.",-1,__LINE__,__FILE__);
            $this->success = false;
            return;
        }

        if(empty($this->cnf['groupkey'])) $this->cnf['groupkey'] = 'cn';

        // try to connect
        if(!$this->_openLDAP()) $this->success = false;

        // auth_ldap currently just handles authentication, so no
        // capabilities are set
    }

    /**
     * Check user+password
     *
     * Checks if the given user exists and the given
     * plaintext password is correct by trying to bind
     * to the LDAP server
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @return  bool
     */
    function checkPass($user,$pass){
        // reject empty password
        if(empty($pass)) return false;
        if(!$this->_openLDAP()) return false;

        // check if local user exists
        if($this->users === null) $this->_loadUserData();
        if(!isset($this->users[$user])) return false;

        // indirect user bind
        if($this->cnf['binddn'] && $this->cnf['bindpw']){
            // use superuser credentials
            if(!@ldap_bind($this->con,$this->cnf['binddn'],$this->cnf['bindpw'])){
                if($this->cnf['debug'])
                    msg('LDAP bind as superuser: '.htmlspecialchars(ldap_error($this->con)),0,__LINE__,__FILE__);
                return false;
            }
            $this->bound = 2;
        }else if($this->cnf['binddn'] &&
                 $this->cnf['usertree'] &&
                 $this->cnf['userfilter']) {
            // special bind string
            $dn = $this->_makeFilter($this->cnf['binddn'],
                                     array('user'=>$user,'server'=>$this->cnf['server']));

        }else if(strpos($this->cnf['usertree'], '%{user}')) {
            // direct user bind
            $dn = $this->_makeFilter($this->cnf['usertree'],
                                     array('user'=>$user,'server'=>$this->cnf['server']));

        }else{
            // Anonymous bind
            if(!@ldap_bind($this->con)){
                msg("LDAP: can not bind anonymously",-1);
                if($this->cnf['debug'])
                    msg('LDAP anonymous bind: '.htmlspecialchars(ldap_error($this->con)),0,__LINE__,__FILE__);
                return false;
            }
        }

        // Try to bind to with the dn if we have one.
        if(!empty($dn)) {
            // User/Password bind
            if(!@ldap_bind($this->con,$dn,$pass)){
                if($this->cnf['debug']){
                    msg("LDAP: bind with $dn failed", -1,__LINE__,__FILE__);
                    msg('LDAP user dn bind: '.htmlspecialchars(ldap_error($this->con)),0);
                }
                return false;
            }
            $this->bound = 1;
            return true;
        }else{
            // See if we can find the user
            $info = $this->getUserData($user,true);
            if(empty($info['dn'])) {
                return false;
            } else {
                $dn = $info['dn'];
            }

            // Try to bind with the dn provided
            if(!@ldap_bind($this->con,$dn,$pass)){
                if($this->cnf['debug']){
                    msg("LDAP: bind with $dn failed", -1,__LINE__,__FILE__);
                    msg('LDAP user bind: '.htmlspecialchars(ldap_error($this->con)),0);
                }
                return false;
            }
            $this->bound = 1;
            return true;
        }

        return false;
    }

    /**
     * Return user info
     *
     * Returns info about the given user needs to contain
     * at least these fields:
     *
     * name string  full name of the user
     * mail string  email addres of the user
     * grps array   list of groups the user is in
     *
     * This LDAP specific function returns the following
     * addional fields:
     *
     * dn     string  distinguished name (DN)
     * uid    string  Posix User ID
     * inbind bool    for internal use - avoid loop in binding
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Trouble
     * @author  Dan Allen <dan.j.allen@gmail.com>
     * @author  <evaldas.auryla@pheur.org>
     * @author  Stephane Chazelas <stephane.chazelas@emerson.com>
     * @return  array containing user data or false
     */
    function getUserData($user,$inbind=false) {
        global $conf;
        if(!$this->_openLDAP()) return false;

        // force superuser bind if wanted and not bound as superuser yet
        if($this->cnf['binddn'] && $this->cnf['bindpw'] && $this->bound < 2){
            // use superuser credentials
            if(!@ldap_bind($this->con,$this->cnf['binddn'],$this->cnf['bindpw'])){
                if($this->cnf['debug'])
                    msg('LDAP bind as superuser: '.htmlspecialchars(ldap_error($this->con)),0,__LINE__,__FILE__);
                return false;
            }
            $this->bound = 2;
        }elseif($this->bound == 0 && !$inbind) {
            // in some cases getUserData is called outside the authentication workflow
            // eg. for sending email notification on subscribed pages. This data might not
            // be accessible anonymously, so we try to rebind the current user here
            $pass = PMA_blowfish_decrypt($_SESSION[DOKU_COOKIE]['auth']['pass'],auth_cookiesalt());
            $this->checkPass($_SESSION[DOKU_COOKIE]['auth']['user'], $pass);
        }

        $info['user']   = $user;
        $info['server'] = $this->cnf['server'];

        //get info for given user
        $base = $this->_makeFilter($this->cnf['usertree'], $info);
        if(!empty($this->cnf['userfilter'])) {
            $filter = $this->_makeFilter($this->cnf['userfilter'], $info);
        } else {
            $filter = "(ObjectClass=*)";
        }

        $sr     = @ldap_search($this->con, $base, $filter);
        $result = @ldap_get_entries($this->con, $sr);
        if($this->cnf['debug'])
            msg('LDAP user search: '.htmlspecialchars(ldap_error($this->con)),0,__LINE__,__FILE__);

        // Don't accept more or less than one response
        if($result['count'] != 1){
            return false; //user not found
        }

        $user_result = $result[0];
        ldap_free_result($sr);

        // general user info
        $info['dn']   = $user_result['dn'];
        $info['gid']  = $user_result['gidnumber'][0];
        $info['mail'] = $user_result['mail'][0];
        $info['name'] = $user_result['cn'][0];
        $info['grps'] = array();

        // overwrite if other attribs are specified.
        if(is_array($this->cnf['mapping'])){
            foreach($this->cnf['mapping'] as $localkey => $key) {
                if(is_array($key)) {
                    // use regexp to clean up user_result
                    list($key, $regexp) = each($key);
                    if($user_result[$key]) foreach($user_result[$key] as $grp){
                        if (preg_match($regexp,$grp,$match)) {
                            if($localkey == 'grps') {
                                $info[$localkey][] = $match[1];
                            } else {
                                $info[$localkey] = $match[1];
                            }
                        }
                    }
                } else {
                    $info[$localkey] = $user_result[$key][0];
                }
            }
        }
        $user_result = array_merge($info,$user_result);

        //get groups for given user if grouptree is given
        if ($this->cnf['grouptree'] && $this->cnf['groupfilter']) {
            $base   = $this->_makeFilter($this->cnf['grouptree'], $user_result);
            $filter = $this->_makeFilter($this->cnf['groupfilter'], $user_result);

            $sr = @ldap_search($this->con, $base, $filter, array($this->cnf['groupkey']));
            if(!$sr){
                msg("LDAP: Reading group memberships failed",-1);
                if($this->cnf['debug'])
                    msg('LDAP group search: '.htmlspecialchars(ldap_error($this->con)),0,__LINE__,__FILE__);
                return false;
            }
            $result = ldap_get_entries($this->con, $sr);
            ldap_free_result($sr);

            foreach($result as $grp){
                if(!empty($grp[$this->cnf['groupkey']][0])){
                    if($this->cnf['debug'])
                        msg('LDAP usergroup: '.htmlspecialchars($grp[$this->cnf['groupkey']][0]),0,__LINE__,__FILE__);
                    $info['grps'][] = $grp[$this->cnf['groupkey']][0];
                }
            }
        }
        // read local groups
        if($this->users === null) $this->_loadUserData();
        if(is_array($this->users[$user]['grps'])) {
            foreach($this->users[$user]['grps'] as $group) {
                if(in_array($group,$info['grps'])) continue;
                $info['grps'][] = $group;
            }
        }
        // add the default group to the list of groups if list is empty
        if(!count($info['grps'])) {
            $info['grps'][] = $conf['defaultgroup'];
        }
        return $info;
    }

    /**
     * Create a new User
     *
     * Returns false if the user already exists, null when an error
     * occurred and true if everything went well.
     *
     * The new user will be added to the default group by this
     * function if grps are not specified (default behaviour).
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Chris Smith <chris@jalakai.co.uk>
     */
    function createUser($user,$pwd,$name,$mail,$grps=null){
      global $conf;

      // local user mustn't already exist
      if($this->users === null) $this->_loadUserData();
      if(isset($this->users[$user])) return false;
      // but the user must exist in LDAP
      $info = $this->getUserData($user,true);
      if(empty($info['dn'])) return false;
      // fetch real name and email from LDAP
      $name = $info['name'];
      $mail = $info['mail'];
      $pass = '';

      // set default group if no groups specified
      if (!is_array($grps)) $grps = array($conf['defaultgroup']);

      // prepare user line
      $groups = join(',',$grps);
      $userline = join(':',array($user,$pass,$name,$mail,$groups))."\n";

      if (io_saveFile(AUTH_USERFILE,$userline,true)) {
        $this->users[$user] = compact('pass','name','mail','grps');
        return true;
      }

      msg('The '.AUTH_USERFILE.' file is not writable. Please inform the Wiki-Admin',-1);
      return null;
    }

    /**
     * Modify user data
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     * @param   $user      nick of the user to be changed
     * @param   $changes   array of field/value pairs to be changed (password will be clear text)
     * @return  bool
     */
    function modifyUser($user, $changes) {
      global $conf;
      global $ACT;
      global $INFO;

      // sanity checks, user must already exist and there must be something to change
      if (!is_array($changes) || !count($changes)) return true;
      if($this->users === null) $this->_loadUserData();
      if(!isset($this->users[$user])) return false;

      $userinfo = $this->getUserData($user, true);

      // update userinfo with new data, remembering to encrypt any password
      $newuser = $user;
      foreach ($changes as $field => $value) {
        if ($field == 'user') {
          $newuser = $value;
          continue;
        }
#        if ($field == 'pass') $value = auth_cryptPassword('password');
        $userinfo[$field] = $value;
      }

      $groups = join(',',$userinfo['grps']);
      $userline = join(':',array($newuser, 'pass', $userinfo['name'], $userinfo['mail'], $groups))."\n";
      if (!$this->deleteUsers(array($user))) {
        msg('Unable to modify user data. Please inform the Wiki-Admin',-1);
        return false;
      }

      if (!io_saveFile(AUTH_USERFILE,$userline,true)) {
        msg('There was an error modifying your user data. You should register again.',-1);
        // FIXME, user has been deleted but not recreated, should force a logout and redirect to login page
        $ACT == 'register';
        return false;
      }

      $this->users[$newuser] = $userinfo;
      return true;
    }

    /**
     *  Remove one or more users from the list of registered users
     *
     *  @author  Christopher Smith <chris@jalakai.co.uk>
     *  @param   array  $users   array of users to be deleted
     *  @return  int             the number of users deleted
     */
    function deleteUsers($users) {

      if (!is_array($users) || empty($users)) return 0;

      if ($this->users === null) $this->_loadUserData();

      $deleted = array();
      foreach ($users as $user) {
        if (isset($this->users[$user])) $deleted[] = preg_quote($user,'/');
      }

      if (empty($deleted)) return 0;

      $pattern = '/^('.join('|',$deleted).'):/';

      if (io_deleteFromFile(AUTH_USERFILE,$pattern,true)) {
        foreach ($deleted as $user) unset($this->users[$user]);
        return count($deleted);
      }

      // problem deleting, reload the user list and count the difference
      $count = count($this->users);
      $this->_loadUserData();
      $count -= count($this->users);
      return $count;
    }

    /**
     * Return a count of the number of user which meet $filter criteria
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     */
    function getUserCount($filter=array()) {

      if($this->users === null) $this->_loadUserData();

      if (!count($filter)) return count($this->users);

      $count = 0;
      $this->_constructPattern($filter);

      foreach ($this->users as $user => $info) {
          $count += $this->_filter($user, $info);
      }

      return $count;
    }

    /**
     * Bulk retrieval of user data
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     * @param   start     index of first user to be returned
     * @param   limit     max number of users to be returned
     * @param   filter    array of field/pattern pairs
     * @return  array of userinfo (refer getUserData for internal userinfo details)
     */
    function retrieveUsers($start=0,$limit=0,$filter=array()) {

      if ($this->users === null) $this->_loadUserData();

      ksort($this->users);

      $i = 0;
      $count = 0;
      $out = array();
      $this->_constructPattern($filter);

      foreach ($this->users as $user => $info) {
        if ($this->_filter($user, $info)) {
          if ($i >= $start) {
            $out[$user] = $info;
            $count++;
            if (($limit > 0) && ($count >= $limit)) break;
          }
          $i++;
        }
      }

      return $out;
    }

    /**
     * Load all user data
     *
     * loads the user file into a datastructure
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     */
    function _loadUserData(){
      $this->users = array();

      if(!@file_exists(AUTH_USERFILE)) return;

      $lines = file(AUTH_USERFILE);
      foreach($lines as $line){
        $line = preg_replace('/#.*$/','',$line); //ignore comments
        $line = trim($line);
        if(empty($line)) continue;

        $row    = split(":",$line,5);
        $groups = split(",",$row[4]);

        $this->users[$row[0]]['pass'] = $row[1];
        $this->users[$row[0]]['name'] = urldecode($row[2]);
        $this->users[$row[0]]['mail'] = $row[3];
        $this->users[$row[0]]['grps'] = $groups;
      }
    }

    /**
     * return 1 if $user + $info match $filter criteria, 0 otherwise
     *
     * @author   Chris Smith <chris@jalakai.co.uk>
     */
    function _filter($user, $info) {
        // FIXME
        foreach ($this->_pattern as $item => $pattern) {
            if ($item == 'user') {
                if (!preg_match($pattern, $user)) return 0;
            } else if ($item == 'grps') {
                if (!count(preg_grep($pattern, $info['grps']))) return 0;
            } else {
                if (!preg_match($pattern, $info[$item])) return 0;
            }
        }
        return 1;
    }

    function _constructPattern($filter) {
      $this->_pattern = array();
      foreach ($filter as $item => $pattern) {
//        $this->_pattern[$item] = '/'.preg_quote($pattern,"/").'/i';          // don't allow regex characters
        $this->_pattern[$item] = '/'.str_replace('/','\/',$pattern).'/i';    // allow regex characters
      }
    }
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
