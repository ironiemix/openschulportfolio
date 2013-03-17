<?php
/**
 * HTTP Basic Authentication backend,
 * 
 * This backend only attempts to provide a logoff function to httpexternal where basic authentication is used.
 * It does not send the WWW:Authenticate headers at login, you need to configure your webserver to do that and
 * the set $conf['httpbasic_realm'] to be the same as the realm used by your webserver configuration.
 *
 * Configuration
   $conf['auth']['httpbasic']['realm'] = 'dokuwiki' # must match the realm used for BASIC auth of your webserver.
   $conf['auth']['httpbasic']['logout'] = '' # custom logout message (may not be displayed in all browsers)
 *
 * NOTES:
 * 
 *  - Logout is not strictly possible with BASIC auth (see apache docs), and this is a very poor user unfriendly alternative.
 *  @TODO try out the techniques mentioned here http://www.eecho.info/Echo/apache/basic-authentication/
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Grant Gardner <grant@lastweekend.com.au>
 * @version    0.3
 */

 define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/http.class.php');

class auth_httpbasic extends auth_http {

  var $realm = null;
  var $logoutMsg = null;

  /**
   * Constructor
   */
  function auth_httpbasic() {
    global $conf;
    
    if (method_exists($this, 'auth_http')) {
      parent::auth_http();
    }
    
    $this->realm = $conf['auth']['httpbasic']['realm'];
    
    if (!empty($this->realm) && $this->canDo('external')) {
      $this->log_debug("Enabling logoff for realm %s",$this->realm);
      $this->cando['logoff'] = true;
    }
    
    if (isset ($conf['auth']['httpbasic']['logout'])) {
        $this->logoutMsg = $conf['auth']['httpbasic']['logout'];
    } else {
      $this->logoutMsg = 'Successful logout. Retry login <a href="' . DOKU_BASE . '">here</a>';
    }

  }

	
	function logOff() {
    
  //This will cause most browsers to throw up a login box, if you "cancel" the credentials are removed.
    header('WWW-Authenticate: Basic realm="' . $this->realm . '"');
    header('HTTP/1.0 401 Unauthorized');
    $this->log_debug("logging out with message=%s",$this->logoutMsg);
    #print ($this->logoutMsg);
    flush();
    exit;
	}

}

