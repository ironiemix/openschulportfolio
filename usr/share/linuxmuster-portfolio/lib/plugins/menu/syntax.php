<?php
/**
 * Plugin: Displays a link list in a nice way
 *
 * Syntax: <menu col=2,align=center,caption="headline">
 *           <item>name|description|link|image</item>
 *         </menu>
 *
 * Options have to be separated by comma.
 * col (opt)     The number of columns of the menu. Allowed are 1-4, default is 1
 * align (opt)   Alignment of the menu. Allowed are "left", "center" or "right",
 *               default is "left"
 * caption (opt) Headline of the menu, default is none
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Matthias Grimm <matthiasgrimm@users.sourceforge.net>
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_menu extends DokuWiki_Syntax_Plugin {

    var $rcmd = array();  /**< Command array for the renderer */

   /**
    * Get an associative array with plugin info.
    *
    * <p>
    * The returned array holds the following fields:
    * <dl>
    * <dt>author</dt><dd>Author of the plugin</dd>
    * <dt>email</dt><dd>Email address to contact the author</dd>
    * <dt>date</dt><dd>Last modified date of the plugin in
    * <tt>YYYY-MM-DD</tt> format</dd>
    * <dt>name</dt><dd>Name of the plugin</dd>
    * <dt>desc</dt><dd>Short description of the plugin (Text only)</dd>
    * <dt>url</dt><dd>Website with more information on the plugin
    * (eg. syntax description)</dd>
    * </dl>
    * @param none
    * @return Array Information about this plugin class.
    * @public
    * @static
    */
    function getInfo(){
        return array(
            'author' => 'Matthias Grimm',
            'email'  => 'matthiasgrimm@users.sourceforge.net',
            'date'   => '2009-07-25',
            'name'   => 'Menu Plugin',
            'desc'   => 'Shows a list of links as a nice menu card',
            'url'    => 'http://www.dokuwiki.org/wiki:plugins',
        );
    }
 
   /**
    * Get the type of syntax this plugin defines.
    *
    * The type of this plugin is "protected". It has a start and an end
    * token and no other wiki commands shall be parsed between them.
    *
    * @param none
    * @return String <tt>'protected'</tt>.
    * @public
    * @static
    */
    function getType(){
        return 'protected';
    }
 
   /**
    * Define how this plugin is handled regarding paragraphs.
    *
    * <p>
    * This method is important for correct XHTML nesting. It returns
    * one of the following values:
    * </p>
    * <dl>
    * <dt>normal</dt><dd>The plugin can be used inside paragraphs.</dd>
    * <dt>block</dt><dd>Open paragraphs need to be closed before
    * plugin output.</dd>
    * <dt>stack</dt><dd>Special case: Plugin wraps other paragraphs.</dd>
    * </dl>
    * @param none
    * @return String <tt>'block'</tt>.
    * @public
    * @static
    */
    function getPType(){
        return 'block';
    }
 
   /**
    * Where to sort in?
    *
    * Sort the plugin in just behind the formating tokens
    *
    * @param none
    * @return Integer <tt>135</tt>.
    * @public
    * @static
    */
    function getSort(){
        return 135;
    }
 
   /**
    * Connect lookup pattern to lexer.
    *
    * @param $aMode String The desired rendermode.
    * @return none
    * @public
    * @see render()
    */
    function connectTo($mode) {
       $this->Lexer->addEntryPattern('<menu>(?=.*?</menu.*?>)',$mode,'plugin_menu');
       $this->Lexer->addEntryPattern('<menu\s[^\r\n\|]*?>(?=.*?</menu.*?>)',$mode,'plugin_menu');
    }
 
    function postConnect() {
      $this->Lexer->addPattern('<item>.+?</item>','plugin_menu');
      $this->Lexer->addExitPattern('</menu>','plugin_menu');
    }
 
   /**
    * Handler to prepare matched data for the rendering process.
    *
    * <p>
    * The <tt>$aState</tt> parameter gives the type of pattern
    * which triggered the call to this method:
    * </p>
    * <dl>
    * <dt>DOKU_LEXER_ENTER</dt>
    * <dd>a pattern set by <tt>addEntryPattern()</tt></dd>
    * <dt>DOKU_LEXER_MATCHED</dt>
    * <dd>a pattern set by <tt>addPattern()</tt></dd>
    * <dt>DOKU_LEXER_EXIT</dt>
    * <dd> a pattern set by <tt>addExitPattern()</tt></dd>
    * <dt>DOKU_LEXER_SPECIAL</dt>
    * <dd>a pattern set by <tt>addSpecialPattern()</tt></dd>
    * <dt>DOKU_LEXER_UNMATCHED</dt>
    * <dd>ordinary text encountered within the plugin's syntax mode
    * which doesn't match any pattern.</dd>
    * </dl>
    * @param $aMatch String The text matched by the patterns.
    * @param $aState Integer The lexer state for the match.
    * @param $aPos Integer The character position of the matched text.
    * @param $aHandler Object Reference to the Doku_Handler object.
    * @return Integer The current lexer state for the match.
    * @public
    * @see render()
    * @static
    */
    function handle($match, $state, $pos, &$handler)
    {
        switch ($state) {
            case DOKU_LEXER_ENTER: 
                $this->_reset();        // reset object;

                $opts = $this->_parseOptions(trim(substr($match,5,-1)));
                $col = $opts['col'];
                if (!empty($col) && is_numeric($col) && $col > 0 && $col < 5)
                    $this->rcmd['columns'] = $col;
                if ($opts['align'] == "left")   $this->rcmd['float'] = "left";
                if ($opts['align'] == "center") $this->rcmd['float'] = "center";
                if ($opts['align'] == "right")  $this->rcmd['float'] = "right";
                if (!empty($opts['caption']))
                    $this->rcmd['caption'] = hsc($opts['caption']);
                if (!empty($opts['type']))
                    $this->rcmd['type'] = hsc($opts['type']);
            break;
          case DOKU_LEXER_MATCHED:
                $menuitem = split('\|', trim(substr($match,6,-7)));

                $title = hsc($menuitem[0]);
                if (substr($menuitem[2],0,2) == "{{")
                    $link = $this->_itemimage($menuitem[2], $title);
                else
                    $link = $this->_itemLink($menuitem[2], $title);
                $image = $this->_itemimage($menuitem[3], $title);

                $this->rcmd['items'][] = array("image" => $image,
                                               "link"  => $link,
                                               "descr" => hsc($menuitem[1]));

                // find out how much space the biggest menu item needs
                $titlelen = mb_strlen($menuitem[0], "UTF-8");
                if ($titlelen > $this->rcmd['width'])
                    $this->rcmd['width'] = $titlelen;
            break;
          case DOKU_LEXER_EXIT:
              // give the menu a convinient width. IE6 needs more space here than Firefox
              $this->rcmd['width'] += 5;
              return $this->rcmd;
          default:
            break;
        }
        return array();
    }
 
    function _reset()
    {
        $this->rcmd = array();
        $this->rcmd['columns'] = 1;
        $this->rcmd['float']   = "left";
    }

    function _itemlink($match, $title) {
        // Strip the opening and closing markup
        $link = preg_replace(array('/^\[\[/','/\]\]$/u'),'',$match);

        // Split title from URL
        $link = explode('|',$link,2);
        $ref  = trim($link[0]);
        
        //decide which kind of link it is
        if ( preg_match('/^[a-zA-Z0-9\.]+>{1}.*$/u',$ref) ) {
            // Interwiki
            $interwiki = explode('>',$ref,2);
            return array('interwikilink',
                         array($ref,$title,strtolower($interwiki[0]),$interwiki[1]));
        } elseif ( preg_match('/^\\\\\\\\[\w.:?\-;,]+?\\\\/u',$ref) ) {
            // Windows Share
            return array('windowssharelink', array($ref,$title));
        } elseif ( preg_match('#^([a-z0-9\-\.+]+?)://#i',$ref) ) {
            // external link (accepts all protocols)
            return array('externallink', array($ref,$title));
        } elseif ( preg_match('<'.PREG_PATTERN_VALID_EMAIL.'>',$ref) ) {
            // E-Mail (pattern above is defined in inc/mail.php)
            return array('emaillink', array($ref,$title));
        } elseif ( preg_match('!^#.+!',$ref) ) {
            // local link
            return array('locallink', array(substr($ref,1),$title));
        } else {
            // internal link
            return array('internallink', array($ref,$title));
        }
    }

    function _itemimage($match, $title) {
        $p = Doku_Handler_Parse_Media($match);

        return array($p['type'],
                     array($p['src'], $title, $p['align'], $p['width'],
                     $p['height'], $p['cache'], $p['linking']));
    }

   /**
    * Handle the actual output creation.
    *
    * <p>
    * The method checks for the given <tt>$aFormat</tt> and returns
    * <tt>FALSE</tt> when a format isn't supported. <tt>$aRenderer</tt>
    * contains a reference to the renderer object which is currently
    * handling the rendering. The contents of <tt>$aData</tt> is the
    * return value of the <tt>handle()</tt> method.
    * </p>
    * @param $aFormat String The output format to generate.
    * @param $aRenderer Object A reference to the renderer object.
    * @param $aData Array The data created by the <tt>handle()</tt>
    * method.
    * @return Boolean <tt>TRUE</tt> if rendered successfully, or
    * <tt>FALSE</tt> otherwise.
    * @public
    * @see handle()
    */
    function render($mode, &$renderer, $data) {
        global $ID;
        global $conf;

        if (empty($data)) return false;

        if($mode == 'xhtml'){
            if ($data['type'] != "menubar"){  
                    // for IE6 2x10em does not fit into 20em, it needs 21em
                    $renderer->doc .= '<div class="menu" id="menu'.$data['float'].'"';
                    $renderer->doc .= ' style="width:'.($data['columns'] * $data['width'] + 2).'em;">'."\n";
                    if (isset($data['caption']))
                        $renderer->doc .= '<p class="caption">'.$data['caption'].'</p>'."\n";

                    foreach($data['items'] as $item) {
                        $renderer->doc .= '<div class="menuitem" style="width:'.$data['width'].'em;">'."\n";

                        // create <img .. /> tag
                        list($type, $args) = $item['image'];
                        list($ext,$mime,$dl) = mimetype($args[0]);
                        $class = ($ext == 'png') ? ' png' : NULL;
                        $img = $renderer->_media($args[0],$args[1],$class,$args[3],$args[4],$args[5]);

                        // create <a href= .. /> tag
                        list($type, $args) = $item['link'];
                        $link = $this->_getLink($type, $args, $renderer);
                        $link['title'] = $args[1];

                        $link['name']  = $img;
                        $renderer->doc .= $renderer->_formatLink($link);

                        $link['name']  = '<p class="menutext">'.$args[1].'</p>';
                        $renderer->doc .= $renderer->_formatLink($link);
                        $renderer->doc .= '<p class="menudesc">'.$item['descr'].'</p>';
                        $renderer->doc .= '</div>'."\n";
                    }

                    $renderer->doc .= '</div>'."\n";
                    if ($data['float'] == "center") /* center: clear text floating */
                        $renderer->doc .= '<p style="clear:both;" />';

                    return true;
            }
            // menubar mode: 1 row with small captions
            if ($data['type'] == "menubar"){  
                    // for IE6 2x10em does not fit into 20em, it needs 21em
                    $renderer->doc .= '<div id="menu"><ul class="menubar">'."\n";
                  //  if (isset($data['caption']))
                  //      $renderer->doc .= '<p class="caption">'.$data['caption'].'</p>'."\n";

                    foreach($data['items'] as $item) {
                        $renderer->doc .= '<li>'."\n";

                        // create <img .. /> tag
                        list($type, $args) = $item['image'];
                        list($ext,$mime,$dl) = mimetype($args[0]);
                        $class = ($ext == 'png') ? ' png' : NULL;
                        $img = $renderer->_media($args[0],$item['descr'],$class,$args[3],$args[4],$args[5]);

                        // create <a href= .. /> tag
                        list($type, $args) = $item['link'];
                        $link = $this->_getLink($type, $args, $renderer);
                        $link['title'] = $args[1];

                        $link['name']  = $img;
                        $renderer->doc .= $renderer->_formatLink($link);

                        $link['name']  = '<p class="menutext">'.$args[1].'</p>';
                        $renderer->doc .= $renderer->_formatLink($link);
                        //$renderer->doc .= '<p class="menudesc">'.$item['descr'].'</p>';
                        $renderer->doc .= '</li>';
                    }

                    $renderer->doc .= '</ul></div>'."\n";
                    if ($data['float'] == "center") /* center: clear text floating */
                        $renderer->doc .= '<p style="clear:both;" />';

                    return true;
            }

        }
        return false;
    }

    function _createLink($url, $target=NULL)
    {
        global $conf;

        $link = array();
        $link['class']  = '';
        $link['style']  = '';
        $link['pre']    = '';
        $link['suf']    = '';
        $link['more']   = '';
        $link['title']  = '';
        $link['name']   = '';
        $link['url']    = $url;

        $link['target'] = $target == NULL ? '' : $conf['target'][$target];
        if ($target == 'interwiki' && strpos($url,DOKU_URL) === 0) {
            //we stay at the same server, so use local target
            $link['target'] = $conf['target']['wiki'];
        }

        return $link;
    }

    function _getLink($type, $args, &$renderer)
    {
        global $ID;

        $check = false;
        $exists = false;

        switch ($type) {
        case 'interwikilink':
            $url  = $renderer->_resolveInterWiki($args[2],$args[3]);
            $link = $this->_createLink($url, 'interwiki');
            break;
        case 'windowssharelink':
            $url  = str_replace('\\','/',$args[0]);
            $url  = 'file:///'.$url;
            $link = $this->_createLink($url, 'windows');
            break;
        case 'externallink':
            $link = $this->_createLink($args[0], 'extern');
            break;
        case 'emaillink':
            $address = $renderer->_xmlEntities($args[0]);
            $address = obfuscate($address);
            if ($conf['mailguard'] == 'visible')
                 $address = rawurlencode($address);
      
            $link = $this->_createLink('mailto:'.$address);
            $link['class'] = 'JSnocheck';
            break;
        case 'locallink':
            $hash = sectionID($args[0], $check);
            $link = $this->_createLink('#'.$hash);
            $link['class'] = "wikilink1";
            break;
        case 'internallink':
            resolve_pageid(getNS($ID),$args[0],$exists);
            $url  = wl($args[0]);
            list($id,$hash) = explode('#',$args[0],2);
            if (!empty($hash)) $hash = sectionID($hash, $check);
            if ($hash) $url .= '#'.$hash;    //keep hash anchor

            $link = $this->_createLink($url, 'wiki');
            $link['class'] = $exists ? 'wikilink1' : 'wikilink2';
            break;
        case 'internalmedia':
            resolve_mediaid(getNS($ID),$args[0], $exists);
            $url  = ml($args[0],array('id'=>$ID,'cache'=>$args[5]),true);
            $link = $this->_createLink($url);
            if (!$exists) $link['class'] = 'wikilink2';
            break;
        case 'externalmedia':
            $url  = ml($args[0],array('cache'=>$args[5]));
            $link = $this->_createLink($url);
            break;
        }
        return $link;
    }

   /**
    * Parse menu options
    *
    *
    * @param $string String Option string from <menu> tag.
    * @return array of options (name >= option). the array will be empty
    *         if no options are found
    * @private
    */
    function _parseOptions($string) {
		$data = array();

		$dq    = false;
		$iskey = true;
		$key   = '';
		$val   = '';

		$len = strlen($string);
		for ($i=0; $i<=$len; $i++) {
			// done for this one?
			if ($string[$i] == ',' || $i == $len) {
				$key = trim($key);
				$val = trim($val);
				if($key && $val) $data[strtolower($key)] = $val;
				$iskey = true;
				$key = '';
				$val = '';
				continue;
			}

			// double quotes
			if ($string[$i] == '"') {
				$dq = $dq ? false : true;
				continue;
			}

			// key value separator
			if ($string[$i] == '=' && !$dq && $iskey) {
				$iskey = false;
				continue;
			}

			// default
			if ($iskey)
				$key .= $string[$i];
			else
				$val .= $string[$i];
		}
		return $data;
    }

}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
