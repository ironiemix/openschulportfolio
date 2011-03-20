<?php
/**
 * Mock external authentication backend.
 * 
 * Simple backend that supports external authentication by looking at the request parameter "User"
 * 
 * Good for testing and not much else
 *
 * Configuration: None.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Grant Gardner <grant@lastweekend.com.au>
 * @version    0.4
 */

 define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/simple.class.php');

class auth_mock extends auth_simple {

    /**
     * Constructor
     *
     * Carry out sanity checks to ensure the object is
     * able to operate. Set capabilities.
     *
     */
    function auth_mock() {
      
       // Call parent constructor
       if (method_exists($this, 'auth_simple')) {
          parent::auth_simple();
       }
        
       $this->cando['external'] = true;
       $this->cando['logoff'] = true;
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
    
    if (isset($_SERVER['HTTP_X_MOCK_DW_AUTH'])) {
      $user = $_SERVER['HTTP_X_MOCK_DW_AUTH'];
    
      $USERINFO = $this->getUserData($user);
    
      $_SERVER['REMOTE_USER'] = $user;
      $_SESSION[DOKU_COOKIE]['auth']['user'] = $user;
      $_SESSION[DOKU_COOKIE]['auth']['pass'] = "";
      $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
      
      return true;
      
    } else {
      return false;
    }
  }

  function logOff(){
    $this->log_debug("Logging off from mock");
  }
}

