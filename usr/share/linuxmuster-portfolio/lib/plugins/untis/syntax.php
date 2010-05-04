<?php
/**
 * Filelist Plugin: Lists files matching a given glob pattern.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/confutils.php');
require_once(DOKU_INC.'inc/pageutils.php');


/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_untis extends DokuWiki_Syntax_Plugin {

    var $mediadir;

    function syntax_plugin_untis() {

        global $conf;

    }

    /**
     * return some info about this plugin
     */
    function getInfo() {
        return array(
            'author' => 'Frank Schiebel',
            'email'  => 'frank@linuxmuster.net',
            'date'   => '2010-05-02',
            'name'   => 'Untis Plugin',
            'desc'   => 'Includes data from GP Untis',
            'url'    => '',
        );
    }

    function getType(){ return 'substition'; }
    function getPType(){ return 'block'; }
    function getSort(){ return 222; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{untis>.+?\}\}',$mode,'plugin_untis');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {

        $match = substr($match, 2, -2);
        list($type, $match) = split('>', $match, 2);
        list($days, $flags) = split('&', $match, 2);

        return array($type, $days, $flags);

    }
    
    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        global $conf;
        // disable caching
        $renderer->info['cache'] = false;


        list($type, $pattern, $params, $title) = $data;

        if ($mode == 'xhtml') {
           
           $menu_days = $data[1];

           $untis_date = isset($_REQUEST['untisdate']) ? $_REQUEST['untisdate'] : $this->_get_date();

   
          if ( preg_match("/^\/.*/", $conf['savedir']) ) {
                $this->plan_input = $conf['savedir'] . "media/" . $this->getConf('planinputdir');
           } else {
                $this->plan_input = DOKU_INC . $conf['savedir'] . "media/" . $this->getConf('planinputdir');
           }
           
           // Plan fuer das gegebene Datum aus den Dateien lesen
           $plan = $this->_read_plan_files($untis_date);
           $showfields = $this->getConf('showfields');
           
           // Sortieren
           usort($plan, array($this, "_sort_plan"));

           // Menu 
           $this->_build_menu_from_files($untis_date);

           // Tabelle ausgeben
           print "<table class='untis' cellpadding='0' cellspacing='0'>\n";
           print "<tr>";
           foreach($showfields as $fieldname => $fieldprintname) {
              print '<th>' . $fieldprintname . "</th>";
           }

           $trclass = "eins";
           $merker  =  "";
           
           print "</tr>\n";
           foreach($plan as $vertretung) {
             if ( $merker != $vertretung['kuerzel'] ) {
                 $trclass = $trclass == "zwei" ? "eins" : "zwei";
                 $merker = $vertretung['kuerzel'];
             }
             print '<tr class="' . $trclass . '">';
             foreach($showfields as $fieldname => $fieldprintname) {
                if ( $fieldname == "art" ) {
                    $vertretungstext = $this->_substitute_long_words($vertretung["$fieldname"]);
                } else {
                    $vertretungstext = $vertretung["$fieldname"];
                }
                print "<td>" . $vertretungstext . "</td>";
             }
             print "</tr>\n";
           }
           print "</table>\n";


        }
        return false;
    }
    
    /* Compares kuerzel to sort list 
     *
     */
    function _sort_plan($wert_a, $wert_b) {
        
        $a = $wert_a['kuerzel'];
        $b = $wert_b['kuerzel'];


        $compare = strcmp($a, $b);

        if ( $compare == 0 ) {
            $c = $wert_a['stunde'];
            $d = $wert_b['stunde'];
            $compare = strcmp($c, $d);
        }

        return $compare;
    }

    /**
    * Substitutes long words.
    * Move words to config array!
    *
    * @param $string to check
    * @return  string
    */
    function _substitute_long_words($string) {
      
        $substitutions = $this->getConf('substitutions');
        $tplimagepath = DOKU_TPL;
        
        foreach($substitutions as $pattern => $target) {
            $string = str_replace($pattern , $target , $string);
            if( ! preg_match('/img src="\//', $string) ) {
                // relative image path substitute!
                $string = preg_replace('/src="/', "src=\"$tplimagepath", $string);
            }
        }

        return $string;

     }

     /*
      * Builds day menu
      */
     function _get_date() {
        setlocale(LC_TIME, 'de_DE');
        $untisdate = strtolower(strftime("%b")) . "_" . trim(strftime("%e"));
        return $untisdate;
     }

     /*
      * Builds day menu
      */
      function _build_menu_from_files($chosendate) {

        // Vorhandene Pläne ermitteln
        if (($dir = opendir($this->plan_input)) !== false) {
            while (($file = readdir($dir)) !== false) {
                if ($file == '.' || $file == '..') {
                    // ignore . and ..
                    continue;
                }
                if ($file[0] == '.') {
                    // ignore hidden files
                    continue;
                }

                if (preg_match("/^([a-z]+_[1-9]+).*\.htm$/", $file, $treffer)) {
                  $plan_exists[$treffer[1]] = $treffer[1];
                }
            }
        }
         
        // Menue ausgeben
        print "<div id='menu'><ul>";
        foreach ($plan_exists as $menuentry){
            $class = "untis_menu_link";
            if ($menuentry == $chosendate) {
                 $class .= " untis_active_day";
            }
            print "<li><a class='" . $class ."' href='?untisdate=" . $menuentry . "'>" . $this->_untisdate_to_hrdate($menuentry) .  "</a></li>";
        }
        print "</ul></div>";

        
      }

     /*
      * Converts untisdate to HRdate 
      */
      function _untisdate_to_hrdate($untisdate) {
            list($month, $day) = split('_', $untisdate);
            $hrdate = "$day. " . ucfirst($month); 
            return $hrdate;

      }


    /* Reads Plan file for given date 
     * date has to be in format month_numday, like
     * mai_3
     */
    function _read_plan_files($untis_date) {
        
        // untis files haengen _  ans datum
        $untis_date = $untis_date . "_";

        if (($dir = opendir($this->plan_input)) !== false) {
            while (($file = readdir($dir)) !== false) {
                if ($file == '.' || $file == '..') {
                    // ignore . and ..
                    continue;
                }
                if ($file[0] == '.') {
                    // ignore hidden files
                    continue;
                }
                $filepath = $this->plan_input . '/' . $file;
                $filename = $file;
                
                // Vertretungen
                if (preg_match("/^$untis_date([a-z]*)\.htm$/", $filename)) {

                    if ($this->getConf('debug')) {
                       print("reading: VERT PATH: $filepath  NAME:$filename  DATE: $untis_date<br />");
                    }
                
                    $input = file_get_contents($filepath);

                    // Lehrername und Kuerzel
                    preg_match_all("/<font size=\"8\" face=\"Arial\">(.*)<font/sU", $input, $treffer);
                    $vertretung['kuerzel'] = $treffer[1][0];

                    preg_match_all("/<font size=\"6\" face=\"Arial\">(.*)<\/font/sU", $input, $treffer);
                    $vertretung['lehrer'] = utf8_encode($treffer[1][1]);



                    // Body der ersten Tabelle auf der Seite
                    preg_match_all("/<TABLE.*>(.*)<\/TABLE>/sU",$input, $treffer);
                    $first_table_body = $treffer[1][0];
                    
                    preg_match_all("/<TR>(.*)<\/TR>/sU",$first_table_body, $treffer);
                    
                    $rownum = 0;
                    foreach($treffer[0] as $row) { 
                        if ($rownum > 0 ) {
                            // TD sind die Feldtrenner
                            $row = preg_replace("/<TD.*>/U", "###F###", $row);
                            // alle start-tags weg
                            $row = preg_replace("/<.*>/U", "", $row);
                            // alle end-tags weg
                            $row = preg_replace("/<\/.*>/U", "", $row);
                            // alle &nbsps weg
                            $row = preg_replace("/&nbsp;/U", "", $row);
                            //  zeilenenden weg
                            $row = preg_replace("/\r|\n/U", "", $row);
                            //  leerzeichen weg
                            $row = preg_replace("/\s/U", "", $row);
                            //  Erster Feldtrenner weg
                            $row = preg_replace("/^###F###/U", "" ,$row);
                            // Feldtrenner Strichpunkt
                            $row = preg_replace("/###F###/U", " ; " ,$row);
                            
                            list(
                                $vertretung['art'], 
                                $vertretung['stunde'], 
                                $vertretung['klasse'],  
                                $vertretung['fach'],  
                                $vertretung['raum'],   
                                $vertretung['vvon'],   
                                $vertretung['lenach'],   
                                $vertretung['lehreralt'],   
                                $vertretung['fachalt'],   
                                $vertretung['raumalt'],   
                                $vertretung['vtext']   
                             )   = explode(";", $row);

                             $spos=strpos($vertretung['art'], "Entf");
                             if (is_int($spos)&&($spos==0)) {
                                $vertretung['kuerzel'] = $vertretung['lehreralt'];
                                $vertretung['lehrer'] = "";
                             }
                             $vertretung['kuerzel'] = trim($vertretung['kuerzel']);
                             $vertretung['klasse'] = trim($vertretung['klasse']);
                             $vertretung['stunde'] = trim($vertretung['stunde']);
                             $vertretung['lehrer'] = trim($vertretung['lehrer']);

                             $plan[] = $vertretung;
                        }
                        $rownum++;
                    }

                }


            }
            return $plan;
         }



    }


}
