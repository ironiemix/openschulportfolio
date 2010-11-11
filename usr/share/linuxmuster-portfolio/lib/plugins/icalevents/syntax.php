<?php
/**
 * Plugin icalevents: Renders an iCal .ics file as an HTML table.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version    1.0
 * @date       October 2008
 * @author     Robert Rackl <wiki@doogie.de>
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
 
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
  
/**
 * This plugin gets an iCalendar file via HTTP and then
 * parses this file into an HTML table.
 *
 * Usage: {{icalevents>http://host/myCalendar.ics#from=today&previewDays=30}}
 * 
 * You can filter the events that are shown with two parametes:
 * 1. 'from' a unix timestamp from which date on to show events.  
 *           Also 'from=today' is allowed as a minimacro.
 *           If from is ommited, then all events are shown.
 * 2. 'previewDays' amount of days to preview into the future.
 *           Default ist 60 days.
 *
 * from <= eventdate <= from+(previewDays*24*60*3600)
 *
 * @see http://de.wikipedia.org/wiki/ICalendar
 */
class syntax_plugin_icalevents extends DokuWiki_Syntax_Plugin {
 
    function getInfo(){
      return array(
        'author' => 'Robert Rackl, Frank Schiebel',
        'email'  => 'wiki@doogie.de, frank@linuxmuster.net',
        'date'   => '2010-11-11',
        'name'   => 'icalevents',
        'desc'   => 'Parses an iCalalendar .ics file and renders it as an HTML table',
        'url'    => 'http://www.openschulportfolio.de/',
      );
    }
 
    // implement necessary Dokuwiki_Syntax_Plugin methods
    function getType() { return 'substition'; }
    function getSort() { return 42; }
    function connectTo($mode) { $this->Lexer->addSpecialPattern('\{\{icalevents>.*?\}\}',$mode,'plugin_icalevents'); }
    
    /**
     * parse parameters from the {{icalevents>...}} tag.
     * @return an array that will be passed to the renderer function
     */
    function handle($match, $state, $pos, &$handler) {
      $match = substr($match, 13, -2); // strip {{icalevents> from start and }} from end
      list($icsURL, $flagStr) = explode('#', $match);
      parse_str($flagStr, $params);
            
      if ($params['from'] == 'today') {
        $from = time();
      } else if ($params['from']) {
        $from = $params['from']; 
      }
      if ($params['previewDays']) {
        $previewSec = $params['previewDays']*24*3600;
      } else {
        $previewSec = 60*24*3600;  # two month
      }
      
      #echo "url=$icsURL from = $from    previewSec = $previewSec<br>";
      
      return array($icsURL, $from, $previewSec); 
    }
    
    /**
     * loads the ics file via HTTP, parses it and renders an HTML table.
     */
    function render($mode, &$renderer, $data) {
      list($url, $from, $previewSec) = $data;
      $ret = '';
      if($mode == 'xhtml'){
          $entries = $this->_parseIcs($url, $from, $previewSec);
          if ($this->error) {
            $renderer->doc .= "Error in Plugin icalevents: ".$this->error;
            return true;
          }
          #loop over entries and create a table row for each one.
          $rowCount = 0;
          # Locale setzen
          setlocale(LC_TIME, 'de_DE');
          $monthname = "";

          foreach ($entries as $entry) {
            $monthname_new = strftime("%B %Y",$entry['unixdate']);

            if ( $this->getConf('list_split_months') ) {
                if ( $monthname_new != $monthname ) {
                    if ($rowCount > 0 ) {
                        $ret .= "</table>".NL.NL;
                    }  
                    $ret .= "<h3>" . utf8_encode($monthname_new) . "</h3>".NL.NL;
                    $monthname = $monthname_new;
                    $ret .= '<table class="inline icalevents"><tr>'.
                            '<th class="when">'.$this->getLang('when').'</th>'.
                            '<th class="what">'.$this->getLang('what').'</th>';
                    if ( ! $this->getConf('list_desc_as_acronym') ) {
                            $ret .= '<th>'.$this->getLang('description').'</th>';
                    }
                    $ret .= '<th class="where">'.$this->getLang('where').'</th></tr>'.NL;
                }
            } else {
                if ( $rowCount == 0 ) {
                    $ret .= '<table class="inline icalevents"><tr>'.
                            '<th class="when">'.$this->getLang('when').'</th>'.
                            '<th class="what">'.$this->getLang('what').'</th>';
                    if ( ! $this->getConf('list_desc_as_acronym') ) {
                            $ret .= '<th>'.$this->getLang('description').'</th>';
                    }
                    $ret .= '<th class="where">'.$this->getLang('where').'</th></tr>'.NL;
                }
            }

            $rowCount++;
            $ret .= '<tr>';
            $ret .= '<td>'.$entry['date'].' <span class="icaltime">'.$entry['time'].'</span></td>';
            if ( ! $this->getConf('list_desc_as_acronym') ) {
                $ret .= '<td>'.$entry['summary'].'</td>';
                $ret .= '<td>'.$entry['description'].'</td>';
            } else {
                if ( $entry['description'] != "" ) {
                    $ret .= '<td><acronym title="'.$entry['description'].'">'.$entry['summary'].'</acronym></td>';
                } else {
                    $ret .= '<td>'.$entry['summary'].'</td>';
                }
            }
            $ret .= '<td>'.$entry['location'].'</td>';
            $ret .= '</tr>'.NL;
          }
          $ret .= '</table>';
          $renderer->doc .= $ret;
          return true;
      }
      return false;
    }
    
