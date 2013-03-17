<?php
/**
 * Super class for auth backends that only do simple authentication. 
 * 
 * Subclasses need only implement checkPassword() 
 * 
 * Assumes there is no way to test for existance of a user, every request for a specific user returns a default user.
 *
 * A password will only be set if the checkPass method has been previously called successfully for that user.
 *
 *
 * Configuration
 
  $conf['auth']['maildomain'] = 'localhost'; # user email will be set to <user>@defaultDomain
  $conf['defaultgroup'] = 'somegroup'; # every user will be a member of this group
  
  $conf['auth']['debug'] = false; # debugging
  $conf['auth']['debug_hidden'] = false; #hide debug in html output @see dbg() in infoutils
  $conf['auth']['debug_to_file'] = false; #also debug to file @see dbglog in infoutils
 *
 * 
 * @license GPL2 http://www.gnu.org/licenses/gpl.html
 * @author  Grant Gardner <grant@lastweekend.com.au>
 * @version 0.3
 */
 
 
define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/basic.class.php');
 
/*abstract*/ class auth_simple extends auth_basic {
 
    var $debug = false;
    var $debug_hidden = false;
    var $debug_file = false;
  
    /**
     * Constructor
     *
     */
    function auth_simple() {
        global $conf;
        
        // Call parent constructor
        if (method_exists($this, 'auth_basic'))
            parent::auth_basic();
        
        $this->debug = !empty($conf['auth']['debug']);
        $this->debug_hidden = !empty($conf['auth']['debug_hidden']);
        $this->debug_file = !empty($conf['auth']['debug_to_file']);
    }
 
    /**
     * Checks the provided username and password using checkPassword().
     *
     * Stores user/password combination in a hash for later use with getUserData
     *
     * Only override this if you also provide a different implementation for getUserData
     */
    function checkPass( $user, $pass )
    {
     
      if( $this->checkPassword($user,$pass )) {
          $this->passwords[$user] = $pass;
          return true;
      }
 
      return false; 
    }



    /**
     * To be implemented by subclasses
     * @param   string  $user   Username
     * @param   string  $pass   Password
     * @return  boolean True if authentication is successful
     */
/*abstract*/ function checkPassword ($user, $pass)
    {
        $this->show_error("checkPassword is abstract",-1);
        return false;
    }
    
    /**
     * 
     * $userinfo = $this->getDefaultUser($user);
     * $userinfo['xxx'] = your more specific value for this data
     * return $userinfo;
     */
    function getUserData($user)
    {
	    return $this->getDefaultUser($user);
    }
    
   /**
   * Provides default values for user
   *
   * name - default name for the user
   * pass - users password if previously called checkPass
   * mail - user (@$conf['maildomain'])
   * grps - array($conf['defaultgroup']
   *
   * @return  array containing user data 
   */

    function getDefaultUser($user) {
    
      global $conf;
      
      $pass = $this->passwords[$user];
      $grps = array($conf['defaultgroup']);
      $name = $user;
        
      $defaultDomain = empty($conf['auth']['maildomain']) ? "localhost" : $conf['auth']['maildomain'];
      $mail = $user."@".$defaultDomain;
      return compact("pass","grps","name","mail");
    }
 
    function show_error($msg) {
      msg($msg,-1);  
    }
    
    function log_debug() {
      
      if ($this->debug) {
          $args = func_get_args();
          $msg = array_shift($args);
          
          if (count($args) > 0) {
              $msg = vsprintf($msg,$args);
          }

          if (headers_sent()) {
            dbg($msg,$this->debug_hidden);
          } elseif (!$this->debug_hidden) {
            msg($msg,0);
          }
          
          if ($this->debug_file) {
            dbglog($msg);
          }
      }
      
    }
}
