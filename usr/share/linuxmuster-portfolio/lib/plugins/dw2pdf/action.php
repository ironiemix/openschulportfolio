<?php
 /**
 * dw2Pdf Plugin: Conversion from dokuwiki content to pdf.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Luigi Micco <l.micco@tiscali.it>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
 
require_once (DOKU_PLUGIN . 'action.php');
 
class action_plugin_dw2pdf extends DokuWiki_Action_Plugin
{
    /**
     * Constructor.
     */
    function action_plugin_dw2pdf(){
    }

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/info.txt');
    }

    /**
     * Register the events
     */
    function register(&$controller)
    {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'convert',array());
    }

    function convert(&$event, $param)
    {
      global $ACT;
      global $REV;
      global $ID;
      global $conf;

      //$ID = $param[0];
      if ( $ACT == 'export_pdf' ) {
      
        $event->preventDefault();

	$idparam = $ID;
        if ($REV != 0) {  $idparam = $idparam."&rev=".$REV; };

        $pos = strrpos(utf8_decode($ID), ':');
        $pageName = p_get_first_heading($ID);
        if($pageName == NULL) {
          if($pos != FALSE) {
            $pageName = utf8_substr($ID, $pos+1, utf8_strlen($ID));
          } else {
            $pageName = $ID;
          }
          $pageName = str_replace('_', ' ', $pageName);
        }
        
        $iddata = p_get_metadata($ID,'date');
          
        $html = '
        <html><head>
        <style>
        table {
          border: 1px solid #808080;
          border-collapse: collapse;
        }
        td, th {
          border: 1px solid #808080;
        }
        </style>
        </head>
        <body>';
        
        $html = $html . p_wiki_xhtml($ID,$REV,false);
        $html = $html . "<br><br><div style='font-size: 80%; border: solid 0.5mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm 2mm; width: 100%;'>";

        $html = $html . "From:<br>";
        $html = $html . "<a href='".DOKU_URL."'>".DOKU_URL."</a>&nbsp;-&nbsp;"."<b>".$conf['title']."</b>";
        $html = $html . "<br><br>Permanent link:<br>";
        $html = $html . "<b><a href='".wl($idparam, false, true, "&")."'>".wl($idparam, false, true, "&")."</a></b>";
        $html = $html . "<br><br>Last update: <b>".dformat($iddata['modified'])."</b><br>";
        $html = $html . "</div>";

        $html = str_replace('href="/','href="https://'.$_SERVER['HTTP_HOST'].'/',$html);

        require_once(dirname(__FILE__)."/mpdf/mpdf.php");
        $mpdf=new mPDF(); 

        // Temp dir
        $mpdf->tmpalphadir = $conf['savedir'].'/tmp/'; 
        // ECHO $mpdf->tmpalphadir;
        //$mpdf->SetAutoFont(AUTOFONT_ALL);	
        $mpdf->ignore_invalid_utf8 = true;
        //$mpdf->useOnlyCoreFonts = true;	// false is default

        $mpdf->mirrorMargins = 1;	// Use different Odd/Even headers and footers and mirror margins

        $mpdf->defaultheaderfontsize = 9;	/* in pts */
        $mpdf->defaultheaderfontstyle = '';	/* blank, B, I, or BI */
        $mpdf->defaultheaderline = 1; 	/* 1 to include line below header/above footer */

        $mpdf->defaultfooterfontsize = 9;	/* in pts */
        $mpdf->defaultfooterfontstyle = '';	/* blank, B, I, or BI */
        $mpdf->defaultfooterline = 1; 	/* 1 to include line below header/above footer */

        $mpdf->SetHeader('{DATE j-m-Y}|{PAGENO}/{nb}|'.$pageName);
        $mpdf->SetFooter($conf['title'].' - '.DOKU_URL.'||');	/* defines footer for Odd and Even Pages - placed at Outer margin */

        $mpdf->SetFooter(array(
          'L' => array(
            'content' => DOKU_URL,
            'font-family' => 'sans-serif',
            'font-style' => '',	/* blank, B, I, or BI */
            'font-size' => '9',	/* in pts */
          ),

          'R' => array(
            'content' => 'Printed on '.dformat(),
            'font-family' => 'sans-serif',
            'font-style' => '',
            'font-size' => '9',
          ),
          'line' => 1,		/* 1 to include line below header/above footer */
        ), 'E'	/* defines footer for Even Pages */
        );
//        $mpdf->debug = true;
//        $mpdf->showImageErrors = true;
        $mpdf->SetTitle($pageName);
        $mpdf->WriteHTML($html);
        $mpdf->Output(urlencode($pageName).'.pdf','D');

        die();
      }
    }
}
?>
