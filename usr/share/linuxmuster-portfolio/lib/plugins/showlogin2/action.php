<?php
/**
 * Dokuwiki Action Plugin: Show Login Page on "Access Denied"
 *
 * @author Oliver Geisen <oliver.geisen@kreisbote.de>
 * @author Klaus Vormweg <klaus.vormweg@gmx.de>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_showlogin2 extends DokuWiki_Action_Plugin {

  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Klaus Vormweg',
      'email'  => 'klaus.vormweg@gmx.de',
      'date'   => '2014-02-23',
      'name'   => 'Show Login 2',
      'desc'   => 'If access to page is denied, show login form to users not already logged in.',
      'url'    => 'http://www.tuhh.de/~psvkv/dokuwiki/showlogin2.tar.gz',
    );
  }

  /**
   * Register its handlers with the dokuwiki's event controller
   */
  function register(Doku_Event_Handler $controller) {
    # TPL_CONTENT_DISPLAY is called before and after content of wikipage
    # is written to output buffer
    if(!$this->getConf('show_denied')) {
      $controller->register_hook(
        'TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'showlogin2'
      );
    } else {
      $controller->register_hook(
        'TPL_CONTENT_DISPLAY', 'AFTER', $this, 'showlogin2'
      );
    }
  }

  /**
   * Handle the event
   */
  function showlogin2(&$event, $param) {
    global $ACT;
    global $ID;

    # add login form to page, only on access denied
    # and if user is not logged in
    if (($ACT == 'denied') && (! $_SERVER['REMOTE_USER'])) {
      if(!$this->getConf('show_denied')) {
        $event->preventDefault();
      }
      html_login();
    }
  }
}

