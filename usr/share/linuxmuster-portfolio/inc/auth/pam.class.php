<?php
/**
  PAM authentication backend
 
  Simple backend, only does authentication and password change (if supported and enabled)
 
  $conf['authtype'] = 'pam';
  $conf['auth']['pam']['chpass'] = false; #if enabled and module supports chpass, then user will be able to modify their own password
  @see simple.class.php for additional config.
 
  As we have no way to check if a user exists, every explicit test for a user returns a user.
  A password will only be set if the checkPass method has been previously called (successfully) for that user.
  
  
  @author  Grant Gardner <grant@lastweekend.com.au>
  @author  Michael Gorven <michael003+dokuwiki@gmail.com>
  @license GPL2 http://www.gnu.org/licenses/gpl.html
  @version 0.3
 */
 
# This class requires the PHP PAM module
# The Ubuntu package renames it to "pam_auth", so we check both
if ( !extension_loaded('pam') && !extension_loaded('pam_auth') )
	if ( !dl('pam.so') && !dl('pam_auth.so') )
    	msg( "PHP PAM module cannot be loaded", -1 );
 
define('DOKU_AUTH', dirname(__FILE__));
require_once(DOKU_AUTH.'/simple.class.php');
 
class auth_pam extends auth_simple
{
 
    /**
     * Constructor
     *
     */
    function auth_pam()
    {
        // Call parent constructor
        if (method_exists($this, 'auth_simple'))
            parent::auth_simple();
          
        
        global $ACT;
        if (!empty($conf['auth']['pam']['chpass']) && function_exists('pam_chpass') && $ACT != "admin" && $_REQUEST['page'] != "usermanager" ) {
           // assume we are in user profile land.
           $this->cando['modPass'] = true;     
        }
        
        $this->success = function_exists('pam_auth');

        
    }
 
    /**
     * Checks the provided username and password using PAM.
     *
     * @return  boolean True if authentication is successful
     */
    function checkPassword( $user, $pass )
    {
      
      if( pam_auth( $user, $pass, &$error ) ) {
          return true;
      }
 
      $this->log_debug("PAM error: $error for user $user");
      return false; 
    }
   
   /**
   * Modify user data [implement only where required/possible]
   *
   * Set the mod* capabilities according to the implemented features
   *
   * @param   $user      nick of the user to be changed
   * @param   $changes   array of field/value pairs to be changed (password will be clear text)
   * @return  bool
   */

    function modifyUser($user,$changes) 
    {
      $oldpassword = $this->passwords[$user];
      $newpassword = $changes['pass'];
      
      $result = false;
      
      if (!empty($newpassword) and !empty($oldpassword))
      {
        $result = pam_chpass( $user , $oldpassword, $newpassword ,   &$error );
        if (empty($result)) {
          $this->log_debug("PAM chpass: ".$error.", user=".$user);
        }
      }
      
      return $result;
    
    }
}
