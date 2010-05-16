<?php
/**
 * Untis-plugin: reads teacher substitution-tables exported by
 * GP-Untis and displays the tables in a nice way in DokuWiki
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
            'desc'   => 'Includes data for teacher substitutions from GP Untis',
            'url'    => 'http://www.openschulportfolio.de/',
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
          
        if ( preg_match("/^\/.*/", $conf['savedir']) ) {
            $this->plan_input = $conf['savedir'] . "media/" . $this->getConf('planinputdir');
        } else {
            $this->plan_input = DOKU_INC . $conf['savedir'] . "media/" . $this->getConf('planinputdir');
        }
        // Look for new zip-file an extract it if necessary
        $this->_extract_zip();

        list($type, $pattern, $params, $title) = $data;

        if ($mode == 'xhtml') {
           // show "menu_days" next available plans 
           $menu_days = $data[1];
           // if no untisdate is set, get one
           $untis_date = isset($_REQUEST['untisdate']) ? $_REQUEST['untisdate'] : $this->_get_date();
           // Read plan from untis html-files 
           $plan = $this->_read_plan_files($untis_date);
           // Which fields to show?
           $showfields = $this->getConf('showfields');
           // Sort plan array: kuerzel > stunde
           usort($plan, array($this, "_sort_plan"));
           // Build top-menu 
           $this->_build_menu_from_files($untis_date);
           // Print substitution table
           print "<table class='untis' cellpadding='0' cellspacing='0'>\n";
           print "<tr>";
           foreach($showfields as $fieldname => $fieldprintname) {
              print '<th>' . $fieldprintname . "</th>";
           }
           // Different colors for different teachers
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
    
   /*
    * Looks for zipfile with untis plans
    * Extracts and deletes zip file
    */
    function _extract_zip() {
        
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
                
                // Look for zip-files
                if (preg_match("/.*\.zip$/", $filename)) {
                    // open zip
                    $zip = zip_open($filepath);
                    if (is_resource($zip)) {
                        // find potential entrys
                         while ($zip_entry = zip_read($zip)) {
                            $completeName = zip_entry_name($zip_entry);
                            // ignoring untis index file
                            if (preg_match('/.*_(.*)\.htm/', $completeName, $treffer)) {
                                $kuerzel = strtolower($treffer[1]);
                                $single_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                                   
                                // Checking if it is untis generated html output
                                if ( preg_match("/<!DOCTYPE HTML PUBLIC/sU", $single_content) &&
                                     preg_match('/<meta name="GENERATOR" content="Untis .*">/sU', $single_content)  &&
                                     preg_match('/<B>.*(\d*\.\d*.\/.*)<\/B>/sU', $single_content, $datestring)
                                     ) {
                                        list($date, $wday) = split('/', $datestring[1]);
                                        list($day, $month) = split('\.', $date);
                                        // This is dumb coding - more elegant solution needed...
                                        $month = str_replace('1','jan',$month);
                                        $month = str_replace('2','feb',$month);
                                        $month = str_replace('3','mar',$month);
                                        $month = str_replace('4','apr',$month);
                                        $month = str_replace('5','may',$month);
                                        $month = str_replace('6','jun',$month);
                                        $month = str_replace('7','jul',$month);
                                        $month = str_replace('8','aug',$month);
                                        $month = str_replace('9','sep',$month);
                                        $month = str_replace('10','oct',$month);
                                        $month = str_replace('11','nov',$month);
                                        $month = str_replace('12','dec',$month);
                                        // Outfile name: 8_21_ZELL.htm
                                        $outfile = $this->plan_input . "/" . $month ."_" . $day . "_" . $kuerzel . ".htm";
                                        if ($fd = fopen($outfile, 'w')) {
                                            fwrite($fd, $single_content);
                                            fclose($fd);
                                        } else {
                                        // Error message has to be included here!
                                        }
                                }

                            }
                            zip_entry_close($zip_entry);
                         }
                    } else {
                        print "Error opening $filepath";
                    }
                }
            }
        }
    }

    /* 
     *  sortiert die plaene nach lehrerkuerzel
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

     /**
      *
      *  Substitutes untis fieldnames
      *
      **/
     function _substitute_untis_fieldnames($string) {
        
        $substitutions = $this->getConf('untisfieldmapping');
        
        foreach($substitutions as $pattern => $target) {
            $string = str_ireplace($pattern , $target , $string);
        }
        return $string;
     }

     /*
      * Gets untis plan date: mai_17 jun_28 and so on
      */
     function _get_date() {
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

                    // Body der ersten Tabelle auf der Seite
                    preg_match_all("/<TABLE.*>(.*)<\/TABLE>/sU",$input, $treffer);
                    $first_table_body = $treffer[1][0];
                    
                    preg_match_all("/<TR>(.*)<\/TR>/sU",$first_table_body, $treffer);
                    
                    $rownum = 0;
                    foreach($treffer[0] as $row) { 
                        if ($rownum >= 0 ) {
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

                            // Erste Zeile Feldreihenfolge
                            if ($rownum == 0) {
                                $row = $this->_substitute_untis_fieldnames($row);
                                $index = explode(";",$row);

                            } else {
                                $vertretung['kuerzel'] = "";                            
                                $vertr_line = explode(";", $row);
                                foreach($index as $idx_num => $idx_name) {
                                    $idx_name = trim($idx_name);
                                    $vertretung[$idx_name] = $vertr_line[$idx_num];
                                }

                                // Versuche, Lehrername und Kuerzel aus dem Seitenkopf zu bekommen
                                if($vertretung['kuerzel'] == "" ){
                                        preg_match_all("/<font size=\"8\" face=\"Arial\">(.*)<font/sU", $input, $treffer);
                                        $vertretung['kuerzel'] = $treffer[1][0];
                                }

                                $spos=strpos($vertretung['art'], "Entf");
                                if (is_int($spos)&&($spos==0)) {
                                         $vertretung['kuerzel'] = $vertretung['lehreralt'];
                                } 

                                $vertretung['kuerzel'] = trim($vertretung['kuerzel']);

                                $plan[] = $vertretung;
                            
                            }

                        }
                        $rownum++;
                    }

                }


            }
            return $plan;
         }



    }


}
