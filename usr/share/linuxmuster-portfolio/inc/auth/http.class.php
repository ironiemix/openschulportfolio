<?php
/**
 * HTTP External Authentication backend
 * 
 * Simple backend that supports external authentication via PHP_AUTH_USER and PHP_AUTH_PW
 *
 * Configuration: None.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Grant Gardner <grant@lastweekend.com.au>
 * @version    0.3
 */

 define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/simple.class.php');

class auth_http extends auth_simple {

    /**
     * Constructor
     *
     * Carry out sanity checks to ensure the object is
     * able to operate. Set capabilities.
     *
     */
    function auth_http() {
      
       // Call parent constructor
       if (method_exists($this, 'auth_simple')) {
          parent::auth_simple();
       }
        
      if (isset ($_SERVER['PHP_AUTH_USER']) and isset ($_SERVER['PHP_AUTH_PW'])) {			
        $this->cando['external'] = true;
      } else {
        $this->log_debug("PHP_AUTH_ vars not set!");
      }

    }
    
    /**
     * Confirm user and password matches the external parameters
     * Note. subclasses may override this to verify the password externally
     */
    function checkPassword($user,$pass) {
       return (!empty($user) && !empty($pass) && $user == $_SERVER['PHP_AUTH_USER'] && $pass = $_SERVER['PHP_AUTH_PW']);
    }
    
    /**
   *
   * @see auth_login()
   *
   * @param   string  $user    Username
   * @param   string  $pass    Cleartext Password
   * @param   bool    $sticky  Cookie should not expire (rememberme)
   * @return  bool             true on successful auth
   */
  function trustExternal($user,$pass,$sticky=false){
    global $USERINFO;
    global $conf;
    
    //Ignore what is passed in in favour of the external parameters.
    $user = $_SERVER['PHP_AUTH_USER'];
    $pass = $_SERVER['PHP_AUTH_PW'];
    
    //Verify user/pass
    if ($this->checkPassword($user,$pass)) {
      
      $USERINFO = $this->getUserData($user);
      $USERINFO['pass'] = $pass;
  
      $_SERVER['REMOTE_USER'] = $user;
      $_SESSION[DOKU_COOKIE]['auth']['user'] = $user;
      $_SESSION[DOKU_COOKIE]['auth']['pass'] = $pass;
      $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
      
      return true;
      
    } else {
      return false;
    }
  }

}