    /**
     * Load the iCalendar file from 'url' and parse all
     * events that are within the range
     * from <= eventdate <= from+previewSec
     *
     * @param url HTTP URL of an *.ics file
     * @param from unix timestamp in seconds (may be null)
     * @param previewSec preview range also in seconds 
     * @return a multidimensional array of entries sorted by their date
     */
    function _parseIcs($url, $from, $previewSec) {
        $http    = new DokuHTTPClient();
        if (!$http->get($url)) {
          $this->error = "Could not get '$url': ".$http->status;
          return array();
        }
        $content    = $http->resp_body;
        $entries     = array();
        
        # regular expressions for items that we want to extract from the iCalendar file
        $regex_vevent      = '/BEGIN:VEVENT(.*?)END:VEVENT/s';
        $regex_summary     = '/SUMMARY:(.*?)\n/';
        $regex_description = '/DESCRIPTION:(.*?)\n[^ ]/s';  # descriptions may be continued with a space at the start of the next line
        $regex_allday      = '/DTSTART;VALUE=DATE:([0-9]{4})([0-9]{2})([0-9]{2})/';  #all day event
        $regex_dtstart     = '/DTSTART.*?:([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2})([0-9]{2})([0-9]{2})/';
        $regex_dtend       = '/DTEND.*?:([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2})([0-9]{2})([0-9]{2})/';
        $regex_location    = '/LOCATION:(.*?)\n/';
        
        
        $entry['time'] = "";

        #split the whole content into VEVENTs        
        preg_match_all($regex_vevent, $content, $matches, PREG_PATTERN_ORDER);

        #loop over VEVENTs and parse out some itmes
        foreach ($matches[1] as $vevent) {
          $entry = array();
          if (preg_match($regex_summary, $vevent, $summary)) {
            $entry['summary'] = str_replace('\,', ',', $summary[1]);
          }
          if (preg_match($regex_dtstart, $vevent, $dtstart)) {
            $entry['date'] = $dtstart[3].'.'.$dtstart[2].'.'.$dtstart[1];
            $entry_hour = $dtstart[4] + 2;
            $entry['time'] = $entry_hour.':'.$dtstart[5];
            $entry['unixdate'] = mktime($dtstart[4], $dtstart[5], $dtstart[6], $dtstart[2], $dtstart[3], $dtstart[1]);
            # get end-time
            if (preg_match($regex_dtend, $vevent, $dtend)) {
                $entry_hour = $dtend[4] + 2;
                $entry['time'] .= '-'.$entry_hour.':'.$dtend[5];
            }
          }
          if (preg_match($regex_allday, $vevent, $allday)) {
            $entry['date'] = $allday[3].'.'.$allday[2].'.'.$allday[1];
            $entry['unixdate'] = mktime(0, 0, 0, $allday[2], $allday[3], $allday[1]);
            $entry['allday'] = true;
          }
          if (preg_match($regex_description, $vevent, $description)) {
            $entry['description'] = $this->_parseDesc($description[1]);
          }
          if (preg_match($regex_location, $vevent, $location)) {
            $entry['location'] = str_replace('\,', ',', $location[1]);
          }
          if ($from && $entry['unixdate']) { 
            if ($entry['unixdate'] < $from) { continue; } 
            if ($previewSec && ($entry['unixdate'] > time()+$previewSec)) { continue; }
          }
          if (preg_match('/@@@/', $entry['description'])) { continue; }  # PalmPilot internal
          $entries[] = $entry; 
        }
        
        #sort entries by unixdate
        usort($entries, 'compareByUnixDate'); 
        
        #echo '<pre>';   
        #print_r($entries);
        #echo '</pre>';
        
        return $entries;
    }
    
    /**
     * Clean description text and render HTML links.
     * In an ics file the description may span over multiple lines.
     * Subsequent lines are indented by one space.
     * And the comma character is escaped.
     * DokuWiki Links <code>[[http://www.domain.de|linktext]]</code> will be rendered to HTML links.
     */
    function _parseDesc($str) {
      $str = str_replace('\,', ',', $str);
      $str = preg_replace("/[\n\r] ?/","",$str);
      $str = preg_replace("/\[\[(.*?)\|(.*?)\]\]/", "<a href=\"$1\">$2</a>", $str);
      $str = preg_replace("/\[\[(.*?)\]\]/e", "html_wikilink('$1')", $str);
      return $str;
    }
    
}

/** compares two entries by their unixdate value */
function compareByUnixDate($a, $b) {
  return strnatcmp($a['unixdate'], $b['unixdate']);
}

?>
