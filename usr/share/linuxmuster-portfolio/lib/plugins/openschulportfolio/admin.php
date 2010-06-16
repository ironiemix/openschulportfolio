<?php
/**
 * Easy confiiguration of openschulportfolios design
 * only works with template "portfolio"
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */


if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_INCLUDE')) define('DOKU_INCLUDE',DOKU_INC.'inc/');
require_once(DOKU_PLUGIN . 'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_openschulportfolio extends DokuWiki_Admin_Plugin
{
var $state = 0;
var $backup = '';

	/**
	 * Constructor
	 */
	function admin_plugin_openschulportfolio()
	{
		$this->setupLocale();
	}

	/**
	 * return some info
	 */
	function getInfo()
	{
		return array(
			'author' => 'Frank Schiebel',
			'email'  => 'frank@linuxmuster.net',
			'date'   => '2010-05-25',
			'name'   => 'Configure design elements of openSchulportfolio',
			'desc'   => 'Allows to change logos an link-colors of openSchulportfolio without file access to the webspace.',
			'url'    => 'http://openschulportfolio.de/',
		);
	}

	/**
	 * return sort order for position in admin menu
	 */
	function getMenuSort()
	{
		return 999;
	}
	
	/**
	 *  return a menu prompt for the admin menu
	 *  NOT REQUIRED - its better to place $lang['menu'] string in localised string file
	 *  only use this function when you need to vary the string returned
	 */
	function getMenuText()
	{
		return 'openSchulportfolio Logos und Farben';
	}

	/**
	 * handle user request
	 */
	function handle()
	{
	}

	/**
	 * output appropriate html
	 */
	function html() {

	global $conf;

	if (preg_match("/etc\/linuxmuster-portfolio/",DOKU_CONF)) {
		// Wenn als Musterloesungspaket -> "usermod" in etc
        	$usermod_path = DOKU_CONF . "usermod/";
	} else {
		// Wenn als opensSchulportfolioarchiv -> "usermod" 
	 	// in /lib/tpl/portfolio
        	$usermod_path = DOKU_TPLINC . "usermod/";
	}

	$usermod_web_path = DOKU_TPL . "usermod/";
	

        $logo_file =  "header_logo.png";
        $topback_file = "header_back.png";


        ptln('<h1>Anpassungen openSchulportfolio</h2>');
        ptln('<h2>Logo Bild austauschen</h2>');
        ptln('<img style="float:right;width: 100px; border: 1px solid #aaa;" src="'.$usermod_web_path . $logo_file .'" />');
        ptln('<p>Laden Sie ein Bild von Ihrer Festplatte auf den Server um das Logo zu ersetzen. Das Logobild darf höchtens 120x100 Pixel groß sein und sollte im
        PNG Format vorliegen.');

        $error = 0;
        if($_FILES['logo'] == "") {
            ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
            ptln('	<input type="hidden" name="do"   value="admin" />');
            ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            ptln('  <input type="file" name="logo">');
            ptln('  <input type="submit" value="Neues Logo hochladen"> ');
            ptln(' </form>');
        } else {
          $dateityp = GetImageSize($_FILES['logo']['tmp_name']);
          if($dateityp[2] != 0) {
              if($_FILES['logo']['size'] <  102400) {
                    move_uploaded_file($_FILES['logo']['tmp_name'], $usermod_path.$logo_file);
                    ptln('<p></p><p style="color: #003d00;">Das Bild wurde erfolgreich ins Konfigurationsverzeichnis hochgeladen</p>');
              } else {
                    ptln('<p></p><p style="color: #f80000;">Es ist ein Fehler aufgetreten: Es dürfen nur Bilddateien hochgeladen werden, die kleiner als 100kB sind.</p>');
                    $error = 1;
              }
          } else { 
              ptln('<p></p><p style="color: #f80000;">Es ist ein Fehler aufgetreten: Es dürfen nur Bilddateien hochgeladen werden, die kleiner als 100kB sind.</p>');;
              $error = 1;
          }
        }

        // Reset Knopf
        if (!$error) {
              ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
              ptln('	<input type="hidden" name="do" value="admin" />');
              ptln('	<input type="hidden" name="osplogo" value="reset" />');
              ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
              ptln('  <input type="submit" value="Standardlogo wieder herstellen"> ');
              ptln(' </form>');
        } else {
              ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
              ptln('	<input type="hidden" name="do" value="admin" />');
              ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
              ptln('  <input type="submit" value="Weiter"> ');
              ptln(' </form>');
        }


        if ($_POST['osplogo'] == "reset" ) {
            if (!copy($usermod_path.$logo_file.".default", $usermod_path.$logo_file)) {
                echo "Hat nicht geklappt: $logo_file...\n";
            } else {
                ptln('<p></p><p style="color: #003d00;">Das Logo wurde durch das Standardlogo ersetzt - möglicherweise müssen Sie die Startseite neu laden, damit die
                Änderungen sichtbar werden.</p>');
            }

        }
       


        print "<p></p>"; 
        print "<p></p>"; 
        print "<p></p>"; 
        print "<p></p>"; 
        ptln('<h2>Hintergrundbild des oberen Menüs austauschen</h2>');
        ptln('<img style="float:right;width: 300px; border: 1px solid #aaa;" src="'.$usermod_web_path . $topback_file .'" />');
        ptln('<p>Laden Sie ein Bild von Ihrer Festplatte auf den Server um das Hintergrundbild für die obere Menüleiste zu ersetzen.');
        ptln('Das Hintergrundbild darf höchstens 970x100 Pixel groß sein und sollte im PNG Format vorliegen.');

        $error = 0;

        if($_FILES['topback'] == "") {
            ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
            ptln('	<input type="hidden" name="do"   value="admin" />');
            ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
            ptln('  <input type="file" name="topback">');
            ptln('  <input type="submit" value="Neuen Hintergrund hochladen"> ');
            ptln(' </form>');
        } else {
          $dateityp = GetImageSize($_FILES['topback']['tmp_name']);
          if($dateityp[2] != 0) {
              if($_FILES['topback']['size'] <  102400) {
                    move_uploaded_file($_FILES['topback']['tmp_name'], $usermod_path.$topback_file);
                    ptln('<p></p><p style="color: #003d00;">Das Bild wurde erfolgreich ins Konfigurationsverzeichnis hochgeladen</p>');;
              } else {
                    ptln('<p></p><p style="color: #f80000;">Es ist ein Fehler aufgetreten: Es dürfen nur Bilddateien hochgeladen werden, die kleiner als 100kB sind.</p>');
                    $error = 1;
              }
          } else { 
              ptln('<p></p><p style="color: #f80000;">Es ist ein Fehler aufgetreten: Es dürfen nur Bilddateien hochgeladen werden, die kleiner als 100kB sind.</p>');;
              $error = 1;
          }
        }

        // Reset Knopf
        if (!$error) {
              ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
              ptln('	<input type="hidden" name="do" value="admin" />');
              ptln('	<input type="hidden" name="ospheader" value="reset" />');
              ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
              ptln('  <input type="submit" value="Standardhintergrund wieder herstellen"> ');
              ptln(' </form>');
        } else {
              ptln(' <form enctype="multipart/form-data" action="'.DOKU_BASE.'" method="post" /> ');
              ptln('	<input type="hidden" name="do" value="admin" />');
              ptln('	<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
              ptln('  <input type="submit" value="Weiter"> ');
              ptln(' </form>');
        }
        
        if ($_POST['ospheader'] == "reset" ) {
            if (!copy($usermod_path.$topback_file.".default", $usermod_path.$topback_file)) {
                echo "Hat nicht geklappt: $topback_file...\n";
            } else {
                ptln('<p></p><p style="color: #003d00;">Das Logo wurde durch das Standardlogo ersetzt');
                ptln('- möglicherweise müssen Sie die Startseite neu laden, damit die Änderungen sichtbar werden.</p>');
            }

        }
       



      
      }

  }
