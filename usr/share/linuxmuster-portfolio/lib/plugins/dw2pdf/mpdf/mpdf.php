<?php


// ******************************************************************************
// Software: mPDF, Unicode-HTML Free PDF generator                              *
// Version:  4.1.1   based on                                                   *
//           FPDF 1.52 by Olivier PLATHEY                                       *
//           UFPDF 0.1 by Steven Wittens                                        *
//           HTML2FPDF 3.0.2beta by Renato Coelho                               *
// Date:     2009-12-21                                                         *
// Author:   Ian Back <ianb@bpm1.com>                                           *
// License:  GPL                                                                *
//                                                                              *
// Changes:  See changelog.txt                                                  *
// ******************************************************************************


define('mPDF_VERSION','4.1.1');

define('AUTOFONT_CJK',1);
define('AUTOFONT_THAIVIET',2);
define('AUTOFONT_RTL',4);
define('AUTOFONT_INDIC',8);
define('AUTOFONT_ALL',15);

define('_BORDER_ALL',15);
define('_BORDER_TOP',8);
define('_BORDER_RIGHT',4);
define('_BORDER_BOTTOM',2);
define('_BORDER_LEFT',1);

// mPDF 4.0
//if (!defined('_MPDF_PATH')) define('_MPDF_PATH','');
if (!defined('_MPDF_PATH')) define('_MPDF_PATH', dirname(__FILE__) . '/');

require_once(_MPDF_PATH.'includes/functions.php');
require_once(_MPDF_PATH.'config_cp.php');

if (!defined('_JPGRAPH_PATH')) define("_JPGRAPH_PATH", _MPDF_PATH.'jpgraph/'); 

$errorlevel=error_reporting();
$errorlevel=error_reporting($errorlevel & ~E_NOTICE);

//error_reporting(E_ALL);

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());
if (!function_exists("mb_strlen")) { die("Error - mPDF requires mb_string functions. Ensure that PHP is compiled with php_mbstring.dll enabled."); }

class mPDF
{

///////////////////////////////
// EXTERNAL (PUBLIC) VARIABLES
// Define these in config.php
///////////////////////////////

// for DokuWiki
var $tmpalphadir;
// for DokuWiki


var $debug;
var $showStats;
var $setAutoTopMargin;
var $setAutoBottomMargin;
var $autoMarginPadding;

var $gif2jpg;
var $hyphenate;
var $hyphenateTables;
var $SHYlang;
var $SHYleftmin;
var $SHYrightmin;
var $SHYcharmin;
var $SHYcharmax;
var $SHYlanguages;
// PageNumber Conditional Text
var $pagenumPrefix;
var $pagenumSuffix;
var $nbpgPrefix;
var $nbpgSuffix;
var $showImageErrors;
var $allow_output_buffering;
var $autoPadding;
var $useGraphs;
var $autoFontGroupSize;
var $disableMultilingualJustify;
var $tabSpaces;
var $useLang;
var $restoreBlockPagebreaks;
var $watermarkTextAlpha;
var $watermarkImageAlpha;
var $watermark_size;
var $watermark_pos;
var $annotSize;
var $annotMargin;
var $annotOpacity;
var $title2annots;
var $keepColumns;
var $keep_table_proportions;
var $ignore_table_widths;
var $ignore_table_percents;
var $list_align_style;
var $list_number_suffix;
var $useSubstitutions;
var $disablePrintCSS;
var $forcePortraitHeaders;
var $forcePortraitMargins;
var $displayDefaultOrientation;
var $ignore_invalid_utf8;
var $allowedCSStags;
var $useOnlyCoreFonts;
var $use_CJK_only;
var $allow_charset_conversion;

var $jSpacing;
var $jSWord;
var $jSmaxChar;
var $jSmaxCharLast;
var $jSmaxWordLast;

var $orphansAllowed;
var $max_colH_correction;


var $table_error_report;
var $table_error_report_param;
var $biDirectional;
var $text_input_as_HTML; 
var $anchor2Bookmark;
var $list_indent_first_level;
var $shrink_tables_to_fit;

var $rtlCSS;

var $allow_html_optional_endtags;

var $img_dpi;

var $defaultheaderfontsize;
var $defaultheaderfontstyle;
var $defaultheaderline;
var $defaultfooterfontsize;
var $defaultfooterfontstyle;
var $defaultfooterline;	
var $header_line_spacing;
var $footer_line_spacing;

var $textarea_lineheight;

var $pregRTLchars;	
var $pregUHCchars;
var $pregSJISchars;
var $pregCJKchars;
var $pregASCIIchars1;
var $pregASCIIchars2;
var $pregASCIIchars3;
var $pregVIETchars;	
var $pregVIETPluschars;
var $pregHEBchars;
var $pregARABICchars;	
var $pregNonARABICchars;	

// INDIC
var $pregHIchars;
var $pregBNchars;
var $pregPAchars;
var $pregGUchars;
var $pregORchars;
var $pregTAchars;
var $pregTEchars;
var $pregKNchars;
var $pregMLchars;
var $pregSHchars;
var $pregINDextra;

var $mirrorMargins;
var $default_lineheight_correction;
var $watermarkText;
var $watermarkImage;
var $showWatermarkText;
var $showWatermarkImage;

var $fontsizes;

// Aliases for backward compatability
var $UnvalidatedText;	// alias = $watermarkText
var $TopicIsUnvalidated;	// alias = $showWatermarkText
var $use_embeddedfonts_1252;	// alias = $useOnlyCoreFonts
var $useOddEven;		// alias = $mirrorMargins

//////////////////////
// INTERNAL VARIABLES
//////////////////////
var $pdf_version;

// mPDF 4.1
var $fullImageHeight;
// mPDF 4.0
var $inFixedPosBlock;		// Internal flag for position:fixed block
var $fixedPosBlock;		// Buffer string for position:fixed block
var $fixedPosBlockDepth;
var $fixedPosBlockBBox;
var $fixedPosBlockSave;
var $maxPosL;
var $maxPosR;

var $loaded;

var $useSubsets;	
var $extraFontSubsets;
var $docTemplateStart;		// Internal flag for page (page no. -1) that docTemplate starts on
var $time0;

// Classes
var $t1asm;
var $indic;
var $barcode;

var $SHYpatterns;
var $loadedSHYpatterns;
var $loadedSHYdictionary;
var $SHYdictionary;
var $SHYdictionaryWords;

var $spanbgcolorarray;
var $default_font;
var $list_lineheight;
var $headerbuffer;
var $lastblocklevelchange;
var $nestedtablejustfinished;
var $linebreakjustfinished;
var $cell_border_dominance_L;
var $cell_border_dominance_R;
var $cell_border_dominance_T;
var $cell_border_dominance_B;
var $tbCSSlvl;
var $listCSSlvl;
var $table_keep_together;
var $plainCell_properties;
var $inherit_lineheight;
var $listitemtype;
var $shrin_k1;
var $outerfilled;

var $blockContext;
var $floatDivs;

var $tablecascadeCSS;
var $listcascadeCSS;

var $patterns;
var $pageBackgrounds;

var $bodyBackgroundGradient;
var $bodyBackgroundImage;
var $bodyBackgroundColor;

var $writingHTMLheader;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
var $writingHTMLfooter;
var $autoFontGroups;
var $angle;

var $gradients;

var $kwt_Reference;
var $kwt_BMoutlines;
var $kwt_toc;

var $tbrot_Reference;
var $tbrot_BMoutlines;
var $tbrot_toc;

var $col_Reference;
var $col_BMoutlines;
var $col_toc;

var $currentGraphId;
var $graphs;

var $floatbuffer;
var $floatmargins;

var $bullet;
var $bulletarray;

var $rtlAsArabicFarsi;		// DEPRACATED

var $currentLang;
var $default_lang;
var $default_jSpacing;
var $default_available_fonts;
var $default_dir;

var $pageTemplate;
var $docTemplate;
var $docTemplateContinue;

var $arabGlyphs;
var $arabHex;
var $persianGlyphs;
var $persianHex;
var $arabVowels;
var $arabPrevLink;
var $arabNextLink;


var $formobjects; // array of Form Objects for WMF
var $gdiObjectArray;      // array of GDI objects for WMF
var $InlineProperties;	// Should have done this a long time ago
var $InlineAnnots;
var $ktAnnots;
var $tbrot_Annots;
var $kwt_Annots;
var $columnAnnots;

var $PageAnnots;

var $pageDim;	// Keep track of page wxh for orientation changes - set in _beginpage, used in _putannots

var $breakpoints;


var $tableLevel;
var $tbctr;
var $innermostTableLevel;
var $saveTableCounter;
var $cellBorderBuffer;

var $saveHTMLFooter_height;
var $saveHTMLFooterE_height;

var $firstPageBoxHeader;
var $firstPageBoxHeaderEven;
var $firstPageBoxFooter;
var $firstPageBoxFooterEven;

var $page_box;
var $show_marks;	// crop or cross marks

var $basepathIsLocal;

var $use_kwt;
var $kwt;
var $kwt_height;
var $kwt_y0;
var $kwt_x0;
var $kwt_buffer;
var $kwt_Links;
var $kwt_moved;
var $kwt_saved;

// NOT USED???
var $formBgColor;
var $formBgColorSmall;	// Color used for background of form fields if reduced in size (so border disappears)

var $PageNumSubstitutions;

 
var $table_borders_separate;
var $base_table_properties;
var $borderstyles;
// doesn't include none or hidden

var $listjustfinished;
var $blockjustfinished;

var $orig_bMargin;
var $orig_tMargin;
var $orig_lMargin;
var $orig_rMargin;
var $orig_hMargin;
var $orig_fMargin;

var $pageheaders;
var $pagefooters;

var $pageHTMLheaders;
var $pageHTMLfooters;

var $saveHTMLHeader;
var $saveHTMLFooter;

var $HTMLheaderPageLinks;

// See config_fonts.php for these next 5 values
var $available_fonts;
var $available_unifonts;
var $sans_fonts;
var $serif_fonts;
var $mono_fonts;

// List of ALL available CJK fonts (incl. styles) (Adobe add-ons)  hw removed
var $available_CJK_fonts;

var $cascadeCSS;

var $HTMLHeader;
var $HTMLFooter;
var $HTMLHeaderE;	// for Even pages
var $HTMLFooterE;	// for Even pages
var $bufferoutput; 



var $showdefaultpagenos;	// DEPRACATED -left for backward compatability


var $chrs;
var $ords;

// CJK fonts
var $Big5_widths;
var $GB_widths;
var $SJIS_widths;
var $UHC_widths;

// SetProtection
var $encrypted;    //whether document is protected
var $Uvalue;             //U entry in pdf document
var $Ovalue;             //O entry in pdf document
var $Pvalue;             //P entry in pdf document
var $enc_obj_id;         //encryption object id
var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)
var $last_rc4_key_c;     //last RC4 computed key
var $encryption_key;
var $padding;		//used for encryption

// Bookmark
var $BMoutlines;
var $OutlineRoot;
// TOC
var $_toc;
var $TOCmark;
var $TOCfont;
var $TOCfontsize;
var $TOCindent;
var $TOCheader;
var $TOCfooter;
var $TOCpreHTML;
var $TOCpostHTML;
var $TOCbookmarkText;
var $TOCusePaging;
var $TOCuseLinking;
var $TOCorientation;
var $TOC_margin_left;
var $TOC_margin_right;
var $TOC_margin_top;
var $TOC_margin_bottom;
var $TOC_margin_header;
var $TOC_margin_footer;
var $TOC_odd_header_name;
var $TOC_even_header_name;
var $TOC_odd_footer_name;
var $TOC_even_footer_name;
var $TOC_odd_header_value;
var $TOC_even_header_value;
var $TOC_odd_footer_value;
var $TOC_even_footer_value;
var $TOC_start;
var $TOC_end;
var $TOC_npages;
var $m_TOC; 
// INDEX
var $ColActive;
var $ChangePage;		//Flag indicating that a page break has occurred
var $Reference;
var $CurrCol;
var $NbCol;
var $y0;			//Top ordinate of columns
var $ColL;
var $ColWidth;
var $ColGap;
// COLUMNS 
var $ColR;
var $ChangeColumn;
var $columnbuffer;
var $ColDetails;
var $columnLinks;
var $colvAlign;
// Substitutions
var $substitute;		// Array of substitution strings e.g. <ttz>112</ttz>
var $entsearch;		// Array of HTML entities (>ASCII 127) to substitute
var $entsubstitute;	// Array of substitution decimal unicode for the Hi entities


// Default values if no style sheet offered	(cf. http://www.w3.org/TR/CSS21/sample.html)
var $defaultCSS;

var $form_element_spacing;
var $linemaxfontsize;
var $lineheight_correction;
var $lastoptionaltag;	// Save current block item which HTML specifies optionsl endtag
var $pageoutput;
var $charset_in;
var $blk;
var $blklvl;
var $ColumnAdjust;
var $ws;	// Word spacing
var $HREF;
var $pgwidth;
var $fontlist; 
var $issetfont;
var $issetcolor;
var $titulo;
var $oldx;
var $oldy;
var $B;
var $U;
var $I;

var $tdbegin;
var $table;
var $cell;
var $col;
var $row;

var $divbegin;
var $divalign;
var $divwidth;
var $divheight;
var $divrevert;
var $spanbgcolor;

var $spanlvl;
var $listlvl;
var $listnum;
var $listtype;
var $listoccur;
var $listlist;
var $listitem;

var $pjustfinished;
var $ignorefollowingspaces;
var $SUP;
var $SUB;
var $SMALL;
var $BIG;
var $toupper;
var $tolower;
var $dash_on;
var $dotted_on;
var $strike;

var $CSS;
var $textbuffer;
var $currentfontstyle;
var $currentfontfamily;
var $currentfontsize;
var $colorarray;
var $bgcolorarray;
var $internallink;
var $enabledtags;

var $lineheight;
var $basepath;
var $outlineparam;
var $outline_on;

var $specialcontent;
var $selectoption;

var $usecss;
var $usepre;
var $usetableheader;

var $tableheadernrows;
var $tablefooternrows;	// mPDF 4.0

var $shownoimg;

var $objectbuffer;

// Table Rotation
var $table_rotate;
var $tbrot_maxw;
var $tbrot_maxh;
var $tablebuffer;	

var $tbrot_align;
var $tbrot_Links;

var $divbuffer;		// Buffer used when keeping DIV on one page
var $keep_block_together;	// Keep a Block from page-break-inside: avoid
var $ktLinks;		// Keep-together Block links array
var $ktBlock;		// Keep-together Block array
var $ktReference;
var $ktBMoutlines;
var $_kttoc;

var $tbrot_y0;
var $tbrot_x0;
var $tbrot_w;
var $tbrot_h;

var $is_MB;		// renamed from isunicode
var $codepage;
var $isCJK;
var $mb_enc;
var $directionality;

var $extgstates; // Used for alpha channel - Transparency (Watermark)
var $tt_savefont;
var $mgl;
var $mgt;
var $mgr;
var $mgb;

var $tts;
var $ttz;
var $tta;

var $headerDetails;
var $footerDetails;

// Best to alter the below variables using default stylesheet above
var $div_margin_bottom;	
var $div_bottom_border;
var $p_margin_bottom;
var $p_bottom_border;
var $page_break_after_avoid;
var $margin_bottom_collapse;
var $img_margin_top;	// default is set at top of fn.openTag 'IMG'
var $img_margin_bottom;
var $list_indent;
var $list_align;
var $list_margin_bottom; 
var $default_font_size;	// in pts
var $original_default_font_size;	// used to save default sizes when using table default
var $original_default_font;
var $watermark_font;
var $defaultAlign;

// TABLE
var $defaultTableAlign;
var $tablethead;
var $thead_font_weight;
var $thead_font_style;
var $thead_valign_default;
var $thead_textalign_default;	

var $tabletfoot;
var $tfoot_font_weight;
var $tfoot_font_style;
var $tfoot_valign_default;
var $tfoot_textalign_default;

var $trow_text_rotate;

var $cellPaddingL;
var $cellPaddingR;
var $cellPaddingT;
var $cellPaddingB;
var $table_lineheight;
var $table_border_attr_set;
var $table_border_css_set;

var $shrin_k;			// factor with which to shrink tables - used internally - do not change
var $shrink_this_table_to_fit;	// 0 or false to disable; value (if set) gives maximum factor to reduce fontsize
var $MarginCorrection;	// corrects for OddEven Margins
var $margin_footer;
var $margin_header;

var $tabletheadjustfinished;
var $usingCoreFont;
var $charspacing;

//Private properties FROM FPDF
var $DisplayPreferences; 
var $outlines;
var $flowingBlockAttr;
var $page;               //current page number
var $n;                  //current object number
var $offsets;            //array of object offsets
var $buffer;             //buffer holding in-memory PDF
var $pages;              //array containing pages
var $state;              //current document state
var $compress;           //compression flag
var $DefOrientation;     //default orientation
var $CurOrientation;     //current orientation
var $OrientationChanges; //array indicating orientation changes
var $k;                  //scale factor (number of points in user unit)
var $fwPt;
var $fhPt;         //dimensions of page format in points
var $fw;
var $fh;             //dimensions of page format in user unit
var $wPt;
var $hPt;           //current dimensions of page in points
var $w;
var $h;               //current dimensions of page in user unit
var $lMargin;            //left margin
var $tMargin;            //top margin
var $rMargin;            //right margin
var $bMargin;            //page break margin
var $cMarginL;            //cell margin Left
var $cMarginR;            //cell margin Right
var $cMarginT;            //cell margin Left
var $cMarginB;            //cell margin Right
var $DeflMargin;            //Default left margin
var $DefrMargin;            //Default right margin
var $x;
var $y;               //current position in user unit for cell positioning
var $lasth;              //height of last cell printed
var $LineWidth;          //line width in user unit
var $CoreFonts;          //array of standard font names
var $fonts;              //array of used fonts
var $FontFiles;          //array of font files
var $diffs;              //array of encoding differences
var $images;             //array of used images
var $PageLinks;          //array of links in pages
var $links;              //array of internal links
var $FontFamily;         //current font family
var $FontStyle;          //current font style
var $underline;          //underlining flag
var $CurrentFont;        //current font info
var $FontSizePt;         //current font size in points
var $FontSize;           //current font size in user unit
var $DrawColor;          //commands for drawing color
var $FillColor;          //commands for filling color
var $TextColor;          //commands for text color
var $ColorFlag;          //indicates whether fill and text colors are different
var $autoPageBreak;      //automatic page breaking
var $PageBreakTrigger;   //threshold used to trigger page breaks
var $InFooter;           //flag set when processing footer
var $InHTMLFooter;

var $processingFooter;   //flag set when processing footer - added for columns
var $processingHeader;   //flag set when processing header - added for columns
var $ZoomMode;           //zoom display mode
var $LayoutMode;         //layout display mode
var $title;              //title
var $subject;            //subject
var $author;             //author
var $keywords;           //keywords
var $creator;            //creator

var $aliasNbPg;       //alias for total number of pages
var $aliasNbPgGp;       //alias for total number of pages in page group

var $ispre;

var $outerblocktags;
var $innerblocktags;
// NOT Currently used
var $inlinetags;
var $listtags;
var $tabletags;
var $formtags;


// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************

function mPDF($codepage='win-1252',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {

	// mPDF 4.0
	$this->time0 = microtime(true);
	$unit='mm';
	if (strlen($codepage)==0) { $codepage='win-1252'; }
	//Some checks
	$this->_dochecks();

	// Set up Aliases for backwards compatability
	$this->UnvalidatedText =& $this->watermarkText;
	$this->TopicIsUnvalidated =& $this->showWatermarkText;
	$this->AliasNbPg =& $this->aliasNbPg;
	$this->AliasNbPgGp =& $this->aliasNbPgGp;
	$this->BiDirectional =& $this->biDirectional;
	$this->Anchor2Bookmark =& $this->anchor2Bookmark;
	$this->KeepColumns =& $this->keepColumns;
	$this->use_embeddedfonts_1252 =& $this->useOnlyCoreFonts;
	$this->useOddEven =& $this->mirrorMargins;


	//Initialization of properties
	$this->page=0;
	$this->n=2;
	$this->buffer='';
	$this->objectbuffer = array();
	$this->pages=array();
	$this->OrientationChanges=array();
	$this->state=0;
	$this->fonts=array();
	$this->FontFiles=array();
	$this->diffs=array();
	$this->images=array();
	$this->links=array();
	$this->InFooter=false;
	$this->processingFooter=false;
	$this->processingHeader=false;
	$this->lasth=0;
	$this->FontFamily='';
	$this->FontStyle='';
	$this->FontSizePt=9;
	$this->underline=false;
	$this->DrawColor='0 G';
	$this->FillColor='0 g';
	$this->TextColor='0 g';
	$this->ColorFlag=false;
	$this->extgstates = array();

	$this->is_MB=false;		// renamed from isunicode
	$this->isCJK = false;
	$this->mb_enc='windows-1252';
	$this->directionality='ltr';
	$this->defaultAlign = 'L';
	$this->defaultTableAlign = 'L';

	// mPDF 4.0
	$this->fixedPosBlockSave = array();
	$this->useSubsets = false;
	$this->extraFontSubsets = 0;
	$this->loaded = array();

	$this->SHYpatterns = array();
	$this->loadedSHYdictionary = false;
	$this->SHYdictionary = array();
	$this->SHYdictionaryWords = array();
	$this->blockContext = 1;
	$this->floatDivs = array();
	$this->DisplayPreferences=''; 

	$this->tablecascadeCSS = array();
	$this->listcascadeCSS = array();

	$this->patterns = array();		// Tiling patterns used for backgrounds
	$this->pageBackgrounds = array();
	$this->writingHTMLheader = false;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
	$this->writingHTMLfooter = false;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
	$this->gradients = array();

	$this->kwt_Reference = array();
	$this->kwt_BMoutlines = array();
	$this->kwt_toc = array();

	$this->tbrot_Reference = array();
	$this->tbrot_BMoutlines = array();
	$this->tbrot_toc = array();

	$this->col_Reference = array();
	$this->col_BMoutlines = array();
	$this->col_toc = array();
	$this->graphs = array();

	// mPDF 4.1
	$this->fullImageHeight = false;
	$this->floatbuffer = array();
	$this->floatmargins = array();
	$this->autoFontGroups = 0;
	$this->formobjects=array(); // array of Form Objects for WMF
	$this->InlineProperties=array();
	$this->InlineAnnots=array();
	$this->ktAnnots=array();
	$this->tbrot_Annots=array();
	$this->kwt_Annots=array();
	$this->columnAnnots=array();
	$this->pageDim=array();	
	$this->breakpoints = array();	// used in columnbuffer
	$this->tableLevel=0;
	$this->tbctr=array();	// counter for nested tables at each level
	$this->page_box = array();
	$this->show_marks = '';	// crop or cross marks
	$this->kwt = false;
	$this->kwt_height = 0;
	$this->kwt_y0 = 0;
	$this->kwt_x0 = 0;
	$this->kwt_buffer = array();
	$this->kwt_Links = array();
	$this->kwt_moved = false;
	$this->kwt_saved = false;
	$this->PageNumSubstitutions = array();
	$this->base_table_properties=array();
	$this->borderstyles = array('inset','groove','outset','ridge','dotted','dashed','solid','double');
	$this->tbrot_align = 'C';
	$this->pageheaders=array();
	$this->pagefooters=array();

	$this->pageHTMLheaders=array();
	$this->pageHTMLfooters=array();
	$this->HTMLheaderPageLinks = array();
	$this->cascadeCSS = array();
	$this->bufferoutput = false; 
	$this->encrypted=false;    		//whether document is protected
	$this->BMoutlines=array();
	$this->_toc=array();
	$this->TOCheader=false;
	$this->TOCfooter=false;
	$this->ColActive=0;        		//Flag indicating that columns are on (the index is being processed)
	$this->ChangePage=0;       		//Flag indicating that a page break has occurred
	$this->Reference=array();  		//Array containing the references
	$this->CurrCol=0;              	//Current column number
	$this->ColL = array(0);			// Array of Left pos of columns - absolute - needs Margin correction for Odd-Even
	$this->ColR = array(0);			// Array of Right pos of columns - absolute pos - needs Margin correction for Odd-Even
	$this->ChangeColumn = 0;
	$this->columnbuffer = array();
	$this->ColDetails = array();		// Keeps track of some column details
	$this->columnLinks = array();		// Cross references PageLinks
	$this->substitute = array();		// Array of substitution strings e.g. <ttz>112</ttz>
	$this->entsearch = array();		// Array of HTML entities (>ASCII 127) to substitute
	$this->entsubstitute = array();	// Array of substitution decimal unicode for the Hi entities
	$this->lastoptionaltag = '';
	$this->charset_in = '';
	$this->blk = array();
	$this->blklvl = 0;
	$this->TOCmark = 0;
	$this->tts = false;
	$this->ttz = false;
	$this->tta = false;
	$this->ispre=false;

	$this->headerDetails=array();
	$this->footerDetails=array();
	$this->div_bottom_border = '';
	$this->p_bottom_border = '';
	$this->page_break_after_avoid = false;
	$this->margin_bottom_collapse = false;
	$this->tablethead = 0;
	$this->tabletfoot = 0;
	$this->table_border_attr_set = 0;
	$this->table_border_css_set = 0;

	$this->shrin_k = 1.0;
	$this->shrink_this_table_to_fit = 0;
	$this->MarginCorrection = 0;

	$this->tabletheadjustfinished = false;
	$this->usingCoreFont = false;
	$this->charspacing=0;

	$this->outlines=array();
	$this->autoPageBreak = true;


	require(_MPDF_PATH.'config.php');	// config data

	//Scale factor
	$this->k=72/25.4;	// Will only use mm

	//Page format
	if(is_string($format))
	{
		if ($format=='') { $format = 'A4'; }
		if(preg_match('/([0-9a-zA-Z]*)-L/i',$format,$m)) {	// e.g. A4-L = A$ landscape
			$format=$m[1]; 
			$orientation='L'; 	// Overrides orientation
		}
		switch (strtoupper($format)) {
			case '4A0': {$format = array(4767.87,6740.79); break;}
			case '2A0': {$format = array(3370.39,4767.87); break;}
			case 'A0': {$format = array(2383.94,3370.39); break;}
			case 'A1': {$format = array(1683.78,2383.94); break;}
			case 'A2': {$format = array(1190.55,1683.78); break;}
			case 'A3': {$format = array(841.89,1190.55); break;}
			case 'A4': default: {$format = array(595.28,841.89); break;}
			case 'A5': {$format = array(419.53,595.28); break;}
			case 'A6': {$format = array(297.64,419.53); break;}
			case 'A7': {$format = array(209.76,297.64); break;}
			case 'A8': {$format = array(147.40,209.76); break;}
			case 'A9': {$format = array(104.88,147.40); break;}
			case 'A10': {$format = array(73.70,104.88); break;}
			case 'B0': {$format = array(2834.65,4008.19); break;}
			case 'B1': {$format = array(2004.09,2834.65); break;}
			case 'B2': {$format = array(1417.32,2004.09); break;}
			case 'B3': {$format = array(1000.63,1417.32); break;}
			case 'B4': {$format = array(708.66,1000.63); break;}
			case 'B5': {$format = array(498.90,708.66); break;}
			case 'B6': {$format = array(354.33,498.90); break;}
			case 'B7': {$format = array(249.45,354.33); break;}
			case 'B8': {$format = array(175.75,249.45); break;}
			case 'B9': {$format = array(124.72,175.75); break;}
			case 'B10': {$format = array(87.87,124.72); break;}
			case 'C0': {$format = array(2599.37,3676.54); break;}
			case 'C1': {$format = array(1836.85,2599.37); break;}
			case 'C2': {$format = array(1298.27,1836.85); break;}
			case 'C3': {$format = array(918.43,1298.27); break;}
			case 'C4': {$format = array(649.13,918.43); break;}
			case 'C5': {$format = array(459.21,649.13); break;}
			case 'C6': {$format = array(323.15,459.21); break;}
			case 'C7': {$format = array(229.61,323.15); break;}
			case 'C8': {$format = array(161.57,229.61); break;}
			case 'C9': {$format = array(113.39,161.57); break;}
			case 'C10': {$format = array(79.37,113.39); break;}
			case 'RA0': {$format = array(2437.80,3458.27); break;}
			case 'RA1': {$format = array(1729.13,2437.80); break;}
			case 'RA2': {$format = array(1218.90,1729.13); break;}
			case 'RA3': {$format = array(864.57,1218.90); break;}
			case 'RA4': {$format = array(609.45,864.57); break;}
			case 'SRA0': {$format = array(2551.18,3628.35); break;}
			case 'SRA1': {$format = array(1814.17,2551.18); break;}
			case 'SRA2': {$format = array(1275.59,1814.17); break;}
			case 'SRA3': {$format = array(907.09,1275.59); break;}
			case 'SRA4': {$format = array(637.80,907.09); break;}
			case 'LETTER': {$format = array(612.00,792.00); break;}
			case 'LEGAL': {$format = array(612.00,1008.00); break;}
			case 'EXECUTIVE': {$format = array(521.86,756.00); break;}
			case 'FOLIO': {$format = array(612.00,936.00); break;}
			case 'B': {$format=array(362.83,561.26 );	 break;}		//	'B' format paperback size 128x198mm
			case 'A': {$format=array(314.65,504.57 );	 break;}		//	'A' format paperback size 111x178mm
			case 'DEMY': {$format=array(382.68,612.28 );  break;}		//	'Demy' format paperback size 135x216mm
			case 'ROYAL': {$format=array(433.70,663.30 );  break;}	//	'Royal' format paperback size 153x234mm
			default: $this->Error('Unknown page format: '.$format);
		}
		$this->fwPt=$format[0];
		$this->fhPt=$format[1];
	}
	else
	{
		$this->fwPt=$format[0]*$this->k;
		$this->fhPt=$format[1]*$this->k;
	}
	$this->fw=$this->fwPt/$this->k;
	$this->fh=$this->fhPt/$this->k;
	//Page orientation
	$orientation=strtolower($orientation);
	if($orientation=='p' or $orientation=='portrait')
	{
		$this->DefOrientation='P';
		$this->wPt=$this->fwPt;
		$this->hPt=$this->fhPt;
	}
	elseif($orientation=='l' or $orientation=='landscape')
	{
		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;
	}
	else $this->Error('Incorrect orientation: '.$orientation);
	$this->CurOrientation=$this->DefOrientation;

	$this->w=$this->wPt/$this->k;
	$this->h=$this->hPt/$this->k;

	//PAGE MARGINS
	//mm=2.835/$this->k;

	$this->margin_header=$mgh;
	$this->margin_footer=$mgf;

	$bmargin=$mgb;

	$this->DeflMargin = $mgl;
	$this->DefrMargin = $mgr;

	// v1.4 Save orginal settings in case of changed orientation
	$this->orig_tMargin = $mgt;
	$this->orig_bMargin = $bmargin;
	$this->orig_lMargin = $this->DeflMargin;
	$this->orig_rMargin = $this->DefrMargin;
	$this->orig_hMargin = $this->margin_header;
	$this->orig_fMargin = $this->margin_footer;

	// mPDF 4.0
	if ($this->setAutoTopMargin=='pad') { $mgt += $this->margin_header; }
	if ($this->setAutoBottomMargin=='pad') { $mgb += $this->margin_footer; }
	$this->SetMargins($this->DeflMargin,$this->DefrMargin,$mgt);	// sets l r t margin
	//Automatic page break
	$this->SetAutoPageBreak($this->autoPageBreak,$bmargin);	// sets $this->bMargin & PageBreakTrigger

	$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;

	//Interior cell margin (1 mm) ? not used
	$this->cMarginL = 1;
	$this->cMarginR = 1;
	//Line width (0.2 mm)
	$this->LineWidth=.567/$this->k;

	//To make the function Footer() work - replaces {nb} with page number
	$this->AliasNbPages();
	$this->AliasNbPageGroups();


	//Enable all tags as default
	$this->DisableTags();
	//Full width display mode
	$this->SetDisplayMode(100);	// fullwidth?		'fullpage'
	//Compression
	$this->SetCompression(true);
	//Set default display preferences
	$this->SetDisplayPreferences(''); // mPDF 3.0

	// mPDF 4.0
	if (substr($codepage,-2)=='-s') {
		$codepage = substr($codepage,0,(strlen($codepage)-2));
		$useSubsets = true;
	}
	require(_MPDF_PATH.'config_fonts.php');	// font data

	//Standard fonts
	$this->CoreFonts=array('courier'=>'Courier','courierB'=>'Courier-Bold','courierI'=>'Courier-Oblique','courierBI'=>'Courier-BoldOblique',
		'helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold','helveticaI'=>'Helvetica-Oblique','helveticaBI'=>'Helvetica-BoldOblique',
		'times'=>'Times-Roman','timesB'=>'Times-Bold','timesI'=>'Times-Italic','timesBI'=>'Times-BoldItalic',
		'symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');
	$this->fontlist=array("times","courier","helvetica","symbol","zapfdingbats");

	switch(strtolower($codepage)){
	case 'utf-8': $codepage='UTF-8';break;
	case 'big5': case 'big-5': $codepage='BIG5';break;
	case 'gbk': case 'cp936': $codepage='GBK';break;
	case 'uhc': case 'cp949': $codepage='UHC';break;
	case 'shift_jis': case 'shift-jis': case 'sjis': $codepage='SHIFT_JIS';break;
	case 'win-1251': case 'windows-1251': case 'cp1251': $codepage='win-1251';break;
	case 'win-1252': case 'windows-1252': case 'cp1252': $codepage='win-1252';break;
	case 'iso-8859-2': $codepage='iso-8859-2';break;
	case 'iso-8859-4': $codepage='iso-8859-4';break;
	case 'iso-8859-7': $codepage='iso-8859-7';break;
	case 'iso-8859-9': $codepage='iso-8859-9';break; 
	}

	$this->default_available_fonts = $this->available_unifonts;

	// Autodetect IF codepage is a language_country string (en-GB or en_GB or en)
	if ((strlen($codepage) == 5 && $codepage != 'UTF-8') || strlen($codepage) == 2) {
		// in HTMLToolkit
		list ($codepage,$mpdf_pdf_unifonts,$mpdf_directionality,$mpdf_jSpacing) = GetCodepage($codepage);
		$this->jSpacing = $mpdf_jSpacing;
		if (($codepage != 'BIG5') && ($codepage != 'GBK') && ($codepage != 'UHC') && ($codepage != 'SHIFT_JIS')) { 
			if ($mpdf_pdf_unifonts) { 
				$this->RestrictUnicodeFonts($mpdf_pdf_unifonts); 
				$this->default_available_fonts = $mpdf_pdf_unifonts;
			}
		}
		$this->SetDirectionality($mpdf_directionality);
		$this->currentLang = $codepage;
		$this->default_lang = $codepage;
		$this->default_jSpacing = $mpdf_jSpacing;
		$this->default_dir = $mpdf_directionality;
	}

	// mPDF 4.0 TEMPORARY FIX to disable win-1251 dejavu fonts
	if ($codepage=='win-1251') {
		foreach($this->available_fonts AS $k=>$v) {
			if (substr($v,0,6)=='dejavu') { unset($this->available_fonts[$k]); }
		}
	}

	$this->codepage =  $codepage;

	// mPDF 4.0
	if ($useSubsets && $codepage=='UTF-8') {
		$this->useSubsets = true;
	}

	if ($codepage == 'UTF-8') { $this->is_MB = true; }

	// mPDF 4.0
	if ($codepage=='win-1252' || $codepage=='win-1251' || $codepage=='iso-8859-2' || $codepage=='iso-8859-4' || $codepage=='iso-8859-7' || $codepage=='iso-8859-9' || $codepage == 'BIG5' || $codepage == 'GBK' || $codepage == 'UHC' || $codepage == 'SHIFT_JIS') {
		$this->useSubstitutions = true;
		$this->SetSubstitutions();
	}
	else { $this->useSubstitutions = false; }

	if (!defined('MPDF_FONTPATH')) {
		if ($this->is_MB) { define('MPDF_FONTPATH',_MPDF_PATH.'unifont/'); }
		else { define('MPDF_FONTPATH',_MPDF_PATH.'font/'); }
	}

	if (file_exists(_MPDF_PATH.'mpdf.css')) {
		$css = file_get_contents(_MPDF_PATH.'mpdf.css');
		$css2 = $this->ReadDefaultCSS($css);
		$this->defaultCSS = $this->array_merge_recursive_unique($this->defaultCSS,$css2); 
	}

	if ($default_font=='') { 
	  if ($codepage == 'win-1252') { 
		if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']),$this->mono_fonts)) { $default_font = 'courier'; }
		else if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']),$this->sans_fonts)) { $default_font = 'helvetica'; }
		else { $default_font = 'times'; }
	  }
	  else { $default_font = $this->defaultCSS['BODY']['FONT-FAMILY']; }
	}
	if (!$default_font_size) { 
		$mmsize = $this->ConvertSize($this->defaultCSS['BODY']['FONT-SIZE']);
		$default_font_size = $mmsize*(72/25.4);
	}

	if ($default_font) { $this->SetDefaultFont($default_font); }
	if ($default_font_size) { $this->SetDefaultFontSize($default_font_size); }

	$this->setMBencoding($this->codepage);	// sets $this->mb_enc
	@mb_regex_encoding('UTF-8'); 

	$this->setHiEntitySubstitutions();

	$this->SetLineHeight();	// lineheight is in mm

	$this->SetFillColor(255);
	$this->HREF='';
	$this->titulo='';
	$this->oldy=-1;
	$this->B=0;
	$this->U=0;
	$this->I=0;

	$this->listlvl=0;
	$this->listnum=0; 
	$this->listtype='';
	$this->listoccur=array();
	$this->listlist=array();
	$this->listitem=array();

	$this->tdbegin=false; 
	$this->table=array(); 
	$this->cell=array();  
	$this->col=-1; 
	$this->row=-1; 
	$this->cellBorderBuffer = array();

	$this->divbegin=false;
	$this->divalign=$this->defaultAlign;
	$this->divwidth=0; 
	$this->divheight=0; 
	$this->spanbgcolor=false;
	$this->divrevert=false;

	$this->issetfont=false;
	$this->issetcolor=false;

	$this->blockjustfinished=false;
	$this->listjustfinished=false;
	$this->ignorefollowingspaces = true; //in order to eliminate exceeding left-side spaces
	$this->toupper=false;
	$this->tolower=false;
	$this->dash_on=false;
	$this->dotted_on=false;
	$this->SUP=false;
	$this->SUB=false;
	$this->strike=false;

	$this->currentfontfamily='';
	$this->currentfontsize='';
	$this->currentfontstyle='';
	$this->colorarray=array();
	$this->spanbgcolorarray=array();
	$this->textbuffer=array();
	$this->CSS=array();
	$this->internallink=array();
	$this->basepath = "";

	$this->SetBasePath('');

	$this->outlineparam = array();
	$this->outline_on = false;

	$this->specialcontent = '';
	$this->selectoption = array();

	$this->shownoimg=true;
	$this->usetableheader=false;
	$this->usecss=true;
	$this->usepre=true;

	for($i=0;$i<256;$i++) {
		$this->chrs[$i] = chr($i);
		$this->ords[chr($i)] = $i;

	}
}


function RestrictUnicodeFonts($res) {
//	$this->available_unifonts = $this->default_available_fonts;
//	if (count($res)) {	// Leave full list of available fonts if passed blank array
//	   foreach($this->available_unifonts AS $k => $f) {
//		if (!in_array($f,$res)) { 
//			unset($this->available_unifonts[$k]);
//		}
//	   }
//	}
	// $res = array of (Unicode) fonts to restrict to: e.g. norasi|norasiB - language specific
	if (count($res)) {	// Leave full list of available fonts if passed blank array
		$this->available_unifonts = $res;
	}
	else { $this->available_unifonts = $this->default_available_fonts; }
	if (count($this->available_unifonts) == 0) { $this->available_unifonts[] = $this->default_available_fonts[0]; }
	$this->available_unifonts = array_values($this->available_unifonts);
}


function setMBencoding($enc) {
	// Edited mPDF1.1 - only call mb_internal_encoding if need to change
	$curr = $this->mb_enc;
	// Sets encoding string for use in mb_string functions
	if ($enc == 'win-1252') { $this->mb_enc = 'windows-1252'; }
	else if ($enc == 'win-1251') { $this->mb_enc = 'windows-1251'; }
	else if ($enc == 'UTF-8') { $this->mb_enc = 'UTF-8'; }
	else if ($enc == 'BIG5') { $this->mb_enc = 'UTF-8'; }
	else if ($enc == 'GBK') { $this->mb_enc = 'UTF-8'; }	// cp936
	else if ($enc == 'SHIFT_JIS') { $this->mb_enc = 'UTF-8'; }
	else if ($enc == 'UHC') { $this->mb_enc = 'UTF-8'; }	// cp949
	else { $this->mb_enc = $enc; }	// works for iso-8859-n
	if ($this->mb_enc && $curr != $this->mb_enc) { 
		mb_internal_encoding($this->mb_enc); 
	}
}

function getMBencoding() {
	return $this->mb_enc;
}



function SetMargins($left,$right,$top)
{
	//Set left, top and right margins
	$this->lMargin=$left;
	$this->rMargin=$right;
	$this->tMargin=$top;
}

function ResetMargins()
{
	//ReSet left, top margins
	if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P' && $this->CurOrientation=='L') {
	    if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->tMargin=$this->orig_rMargin;
		$this->bMargin=$this->orig_lMargin;
	    }
	    else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS
		$this->tMargin=$this->orig_lMargin;
		$this->bMargin=$this->orig_rMargin;
	    }
	   $this->lMargin=$this->DeflMargin;
	   $this->rMargin=$this->DefrMargin;
	   $this->MarginCorrection = 0;
	   $this->PageBreakTrigger=$this->h-$this->bMargin;
	}
	else  if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->lMargin=$this->DefrMargin;
		$this->rMargin=$this->DeflMargin;
		$this->MarginCorrection = $this->DefrMargin-$this->DeflMargin;

	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS
		$this->lMargin=$this->DeflMargin;
		$this->rMargin=$this->DefrMargin;
		if ($this->mirrorMargins) { $this->MarginCorrection = $this->DeflMargin-$this->DefrMargin; }
	}
	$this->x=$this->lMargin;

}

function SetLeftMargin($margin)
{
	//Set left margin
	$this->lMargin=$margin;
	if($this->page>0 and $this->x<$margin) $this->x=$margin;
}

function SetTopMargin($margin)
{
	//Set top margin
	$this->tMargin=$margin;
}

function SetRightMargin($margin)
{
	//Set right margin
	$this->rMargin=$margin;
}

function SetAutoPageBreak($auto,$margin=0)
{
	//Set auto page break mode and triggering margin
	$this->autoPageBreak=$auto;
	$this->bMargin=$margin;
	$this->PageBreakTrigger=$this->h-$margin;
}

function SetDisplayMode($zoom,$layout='continuous')
{
	//Set display mode in viewer
	if($zoom=='fullpage' or $zoom=='fullwidth' or $zoom=='real' or $zoom=='default' or !is_string($zoom))
		$this->ZoomMode=$zoom;
	else
		$this->Error('Incorrect zoom display mode: '.$zoom);
	if($layout=='single' or $layout=='continuous' or $layout=='two' or $layout=='default')
		$this->LayoutMode=$layout;
	else
		$this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress)
{
	//Set page compression
	if(function_exists('gzcompress'))	$this->compress=$compress;
	else $this->compress=false;
}

function SetTitle($title)
{
	//Title of document // Arrives as UTF-8
	$this->title = $title;
}

function SetSubject($subject)
{
	//Subject of document
	$this->subject= $subject;
}

function SetAuthor($author)
{
	//Author of document
	$this->author= $author;
}

function SetKeywords($keywords)
{
	//Keywords of document
	$this->keywords= $keywords;
}

function SetCreator($creator)
{
	//Creator of document
	$this->creator= $creator;
}


function SetAnchor2Bookmark($x) {
	$this->anchor2Bookmark = $x;
}

function AliasNbPages($alias='{nb}')
{
	//Define an alias for total number of pages
	$this->aliasNbPg=$alias;
}

function AliasNbPageGroups($alias='{nbpg}')
{
	//Define an alias for total number of pages in a group
	$this->aliasNbPgGp=$alias;
}

function SetAlpha($alpha, $bm='Normal') {
// alpha: real value from 0 (transparent) to 1 (opaque)
// bm:    blend mode, one of the following:
//          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
//          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
// set alpha for stroking (CA) and non-stroking (ca) operations
	$gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
	$this->SetExtGState($gs);
}

function AddExtGState($parms) {
	$n = count($this->extgstates);
	// check if graphics state already exists
	for ($i=1; $i<=$n; $i++) {
		if ($this->extgstates[$i]['parms']['ca']==$parms['ca'] && $this->extgstates[$i]['parms']['CA']==$parms['CA'] && $this->extgstates[$i]['parms']['BM']==$parms['BM']) {
			return $i;
		}
	}
	$n++;
	$this->extgstates[$n]['parms'] = $parms;
	return $n;
}

function SetExtGState($gs) {
	$this->_out(sprintf('/GS%d gs', $gs));
}


function Error($msg)
{
	//Fatal error
	header('Content-Type: text/html; charset=utf-8');
	die('<B>mPDF error: </B>'.$msg);
}

function Open()
{
	//Begin document
	if($this->state==0)	$this->_begindoc();
}

function Close()
{
	//Terminate document
	if($this->state==3)	return;
	if($this->page==0) $this->AddPage($this->CurOrientation);
	if (count($this->cellBorderBuffer)) { $this->printcellbuffer(); }	// *TABLES*
	if (count($this->tablebuffer)) { $this->printtablebuffer(); }	// *TABLES*
	if (count($this->divbuffer)) { $this->printdivbuffer(); }

	// BODY Backgrounds
	$s = '';
	$bby = $this->h;
	$bbw = $this->w;
	$bbh = $this->h;
	if ($this->bodyBackgroundColor) {
		$s .= sprintf('%.3f %.3f %.3f rg',$this->bodyBackgroundColor['R']/255,$this->bodyBackgroundColor['G']/255,$this->bodyBackgroundColor['B']/255)."\n";
		$s .= sprintf('%.3f %.3f %.3f %.3f re f',0,$bby*$this->k,$bbw*$this->k,-$bbh*$this->k)."\n";
	}



	$s .= $this->PrintPageBackgrounds();
	$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n".$s."\n".'\\1', $this->pages[$this->page]);
	$this->pageBackgrounds = array();

	if (!$this->TOCmark) { //Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
	}

	//Close page
	$this->_endpage();

	//Close document
	$this->_enddoc();
}


function PrintPageBackgrounds() {
	$s = '';
	ksort($this->pageBackgrounds);
	foreach($this->pageBackgrounds AS $bl=>$pbs) {
		foreach ($pbs AS $pb) {
		  if (!isset($pb['image_id']) && !isset($pb['gradient'])) {	// Background colour
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			$s .= sprintf('%.3f %.3f %.3f rg',$pb['col']['R']/255,$pb['col']['G']/255,$pb['col']['B']/255)."\n";
			$s .= sprintf('%.3f %.3f %.3f %.3f re f',$pb['x']*$this->k,($this->h-$pb['y'])*$this->k,$pb['w']*$this->k,-$pb['h']*$this->k)."\n";
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
		  }
		}
	}
	return $s;
}

// Depracated - can use AddPage for all
function AddPages($orientation='',$condition='', $resetpagenum='', $pagenumstyle='', $suppress='',$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0)
{
	$this->AddPage($orientation,$condition,$resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue);
}


function AddPage($orientation='',$condition='', $resetpagenum='', $pagenumstyle='', $suppress='',$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0)
{

	// Float DIV
	// Cannot do with columns on, or if any change in page orientation/margins etc.
	// If next page already exists - i.e background /headers and footers already written
	if ($this->state > 0 && $this->page < count($this->pages)) {
		$bak_cml = $this->cMarginL;
		$bak_cmr = $this->cMarginR; 
		$bak_dw = $this->divwidth;
		// Paint Div Border if necessary
   		if ($this->blklvl > 0) {
			$save_tr = $this->table_rotate;	// *TABLES*
			$this->table_rotate = 0;	// *TABLES*
			if ($this->y == $this->blk[$this->blklvl]['y0']) {  $this->blk[$this->blklvl]['startpage']++; }
			if (($this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table'] ) { $toplvl = $this->blklvl; }
			else { $toplvl = $this->blklvl-1; }
			$sy = $this->y;
			for ($bl=1;$bl<=$toplvl;$bl++) {
				$this->PaintDivBB('pagebottom',0,$bl);
			}
			$this->y = $sy;
			$this->table_rotate = $save_tr;	// *TABLES*
		}
		$s = $this->PrintPageBackgrounds();

		// Writes after the marker so not overwritten later by page background etc.
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
		$this->pageBackgrounds = array();
	
		$family=$this->FontFamily;
		$style=$this->FontStyle.($this->underline ? 'U' : '');
		$size=$this->FontSizePt;
		$lw=$this->LineWidth;
		$dc=$this->DrawColor;
		$fc=$this->FillColor;
		$tc=$this->TextColor;
		$cf=$this->ColorFlag;

		$this->printfloatbuffer();

		//Move to next page
		$this->page++;
	
		$this->ResetMargins();
		$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);
		$this->x=$this->lMargin;
		$this->y=$this->tMargin;
		$this->FontFamily='';
		$this->_out('2 J');
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.3f w',$lw*$this->k));
		if($family)	$this->SetFont($family,$style,$size,true,true);
		$this->DrawColor=$dc;
		if($dc!='0 G') $this->_out($dc);
		$this->FillColor=$fc;
		if($fc!='0 g') $this->_out($fc);
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
		for($bl=1;$bl<=$this->blklvl;$bl++) {
			$this->blk[$bl]['y0'] = $this->y;
			// Don't correct more than once for background DIV containing a Float
			if (!isset($this->blk[$bl]['marginCorrected'][$this->page])) { $this->blk[$bl]['x0'] += $this->MarginCorrection; }
			$this->blk[$bl]['marginCorrected'][$this->page] = true; 
		}
		$this->cMarginL = $bak_cml;
		$this->cMarginR = $bak_cmr;
		$this->divwidth = $bak_dw;
		return '';
	}

	//Start a new page
	if($this->state==0) $this->Open();

	// mPDF 3.0 - Moved here from WriteFlowingBlock, FinishFlowingBlock and printbuffer
	$bak_cml = $this->cMarginL;
	$bak_cmr = $this->cMarginR; 
	$bak_dw = $this->divwidth;

	$orientation = substr(strtoupper($orientation),0,1);
	$condition = strtoupper($condition);

	if ($condition == 'NEXT-EVEN') {	// always adds at least one new page to create an Even page
	   if (!$this->mirrorMargins) { $condition = ''; }
	   else { 
		if ($this->page_box['changed']) { $this->page_box['changed'] = false; $pbch = true; }
		else { $pbch = false; }
		$this->AddPage($this->CurOrientation,'O'); 
		if ($pbch ) { $this->page_box['changed'] = true; }	// *CSS-PAGE*
		$condition = ''; 
	   }
	}
	if ($condition == 'NEXT-ODD') {	// always adds at least one new page to create an Odd page
	   if (!$this->mirrorMargins) { $condition = ''; }
	   else { 
		if (isset($this->page_box['changed']) && $this->page_box['changed']) { $this->page_box['changed'] = false; $pbch = true; }
		else { $pbch = false; }
		$this->AddPage($this->CurOrientation,'E'); 
		if ($pbch ) { $this->page_box['changed'] = true; }	// *CSS-PAGE*
		$condition = ''; 
	   }
	}


	if ($condition == 'E') {	// only adds new page if needed to create an Even page
	   if (!$this->mirrorMargins || ($this->page)%2==0) { return false; }
	}
	if ($condition == 'O') {	// only adds new page if needed to create an Odd page
	   if (!$this->mirrorMargins || ($this->page)%2==1) { return false; }
	}

	if ($resetpagenum || $pagenumstyle || $suppress) {
		$this->PageNumSubstitutions[] = array('from'=>($this->page+1), 'reset'=> $resetpagenum, 'type'=>$pagenumstyle, 'suppress'=>$suppress);
	}

	// mPDF 4.0
	$save_tr = $this->table_rotate;	// *TABLES*
	$this->table_rotate = 0;	// *TABLES*
	$save_kwt = $this->kwt;
	$this->kwt = 0;


	// Paint Div Border if necessary
   	//PAINTS BACKGROUND COLOUR OR BORDERS for DIV - DISABLED FOR COLUMNS (cf. AcceptPageBreak) AT PRESENT in ->PaintDivBB
   	if (!$this->ColActive && $this->blklvl > 0) {
		if (isset($this->blk[$this->blklvl]['y0']) && $this->y == $this->blk[$this->blklvl]['y0']) {  
			if (isset($this->blk[$this->blklvl]['startpage'])) { $this->blk[$this->blklvl]['startpage']++; }
			else { $this->blk[$this->blklvl]['startpage'] = 1; }
		}
		if ((isset($this->blk[$this->blklvl]['y0']) && $this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table'] ) { $toplvl = $this->blklvl; }
		else { $toplvl = $this->blklvl-1; }
		$sy = $this->y;
		for ($bl=1;$bl<=$toplvl;$bl++) {
			$this->PaintDivBB('pagebottom',0,$bl);
		}
		$this->y = $sy;
		// RESET block y0 and x0 - see below
	}

	// BODY Backgrounds
	if ($this->page > 0) {
		$s = '';
		$bby = $this->h;
		$bbw = $this->w;
		$bbh = $this->h;
		if ($this->bodyBackgroundColor) {
			$s .= sprintf('%.3f %.3f %.3f rg',$this->bodyBackgroundColor['R']/255,$this->bodyBackgroundColor['G']/255,$this->bodyBackgroundColor['B']/255)."\n";
			$s .= sprintf('%.3f %.3f %.3f %.3f re f',0,$bby*$this->k,$bbw*$this->k,-$bbh*$this->k)."\n";
		}

		$s .= $this->PrintPageBackgrounds();
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n".$s."\n".'\\1', $this->pages[$this->page]);
		$this->pageBackgrounds = array();
	}

	$save_cols = false;

	$family=$this->FontFamily;
	$style=$this->FontStyle.($this->underline ? 'U' : '');
	$size=$this->FontSizePt;
	$this->ColumnAdjust = true;	// enables column height adjustment for the page
	$lw=$this->LineWidth;
	$dc=$this->DrawColor;
	$fc=$this->FillColor;
	$tc=$this->TextColor;
	$cf=$this->ColorFlag;
	if($this->page>0)
	{
		//Page footer
		$this->InFooter=true;

		// mPDF 3.0
		$this->Reset();
		$this->pageoutput[$this->page] = array();

		$this->Footer();
		//Close page
		$this->_endpage();
	}


	$mgtfp = $mgbfp = 0; 


	// Paged media (page-box)
	if (isset($this->page_box['changed']) && $this->page_box['changed']) {
		list($orientation,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$mgtfp,$mgbfp,$ohname,$ehname,$ofname,$efname,$bg) = $this->SetPagedMediaCSS();
		if ($ohname) { $ohvalue = 1; } else { $ohvalue = -1; }
		if ($ehname) { $ehvalue = 1; } else { $ehvalue = -1; }
		if ($ofname) { $ofvalue = 1; } else { $ofvalue = -1; }
		if ($efname) { $efvalue = 1; } else { $efvalue = -1; }
  		// PAGED MEDIA - CROP / CROSS MARKS from @PAGE
		if (isset($this->page_box['CSS']['MARKS'])) {
	  		if (strtoupper($this->page_box['CSS']['MARKS']) == 'CROP') {
				$this->show_marks = 'CROP';
	  		}
	  		else if (strtoupper($this->page_box['CSS']['MARKS']) == 'CROSS') {
				$this->show_marks = 'CROSS';
	 		}
			else { $this->show_marks = ''; }
		}
		else { $this->show_marks = ''; }

		// Background color
		if (isset($bg['BACKGROUND-COLOR'])) {
			$cor = $this->ConvertColor($bg['BACKGROUND-COLOR']);
			if ($cor) { 
				$this->bodyBackgroundColor = $cor; 
				$this->bodyBackgroundImage = false; 
				$this->bodyBackgroundGradient = false; 
			}
		}


		$this->page_box['start_page'] = $this->page+1;
	}
	$this->page_box['changed'] = false;


	//Start new page
	$this->_beginpage($orientation,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue,$mgtfp,$mgbfp);

	// Tiling Patterns	// Moved here mPDF 4.0
	$this->_out('___BACKGROUND___PATTERNS'.date('jY'));
	$this->pageBackgrounds = array();

	//Set line cap style to square
	$this->_out('2 J');
	//Set line width
	$this->LineWidth=$lw;
	$this->_out(sprintf('%.3f w',$lw*$this->k));
	//Set font
	if($family)	$this->SetFont($family,$style,$size,true,true);	// forces write
	//Set colors
	$this->DrawColor=$dc;
	if($dc!='0 G') $this->_out($dc);
	$this->FillColor=$fc;
	if($fc!='0 g') $this->_out($fc);
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;

	//Page header
	$this->Header();

	//Restore line width
	if($this->LineWidth!=$lw)
	{
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.3f w',$lw*$this->k));
	}
	//Restore font
	if($family)	$this->SetFont($family,$style,$size,true,true);	// forces write
	//Restore colors
	if($this->DrawColor!=$dc)
	{
		$this->DrawColor=$dc;
		$this->_out($dc);
	}
	if($this->FillColor!=$fc)
	{
		$this->FillColor=$fc;
		$this->_out($fc);
	}
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
 	$this->InFooter=false;


   	//RESET BLOCK BORDER TOP
   	if (!$this->ColActive) {
		for($bl=1;$bl<=$this->blklvl;$bl++) {
			$this->blk[$bl]['y0'] = $this->y;
			if (isset($this->blk[$bl]['x0'])) { $this->blk[$bl]['x0'] += $this->MarginCorrection; }
			else { $this->blk[$bl]['x0'] = $this->MarginCorrection; }
			// Added mPDF 3.0 Float DIV
			$this->blk[$bl]['marginCorrected'][$this->page] = true; 
		}
	}

	// mPDF 4.0
	$this->table_rotate = $save_tr;	// *TABLES*
	$this->kwt = $save_kwt;

	$this->cMarginL = $bak_cml;
	$this->cMarginR = $bak_cmr;
	$this->divwidth = $bak_dw;
}


function PageNo()
{
	//Get current page number
	return $this->page;
}

function SetDrawColor($r,$g=-1,$b=-1,$col4=-1)
{
	//Set color for all stroking operations
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1)	$this->DrawColor=sprintf('%.3f G',$r/255);
	else if ($col4 == -1) $this->DrawColor=sprintf('%.3f %.3f %.3f RG',$r/255,$g/255,$b/255);
	else {
		// CMYK
		$this->DrawColor = sprintf('%.3f %.3f %.3f %.3f K', $r/100, $g/100, $b/100, $col4/100);
	}
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['DrawColor']) && $this->pageoutput[$this->page]['DrawColor'] != $this->DrawColor) || !isset($this->pageoutput[$this->page]['DrawColor']) || $this->keep_block_together)) { $this->_out($this->DrawColor); }
	$this->pageoutput[$this->page]['DrawColor'] = $this->DrawColor;
}

function SetFillColor($r,$g=-1,$b=-1,$col4=-1)
{
	//Set color for all filling operations
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1)	$this->FillColor=sprintf('%.3f g',$r/255);
	else if ($col4 == -1) $this->FillColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
	else {
		// CMYK
		$this->FillColor = sprintf('%.3f %.3f %.3f %.3f k', $r/100, $g/100, $b/100, $col4/100);
	}
	$this->ColorFlag = ($this->FillColor != $this->TextColor);
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor']) || $this->keep_block_together)) { $this->_out($this->FillColor); }
	$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
}

function SetTextColor($r,$g=-1,$b=-1,$col4=-1)
{
	//Set color for text
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1)	$this->TextColor=sprintf('%.3f g',$r/255);
	else if ($col4 == -1) $this->TextColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
	else {
		// CMYK
		$this->TextColor = sprintf('%.3f %.3f %.3f %.3f k', $r/100, $g/100, $b/100, $col4/100);
	}
	$this->ColorFlag = ($this->FillColor != $this->TextColor);
}



function GetStringWidth($s) {
			//Get width of a string in the current font
			$s = (string)$s;
			$cw = &$this->CurrentFont['cw'];
			$w = 0;
				$l = strlen($s);
				for($i=0; $i<$l; $i++) {
					// Soft Hyphens chr(173)
					if (substr($s,$i,1) == chr(173) && ($this->FontFamily!='symbol' && $this->FontFamily!='zapfdingbats')) { 
						continue;
					} else if (isset($cw[substr($s,$i,1)])) {
						$w += $cw[substr($s,$i,1)];
					} 
					else if (isset($cw[$this->ords[substr($s,$i,1)]])) {
						$w += $cw[$this->ords[substr($s,$i,1)]];
					}
				}
			unset($cw);
			return ($w * $this->FontSize/ 1000);
}

function SetLineWidth($width)
{
	//Set line width
	$this->LineWidth=$width;
	$lwout = (sprintf('%.3f w',$width*$this->k));
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['LineWidth']) && $this->pageoutput[$this->page]['LineWidth'] != $lwout) || !isset($this->pageoutput[$this->page]['LineWidth']) || $this->keep_block_together)) {
		 $this->_out($lwout); 
	}
	$this->pageoutput[$this->page]['LineWidth'] = $lwout;
}

function Line($x1,$y1,$x2,$y2)
{
	//Draw a line
	$this->_out(sprintf('%.3f %.3f m %.3f %.3f l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Arrow($x1,$y1,$x2,$y2,$headsize=3,$fill='B',$angle=25)
{
  //F == fill //S == stroke //B == stroke and fill 
  // angle = splay of arrowhead - 1 - 89 degrees
  if($fill=='F')	$fill='f';
  elseif($fill=='FD' or $fill=='DF' or $fill=='B') $fill='B';
  else $fill='S';
  $a = atan2(($y2-$y1),($x2-$x1));
  $b = $a + deg2rad($angle);
  $c = $a - deg2rad($angle);
  $x3 = $x2 - ($headsize* cos($b));
  $y3 = $this->h-($y2 - ($headsize* sin($b)));
  $x4 = $x2 - ($headsize* cos($c));
  $y4 = $this->h-($y2 - ($headsize* sin($c)));

  $x5 = $x3-($x3-$x4)/2;	// mid point of base of arrowhead - to join arrow line to
  $y5 = $y3-($y3-$y4)/2;

  $s = '';
  $s.=sprintf('%.3f %.3f m %.3f %.3f l S',$x1*$this->k,($this->h-$y1)*$this->k,$x5*$this->k,$y5*$this->k);
  $this->_out($s);

  $s = '';
  $s.=sprintf('%.3f %.3f m %.3f %.3f l %.3f %.3f l %.3f %.3f l %.3f %.3f l ',$x5*$this->k,$y5*$this->k,$x3*$this->k,$y3*$this->k,$x2*$this->k,($this->h-$y2)*$this->k,$x4*$this->k,$y4*$this->k,$x5*$this->k,$y5*$this->k);
  $s.=$fill;
  $this->_out($s);
}


function Rect($x,$y,$w,$h,$style='')
{
	//Draw a rectangle
	if($style=='F')	$op='f';
	elseif($style=='FD' or $style=='DF') $op='B';
	else $op='S';
	$this->_out(sprintf('%.3f %.3f %.3f %.3f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function AddFont($family,$style='',$file='')
{

	if ($this->isCJK && $this->use_CJK_only) { return; }
	if(empty($family)) { return; }
	//Add a TrueType or Type1 font
	$family = strtolower($family);

	$style=strtoupper($style);
	$style=str_replace('U','',$style);
	if($style=='IB') $style='BI';
	$fontkey = $family.$style;
	// check if the font has been already added
	if(isset($this->fonts[$fontkey])) {
		return;
	}


		if($file=='') {
			$file=str_replace(' ','',$family).strtolower($style);

			if ($this->is_MB) {
				$file=$file.'.php';
			}
			else if ($this->codepage != 'win-1252') {
				$file=$file.'-'.$this->codepage.'.php';
			}
			else {	// is there any other?
				$file=$file.'.php';
			}


		}
		if(defined('MPDF_FONTPATH')) { $file=MPDF_FONTPATH.$file; }
		include($file);
		if(!isset($name))	$this->Error('Could not include font definition file - '.$family.' '.$style);
		$i=count($this->fonts)+$this->extraFontSubsets+1;
		$this->fonts[$family.$style]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'enc'=>$enc,'file'=>$file, 'used'=>false);
		if($diff)
		{
			//Search existing encodings
			$d=0;
			$nb=count($this->diffs);
			for($i=1;$i<=$nb;$i++)
				if($this->diffs[$i]==$diff)
				{
					$d=$i;
					break;
				}
			if($d==0)
			{
				$d=$nb+1;
				$this->diffs[$d]=$diff;
			}
			$this->fonts[$family.$style]['diff']=$d;
		}
		if($file)
		{
			if($type=='TrueType')	$this->FontFiles[$file]=array('length1'=>$originalsize);
			else $this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
		}
		// ADDED fontlist is defined in html2fpdf
		if (isset($this->fontlist)) { $this->fontlist[] = strtolower($family); }
		else { $this->fontlist = array(strtolower($family)); }
}



function SetFont($family,$style='',$size=0, $write=true, $forcewrite=false)
{
	$family=strtolower($family);
	// save previous values - ? not required mPDF 4.0
	// $this->prevFontFamily = $this->FontFamily;
	// $this->prevFontStyle = $this->FontStyle;
	//Select a font; size given in points

	if($family=='') { 
		if ($this->FontFamily) { $family=$this->FontFamily; }
		else if ($this->default_font) { $family=$this->default_font; }
		else { die("ERROR - No font or default font set!"); }
	}

	if (($family == 'symbol') || ($family == 'zapfdingbats')  || ($family == 'times')  || ($family == 'courier') || ($family == 'helvetica')) { $this->usingCoreFont = true; }
	else {  $this->usingCoreFont = false; }

	if($family=='symbol' or $family=='zapfdingbats') { $style=''; }
	$style=strtoupper($style);
	if(is_int(strpos($style,'U'))) {
		$this->underline=true;
		$style=str_replace('U','',$style);
	}
	else { $this->underline=false; }
	if ($style=='IB') $style='BI';
	if ($size==0) $size=$this->FontSizePt;


	$fontkey=$family.$style;


		$this->isCJK = false;
		$this->setMBencoding($this->codepage);

		//Test if font is already selected
		if(($this->FontFamily == $family) AND ($this->FontStyle == $style) AND ($this->FontSizePt == $size) && !$forcewrite) {
			return $family;
		}

		// ALWAYS SUBSTITUTE ARIAL TIMES COURIER IN 1252
		if (!isset($this->CoreFonts[$fontkey]) && ($this->useOnlyCoreFonts) && ($this->codepage == 'win-1252')) {
			if (in_array($family,$this->serif_fonts)) { $family = 'times'; }
			else if (in_array($family,$this->mono_fonts)) { $family = 'courier'; }
			else { $family = 'helvetica'; }
			$this->usingCoreFont = true;
			$fontkey = $family.$style; 
		}

		// Test to see if requested font/style is available - or substitute
		if (!in_array($fontkey,$this->available_fonts) && (!$this->usingCoreFont) ) {

			// If font[nostyle] exists - set it
			if (in_array($family,$this->available_fonts)) {
				$style = '';
			}

			// Else if only one font available - set it (assumes if only one font available it will not have a style)
			else if (count($this->available_fonts) == 1) {
				$family = $this->available_fonts[0];
				$style = '';
			}

			else {
				$found = 0;
				// else substitute font of similar type
				if (in_array($family,$this->sans_fonts)) { 
					$i = array_intersect($this->sans_fonts,$this->available_fonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_fonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}
				else if (in_array($family,$this->serif_fonts)) { 
					$i = array_intersect($this->serif_fonts,$this->available_fonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_fonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}
				else if (in_array($family,$this->mono_fonts)) {
					$i = array_intersect($this->mono_fonts,$this->available_fonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_fonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}

				if (!$found) {
					// set first available font
					$fs = $this->available_unifonts[0];
					// mPDF 4.0 Added 0-9
					preg_match('/^([a-z_0-9]+)([BI]{0,2})$/',$fs,$fas);
					// with requested style if possible
					$ws = $fas[1].$style;
					if (in_array($ws,$this->available_fonts)) {
						$family = $fas[1]; // leave $style as is
					}
					else if (in_array($fas[1],$this->available_fonts)) {
					// or without style
						$family = $fas[1];
						$style = '';
					}
					else {
					// or with the style specified 
						$family = $fas[1];
						$style = $fas[2];
					}
				}
			}
			$fontkey = $family.$style; 
		}

		// mPDF 4.0
		if(!isset($this->fonts[$fontkey])) 	{
			// STANDARD CORE FONTS
			if (isset($this->CoreFonts[$fontkey])) {
				//Load metric file
				$file=$family;
				if($family=='times' || $family=='helvetica' || $family=='courier') { $file.=strtolower($style); }
				$file.='.php';
				include(_MPDF_PATH.'font/'.$file);
				if(!isset($cw)) { $this->Error('Could not include font metric file'); }
				$i=count($this->fonts)+$this->extraFontSubsets+1;
				$this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw);
			}
			else {
				// try to add font 
				$this->AddFont($family, $style);
			}
		}
		//Test if font is already selected
		if(($this->FontFamily == $family) AND ($this->FontStyle == $style) AND ($this->FontSizePt == $size) && !$forcewrite) {
			return $family;
		}
		//Select it
		$this->FontFamily=$family;
		$this->FontStyle=$style;
		$this->FontSizePt=$size;
		$this->FontSize=$size/$this->k;
		$this->CurrentFont=&$this->fonts[$fontkey];
		if ($write) { 
			$fontout = (sprintf('BT /F%d %.3f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}
		// Added - currentfont (lowercase) used in HTML2PDF
		$this->currentfontfamily=$family;
		$this->currentfontsize=$size;
		$this->currentfontstyle=$style.($this->underline ? 'U' : '');

	return $family;
}

function SetFontSize($size,$write=true)
{
	//Set font size in points
	if($this->FontSizePt==$size) return;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	$this->currentfontsize=$size;
		if ($write) { 
			$fontout = (sprintf('BT /F%d %.3f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			// Edited mPDF 3.0
			if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}
}

function AddLink()
{
	//Create a new internal link
	$n=count($this->links)+1;
	$this->links[$n]=array(0,0);
	return $n;
}

function SetLink($link,$y=0,$page=-1)
{
	//Set destination of internal link
	if($y==-1) $y=$this->y;
	if($page==-1)	$page=$this->page;
	$this->links[$link]=array($page,$y);
}

function Link($x,$y,$w,$h,$link)
{
	if ($this->keep_block_together) {	// Save to array - don't write yet
		$this->ktLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
		return;
	}
	else if ($this->table_rotate) {	// *TABLES*
		$this->tbrot_Links[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);	// *TABLES*
		return;	// *TABLES*
	}	// *TABLES*
	else if ($this->kwt) {
		$this->kwt_Links[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
		return;
	}
	// mPDF 4.0
	if ($this->writingHTMLheader || $this->writingHTMLfooter) {
		$this->HTMLheaderPageLinks[]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
		return;
	}
	//Put a link on the page
	$this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
	// Save cross-reference to Column buffer

}

function Text($x,$y,$txt)
{
	// Output a string
	// Called (only) by Watermark
	// Expects input to be mb_encoded if necessary and RTL reversed
	// NON_BREAKING SPACE
	// mPDF 4.0 
	$this->CurrentFont['used']= true;
	// mPDF 4.0 Subset fonts are unibyte at output stage
	if ($this->useSubsets && $this->CurrentFont['type']=='Type1subset' && !$this->isCJK && !$this->usingCoreFont) {
	      $txt2 = str_replace($this->chrs[160],$this->chrs[32],$txt);
		$txt2 = $this->UTF8toSubset($txt2);
		$s=sprintf('BT %.3f %.3f Td %s Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt2);
	}
	else {
	      $txt2 = str_replace($this->chrs[160],$this->chrs[32],$txt);
		$s=sprintf('BT %.3f %.3f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt2));
	}
	if($this->underline and $txt!='') {
		$s.=' '.$this->_dounderline($x,$y + (0.1* $this->FontSize),$txt);
	}
	if($this->ColorFlag) $s='q '.$this->TextColor.' '.$s.' Q';
	$this->_out($s);
}


// mPDF 4.0
function ResetSpacing() {
	if ($this->ws != 0) { $this->_out('BT 0 Tw ET'); }
	$this->ws=0;
	if ($this->charspacing != 0) { $this->_out('BT 0 Tc ET'); }
	$this->charspacing=0;
}

// mPDF 4.0
function SetSpacing($cs,$ws) {
	if ($cs) { $this->_out(sprintf('BT %.3f Tc ET',$cs)); }
	else if ($this->charspacing != 0) { $this->_out('BT 0 Tc ET'); }
	$this->charspacing=$cs;
	if ($ws) { $this->_out(sprintf('BT %.3f Tw ET',$ws)); }
	else if ($this->ws != 0) { $this->_out('BT 0 Tw ET'); }
	$this->ws=$ws;
}

// WORD SPACING
function GetJspacing($nc,$ns,$w) {
	$ws = 0; 
	$charspacing = 0;
	$ww = $this->jSWord;
	$ncx = $nc-1;
	if ($nc == 0 && $ns == 0) { return array(0,0); }
	if ($nc==1) { $charspacing = $w; }
	else if ($this->jSpacing == 'C') {
		if ($nc) { $charspacing = $w / ($ncx ); }
	}
	else if ($this->jSpacing == 'W') {
		if ($ns) { $ws = $w / $ns; }
	}
	else if (!$ns) {
		if ($nc) { $charspacing = $w / ($ncx ); }
		if (($this->jSmaxChar > 0) && ($charspacing > $this->jSmaxChar)) { 
			$charspacing = $this->jSmaxChar;
		}
	}
	else if ($ns == ($ncx )) {
		$charspacing = $w / $ns;
	}
	else {
		if ($nc) { 
			$cs = ($w * (1 - $this->jSWord)) / ($ncx );
			if (($this->jSmaxChar > 0) && ($cs > $this->jSmaxChar)) {
				$cs = $this->jSmaxChar;
				$ww = 1 - (($cs * ($ncx ))/$w);
			}
			$charspacing = $cs; 
			$ws = ($w * ($ww) ) / $ns;
		}
	}
	return array($charspacing,$ws); 
}

// mPDF 4.0 Last parameter added spanbgcolor
function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='', $currentx=0, $lcpaddingL=0, $lcpaddingR=0, $valign='M', $spanfill=0) //EDITEI
{
	//Output a cell
	// Expects input to be mb_encoded if necessary and RTL reversed
	// NON_BREAKING SPACE
	      $txt = str_replace($this->chrs[160],$this->chrs[32],$txt);

	$k=$this->k;

	$oldcolumn = $this->CurrCol;
	// Automatic page break
	// Allows PAGE-BREAK-AFTER = avoid to work

	if ((($this->y+$this->divheight>$this->PageBreakTrigger) || ($this->y+$h>$this->PageBreakTrigger) || 
		($this->y+($h*2)>$this->PageBreakTrigger && $this->blk[$this->blklvl]['page_break_after_avoid'])) and !$this->InFooter and $this->AcceptPageBreak()) {
		$x=$this->x;//Current X position


		// WORD SPACING
		$ws=$this->ws;//Word Spacing
		$charspacing=$this->charspacing;//Character Spacing
		// mPDF 4.0
		$this->ResetSpacing();

		$this->AddPage($this->CurOrientation);
		// Added to correct for OddEven Margins
		$x += $this->MarginCorrection;
		if ($currentx) { 
			$currentx += $this->MarginCorrection;
		} 
		$this->x=$x;
		// WORD SPACING
		// mPDF 4.0
		$this->SetSpacing($charspacing,$ws);
	}


	// KEEP BLOCK TOGETHER Update/overwrite the lowest bottom of printing y value on first page
	if ($this->keep_block_together) {
		if ($h) { $this->ktBlock[$this->page]['bottom_margin'] = $this->y+$h; }
//		else { $this->ktBlock[$this->page]['bottom_margin'] = $this->y+$this->divheight; }
	}

	if($w==0) $w = $this->w-$this->rMargin-$this->x;
	$s='';
	if($fill==1 && $this->FillColor) { 
		// Edited mPDF 3.0
		if((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor']) || $this->keep_block_together) { $s .= $this->FillColor.' '; }
		$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
	}

	// mPDF 4.0
	$boxtop = $this->y;
	$boxheight = $h;
	$boxbottom = $this->y+$h;

	if($txt!='')
	{
		// FONT SIZE - this determines the baseline caculation
		if ($this->linemaxfontsize && !$this->processingHeader) { $bfs = $this->linemaxfontsize; }
		else  { $bfs = $this->FontSize; }

    		//Calculate baseline Superscript and Subscript Y coordinate adjustment
		$bfx = 0.35;
    		$baseline = $bfx*$bfs;
		if($this->SUP) { $baseline += ($bfx-1.05)*$this->FontSize; }
		else if($this->SUB) { $baseline += ($bfx + 0.04)*$this->FontSize; }
		else if($this->bullet) { $baseline += ($bfx-0.7)*$this->FontSize; }

		// Vertical align (for Images)
		if ($this->lineheight_correction) { 
			if ($valign == 'T') { $va = (0.5 * $bfs * $this->lineheight_correction); }
			else if ($valign == 'B') { $va = $h-(0.5 * $bfs * $this->lineheight_correction); }
			else { $va = 0.5*$h; }	// Middle - default
		}
		else { 
			if ($valign == 'T') { $va = (0.5 * $bfs * $this->default_lineheight_correction); }
			else if ($valign == 'B') { $va = $h-(0.5 * $bfs * $this->default_lineheight_correction); }
			else { $va = 0.5*$h; }	// Middle - default
		}

		// ONLY SET THESE IF WANT TO CONFINE BORDER +- FILL TO FIT FONTSIZE - NOT FULL CELL
		if ($spanfill) {
			$boxtop = $this->y+$baseline+$va-($this->FontSize*(0.5+$bfx));
			$boxheight = $this->FontSize;
			$boxbottom = $boxtop + $boxheight;
		}
	}


	if($fill==1 or $border==1)
	{
		if ($fill==1) $op=($border==1) ? 'B' : 'f';
		else $op='S';
		$s.=sprintf('%.3f %.3f %.3f %.3f re %s ',$this->x*$k,($this->h-$boxtop)*$k,$w*$k,-$boxheight*$k,$op);
	}

	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(is_int(strpos($border,'L')))
			$s.=sprintf('%.3f %.3f m %.3f %.3f l S ',$x*$k,($this->h-$boxtop)*$k,$x*$k,($this->h-($boxbottom))*$k);
		if(is_int(strpos($border,'T')))
			$s.=sprintf('%.3f %.3f m %.3f %.3f l S ',$x*$k,($this->h-$boxtop)*$k,($x+$w)*$k,($this->h-$boxtop)*$k);
		if(is_int(strpos($border,'R')))
			$s.=sprintf('%.3f %.3f m %.3f %.3f l S ',($x+$w)*$k,($this->h-$boxtop)*$k,($x+$w)*$k,($this->h-($boxbottom))*$k);
		if(is_int(strpos($border,'B')))
			$s.=sprintf('%.3f %.3f m %.3f %.3f l S ',$x*$k,($this->h-($boxbottom))*$k,($x+$w)*$k,($this->h-($boxbottom))*$k);
	}

	if($txt!='')
	{
		$stringWidth = $this->GetStringWidth($txt) + ( $this->charspacing * mb_strlen( $txt, $this->mb_enc ) / $k )
				 + ( $this->ws * mb_substr_count( $txt, ' ', $this->mb_enc ) / $k );

		// Set x OFFSET FOR PRINTING
		if($align=='R') {
			$dx=$w-$this->cMarginR - $stringWidth - $lcpaddingR;
		}
		elseif($align=='C') {
			$dx=(($w - $stringWidth )/2);
		}
		elseif($align=='L' or $align=='J') $dx=$this->cMarginL + $lcpaddingL;
    		else $dx = 0;


		if($this->ColorFlag) $s.='q '.$this->TextColor.' ';

		// OUTLINE
		if($this->outline_on)
		{
			$s.=' '.sprintf('%.3f w',$this->LineWidth*$k).' ';
			$s.=" $this->DrawColor ";
			$s.=" 2 Tr ";
    		}

		// THE TEXT
		// mPDF 4.0 
		$this->CurrentFont['used']= true;
		// WORD SPACING
		// IF multibyte - Tw has no effect - need to do word spacing by setting character spacing for spaces between words
		// mPDF 4.0 Subset fonts are unibyte at output stage
		  $txt2= $txt;
		  // mPDF 4.0 Subset fonts
		  if ($this->useSubsets && $this->CurrentFont['type']=='Type1subset' && !$this->isCJK && !$this->usingCoreFont) {
			$txt2 = $this->UTF8toSubset($txt2);
			$s.=sprintf('BT %.3f %.3f Td %s Tj ET',($this->x+$dx)*$k,($this->h-($this->y+$baseline+$va))*$k,$txt2);
		  }
		  else {
			$txt2=$this->_escape($txt2); 
			$s.=sprintf('BT %.3f %.3f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+$baseline+$va))*$k,$txt2);
		  }

		// UNDERLINE
		// mPDF 4.0
		if($this->underline) {
			$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			if($this->FillColor!=$c) { $s.= ' '.$c.' '; }
			if (isset($this->CurrentFont['up'])) { $up=$this->CurrentFont['up']; }
			else { $up = -100; }
			$adjusty = (-$up/1000* $this->FontSize);	
 			if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
			else { $ut = 60/1000* $this->FontSize; }
			$olw = $this->LineWidth;
			$s.=' '.(sprintf(' %.3f w',$ut*$this->k));
			$s.=' '.$this->_dounderline($this->x+$dx,$this->y+$baseline+$va+$adjusty,$txt);
			$s.=' '.(sprintf(' %.3f w',$olw*$this->k));
			if($this->FillColor!=$c) { $s.= ' '.$this->FillColor.' '; }
		}

   		// STRIKETHROUGH
		// mPDF 4.0
		if($this->strike) {
			$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			if($this->FillColor!=$c) { $s.= ' '.$c.' '; }
    			//Superscript and Subscript Y coordinate adjustment (now for striked-through texts)
			if (isset($this->CurrentFont['desc']['CapHeight'])) { $ch=$this->CurrentFont['desc']['CapHeight']; }
			else { $ch = 700; }
			$adjusty = (-$ch/1000* $this->FontSize) * 0.35;	
 			if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
			else { $ut = 60/1000* $this->FontSize; }
			$olw = $this->LineWidth;
			$s.=' '.(sprintf(' %.3f w',$ut*$this->k));
			$s.=' '.$this->_dounderline($this->x+$dx,$this->y+$baseline+$va+$adjusty,$txt);
			$s.=' '.(sprintf(' %.3f w',$olw));
			if($this->FillColor!=$c) { $s.= ' '.$this->FillColor.' '; }
		}

		// COLOR
		if($this->ColorFlag) $s.=' Q';

		// LINK
		if($link!='') {
			$this->Link($this->x+$dx,$this->y+$va-.5*$this->FontSize,$stringWidth,$this->FontSize,$link);
		}
	}
	if($s) $this->_out($s);

	// WORD SPACING

	$this->lasth=$h;
	if( strpos($txt,"\n") !== false) $ln=1; //EDITEI - cell now recognizes \n! << comes from <BR> tag
	if($ln>0)
	{
		//Go to next line
		$this->y += $h;
		if($ln==1) //EDITEI
		{
			//Move to next line
			if ($currentx != 0) { $this->x=$currentx; }	
			else { $this->x=$this->lMargin; }
   		}
	}
	else $this->x+=$w;


}


function MultiCell($w,$h,$txt,$border=0,$align='',$fill=0,$link='',$directionality='ltr',$encoded=false)
{
	// Parameter (pre-)encoded - When called internally from ToC or textarea: mb_encoding already done - but not reverse RTL/Indic
	if (!$encoded) {
		$txt = $this->purify_utf8_text($txt);
		if ($this->text_input_as_HTML) {
			$txt = $this->all_entities_to_utf8($txt);
		}
		if (!$this->is_MB) { $txt = mb_convert_encoding($txt,$this->mb_enc,'UTF-8'); }
		// mPDF 4.0 Font-specific ligature substitution for Indic fonts
	}


	if (!$align) { $align = $this->defaultAlign; }

	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)	$w=$this->w-$this->rMargin-$this->x;

		$wmax=($w- ($this->cMarginL+$this->cMarginR))*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		while($nb>0 and $s[$nb-1]=="\n")	$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(is_int(strpos($border,'L')))	$b2.='L';
			if(is_int(strpos($border,'R')))	$b2.='R';
			$b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;


	while($i<$nb)
	{
		//Get next character
		$c=substr($s,$i,1);
		if(preg_match("/[\n]/u", $c)) {
			//Explicit line break
			// WORD SPACING
			// mPDF 4.0
			$this->ResetSpacing();
			$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
			continue;
		}
		if(preg_match("/[ ]/u", $c)) {
			$sep=$i;
			$ls=$l;
			$ns++;
		}

		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j) $i++;
				// WORD SPACING
				// mPDF 4.0
				$this->ResetSpacing();
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link);
			}
			else
			{
				if($align=='J')
				{
					$tmp = rtrim(substr($s,$j,$sep-$j));
					//////////////////////////////////////////
					// JUSTIFY J using Unicode fonts (Word spacing doesn't work)
					// WORD SPACING NON_UNICDOE/CJK
					// Change NON_BREAKING SPACE to spaces so they are 'spaced' properly
					$tmp = str_replace($this->chrs[160],$this->chrs[32],$tmp);
					$len_ligne = $this->GetStringWidth($tmp );
					$nb_carac = strlen( $tmp ) ;  
					$nb_spaces = substr_count( $tmp ,' ' ) ;  
					list($charspacing,$ws) = $this->GetJspacing($nb_carac,$nb_spaces,((($w-2) - $len_ligne) * $this->k));
					// mPDF 4.0
					$this->SetSpacing($charspacing,$ws);
					//////////////////////////////////////////
				}
				$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
		}
		else $i++;
	}
	//Last chunk
	// WORD SPACING
	// mPDF 4.0
	$this->ResetSpacing();


	//Last chunk
   if($border and is_int(strpos($border,'B')))	$b.='B';
   if ($this->is_MB)  {
		$tmp = $this->mb_rtrim(mb_substr($s,$j,$i-$j,$this->mb_enc),'UTF-8');
		// DIRECTIONALITY
   		$this->Cell($w,$h,$tmp,$b,2,$align,$fill,$link);
   }
   else { $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link); }
   $this->x=$this->lMargin;
}




function saveInlineProperties() {
   $saved = array();
   $saved[ 'family' ] = $this->FontFamily;
   $saved[ 'style' ] = $this->FontStyle;
   $saved[ 'sizePt' ] = $this->FontSizePt;
   $saved[ 'size' ] = $this->FontSize;
   $saved[ 'HREF' ] = $this->HREF; 
   $saved[ 'underline' ] = $this->underline; 
   $saved[ 'strike' ] = $this->strike;
   $saved[ 'SUP' ] = $this->SUP; 
   $saved[ 'SUB' ] = $this->SUB; 
   $saved[ 'linewidth' ] = $this->LineWidth;
   $saved[ 'drawcolor' ] = $this->DrawColor;
   $saved[ 'is_outline' ] = $this->outline_on;
   $saved[ 'outlineparam' ] = $this->outlineparam;
   $saved[ 'toupper' ] = $this->toupper;
   $saved[ 'tolower' ] = $this->tolower;

   $saved[ 'I' ] = $this->I;
   $saved[ 'B' ] = $this->B;
   $saved[ 'colorarray' ] = $this->colorarray;
   $saved[ 'bgcolorarray' ] = $this->spanbgcolorarray;
   $saved[ 'color' ] = $this->TextColor; 
   $saved[ 'bgcolor' ] = $this->FillColor;
   $saved['lang'] = $this->currentLang;

   return $saved;
}

function restoreInlineProperties( $saved) {

   // mPDF 4.0 $this->FontFamily Resets below - don't change here - change via SetFont()
	// or it will not change $this->CurrentFont
   //$this->FontFamily = $saved[ 'family' ];
   $FontFamily = $saved[ 'family' ];

   $this->FontStyle = $saved[ 'style' ];
   $this->FontSizePt = $saved[ 'sizePt' ];
   $this->FontSize = $saved[ 'size' ];

   $this->currentLang =  $saved['lang'];

   $this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well

   $this->HREF = $saved[ 'HREF' ]; //EDITEI
   $this->underline = $saved[ 'underline' ]; //EDITEI
   $this->strike = $saved[ 'strike' ]; //EDITEI
   $this->SUP = $saved[ 'SUP' ]; //EDITEI
   $this->SUB = $saved[ 'SUB' ]; //EDITEI
   $this->LineWidth = $saved[ 'linewidth' ]; //EDITEI
   $this->DrawColor = $saved[ 'drawcolor' ]; //EDITEI
   $this->outline_on = $saved[ 'is_outline' ]; //EDITEI
   $this->outlineparam = $saved[ 'outlineparam' ];

   $this->toupper = $saved[ 'toupper' ];
   $this->tolower = $saved[ 'tolower' ];

   // mPDF 4.0
   $this->SetFont($FontFamily, $saved[ 'style' ].($this->underline ? 'U' : ''),$saved[ 'sizePt' ],false);
   //$this->currentfontfamily = $saved[ 'family' ];

   $this->currentfontstyle = $saved[ 'style' ].($this->underline ? 'U' : '');
   $this->currentfontsize = $saved[ 'sizePt' ];
   $this->SetStyle('U',$this->underline);
   $this->SetStyle('B',$saved[ 'B' ]);
   $this->SetStyle('I',$saved[ 'I' ]);

   $this->TextColor = $saved[ 'color' ]; //EDITEI
   $this->FillColor = $saved[ 'bgcolor' ]; //EDITEI
   $this->colorarray = $saved[ 'colorarray' ];
   	$cor = $saved[ 'colorarray' ];
   	if ($cor) $this->SetTextColor($cor['R'],$cor['G'],$cor['B']);
   $this->spanbgcolorarray = $saved[ 'bgcolorarray' ];
   	$cor = $saved[ 'bgcolorarray' ];
   	if ($cor) $this->SetFillColor($cor['R'],$cor['G'],$cor['B']);
}



// mPDF 3.0
// Used when ColActive for tables - updated to return first block with background fill OR borders
function GetFirstBlockFill() {
	// Returns the first blocklevel that uses a bgcolor fill
	$startfill = 0;
	for ($i=1;$i<=$this->blklvl;$i++) {
		if ($this->blk[$i]['bgcolor'] || $this->blk[$i]['border_left']['w'] || $this->blk[$i]['border_right']['w']  || $this->blk[$i]['border_top']['w']  || $this->blk[$i]['border_bottom']['w']  ) {
			$startfill = $i;
			break;
		}
	}
	return $startfill;
}

function SetBlockFill($blvl) {
	if ($this->blk[$blvl]['bgcolor']) {
		$this->SetFillColor($this->blk[$blvl]['bgcolorarray']['R'],$this->blk[$blvl]['bgcolorarray']['G'],$this->blk[$blvl]['bgcolorarray']['B']);
		return 1;
	}
	else {
		$this->SetFillColor(255);
		return 0;
	}
}


//-------------------------FLOWING BLOCK------------------------------------//
//EDITEI some things (added/changed)                                        //
//The following functions were originally written by Damon Kohler           //
//--------------------------------------------------------------------------//

function saveFont()
{
   $saved = array();
   $saved[ 'family' ] = $this->FontFamily;
   $saved[ 'style' ] = $this->FontStyle;
   $saved[ 'sizePt' ] = $this->FontSizePt;
   $saved[ 'size' ] = $this->FontSize;
   $saved[ 'curr' ] = &$this->CurrentFont;
   $saved[ 'color' ] = $this->TextColor; //EDITEI
   $saved[ 'spanbgcolor' ] = $this->spanbgcolor; //EDITEI
   $saved[ 'spanbgcolorarray' ] = $this->spanbgcolorarray; //EDITEI
   $saved[ 'HREF' ] = $this->HREF; //EDITEI
   $saved[ 'underline' ] = $this->underline; //EDITEI
   $saved[ 'strike' ] = $this->strike; //EDITEI
   $saved[ 'SUP' ] = $this->SUP; //EDITEI
   $saved[ 'SUB' ] = $this->SUB; //EDITEI
   $saved[ 'linewidth' ] = $this->LineWidth; //EDITEI
   $saved[ 'drawcolor' ] = $this->DrawColor; //EDITEI
   $saved[ 'is_outline' ] = $this->outline_on; //EDITEI
   $saved[ 'outlineparam' ] = $this->outlineparam;
   return $saved;
}

function restoreFont( $saved, $write=true)
{
   // mPDF 3.0
   if (!isset($saved) || empty($saved)) return;

   $this->FontFamily = $saved[ 'family' ];
   $this->FontStyle = $saved[ 'style' ];
   $this->FontSizePt = $saved[ 'sizePt' ];
   $this->FontSize = $saved[ 'size' ];
   $this->CurrentFont = &$saved[ 'curr' ];
   $this->TextColor = $saved[ 'color' ]; //EDITEI
   $this->spanbgcolor = $saved[ 'spanbgcolor' ]; //EDITEI
   $this->spanbgcolorarray = $saved[ 'spanbgcolorarray' ]; //EDITEI
   $this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well
   $this->HREF = $saved[ 'HREF' ]; //EDITEI
   $this->underline = $saved[ 'underline' ]; //EDITEI
   $this->strike = $saved[ 'strike' ]; //EDITEI
   $this->SUP = $saved[ 'SUP' ]; //EDITEI
   $this->SUB = $saved[ 'SUB' ]; //EDITEI
   $this->LineWidth = $saved[ 'linewidth' ]; //EDITEI
   $this->DrawColor = $saved[ 'drawcolor' ]; //EDITEI
   $this->outline_on = $saved[ 'is_outline' ]; //EDITEI
   $this->outlineparam = $saved[ 'outlineparam' ];
   if ($write) { 
   	$this->SetFont($saved[ 'family' ],$saved[ 'style' ].($this->underline ? 'U' : ''),$saved[ 'sizePt' ],true,true);	// force output
	$fontout = (sprintf('BT /F%d %.3f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
	// Edited mPDF 3.0
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
	$this->pageoutput[$this->page]['Font'] = $fontout;
   }
   else 
   	$this->SetFont($saved[ 'family' ],$saved[ 'style' ].($this->underline ? 'U' : ''),$saved[ 'sizePt' ]);
}

function newFlowingBlock( $w, $h, $a = '', $is_table = false, $is_list = false, $blockstate = 0, $newblock=true )
{
   if (!$a) { $a = $this->defaultAlign; }
   // cell width in points

   $this->flowingBlockAttr[ 'width' ] = ($w * $this->k);
   // line height in user units
   $this->flowingBlockAttr[ 'is_table' ] = $is_table;
   $this->flowingBlockAttr[ 'is_list' ] = $is_list;
   $this->flowingBlockAttr[ 'height' ] = $h;
   $this->flowingBlockAttr[ 'lineCount' ] = 0;
   $this->flowingBlockAttr[ 'align' ] = $a;
   $this->flowingBlockAttr[ 'font' ] = array();
   $this->flowingBlockAttr[ 'content' ] = array();
   $this->flowingBlockAttr[ 'contentWidth' ] = 0;
   $this->flowingBlockAttr[ 'blockstate' ] = $blockstate;

   $this->flowingBlockAttr[ 'newblock' ] = $newblock;
   $this->flowingBlockAttr[ 'valign' ] = 'M';
}

function finishFlowingBlock($endofblock=false)
{
   $currentx = $this->x;
   //prints out the last chunk
   $is_table = $this->flowingBlockAttr[ 'is_table' ];
   $is_list = $this->flowingBlockAttr[ 'is_list' ];
   $maxWidth =& $this->flowingBlockAttr[ 'width' ];
   $lineHeight =& $this->flowingBlockAttr[ 'height' ];
   $align =& $this->flowingBlockAttr[ 'align' ];
   $content =& $this->flowingBlockAttr[ 'content' ];
   $font =& $this->flowingBlockAttr[ 'font' ];
   $contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
   $lineCount =& $this->flowingBlockAttr[ 'lineCount' ];
   $valign =& $this->flowingBlockAttr[ 'valign' ];
   $blockstate = $this->flowingBlockAttr[ 'blockstate' ];

   $newblock = $this->flowingBlockAttr[ 'newblock' ];



	// *********** BLOCK BACKGROUND COLOR *****************//
	if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
		// mPDF 3.0 - Tiling Patterns
		$fill = 0;
//		$fill = 1;
//		$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
//		$this->SetFillColor($bcor['R'],$bcor['G'],$bcor['B']);
	}
	else {
		$this->SetFillColor(255);
		$fill = 0;
	}

	// mPDF 4.0
//	if ($this->ws != 0) { $this->_out('BT 0 Tw ET'); }
//	$this->ws=0;
//	if ($this->charspacing != 0) { $this->_out('BT 0 Tc ET'); }
//	$this->charspacing=0;

	// the amount of space taken up so far in user units
	$usedWidth = 0;

	// COLS
	$oldcolumn = $this->CurrCol;


	// Print out each chunk

	if ($is_table) { 
		$ipaddingL = 0; 
		$ipaddingR = 0; 
		$paddingL = 0;
		$paddingR = 0;
	} 
	else { 
		$ipaddingL = $this->blk[$this->blklvl]['padding_left']; 
		$ipaddingR = $this->blk[$this->blklvl]['padding_right']; 
		$paddingL = ($ipaddingL * $this->k); 
		$paddingR = ($ipaddingR * $this->k);
		$this->cMarginL =  $this->blk[$this->blklvl]['border_left']['w'];
		$this->cMarginR =  $this->blk[$this->blklvl]['border_right']['w'];

		// Added mPDF 3.0 Float DIV
		$fpaddingR = 0;
		$fpaddingL = 0;
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if ($r_exists) { $fpaddingR = $r_width; }
			if ($l_exists) { $fpaddingL = $l_width; }
		}

		$usey = $this->y + 0.002;
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
			$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		}
		// If float exists at this level
		if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
		if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
	}	// *TABLES*

		if ($is_list && is_array($this->bulletarray) && isset($this->bulletarray['occur']) && isset($this->list_lineheight[$this->listlvl][$this->bulletarray['occur']])) { 	// *LISTS*
			$this->lineheight_correction = $this->list_lineheight[$this->listlvl][$this->bulletarray['occur']]; 	// *LISTS*
		} 	// *LISTS*
		else	// *LISTS*
		if ($is_table) {	// *TABLES*
			$this->lineheight_correction = $this->table_lineheight; 	// *TABLES*
		}	// *TABLES*
		else	// *TABLES*
		if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
			$this->lineheight_correction = $this->blk[$this->blklvl]['line_height']; 
		} 
		else {
			$this->lineheight_correction = $this->default_lineheight_correction; 
		}

		//  correct lineheight to maximum fontsize
		$maxlineHeight = 0;
		$maxfontsize = 0;
		foreach ( $content as $k => $chunk )
		{
              $this->restoreFont( $font[ $k ],false );
		  if (isset($this->objectbuffer[$k])) { 
			$maxlineHeight = max($maxlineHeight,$this->objectbuffer[$k]['OUTER-HEIGHT']);
		  }
              else { 
			if ($this->FontFamily!='symbol' && $this->FontFamily!='zapfdingbats') {
			      $content[$k] = $chunk = str_replace($this->chrs[173],'',$chunk );
			}
			// Special case of sub/sup carried over on its own to last line
			if (($this->SUB || $this->SUP) && count($content)==1) { $actfs = $this->FontSize*100/55; } // 55% is font change for sub/sup
			else { $actfs = $this->FontSize; }
			$maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction ); 
			$maxfontsize = max($maxfontsize,$actfs);
		  }
		}

		if ($is_list && is_array($this->bulletarray) && count($this->bulletarray)) {
	  		$actfs = $this->bulletarray['fontsize'];
			$maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction ); 
			$maxfontsize = max($maxfontsize,$actfs);
		}


		$lineHeight = $maxlineHeight;
		$this->linemaxfontsize = $maxfontsize;

		// Get PAGEBREAK TO TEST for height including the bottom border/padding
		$check_h = max($this->divheight,$lineHeight);

		if ($this->blklvl > 0 && !$is_table) { 
		   if ($endofblock && $blockstate > 1) { 
			if ($this->blk[$this->blklvl]['page_break_after_avoid']) {  $check_h += $lineHeight; }
			$check_h += ($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']);
		   }
		   if (($newblock && ($blockstate==1 || $blockstate==3) && $lineCount == 0) || ($endofblock && $blockstate > 1 && $lineCount == 0)) { 
			$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
		   }
		}


		// PAGEBREAK
		//'If' below used in order to fix "first-line of other page with justify on" bug
		if($this->y+$check_h > $this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
     	     		$bak_x=$this->x;//Current X position
			// WORD SPACING
			$ws=$this->ws;//Word Spacing
			$charspacing=$this->charspacing;//Character Spacing
			// mPDF 4.0
			$this->ResetSpacing();

		      $this->AddPage($this->CurOrientation);

		      $this->x=$bak_x;
			// Added to correct for OddEven Margins
			$currentx += $this->MarginCorrection;
			$this->x += $this->MarginCorrection;

			// WORD SPACING
			// mPDF 4.0
			$this->SetSpacing($charspacing,$ws);
		}


		// TOP MARGIN
		if ($newblock && ($blockstate==1 || $blockstate==3) && ($this->blk[$this->blklvl]['margin_top']) && $lineCount == 0 && !$is_table && !$is_list) { 
			$this->DivLn($this->blk[$this->blklvl]['margin_top'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
		}

		if ($newblock && ($blockstate==1 || $blockstate==3) && $lineCount == 0 && !$is_table && !$is_list) { 
			$this->blk[$this->blklvl]['y0'] = $this->y;
			$this->blk[$this->blklvl]['startpage'] = $this->page;
		}

	// ADDED for Paragraph_indent
	$WidthCorrection = 0;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align != 'C')) { 
		// mPDF 4.0
		$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		$WidthCorrection = ($ti*$this->k); 
	} 


	// PADDING and BORDER spacing/fill
	if (($newblock) && ($blockstate==1 || $blockstate==3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 0) && (!$is_table) && (!$is_list)) { 
			// mPDF 3.0 Also does border when Columns active
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'],-3,true,false,1); 
			$this->x = $currentx;
	}


	// Added mPDF 3.0 Float DIV
	$fpaddingR = 0;
	$fpaddingL = 0;
	if (count($this->floatDivs)) {
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
		if ($r_exists) { $fpaddingR = $r_width; }
		if ($l_exists) { $fpaddingL = $l_width; }
	}

	$usey = $this->y + 0.002;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
		$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
	}
	// If float exists at this level
	if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
	if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }

	if ($content) {
		$empty = $maxWidth - $WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* $this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) );
		$empty /= $this->k;

		// In FinishFlowing Block no lines are justified as it is always last line
		// but if orphansAllowed have allowed content width to go over max width, use J charspacing to compress line
		// JUSTIFICATION J - NOT!
		$nb_carac = 0;
		$nb_spaces = 0;
		foreach ( $content as $k => $chunk ) {
			if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
				      $chunk = str_replace($this->chrs[160],$this->chrs[32],$chunk );
				$nb_carac += mb_strlen( $chunk, $this->mb_enc );  
				$nb_spaces += mb_substr_count( $chunk,' ', $this->mb_enc );  
			}
		}
		// if it's justified, we need to find the char/word spacing (or if orphans have allowed length of line to go over the maxwidth)
		// If "orphans" in fact is just a final space - ignore this
		if (($contentWidth > $maxWidth) && ($content[count($content)-1] != ' ') )  {
 		  // WORD SPACING
			list($charspacing,$ws) = $this->GetJspacing($nb_carac,$nb_spaces,($maxWidth-$contentWidth-$WidthCorrection-(($this->cMarginL+$this->cMarginR)*$this->k)-($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) )));
			// mPDF 4.0
			$this->SetSpacing($charspacing,$ws);
		}
		// mPDF 4.0 Check if will fit at word/char spacing of previous line - if so continue it
		// but only allow a maximum of $this->jSmaxWordLast and $this->jSmaxCharLast
		else if ($contentWidth < ($maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)* $this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k)))) {
			if ($this->ws > $this->jSmaxWordLast) {
				$ws=$this->jSmaxWord;
				$this->_out(sprintf('BT %.3f Tw ET',$ws)); 
				$this->ws=$ws;
			}
			if ($this->charspacing > $this->jSmaxCharLast) {
				$charspacing=$this->jSmaxCharLast;
				$this->_out(sprintf('BT %.3f Tc ET',$charspacing)); 
				$this->charspacing=$charspacing;
			}
			$check = $maxWidth - $WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* $this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) ) - ( $this->charspacing * $nb_carac) - ( $this->ws * $nb_spaces);
			if ($check <= 0) {
				// mPDF 4.0
				$this->ResetSpacing();
			}
		}
		else {
			// mPDF 4.0
			$this->ResetSpacing();
		}

		$empty = $maxWidth - $WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* $this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) ) - ( $this->charspacing * $nb_carac) - ( $this->ws * $nb_spaces);
		$empty /= $this->k;

		// mPDF 4.0
		if (!$is_table) { 
			$this->maxPosR = max($this->maxPosR , ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin'] - $empty)); 
			$this->maxPosL = min($this->maxPosL , ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'] + $empty)); 
		}

		$arraysize = count($content);

		$margins = ($this->cMarginL+$this->cMarginR) + ($ipaddingL+$ipaddingR + $fpaddingR + $fpaddingR );

		if (!$is_table) { $this->DivLn($lineHeight,$this->blklvl,false); }	// false -> don't advance y

		// DIRECTIONALITY RTL
		$all_rtl = false;
		$contains_rtl = false;


		$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL;
		if ($align == 'R') { $this->x += $empty; }
		else if ($align == 'J')	{
			if ($this->directionality == 'rtl' && $contains_rtl) { $this->x += $empty; }
			else if ($this->directionality == 'ltr' && $all_rtl) { $this->x += $empty; }
		}
		else if ($align == 'C') { $this->x += ($empty / 2); }

		// Paragraph INDENT
		$WidthCorrection = 0; 
		if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align !='C')) { 
			// mPDF 4.0
			$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
			$this->x += $ti; 
		}


          foreach ( $content as $k => $chunk )
          {

			// FOR IMAGES
		if (($this->directionality=='rtl' && $contains_rtl) || $all_rtl) { $dirk = $arraysize-1 - $k; } else { $dirk = $k; }

		if (isset($this->objectbuffer[$dirk]) && $this->objectbuffer[$dirk]) {
			$xadj = $this->x - $this->objectbuffer[$dirk]['OUTER-X']; 
			$this->objectbuffer[$dirk]['OUTER-X'] += $xadj;
			$this->objectbuffer[$dirk]['BORDER-X'] += $xadj;
			$this->objectbuffer[$dirk]['INNER-X'] += $xadj;
			if ($valign == 'M' || $valign == '') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y'])+($lineHeight - $this->objectbuffer[$dirk]['OUTER-HEIGHT'])/2;
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			}
			else if ($valign == 'B') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y'])+($lineHeight - $this->objectbuffer[$dirk]['OUTER-HEIGHT']);
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			}
			else if ($valign == 'T') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y']);
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			}
		}



			// DIRECTIONALITY RTL
			if ((($this->directionality == 'rtl') && ($contains_rtl )) || ($all_rtl )) { $this->restoreFont( $font[ $arraysize-1 - $k ] ); }
			else { $this->restoreFont( $font[ $k ] ); }
	 		// *********** SPAN BACKGROUND COLOR ***************** //
			if (isset($this->spanbgcolor) && $this->spanbgcolor) { 
				$cor = $this->spanbgcolorarray;
				$this->SetFillColor($cor['R'],$cor['G'],$cor['B']);
				$save_fill = $fill; $spanfill = 1; $fill = 1;
			}
			// WORD SPACING
		      $stringWidth = $this->GetStringWidth($chunk ) + ( $this->charspacing * mb_strlen($chunk,$this->mb_enc ) / $this->k )  
				+ ( $this->ws * mb_substr_count($chunk,' ',$this->mb_enc ) / $this->k );
			if (isset($this->objectbuffer[$dirk])) { $stringWidth = $this->objectbuffer[$dirk]['OUTER-WIDTH']; }

			// mPDF 4.0 Last parameter of Cell indicates spanbgcolor
			if ($k == $arraysize-1) $this->Cell( $stringWidth, $lineHeight, $chunk, '', 1, '', $fill, $this->HREF , $currentx,0,0,$valign, $fill ); //mono-style line or last part (skips line)
			else $this->Cell( $stringWidth, $lineHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0,0,$valign, $fill );//first or middle part


	 		// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
			if (isset($spanfill) && $spanfill) { 
				$fill = $save_fill; $spanfill = 0; 
				if ($fill) { $this->SetFillColor($bcor['R'],$bcor['G'],$bcor['B']); }
			}
          }

	$this->printobjectbuffer($is_table);

	$this->objectbuffer = array();

	// mPDF 4.0
	$this->ResetSpacing();

	// LIST BULLETS/NUMBERS
	if ($is_list && is_array($this->bulletarray) && ($lineCount == 0) ) {
	  // mPDF 4.0
	  $savedFont = $this->saveFont();

	  $bull = $this->bulletarray;
	  if (isset($bull['level']) && isset($bull['occur']) && isset($this->InlineProperties['LIST'][$bull['level']][$bull['occur']])) { 
		$this->restoreInlineProperties($this->InlineProperties['LIST'][$bull['level']][$bull['occur']]); 
	  }
	  if (isset($bull['level']) && isset($bull['occur']) && isset($bull['num']) && isset($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) && $this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) { $this->restoreInlineProperties($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]); }
	  if (isset($bull['font']) && $bull['font'] == 'zapfdingbats') {
		$this->bullet = true;
		$this->SetFont('zapfdingbats','',$this->FontSizePt/2.5);
	  }
	  else { $this->SetFont($this->FontFamily,$this->FontStyle,$this->FontSizePt,true,true); }	// force output
        //Output bullet
	  $this->x = $currentx;
	  if (isset($bull['x'])) { $this->x += $bull['x']; }
	  $this->y -= $lineHeight;
	  // mPDF 4.0
        if (isset($bull['txt'])) { $this->Cell($bull['w'], $lineHeight,$bull['txt'],'','',$bull['align'],0,'',0,-$this->cMarginL, -$this->cMarginR ); }
	  if (isset($bull['font']) && $bull['font'] == 'zapfdingbats') {
		$this->bullet = false;
	  }
	  $this->x = $currentx;	// Reset
	  $this->y += $lineHeight;



	  // mPDF 3.0 ? does nothing as $saved Font is not defined
	  // mPDF 4.0
	   $this->restoreFont( $savedFont );
	  // $font = array( $savedFont );

	  $this->bulletarray = array();	// prevents repeat of bullet/number if <li>....<br />.....</li>
	}


	}	// END IF CONTENT

	// Update values if set to skipline
	if ($this->floatmargins) { $this->_advanceFloatMargins(); }


	if ($endofblock && $blockstate>1) { 
		// If float exists at this level
		if (isset($this->floatmargins['R']['y1'])) { $fry1 = $this->floatmargins['R']['y1']; }
		else { $fry1 = 0; }
		if (isset($this->floatmargins['L']['y1'])) { $fly1 = $this->floatmargins['L']['y1']; }
		else { $fly1 = 0; }
		if ($this->y < $fry1 || $this->y < $fly1) { 
			$drop = max($fry1,$fly1) - $this->y;
			$this->DivLn($drop); 
			$this->x = $currentx;
		}
	}


	// PADDING and BORDER spacing/fill
	if ($endofblock && ($blockstate > 1) && ($this->blk[$this->blklvl]['padding_bottom'] || $this->blk[$this->blklvl]['border_bottom'] || $this->blk[$this->blklvl]['css_set_height']) && (!$is_table) && (!$is_list)) { 
			// mPDF 4.0 If CSS height set, extend bottom - if on same page as block started, and CSS HEIGHT > actual height, 
			//	and does not force pagebreak
			$extra = 0;
			if ($this->blk[$this->blklvl]['css_set_height'] && $this->blk[$this->blklvl]['startpage']==$this->page) {
				// predicted height
				$h1 = ($this->y-$this->blk[$this->blklvl]['y0']) + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'];
				if ($h1 < $this->blk[$this->blklvl]['css_set_height']) { $extra = $this->blk[$this->blklvl]['css_set_height'] - $h1; }
				if($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra > $this->PageBreakTrigger) {
					$extra = $this->PageBreakTrigger - ($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']); 
				}
			}

			// mPDF 3.0 Also does border when Columns active
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra,-3,true,false,2); 
			$this->x = $currentx;


	}

	// SET Bottom y1 of block (used for painting borders)
	if (($endofblock) && ($blockstate > 1) && (!$is_table) && (!$is_list)) { 
		$this->blk[$this->blklvl]['y1'] = $this->y;
	}

	// BOTTOM MARGIN
	if (($endofblock) && ($blockstate > 1) && ($this->blk[$this->blklvl]['margin_bottom']) && (!$is_table) && (!$is_list)) { 
		if($this->y+$this->blk[$this->blklvl]['margin_bottom'] < $this->PageBreakTrigger and !$this->InFooter) {
		  $this->DivLn($this->blk[$this->blklvl]['margin_bottom'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
		}
	}

	// Reset lineheight
	$lineHeight = $this->divheight;
}





function printobjectbuffer($is_table=false) {
		if ($is_table && $this->shrin_k > 1) { $k = $this->shrin_k; } 
		else { $k = 1; }
		$save_y = $this->y;
		$save_x = $this->x;
		$save_currentfontfamily = $this->FontFamily;
		$save_currentfontsize = $this->FontSizePt;
		$save_currentfontstyle = $this->FontStyle.($this->underline ? 'U' : '');
		if ($this->directionality == 'rtl') { $rtlalign = 'R'; } else { $rtlalign = 'L'; }
		foreach ($this->objectbuffer AS $ib => $objattr) { 
    		   // mPDF 3.0
		   if ($objattr['type'] == 'bookmark' || $objattr['type'] == 'indexentry' || $objattr['type'] == 'toc') {
			$x = $objattr['OUTER-X']; 
			$y = $objattr['OUTER-Y'];
			$this->y = $y - $this->FontSize/2;
			$this->x = $x;
		   }
		   else { 
			$y = $objattr['OUTER-Y'];
			$x = $objattr['OUTER-X'];
			$w = $objattr['OUTER-WIDTH'];
			$h = $objattr['OUTER-HEIGHT'];
			if (isset($objattr['text'])) { $texto = $objattr['text']; }
			$this->y = $y;
			$this->x = $x;
			if (isset($objattr['fontfamily'])) { $this->SetFont($objattr['fontfamily'],'',$objattr['fontsize'] ); }
		   }

		// HR
		   if ($objattr['type'] == 'hr') {
			$this->SetDrawColor($objattr['color']['R'],$objattr['color']['G'],$objattr['color']['B']);
			switch($objattr['align']) {
      		    case 'C':
      		        $empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
      		        $empty /= 2;
      		        $x += $empty;
     		        	  break;
      		    case 'R':
      		        $empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
      		        $x += $empty;
      		        break;
			}
      		$oldlinewidth = $this->LineWidth;
			$this->SetLineWidth($objattr['linewidth']/$k );
			$this->y += ($objattr['linewidth']/2) + $objattr['margin_top']/$k;
			$this->Line($x,$this->y,$x+$objattr['INNER-WIDTH'],$this->y);
			$this->SetLineWidth($oldlinewidth);
			$this->SetDrawColor(0);
		   }
		// IMAGE
		   if ($objattr['type'] == 'image') {
			if (isset($objattr['opacity'])) { $this->SetAlpha($objattr['opacity']); }
			if (isset($objattr['itype']) && $objattr['itype']=='wmf') { 
				$sx = $objattr['INNER-WIDTH']*$this->k / $objattr['orig_w'];
				$sy = abs($objattr['INNER-HEIGHT'])*$this->k / abs($objattr['orig_h']);
				$outstring = sprintf('q %f 0 0 %f %f %f cm /FO%d Do Q', $sx, $sy, $objattr['INNER-X']*$this->k-$sx*$objattr['wmf_x'], (($this->h-$objattr['INNER-Y'])*$this->k)-$sy*$objattr['wmf_y'], $objattr['ID']);
			}
			else { 
				$outstring = sprintf("q %.3f 0 0 %.3f %.3f %.3f cm /I%d Do Q",$objattr['INNER-WIDTH'] *$this->k,$objattr['INNER-HEIGHT'] *$this->k,$objattr['INNER-X'] *$this->k,($this->h-($objattr['INNER-Y'] +$objattr['INNER-HEIGHT'] ))*$this->k,$objattr['ID'] );
			}
			$this->_out($outstring);
			// LINK
			if (isset($objattr['link'])) $this->Link($objattr['INNER-X'],$objattr['INNER-Y'],$objattr['INNER-WIDTH'],$objattr['INNER-HEIGHT'],$objattr['link']);
			if (isset($objattr['BORDER-WIDTH'])) { $this->PaintImgBorder($objattr,$is_table); }
			if (isset($objattr['opacity'])) { $this->SetAlpha(1); }
		   }



		// mPDF 3.0 Normal spacing
		// mPDF 4.0
		   $this->ResetSpacing();


		}
		$this->SetFont($save_currentfontfamily,$save_currentfontstyle,$save_currentfontsize);
		$this->y = $save_y;
		$this->x = $save_x;
		unset($content);
}


function WriteFlowingBlock( $s)
{
    $currentx = $this->x; 
    $is_table = $this->flowingBlockAttr[ 'is_table' ];
    $is_list = $this->flowingBlockAttr[ 'is_list' ];
    // width of all the content so far in points
    $contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
    // cell width in points
    $maxWidth =& $this->flowingBlockAttr[ 'width' ];
    $lineCount =& $this->flowingBlockAttr[ 'lineCount' ];
    // line height in user units
    $lineHeight =& $this->flowingBlockAttr[ 'height' ];
    $align =& $this->flowingBlockAttr[ 'align' ];
    $content =& $this->flowingBlockAttr[ 'content' ];
    $font =& $this->flowingBlockAttr[ 'font' ];
    $valign =& $this->flowingBlockAttr[ 'valign' ];
    $blockstate = $this->flowingBlockAttr[ 'blockstate' ];

    $newblock = $this->flowingBlockAttr[ 'newblock' ];

	// *********** BLOCK BACKGROUND COLOR ***************** //
	if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
		// mPDF 3.0 - Tiling Patterns
		$fill = 0;
//		$fill = 1;
//		$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
//		$this->SetFillColor($bcor['R'],$bcor['G'],$bcor['B']);
	}
	else {
		$this->SetFillColor(255);
		$fill = 0;
	}

    $font[] = $this->saveFont();
    $content[] = '';

    $currContent =& $content[ count( $content ) - 1 ];

    // where the line should be cutoff if it is to be justified
    $cutoffWidth = $contentWidth;

	$curlyquote = mb_convert_encoding("\xe2\x80\x9e",$this->mb_enc,'UTF-8');
	$curlylowquote = mb_convert_encoding("\xe2\x80\x9d",$this->mb_enc,'UTF-8');

	// COLS
	$oldcolumn = $this->CurrCol;


   if ($is_table) { 
	$ipaddingL = 0; 
	$ipaddingR = 0; 
	$paddingL = 0; 
	$paddingR = 0; 
	$cpaddingadjustL = 0;
	$cpaddingadjustR = 0;
 	// Added mPDF 3.0
	$fpaddingR = 0;
	$fpaddingL = 0;
  } 
   else { 
		$ipaddingL = $this->blk[$this->blklvl]['padding_left']; 
		$ipaddingR = $this->blk[$this->blklvl]['padding_right']; 
		$paddingL = ($ipaddingL * $this->k); 
		$paddingR = ($ipaddingR * $this->k); 
		$this->cMarginL =  $this->blk[$this->blklvl]['border_left']['w'];
		$cpaddingadjustL = -$this->cMarginL;
		$this->cMarginR =  $this->blk[$this->blklvl]['border_right']['w'];
		$cpaddingadjustR = -$this->cMarginR;
		// Added mPDF 3.0 Float DIV
		$fpaddingR = 0;
		$fpaddingL = 0;
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if ($r_exists) { $fpaddingR = $r_width; }
			if ($l_exists) { $fpaddingL = $l_width; }
		}

		$usey = $this->y + 0.002;
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
			$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		}
		// If float exists at this level
		if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
		if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
   }	// *TABLES*

     //OBJECTS - IMAGES & FORM Elements (NB has already skipped line/page if required - in printbuffer)
      if (substr($s,0,3) == "\xbb\xa4\xac") { //identifier has been identified!
		// mPDF 4.0
		$objattr = $this->_getObjAttr($s);
		$h_corr = 0; 
		if ($is_table) {	// *TABLES*
			$maximumW = ($maxWidth/$this->k) - ($this->cellPaddingL + $this->cMarginL + $this->cellPaddingR + $this->cMarginR); 	// *TABLES*
		}	// *TABLES*
		else {	// *TABLES*
			if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) && (!$is_table)) { $h_corr = $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w']; }
			$maximumW = ($maxWidth/$this->k) - ($this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_right'] + $this->blk[$this->blklvl]['border_right']['w'] + $fpaddingL + $fpaddingR ); 
		}	// *TABLES*
		$objattr = $this->inlineObject($objattr['type'],$this->lMargin + $fpaddingL + ($contentWidth/$this->k),($this->y + $h_corr), $objattr, $this->lMargin,($contentWidth/$this->k),$maximumW,$lineHeight,true,$is_table);

		// SET LINEHEIGHT for this line ================ RESET AT END
		$lineHeight = MAX($lineHeight,$objattr['OUTER-HEIGHT']);
		$this->objectbuffer[count($content)-1] = $objattr;
		if (isset($objattr['vertical-align'])) { $valign = $objattr['vertical-align']; }
		else { $valign = ''; }
		$contentWidth += ($objattr['OUTER-WIDTH'] * $this->k);
		return;
	}


	$tmp = strlen( $s );

   $orphs = 0; 
   $check = 0;


   // for every character in the string
   for ( $i = 0; $i < $tmp; $i++ )  {

	// extract the current character
	// get the width of the character in points
       	$c = substr($s,$i,1);
		// mPDF 3.0 Soft Hyphens chr(173)
		if ($c == chr(173) && ($this->FontFamily!='symbol' && $this->FontFamily!='zapfdingbats')) { $cw = 0;  }
		else { $cw = $this->CurrentFont[ 'cw' ][ $c ] * ( $this->FontSizePt / 1000 ); }
	if ($c==' ') { $check = 1; }

	// CHECK for ORPHANS
	else if ($c=='.' || $c==',' || $c==')' || $c==';' || $c==':' || $c=='!' || $c=='?'|| $c=='"' || $c==$curlyquote || $c==$curlylowquote || $c=="\xef\xbc\x8c" || $c=="\xe3\x80\x82")  {$check++; }
	else { $check = 0; }

	// There's an orphan '. ' or ', ' or <sup>32</sup> about to be cut off at the end of line
	if($check==1) {
		$currContent .= $c;
		$cutoffWidth = $contentWidth;
		$contentWidth += $cw;
		continue;
	}
	if(($this->SUP || $this->SUB) && ($orphs < $this->orphansAllowed)) {	// ? disable orphans in table if  borders used
		$currContent .= $c;
		$cutoffWidth = $contentWidth;
		$contentWidth += $cw;
		$orphs++;
		continue;
	}
	else { $orphs = 0; }

	// ADDED for Paragraph_indent
	$WidthCorrection = 0; 
	if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align != 'C')) { 
		// mPDF 4.0
		$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		$WidthCorrection = ($ti*$this->k); 
	} 

	// Added mPDF 3.0 Float DIV
	$fpaddingR = 0;
	$fpaddingL = 0;
	if (count($this->floatDivs)) {
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
		if ($r_exists) { $fpaddingR = $r_width; }
		if ($l_exists) { $fpaddingL = $l_width; }
	}

	$usey = $this->y + 0.002;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
		$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
	}

	// If float exists at this level
	if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
	if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }



       // try adding another char
	if (( $contentWidth + $cw > $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*$this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) ) +  0.001))  {// 0.001 is to correct for deviations converting mm=>pts
		// it won't fit, output what we already have
		$lineCount++;
 
		// contains any content that didn't make it into this print
		$savedContent = '';
		$savedFont = array();

			$words = explode( ' ', $currContent ); 
			///////////////////
			// HYPHENATION
			$currWord = $words[count($words)-1] ;
			$success = false;


		// if it looks like we didn't finish any words for this chunk
		if ( count( $words ) == 1 ) {
		  // TO correct for error when word too wide for page - but only when one long word from left to right margin
		  if (count($content) == 1 && $currContent != ' ') {
			$lastContent = $words[0]; 
			$savedFont = $this->saveFont();
			// replace the current content with the cropped version
			$currContent = $this->mb_rtrim( $lastContent, $this->mb_enc );
		  }
		  else {
			// save and crop off the content currently on the stack
			$savedContent = array_pop( $content );
			$savedFont = array_pop( $font );
			// trim any trailing spaces off the last bit of content
			$currContent =& $content[ count( $content ) - 1 ];
			$currContent = $this->mb_rtrim( $currContent, $this->mb_enc );
		  }
		}
		else {	// otherwise, we need to find which bit to cut off
             $lastContent = '';
		  // mPDF 3.0
              for ( $w = 0; $w < count( $words ) - 1; $w++) { $lastContent .= $words[ $w ]." "; }
              $savedContent = $words[ count( $words ) - 1 ];
              $savedFont = $this->saveFont();
              // replace the current content with the cropped version
              $currContent = $this->mb_rtrim( $lastContent, $this->mb_enc );
		}

		// Set Current lineheight (correction factor)
		if ($is_list && is_array($this->bulletarray) && isset($this->bulletarray['occur']) && isset($this->list_lineheight[$this->listlvl][$this->bulletarray['occur']])) { 	// *LISTS*
			$this->lineheight_correction = $this->list_lineheight[$this->listlvl][$this->bulletarray['occur']]; 	// *LISTS*
		} 	// *LISTS*
		else	// *LISTS*
		if ($is_table) {	// *TABLES*
			$this->lineheight_correction = $this->table_lineheight; 	// *TABLES*
		}	// *TABLES*
		else	// *TABLES*
		if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
			$this->lineheight_correction = $this->blk[$this->blklvl]['line_height']; 
		} 
		else {
			$this->lineheight_correction = $this->default_lineheight_correction; 
		}

		// update $contentWidth and $cutoffWidth since they changed with cropping
		// Also correct lineheight to maximum fontsize (not for tables)
		$contentWidth = 0;
		$maxlineHeight = 0;
		$maxfontsize = 0;
		foreach ( $content as $k => $chunk )
		{
              $this->restoreFont( $font[ $k ]);
		  if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) { 
			$contentWidth += $this->objectbuffer[$k]['OUTER-WIDTH'] * $this->k; 
			$maxlineHeight = max($maxlineHeight,$this->objectbuffer[$k]['OUTER-HEIGHT']);
		  }
              else { 
			if ($this->FontFamily!='symbol' && $this->FontFamily!='zapfdingbats') {
			      $content[$k] = $chunk = str_replace($this->chrs[173],'',$chunk );
			}
			$contentWidth += $this->GetStringWidth( $chunk ) * $this->k; 
			$maxlineHeight = max($maxlineHeight,$this->FontSize * $this->lineheight_correction ); 
			$maxfontsize = max($maxfontsize,$this->FontSize); 
		  }
		}
		if ($is_list && is_array($this->bulletarray) && $this->bulletarray) {	// mPDF 3.0
	  		$actfs = $this->bulletarray['fontsize'];
			$maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction ); 
			$maxfontsize = max($maxfontsize,$actfs);
		}

		$lineHeight = $maxlineHeight; 
		$cutoffWidth = $contentWidth;
		$this->linemaxfontsize = $maxfontsize;


		// JUSTIFICATION J
		$nb_carac = 0;
		$nb_spaces = 0;
		// if it's justified, we need to find the char/word spacing (or if orphans have allowed length of line to go over the maxwidth)
		if(( $align == 'J' ) || ($cutoffWidth > $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*$this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) ) +  0.001)) {   // 0.001 is to correct for deviations converting mm=>pts
		  // JUSTIFY J (Use character spacing)
 		  // WORD SPACING
			foreach ( $content as $k => $chunk ) {
		  		if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
					      $chunk = str_replace($this->chrs[160],$this->chrs[32],$chunk );
					$nb_carac += mb_strlen( $chunk, $this->mb_enc ) ;  
					$nb_spaces += mb_substr_count( $chunk,' ', $this->mb_enc ) ;  
				}
			}
			list($charspacing,$ws) = $this->GetJspacing($nb_carac,$nb_spaces,($maxWidth-$cutoffWidth-$WidthCorrection-(($this->cMarginL+$this->cMarginR)*$this->k)-($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) )));
			// mPDF 4.0
			$this->SetSpacing($charspacing,$ws);
		}

		// otherwise, we want normal spacing
		else {
			// mPDF 4.0
			$this->ResetSpacing();
		}

		// WORD SPACING
		$empty = $maxWidth - $WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* $this->k) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * $this->k) ) - ( $this->charspacing * $nb_carac) - ( $this->ws * $nb_spaces);
		$empty /= $this->k;
		$b = ''; //do not use borders

		// Get PAGEBREAK TO TEST for height including the top border/padding
		$check_h = max($this->divheight,$lineHeight);
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($this->blklvl > 0) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
		}

		// PAGEBREAK
		// 'If' below used in order to fix "first-line of other page with justify on" bug 
		if(($this->y+$check_h) > $this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {

			$bak_x=$this->x;//Current X position

			// WORD SPACING
			$ws=$this->ws;//Word Spacing
			$charspacing=$this->charspacing;//Character Spacing
			// mPDF 4.0
			$this->ResetSpacing();

		      $this->AddPage($this->CurOrientation);

		      $this->x = $bak_x;
			// Added to correct for OddEven Margins
			$currentx += $this->MarginCorrection;
			$this->x += $this->MarginCorrection;

			// WORD SPACING
			// mPDF 4.0
			$this->SetSpacing($charspacing,$ws);
		}


		// TOP MARGIN
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($this->blk[$this->blklvl]['margin_top']) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$this->DivLn($this->blk[$this->blklvl]['margin_top'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
		}


		// Update y0 for top of block (used to paint border)
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$this->blk[$this->blklvl]['y0'] = $this->y;
			$this->blk[$this->blklvl]['startpage'] = $this->page;
		}

		// TOP PADDING and BORDER spacing/fill
		if (($newblock) && ($blockstate==1 || $blockstate==3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			// mPDF 3.0 Also does border when Columns active
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'],-3,true,false,1);
		}

		$arraysize = count($content);

		$margins = ($this->cMarginL+$this->cMarginR) + ($ipaddingL+$ipaddingR + $fpaddingR + $fpaddingR );
 
		// PAINT BACKGROUND FOR THIS LINE
		if (!$is_table) { $this->DivLn($lineHeight,$this->blklvl,false); }	// false -> don't advance y

	
		$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL ;
		if ($align == 'R') { $this->x += $empty; }
		else if ($align == 'C') { $this->x += ($empty / 2); }

		// Paragraph INDENT
		if (isset($this->blk[$this->blklvl]['text_indent']) && ($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 1) && (!$is_table) && ($this->directionality!='rtl') && ($align !='C')) { 
			// mPDF 4.0
			$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
			$this->x += $ti; 
		}


		// DIRECTIONALITY RTL
		$all_rtl = false;
		$contains_rtl = false;

		foreach ( $content as $k => $chunk ) {

			// FOR IMAGES - UPDATE POSITION
			if (($this->directionality=='rtl' && $contains_rtl) || $all_rtl) { $dirk = $arraysize-1 - $k ; } else { $dirk = $k; }

			if (isset($this->objectbuffer[$dirk]) && $this->objectbuffer[$dirk]) {
			  $xadj = $this->x - $this->objectbuffer[$dirk]['OUTER-X'] ; 

			  $this->objectbuffer[$dirk]['OUTER-X'] += $xadj;
			  $this->objectbuffer[$dirk]['BORDER-X'] += $xadj;
			  $this->objectbuffer[$dirk]['INNER-X'] += $xadj;
			  if ($valign == 'M' || $valign == '') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y'])+($lineHeight - $this->objectbuffer[$dirk]['OUTER-HEIGHT'])/2;
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			  }
			  else if ($valign == 'B') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y'])+($lineHeight - $this->objectbuffer[$dirk]['OUTER-HEIGHT']);
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			  }
			  else if ($valign == 'T') { 
				$yadj = ($this->y - $this->objectbuffer[$dirk]['OUTER-Y']);
				$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
				$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			  }
			}

			// DIRECTIONALITY RTL
			if ((($this->directionality == 'rtl') && ($contains_rtl )) || ($all_rtl )) { $this->restoreFont($font[$arraysize-1 - $k]); }
			else { $this->restoreFont( $font[ $k ] ); }

	 		// *********** SPAN BACKGROUND COLOR ***************** //
			if ($this->spanbgcolor) { 
				$cor = $this->spanbgcolorarray;
				$this->SetFillColor($cor['R'],$cor['G'],$cor['B']);
				$save_fill = $fill; $spanfill = 1; $fill = 1;
			}

			// WORD SPACING
		      $stringWidth = $this->GetStringWidth($chunk ) + ( $this->charspacing * mb_strlen($chunk,$this->mb_enc ) / $this->k )  
				+ ( $this->ws * mb_substr_count($chunk,' ',$this->mb_enc ) / $this->k );
			if (isset($this->objectbuffer[$dirk])) { $stringWidth = $this->objectbuffer[$dirk]['OUTER-WIDTH'];  }

			if ($stringWidth > 0) {
                     if ($k == $arraysize-1) { 
				$stringWidth -= ( $this->charspacing / $this->k ); 
				// mPDF 4.0 Last parameter of Cell indicates spanbgcolor
				$this->Cell( $stringWidth, $lineHeight, $chunk, '', 1, '', $fill, $this->HREF , $currentx,0,0,$valign, $fill ); //mono-style line or last part (skips line)
			   }
                     else $this->Cell( $stringWidth, $lineHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0,0,$valign, $fill );//first or middle part
			}
			else {	// If a space started a new chunk at the end of a line
				$this->x = $currentx; $this->y += $lineHeight; 
			}
	 		// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
			if (isset($spanfill) && $spanfill) { 
				$fill = $save_fill; $spanfill = 0; 
				if ($fill) { $this->SetFillColor($bcor['R'],$bcor['G'],$bcor['B']); }
			}
		}
		// mPDF 4.0
		if (!$is_table) { 
			$this->maxPosR = max($this->maxPosR , ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin'])); 
			$this->maxPosL = min($this->maxPosL , ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'])); 
		}

		// move on to the next line, reset variables, tack on saved content and current char

		$this->printobjectbuffer($is_table);
		$this->objectbuffer = array();

		// LIST BULLETS/NUMBERS
		if ($is_list && is_array($this->bulletarray) && ($lineCount == 1) ) {
		  // mPDF 4.0
		  $this->ResetSpacing();

		  $bull = $this->bulletarray;
	  	  if (isset($bull['level']) && isset($bull['occur']) && isset($this->InlineProperties['LIST'][$bull['level']][$bull['occur']])) { 
			$this->restoreInlineProperties($this->InlineProperties['LIST'][$bull['level']][$bull['occur']]); 
		  }
		  if (isset($bull['level']) && isset($bull['occur']) && isset($bull['num']) && isset($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) && $this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) { $this->restoreInlineProperties($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]); }
	  	  if (isset($bull['font']) && $bull['font'] == 'zapfdingbats') {
			$this->bullet = true;
			$this->SetFont('zapfdingbats','',$this->FontSizePt/2.5);
		  }
		  else { $this->SetFont($this->FontFamily,$this->FontStyle,$this->FontSizePt,true,true); }	// force output
	        //Output bullet
	  	  $this->x = $currentx;
	 	  if (isset($bull['x'])) { $this->x += $bull['x']; }
		  $this->y -= $lineHeight;
		  // mPDF 4.0
		  if (isset($bull['txt'])) { $this->Cell($bull['w'], $lineHeight,$bull['txt'],'','',$bull['align'],0,'',0,-$this->cMarginL, -$this->cMarginR ); }
	  	  if (isset($bull['font']) && $bull['font'] == 'zapfdingbats') {
			$this->bullet = false;
		  }
		  $this->x = $currentx;	// Reset
		  $this->y += $lineHeight;


		  $this->bulletarray = array();	// prevents repeat of bullet/number if <li>....<br />.....</li>
		}


		// Update values if set to skipline
		if ($this->floatmargins) { $this->_advanceFloatMargins(); }

		// Reset lineheight
		$lineHeight = $this->divheight;
		$valign = 'M';


		$this->restoreFont( $savedFont );
		$font = array( $savedFont );
		$content = array( $savedContent . $c );

		$currContent =& $content[ 0 ];
		$contentWidth = $this->GetStringWidth( $currContent ) * $this->k;
		$cutoffWidth = $contentWidth;
      }
      // another character will fit, so add it on
	else {
		$contentWidth += $cw;
		$currContent .= $c;
	}
    }
    unset($content);
}
//----------------------END OF FLOWING BLOCK------------------------------------//


// Update values if set to skipline
function _advanceFloatMargins() {
	// Update floatmargins - L
	if (isset($this->floatmargins['L']) && $this->floatmargins['L']['skipline'] && $this->floatmargins['L']['y0'] != $this->y) {
		$yadj = $this->y - $this->floatmargins['L']['y0'];
		$this->floatmargins['L']['y0'] = $this->y;
		$this->floatmargins['L']['y1'] += $yadj;

		// Update objattr in floatbuffer
		if ($this->floatbuffer[$this->floatmargins['L']['id']]['border_left']['w']) {
			$this->floatbuffer[$this->floatmargins['L']['id']]['BORDER-Y'] += $yadj;
		}
		$this->floatbuffer[$this->floatmargins['L']['id']]['INNER-Y'] += $yadj;
		$this->floatbuffer[$this->floatmargins['L']['id']]['OUTER-Y'] += $yadj;

		// Unset values
		$this->floatbuffer[$this->floatmargins['L']['id']]['skipline'] = false;
		$this->floatmargins['L']['skipline'] = false;
		$this->floatmargins['L']['id'] = '';
	}
	// Update floatmargins - R
	if (isset($this->floatmargins['R']) && $this->floatmargins['R']['skipline'] && $this->floatmargins['R']['y0'] != $this->y) {
		$yadj = $this->y - $this->floatmargins['R']['y0'];
		$this->floatmargins['R']['y0'] = $this->y;
		$this->floatmargins['R']['y1'] += $yadj;

		// Update objattr in floatbuffer
		if ($this->floatbuffer[$this->floatmargins['R']['id']]['border_left']['w']) {
			$this->floatbuffer[$this->floatmargins['R']['id']]['BORDER-Y'] += $yadj;
		}
		$this->floatbuffer[$this->floatmargins['R']['id']]['INNER-Y'] += $yadj;
		$this->floatbuffer[$this->floatmargins['R']['id']]['OUTER-Y'] += $yadj;

		// Unset values
		$this->floatbuffer[$this->floatmargins['R']['id']]['skipline'] = false;
		$this->floatmargins['R']['skipline'] = false;
		$this->floatmargins['R']['id'] = '';
	}
}



////////////////////////////////////////////////////////////////////////////////
// ADDED forcewrap - to call from inline OBJECT functions to breakwords if necessary in cell
////////////////////////////////////////////////////////////////////////////////
function WordWrap(&$text, $maxwidth, $forcewrap = 0)
{
    $biggestword=0;//EDITEI
    $toonarrow=false;//EDITEI

    $text = ltrim($text);
    $text = $this->mb_rtrim($text, $this->mb_enc);

    if ($text==='') return 0;
    $space = $this->GetStringWidth(' ');
    $lines = explode("\n", $text);
    $text = '';
    $count = 0;
    foreach ($lines as $line) {
		$words = explode(' ', $line);	// mPDF 4.0
	$width = 0;
	foreach ($words as $word) {
		$word = $this->mb_rtrim($word, $this->mb_enc);
		$word = ltrim($word);
		$wordwidth = $this->GetStringWidth($word);

		//EDITEI
		//Warn user that maxwidth is insufficient
		if ($wordwidth > $maxwidth + 0.0001) {
			if ($wordwidth > $biggestword) { $biggestword = $wordwidth; }
			$toonarrow=true;//EDITEI
			// ADDED
			if ($forcewrap) {
			  while($wordwidth > $maxwidth) {
				$chw = 0;	// check width
				for ( $i = 0; $i < mb_strlen($word, $this->mb_enc ); $i++ ) {
					$chw = $this->GetStringWidth(mb_substr($word,0,$i+1,$this->mb_enc ));
					if ($chw > $maxwidth ) {
						if ($text) {
							$text = $this->mb_rtrim($text, $this->mb_enc)."\n".mb_substr($word,0,$i,$this->mb_enc );
							$count++;
						}
						else {
							$text = mb_substr($word,0,$i,$this->mb_enc );
						}
						$word = mb_substr($word,$i,mb_strlen($word, $this->mb_enc )-$i,$this->mb_enc );
						$wordwidth = $this->GetStringWidth($word);
						$width = $maxwidth; 
						break;
					}
				}
			  }
			}
		}

		if ($width + $wordwidth  < $maxwidth - 0.0001) {
			$width += $wordwidth + $space;
			$text .= $word.' ';
		}
		else {
			$width = $wordwidth + $space;
			$text = $this->mb_rtrim($text, $this->mb_enc)."\n".$word.' ';
			$count++;
            }
	}

	$text = $this->mb_rtrim($text, $this->mb_enc)."\n";
	$count++;
    }
    $text = $this->mb_rtrim($text, $this->mb_enc);

    //Return -(wordsize) if word is bigger than maxwidth 

	// ADDED
	if ($forcewrap) { return $count; }
      if (($toonarrow) && ($this->table_error_report)) {
		die("Word is too long to fit in table - ".$this->table_error_report_param); 
	}
    if ($toonarrow) return -$biggestword;
    else return $count;
}

function _SetTextRendering($mode) { 
	if (!(($mode == 0) || ($mode == 1) || ($mode == 2))) 
	$this->Error("Text rendering mode should be 0, 1 or 2 (value : $mode)"); 
	$tr = ($mode.' Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;

} 

function SetTextOutline($width, $r=0, $g=-1, $b=-1) //EDITEI
{ 
  if ($width == false) //Now resets all values
  { 
    $this->outline_on = false;
    $this->SetLineWidth(0.2); 
    $this->SetDrawColor(0); 
    $this->_SetTextRendering(0); 
    $tr = ('0 Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;
  }
  else
  { 
    $this->SetLineWidth($width); 
    $this->SetDrawColor($r, $g , $b); 
    $tr = ('2 Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;
  } 
}

function Image($file,$x,$y,$w=0,$h=0,$type='',$link='',$paint=true, $constrain=true, $watermark=false)
{

	//First use of image, get info
	if($type=='') {
		$pos=strrpos($file,'.');
		if(!$pos)	$this->Error('Image file has no extension and no type was specified: '.$file);
		$qpos=strrpos($file,'?');
		if ($qpos) {  $type=substr($file,$pos+1,$qpos-$pos-1); }
		else { $type=substr($file,$pos+1); }
	}
	$type=strtolower($type);

	$info = -1;
	if(isset($this->images[$file])) { $info=$this->images[$file]; }
	else if (isset($this->formobjects[$file])) { $info=$this->formobjects[$file]; }	// *IMAGES-WMF*
	else {
		// mPDF 4.1
		$mqr=ini_get("magic_quotes_runtime");
		if ($mqr) { set_magic_quotes_runtime(0); }
		if($type=='jpg' or $type=='jpeg')	$info=$this->_parsejpg($file);
		elseif($type=='png') 			$info=$this->_parsepng($file);
		elseif($type=='gif') 			$info=$this->_parsegif($file);
		elseif($type=='wmf') 			$info=$this->_parsewmf($file); 	// *IMAGES-WMF*
		else
		{
			//Allow for additional formats
			$mtd='_parse'.$type;
			if(method_exists($this,$mtd)) { $info=$this->$mtd($file); }
			else { 
				$info=$this->_parsephp($file); 
				if (!is_array($info) && $info < 0) { 
					if ($this->showImageErrors) $this->Error('Unsupported image type: '.$type);
					else $info = -1; 
				}
			}
		}
		// mPDF 3.0 - Try again PNG
		if ($type=='png' && $info < -1) { 
			$info=$this->_parsepng2($file, $info); 
			if ($info['UID']) { $file = $info['UID']; }	// A unique alpha blended PNG created
		}
		// mPDF 4.1
		if ($mqr) { set_magic_quotes_runtime($mqr); }
		// mPDF 3.0
		if (!is_array($info) && $info < 0) { 
			if(!$this->shownoimg || !$paint) return false;
			$file = str_replace("\\","/",dirname(__FILE__)) . "/";
			$file .= 'includes/no_image.jpg';
			$type = 'jpg';
			if(isset($this->images[$file])) { $info=$this->images[$file]; }
			else {
				// mPDF 4.1
				if ($mqr) { set_magic_quotes_runtime(0); }
				$info=$this->_parsejpg($file); 
				// mPDF 4.1
				if ($mqr) { set_magic_quotes_runtime($mqr); }
				$info['i']=count($this->images)+1;
				$this->images[$file]=$info;
			}
			$w = (14 * 0.2645); 	// 14 x 16px
			$h = (16 * 0.2645); 	// 14 x 16px
		}
            else if ($type=='wmf') { 
	            $info['i']=count($this->formobjects)+1;
	            $this->formobjects[$file]=$info;
		}
		else {
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
	}
	//Automatic width and height calculation if needed
	if($w==0 and $h==0) {
           if ($type=='wmf') { 
			// WMF units are twips (1/20pt)
			// divide by 20 to get points
			// divide by k to get user units
			$w = abs($info['w'])/(20*$this->k);
			$h = abs($info['h']) / (20*$this->k);
		}
		else {
			//Put image at default dpi
			$w=($info['w']/$this->k) * (72/$this->img_dpi);
			$h=($info['h']/$this->k) * (72/$this->img_dpi);
		}
	}
	if($w==0)	$w=abs($h*$info['w']/$info['h']); 
	if($h==0)	$h=abs($w*$info['h']/$info['w']); 


	if ($constrain) {
	  // Automatically resize to maximum dimensions of page if too large
	  if (isset($this->blk[$this->blklvl]['inner_width']) && $this->blk[$this->blklvl]['inner_width']) { $maxw = $this->blk[$this->blklvl]['inner_width']; }
	  else { $maxw = $this->pgwidth; }
	  if ($w > $maxw) {
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
	  }
	  if ($h > $this->h - ($this->tMargin + $this->bMargin + 10))  {  // see below - +10 to avoid drawing too close to border of page
   		$h = $this->h - ($this->tMargin + $this->bMargin + 10) ;	// mPDF 4.1
		if ($this->fullImageHeight) { $h = $this->fullImageHeight; }	// mPDF 4.1
		$w=abs($h*$info['w']/$info['h']);
	  }


	  //Avoid drawing out of the paper(exceeding width limits). //EDITEI
	  //if ( ($x + $w) > $this->fw ) {
	  if ( ($x + $w) > $this->w ) {
		$x = $this->lMargin;
		$y += 5;
	  }

	  $changedpage = false; //EDITEI
	  $oldcolumn = $this->CurrCol;
	  //Avoid drawing out of the page. //EDITEI
	  if($y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
		$this->AddPage($this->CurOrientation);
		// Added to correct for OddEven Margins
		$x=$x +$this->MarginCorrection;
		$y = $tMargin + $this->margin_header;
		$changedpage = true;
	  }
	}	// end of IF constrain

	if ($type=='wmf') { 
		$sx = $w*$this->k / $info['w'];
		$sy = -$h*$this->k / $info['h'];
		$outstring = sprintf('q %f 0 0 %f %f %f cm /FO%d Do Q', $sx, $sy, $x*$this->k-$sx*$info['x'], (($this->h-$y)*$this->k)-$sy*$info['y'], $info['i']);
	}
	else { 
		$outstring = sprintf("q %.3f 0 0 %.3f %.3f %.3f cm /I%d Do Q",$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']);
	}

	if($paint) { //EDITEI
		$this->_out($outstring);
		if($link) $this->Link($x,$y,$w,$h,$link);

		//Avoid writing text on top of the image. // THIS WAS OUTSIDE THE if ($paint) bit!!!!!!!!!!!!!!!!
		$this->y = $y + $h;
	}


	//Return width-height array //EDITEI
	$sizesarray['WIDTH'] = $w;
	$sizesarray['HEIGHT'] = $h;
	$sizesarray['X'] = $x; //Position before painting image
	$sizesarray['Y'] = $y; //Position before painting image
	$sizesarray['OUTPUT'] = $outstring;
	// mPDF 3.0 Tiling patterns
	$sizesarray['IMAGE_ID'] = $info['i'];
	return $sizesarray;
}



//=============================================================
//=============================================================
//=============================================================
//=============================================================
//=============================================================
// mPDF 4.0
function _getObjAttr($t) {
	$c = explode("\xbb\xa4\xac",$t,2);
	$c = explode(",",$c[1],2);
	foreach($c as $v) {
		$v = explode("=",$v,2);
		$sp[$v[0]] = $v[1];
	}
	return (unserialize($sp['objattr']));
}


function inlineObject($type,$x,$y,$objattr,$Lmargin,$widthUsed,$maxWidth,$lineHeight,$paint=false,$is_table=false)
{
   if ($is_table) { $k = $this->shrin_k; } else { $k = 1; }

   // NB $x is only used when paint=true
	// Lmargin not used
   $w = 0; 
   if (isset($objattr['width'])) { $w = $objattr['width']/$k; }
   $h = 0;
   if (isset($objattr['height'])) { $h = abs($objattr['height']/$k); }	

   $widthLeft = $maxWidth - $widthUsed;
   $maxHeight = $this->h - ($this->tMargin + $this->bMargin + 10) ;	// mPDF 4.1
   if ($this->fullImageHeight) { $maxHeight = $this->fullImageHeight; }	// mPDF 4.1
	// For Images
   if (isset($objattr['border_left'])) {
	$extraWidth = ($objattr['border_left']['w'] + $objattr['border_right']['w'] + $objattr['margin_left']+ $objattr['margin_right'])/$k;
	$extraHeight = ($objattr['border_top']['w'] + $objattr['border_bottom']['w'] + $objattr['margin_top']+ $objattr['margin_bottom'])/$k;
	// mPDF 4.0
	if ($type == 'image' || $type == 'barcode') {
		$extraWidth += ($objattr['padding_left'] + $objattr['padding_right'])/$k;
		$extraHeight += ($objattr['padding_top'] + $objattr['padding_bottom'])/$k;
	}
   }


   if ($type == 'image' || (isset($objattr['subtype']) && $objattr['subtype'] == 'IMAGE')) {
    if (isset($objattr['itype']) && $objattr['itype'] == 'wmf') {
	$file = $objattr['file'];
 	$info=$this->formobjects[$file];
    }
    else if (isset($objattr['file'])) {
	$file = $objattr['file'];
	$info=$this->images[$file];
    }
   }
    // mPDF 3.0
    if ($type == 'annot' || $type == 'bookmark' || $type == 'indexentry' || $type == 'toc') {
	$w = 0.00001;
	$h = 0.00001;
   }

   // TEST whether need to skipline
   if (!$paint) {
	if ($type == 'hr') {	// always force new line
		if (($y + $h + $lineHeight > $this->PageBreakTrigger) && !$this->InFooter && !$is_table) { return array(-2, $w ,$h ); } // New page + new line
		else { return array(1, $w ,$h ); } // new line
	}
	else {
		if ($widthUsed > 0 && $w > $widthLeft && (!$is_table || $type != 'image')) { 	// New line needed
			if (($y + $h + $lineHeight > $this->PageBreakTrigger) && !$this->InFooter) { return array(-2,$w ,$h ); } // New page + new line
			return array(1,$w ,$h ); // new line
		}
		// Will fit on line but NEW PAGE REQUIRED
		else if (($y + $h > $this->PageBreakTrigger) && !$this->InFooter && !$is_table) { return array(-1,$w ,$h ); }
		else { return array(0,$w ,$h ); }
	}
   }

    // mPDF 3.0
   if ($type == 'annot' || $type == 'bookmark' || $type == 'indexentry' || $type == 'toc') {
	$w = 0.00001;
	$h = 0.00001;
	$objattr['BORDER-WIDTH'] = 0;
	$objattr['BORDER-HEIGHT'] = 0;
	$objattr['BORDER-X'] = $x;
	$objattr['BORDER-Y'] = $y;
	$objattr['INNER-WIDTH'] = 0;
	$objattr['INNER-HEIGHT'] = 0;
	$objattr['INNER-X'] = $x;
	$objattr['INNER-Y'] = $y;
  }

  if ($type == 'image') {
	// Automatically resize to width remaining
	if ($w > $widthLeft  && !$is_table) {
		$w = $widthLeft ;
		$h=abs($w*$info['h']/$info['w']);
	}
	$img_w = $w - $extraWidth ;
	$img_h = $h - $extraHeight ;
	// mPDF 4.0
	$objattr['BORDER-WIDTH'] = $img_w + $objattr['padding_left']/$k + $objattr['padding_right']/$k + (($objattr['border_left']['w']/$k + $objattr['border_right']['w']/$k)/2) ;
	$objattr['BORDER-HEIGHT'] = $img_h + $objattr['padding_top']/$k + $objattr['padding_bottom']/$k + (($objattr['border_top']['w']/$k + $objattr['border_bottom']['w']/$k)/2) ;
	$objattr['BORDER-X'] = $x + $objattr['margin_left']/$k + (($objattr['border_left']['w']/$k)/2) ;
	$objattr['BORDER-Y'] = $y + $objattr['margin_top']/$k + (($objattr['border_top']['w']/$k)/2) ;
	$objattr['INNER-WIDTH'] = $img_w;
	$objattr['INNER-HEIGHT'] = $img_h;
	$objattr['INNER-X'] = $x + $objattr['padding_left']/$k + $objattr['margin_left']/$k + ($objattr['border_left']['w']/$k);
	$objattr['INNER-Y'] = $y + $objattr['padding_top']/$k + $objattr['margin_top']/$k + ($objattr['border_top']['w']/$k) ;
	$objattr['ID'] = $info['i'];
   }

   if ($type == 'input' && $objattr['subtype'] == 'IMAGE') { 
	$img_w = $w - $extraWidth ;
	$img_h = $h - $extraHeight ;
	$objattr['BORDER-WIDTH'] = $img_w + (($objattr['border_left']['w']/$k + $objattr['border_right']['w']/$k)/2) ;
	$objattr['BORDER-HEIGHT'] = $img_h + (($objattr['border_top']['w']/$k + $objattr['border_bottom']['w']/$k)/2) ;
	$objattr['BORDER-X'] = $x + $objattr['margin_left']/$k + (($objattr['border_left']['w']/$k)/2) ;
	$objattr['BORDER-Y'] = $y + $objattr['margin_top']/$k + (($objattr['border_top']['w']/$k)/2) ;
	$objattr['INNER-WIDTH'] = $img_w;
	$objattr['INNER-HEIGHT'] = $img_h;
	$objattr['INNER-X'] = $x + $objattr['margin_left']/$k + ($objattr['border_left']['w']/$k);
	$objattr['INNER-Y'] = $y + $objattr['margin_top']/$k + ($objattr['border_top']['w']/$k) ;
	$objattr['ID'] = $info['i'];
   }



   if ($type == 'textarea') {
	// Automatically resize to width remaining
	if ($w > $widthLeft && !$is_table) {
		$w = $widthLeft ;
	}
	if (($y + $h > $this->PageBreakTrigger) && !$this->InFooter) {
		$h=$this->h - $y - $this->bMargin;
	}
   }

   if ($type == 'hr') {
	if ($is_table) { 
		$objattr['INNER-WIDTH'] = $maxWidth * $objattr['W-PERCENT']/100; 
		$objattr['width'] = $objattr['INNER-WIDTH']; 
		// mPDF 3.0
		$w = $maxWidth;
	}
	else { 
		if ($w>$maxWidth) { $w = $maxWidth; }
		$objattr['INNER-WIDTH'] = $w; 
		// mPDF 3.0
		$w = $maxWidth;
	}
  }



   if (($type == 'select') || ($type == 'input' && ($objattr['subtype'] == 'TEXT' || $objattr['subtype'] == 'PASSWORD'))) {
	// Automatically resize to width remaining
	if ($w > $widthLeft && !$is_table) {
		$w = $widthLeft;
	}
   }

   if ($type == 'textarea' || $type == 'select' || $type == 'input') {
	if (isset($objattr['fontsize'])) $objattr['fontsize'] /= $k;
	if (isset($objattr['linewidth'])) $objattr['linewidth'] /= $k;
   }

  // mPDF 3.0
   if (!isset($objattr['BORDER-Y'])) { $objattr['BORDER-Y'] = 0; }
   if (!isset($objattr['BORDER-X'])) { $objattr['BORDER-X'] = 0; }
   if (!isset($objattr['INNER-Y'])) { $objattr['INNER-Y'] = 0; }
   if (!isset($objattr['INNER-X'])) { $objattr['INNER-X'] = 0; }

   //Return width-height array
   $objattr['OUTER-WIDTH'] = $w;
   $objattr['OUTER-HEIGHT'] = $h;
   $objattr['OUTER-X'] = $x;
   $objattr['OUTER-Y'] = $y;
   return $objattr;
}


//=============================================================
//=============================================================
//=============================================================
//=============================================================
//=============================================================




function SetDash($black=false,$white=false)
{
        if($black and $white) $s=sprintf('[%.3f %.3f] 0 d',$black*$this->k,$white*$this->k);
        else $s='[] 0 d';
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['Dash']) && $this->pageoutput[$this->page]['Dash'] != $s) || !isset($this->pageoutput[$this->page]['Dash']) || $this->keep_block_together)) { $this->_out($s); }
	$this->pageoutput[$this->page]['Dash'] = $s;

}

function SetDisplayPreferences($preferences) {
	// String containing any or none of /HideMenubar/HideToolbar/HideWindowUI/DisplayDocTitle/CenterWindow/FitWindow
    $this->DisplayPreferences .= $preferences;
}


function Ln($h='',$collapsible=0)
{
// Added collapsible to allow collapsible top-margin on new page
	//Line feed; default value is last cell height
	$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];
	if ($collapsible && ($this->y==$this->tMargin) && (!$this->ColActive)) { $h = 0; }
	if(is_string($h)) $this->y+=$this->lasth;
	else $this->y+=$h;
}


// mPDF 3.0 Also does border when Columns active
// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
function DivLn($h,$level=-3,$move_y=true,$collapsible=false,$state=0) {
  // this->x is returned as it was
  // adds lines (y) where DIV bgcolors are filled in
  if ($collapsible && ($this->y==$this->tMargin) && (!$this->ColActive)) { return; }

  // mPDF 3.0
	// Still use this method if columns or page-break-inside: avoid as it allows repositioning later
	// otherwise, now uses PaintDivBB()
  if (!$this->ColActive && !$this->keep_block_together && !$this->kwt) { 	// mPDF 4.0 added kwt
	if ($move_y && !$this->ColActive) { $this->y += $h; }
	return; 
  }

  if ($level == -3) { $level = $this->blklvl; }
  $firstblockfill = $this->GetFirstBlockFill();
  if ($firstblockfill && $this->blklvl > 0 && $this->blklvl >= $firstblockfill) {
	$last_x = 0;
	$last_w = 0;
	$last_fc = $this->FillColor;
	$bak_x = $this->x;
	$bak_h = $this->divheight;
	$this->divheight = 0;	// Temporarily turn off divheight - as Cell() uses it to check for PageBreak
	for ($blvl=$firstblockfill;$blvl<=$level;$blvl++) {
		$this->SetBlockFill($blvl);
		$this->x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
		if ($last_x != $this->lMargin + $this->blk[$blvl]['outer_left_margin'] || $last_w != $this->blk[$blvl]['width'] || $last_fc != $this->FillColor) {
			$x = $this->x;	// mPDF 3.0
			$this->Cell( ($this->blk[$blvl]['width']), $h, '', '', 0, '', 1);
			// mPDF 3.0 Also does border when Columns active
			if (!$this->keep_block_together && !$this->writingHTMLheader && !$this->writingHTMLfooter) {
				$this->x = $x;
				// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
				if ($blvl == $this->blklvl) { $this->PaintDivLnBorder($state,$blvl,$h); }
				else { $this->PaintDivLnBorder(0,$blvl,$h); }
			}
		}
		$last_x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
		$last_w = $this->blk[$blvl]['width'];
		$last_fc = $this->FillColor;
	}
	// Reset current block fill
	if (isset($this->blk[$this->blklvl]['bgcolorarray'])) { 
		$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
		$this->SetFillColor($bcor['R'],$bcor['G'],$bcor['B']);
	}
	$this->x = $bak_x;
	$this->divheight = $bak_h;
  }
  if ($move_y) { $this->y += $h; }
}

function GetX()
{
	//Get x position
	return $this->x;
}

function SetX($x)
{
	//Set x position
	if($x >= 0)	$this->x=$x;
	else $this->x = $this->w + $x;
}

function GetY()
{
	//Get y position
	return $this->y;
}

function SetY($y)
{
	//Set y position and reset x
	$this->x=$this->lMargin;
	if($y>=0)
		$this->y=$y;
	else
		$this->y=$this->h+$y;
}

function SetXY($x,$y)
{
	//Set x and y positions
	$this->SetY($y);
	$this->SetX($x);
}



function Output($name='',$dest='')
{
	//Output PDF to some destination
	global $_SERVER;
	// mPDF 4.0
	if ($this->showStats) {
		echo '<div>Generated in '.sprintf('%.2f',(microtime(true) - $this->time0)).' seconds</div>';
	}
	//Finish document if necessary
	if($this->state < 3) $this->Close();
	// fn. error_get_last is only in PHP>=5.2
	if ($this->debug && function_exists('error_get_last') && error_get_last()) {
	   $e = error_get_last(); 
	   if (($e['type'] < 2048 && $e['type'] != 8) || (intval($e['type']) & intval(ini_get("error_reporting")))) {
		echo "<p>Error message detected - PDF file generation aborted.</p>"; 
		echo $e['message'].'<br />';
		echo 'File: '.$e['file'].'<br />';
		echo 'Line: '.$e['line'].'<br />';
		exit; 
	   }
	}

	// mPDF 4.0
	if ($this->showStats) {
		echo '<div>Compiled in '.sprintf('%.2f',(microtime(true) - $this->time0)).' seconds (total)</div>';
		echo '<div>Peak Memory usage '.number_format((memory_get_peak_usage(true)/(1024*1024)),2).' MB</div>';
		echo '<div>PDF file size '.number_format((strlen($this->buffer)/1024)).' kB</div>';
		echo '<div>Number of fonts '.count($this->fonts).'</div>';
		exit;
	}

	//Normalize parameters
	if(is_bool($dest)) $dest=$dest ? 'D' : 'F';
	$dest=strtoupper($dest);
	if($dest=='')
	{
		if($name=='')
		{
			$name='mpdf.pdf';
			$dest='I';
		}
		else
			$dest='F';
	}
	switch($dest)
	{
		case 'I':
			// mPDF 3.0 // Edited mPDF 3.1
			if ($this->debug && !$this->allow_output_buffering && ob_get_contents()) { echo "<p>Output has already been sent from the script - PDF file generation aborted.</p>"; exit; }
			//Send to standard output
			if(isset($_SERVER['SERVER_NAME']))
			{
				//We send to a browser
				header('Content-Type: application/pdf');
				if(headers_sent())
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				header('Content-Length: '.strlen($this->buffer));
				header('Content-disposition: inline; filename='.$name);
			}
			echo $this->buffer;
			break;
		case 'D':
			//Download file
			// mPDF 4.0
			if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
					header('HTTP/1.1 200 OK');
					header('Status: 200 OK');
					header('Pragma: anytextexeptno-cache', true);
					header("Cache-Control: public, must-revalidate");
				} 
				else {
					header('Cache-Control: public, must-revalidate');
					header('Pragma: public');
				}
				header('Content-Type: application/force-download');
			} 
			else {
				header('Content-Type: application/octet-stream');
			}
			if(headers_sent())
				$this->Error('Some data has already been output to browser, can\'t send PDF file');
			header('Content-Length: '.strlen($this->buffer));
			header('Content-disposition: attachment; filename='.$name);
 			echo $this->buffer;
			break;
		case 'F':
			//Save to local file
			$f=fopen($name,'wb');
			if(!$f) $this->Error('Unable to create output file: '.$name);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
			break;
		case 'S':
			//Return as a string
			return $this->buffer;
		default:
			$this->Error('Incorrect output destination: '.$dest);
	}
	return '';
}


// *****************************************************************************
//                                                                             *
//                             Protected methods                               *
//                                                                             *
// *****************************************************************************
function _dochecks()
{
	//Check for locale-related bug
	if(1.1==1)
		$this->Error('Don\'t alter the locale before including class file');
	//Check for decimal separator
	if(sprintf('%.1f',1.0)!='1.0')
		setlocale(LC_NUMERIC,'C');
}

function _begindoc()
{
	//Start document
	$this->state=1;
	$this->_out('%PDF-'.$this->pdf_version);
}




function _putpages()
{
	$nb=$this->page;
	if($this->DefOrientation=='P') {
		$wPt=$this->fwPt;
		$hPt=$this->fhPt;
	}
	else {
		$wPt=$this->fhPt;
		$hPt=$this->fwPt;
	}

	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';

	$annotid=(3+2*$nb);
	for($n=1;$n<=$nb;$n++)
	{
		// mPDF 4.0 Moved
		//Replace number of pages
		if(!empty($this->aliasNbPg)) {
			// mPDF 4.0
			if ($this->is_MB && !$this->useSubsets) { $s1 = $this->UTF8ToUTF16BE($this->aliasNbPg, false); }
			else { $s1 = $this->aliasNbPg; }
			if ($this->useSubsets) { 
				$r = '';
				$nstr = "$nb";
				for($i=0;$i<strlen($nstr);$i++) {
					$r .= sprintf("%02s", strtoupper(dechex(intval($nstr{$i})+33))); 
				}
			}
			else { $r = $nb; }
			if (preg_match_all('/{mpdfheadernbpg (C|R) ff=(\S*) fs=(\S*) fz=(.*?)}/',$this->pages[$n],$m)) {
				for($hi=0;$hi<count($m[0]);$hi++) {
					$pos = $m[1][$hi];
					$hff = $m[2][$hi];
					$hfst = $m[3][$hi];
					$hfsz = $m[4][$hi];
					$this->SetFont($hff,$hfst,$hfsz, false);
					$x1 = $this->GetStringWidth($this->aliasNbPg);
					$x2 = $this->GetStringWidth($nb);
					$xadj = $x1 - $x2;
					if ($pos=='C') { $xadj /= 2; }
					$rep = sprintf(' q 1 0 0 1 %.3f 0 cm ', $xadj*$this->k); 
					$this->pages[$n] = str_replace($m[0][$hi], $rep, $this->pages[$n]);
				}
			}
			$this->pages[$n]=str_replace($s1,$r,$this->pages[$n]);
		}
		//Replace number of pages in group
		if(!empty($this->aliasNbPgGp)) {
			// mPDF 4.0
			if ($this->is_MB && !$this->useSubsets) { $s2 = $this->UTF8ToUTF16BE($this->aliasNbPgGp, false); }
			else { $s2 = $this->aliasNbPgGp; }
			$nbt = $this->docPageNumTotal($n);
			if ($this->useSubsets) { 
				$r = '';
				$nstr = "$nbt";
				for($i=0;$i<strlen($nstr);$i++) {
					$r .= sprintf("%02s", strtoupper(dechex(intval($nstr{$i})+33))); 
				}
			}
			else { $r = $nbt; }
			if (preg_match_all('/{mpdfheadernbpggp (C|R) ff=(\S*) fs=(\S*) fz=(.*?)}/',$this->pages[$n],$m)) {
				for($hi=0;$hi<count($m[0]);$hi++) {
					$pos = $m[1][$hi];
					$hff = $m[2][$hi];
					$hfst = $m[3][$hi];
					$hfsz = $m[4][$hi];
					$this->SetFont($hff,$hfst,$hfsz, false);
					$x1 = $this->GetStringWidth($this->aliasNbPgGp);
					// mPDF 3.0
					$x2 = $this->GetStringWidth($nbt);
					$xadj = $x1 - $x2;
					if ($pos=='C') { $xadj /= 2; }
					$rep = sprintf(' q 1 0 0 1 %.3f 0 cm ', $xadj*$this->k); 
					$this->pages[$n] = str_replace($m[0][$hi], $rep, $this->pages[$n]);
				}
			}
			$this->pages[$n]=str_replace($s2,$r,$this->pages[$n]);
		}

		// mPDF 3.0 Remove Pattern marker
		$this->pages[$n] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n", $this->pages[$n]);

		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		if(isset($this->OrientationChanges[$n])) {
			$this->_out(sprintf('/MediaBox [0 0 %.3f %.3f]',$hPt,$wPt));
			if ($this->displayDefaultOrientation) {
				if ($this->DefOrientation=='P') { $this->_out('/Rotate 270'); }
				else { $this->_out('/Rotate 90'); }
			}
		}
		$this->_out('/Resources 2 0 R');

		$annotsnum = 0;
		if (isset($this->PageLinks[$n])) { $annotsnum += count($this->PageLinks[$n]); }
		if ($annotsnum ) {
			$s = '/Annots [ ';
			for($i=0;$i<$annotsnum;$i++) { 
				$s .= ($annotid + $i) . ' 0 R ';
			} 
			$annotid += $annotsnum;
			$s .= '] ';
			$this->_out($s);
		}

		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');

		//Page content
		$this->_newobj();
		$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_putstream($p);
		$this->_out('endobj');
	}
	$this->_putannots($n);

	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.3f %.3f]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
}


function _putannots($n) {
	$nb=$this->page;
	for($n=1;$n<=$nb;$n++)
	{
		$annotobjs = array();
		if(isset($this->PageLinks[$n]) || isset($this->PageAnnots[$n])) {
			$wPt=$this->pageDim[$n]['w']*$this->k;
			$hPt=$this->pageDim[$n]['h']*$this->k;

			//Links
			if(isset($this->PageLinks[$n])) {
			   foreach($this->PageLinks[$n] as $key => $pl) {
				$this->_newobj();
				$annot='';
				$rect=sprintf('%.3f %.3f %.3f %.3f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annot.='<</Type /Annot /Subtype /Link /Rect ['.$rect.']';
				$annot .= ' /Contents '.$this->_UTF16BEtextstring($pl[4]);
				$annot .= ' /NM ('.sprintf('%04u-%04u', $n, $key).')';
				$annot .= ' /M '.$this->_textstring('D:'.date('YmdHis'));
				$annot .= ' /Border [0 0 0]';
				// mPDF 3.0
				if (strpos($pl[4],'@')===0) {
					$p=substr($pl[4],1);
					//	$h=isset($this->OrientationChanges[$p]) ? $wPt : $hPt;
					$htarg=$this->pageDim[$p]['h']*$this->k;
					$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3f null]>>',1+2*$p,$htarg);
				}
				else if(is_string($pl[4])) {
					$annot .= ' /A <</S /URI /URI '.$this->_textstring($pl[4]).'>> >>';
				}
				else {
					$l=$this->links[$pl[4]];
					// mPDF 3.0
					// may not be set if #link points to non-existent target
					if (isset($this->pageDim[$l[0]]['h'])) { $htarg=$this->pageDim[$l[0]]['h']*$this->k; }
					else { $htarg=$this->h*$this->k; } // doesn't really matter
					$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3f null]>>',1+2*$l[0],$htarg-$l[1]*$this->k);
				}
				$this->_out($annot);
				$this->_out('endobj');
			   }
			}


		}
	}
}






function _putfonts() {

		$nf=$this->n;
		foreach($this->diffs as $diff)
		{
			//Encodings
			$this->_newobj();
			$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
			$this->_out('endobj');
		}
		// mPDF 4.1
		$mqr=ini_get("magic_quotes_runtime");
		if ($mqr) { set_magic_quotes_runtime(0); }
		foreach($this->FontFiles as $file=>$info)
		{
			//Font file embedding
			$this->_newobj();
			$this->FontFiles[$file]['n']=$this->n;
			if(defined('MPDF_FONTPATH'))
				$file=MPDF_FONTPATH.$file;
			$size=filesize($file);
			if(!$size)
				$this->Error('Font file not found');
			$this->_out('<</Length '.$size);
			if(substr($file,-2)=='.z')
				$this->_out('/Filter /FlateDecode');
			$this->_out('/Length1 '.$info['length1']);
			if(isset($info['length2']))
				$this->_out('/Length2 '.$info['length2'].' /Length3 0');
			$this->_out('>>');
			$f=fopen($file,'rb');
			$s = '';
			while (!feof($f)) {
				$s .= fread($f, 2048);
			}

			$this->_putstream($s);
			fclose($f);
			$this->_out('endobj');
		}
		// mPDF 4.1
		if ($mqr) { set_magic_quotes_runtime($mqr); }
		foreach($this->fonts as $k=>$font)
		{
			//Font objects
			$this->fonts[$k]['n']=$this->n+1;
			$type=$font['type'];
			$name=$font['name'];
			if($type=='core')
			{
				//Standard font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /Type1');
				if($name!='Symbol' and $name!='ZapfDingbats')
					$this->_out('/Encoding /WinAnsiEncoding');
				$this->_out('>>');
				$this->_out('endobj');
			}
			elseif($type=='Type1' or $type=='TrueType')
			{
				//Additional Type1 or TrueType font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /'.$type);
				$this->_out('/FirstChar 32 /LastChar 255');
				$this->_out('/Widths '.($this->n+1).' 0 R');
				$this->_out('/FontDescriptor '.($this->n+2).' 0 R');

				// mPDF 4.0
				$mapdata = false;	
				if($font['enc']) 	{
					if(isset($font['diff'])) {
						$this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
						$mapdata = @file(_MPDF_PATH .'maps/'.$font['enc'].'.map');
						if ($mapdata) {
							$this->_out('/ToUnicode '.($this->n + 3).' 0 R');
						}
					}
					else
						$this->_out('/Encoding /WinAnsiEncoding');
				}
				$this->_out('>>');
				$this->_out('endobj');
				//Widths
				$this->_newobj();
				$cw=&$font['cw'];
				$s='[';
				for($i=32;$i<=255;$i++)
					$s.=$cw[$this->chrs[$i]].' ';
				$this->_out($s.']');
				$this->_out('endobj');
				//Descriptor
				$this->_newobj();
				$s='<</Type /FontDescriptor /FontName /'.$name;
				foreach($font['desc'] as $k=>$v)
					$s.=' /'.$k.' '.$v;
				$file=$font['file'];
				if($file)
					$s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
				$this->_out($s.'>>');
				$this->_out('endobj');


				// mPDF 4.0
				// ToUnicode
				// Uses the .map files to determine mapping of 7F-FF to Unicode characters
				// Allows copying and pasting of PDF text to another editor
				if($mapdata) {
					$toUni = "stream\n";
					$toUni .= "/CIDInit /ProcSet findresource begin\n";
					$toUni .= "12 dict begin\n";
					$toUni .= "begincmap\n";
					$toUni .= "/CIDSystemInfo\n";
					$toUni .= "<</Registry (Adobe)\n";
					$toUni .= "/Ordering (UCS)\n";
					$toUni .= "/Supplement 0\n";
					$toUni .= ">> def\n";
					$toUni .= "/CMapName /Adobe-Identity-UCS def\n";
					$toUni .= "/CMapType 2 def\n";
					$toUni .= "1 begincodespacerange\n";
					$toUni .= "<00> <FF>\n";
					$toUni .= "endcodespacerange\n";
					$toUni .= "1 beginbfrange\n";
					$toUni .= "<00> <7F> <0000>\n";
					$toUni .= "endbfrange\n";
					$unip = array();
					foreach($mapdata AS $ms) {
						$tp = hexdec(substr($ms, 1, 2));
						if ($tp > 127) {
							$unip[$tp] = substr($ms, 6, 4);
						}
					}
					$toUni .= count($unip)." beginbfchar\n";
					foreach($unip AS $cp=>$u) {
						$toUni .= sprintf("<%02s> <%04s>\n", strtoupper(dechex($cp)), $u);
					}
					$toUni .= "endbfchar\n";
					$toUni .= "endcmap\n";
					$toUni .= "CMapName currentdict /CMap defineresource pop\n";
					$toUni .= "end\n";
					$toUni .= "end\n";
					$toUni .= "endstream\n";
					$toUni .= "endobj";
					$this->_newobj();
					$this->_out('<</Length '.(strlen($toUni)-24).'>>');
					$this->_out($toUni);
				}
			}
			else
			{
				//Allow for additional types including TrueTypeUnicode
				$mtd='_put'.strtolower($type);
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported font type: '.$type.' ('.$name.')');
				$this->$mtd($font);
			}
		}


}








function _putimages()
{
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	reset($this->images);
	while(list($file,$info)=each($this->images))
	{
		$this->_newobj();
		$this->images[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);
		// mPDF 4.0
		if (isset($info['masked'])) {
			$this->_out('/SMask '.($this->n - 1).' 0 R');
		}

		if($info['cs']=='Indexed')
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		else
		{
			$this->_out('/ColorSpace /'.$info['cs']);
			if($info['cs']=='DeviceCMYK')
				$this->_out('/Decode [1 0 1 0 1 0 1 0]');
		}
		$this->_out('/BitsPerComponent '.$info['bpc']);
		// mPDF 4.0
		if (isset($info['f']) && $info['f']) { $this->_out('/Filter /'.$info['f']); }
		if(isset($info['parms']))
			$this->_out($info['parms']);
		if(isset($info['trns']) and is_array($info['trns']))
		{
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).'>>');
		$this->_putstream($info['data']);
		unset($this->images[$file]['data']);
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed')
		{
			$this->_newobj();
			$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
			$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
			$this->_putstream($pal);
			$this->_out('endobj');
		}
	}
}

function _putinfo()
{
	$this->_out('/Producer '.$this->_UTF16BEtextstring('mPDF '.mPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_UTF16BEtextstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_UTF16BEtextstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_UTF16BEtextstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_UTF16BEtextstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_UTF16BEtextstring($this->creator));
	$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
	$this->_out('/ModDate '.$this->_textstring('D:'.date('YmdHis')));
}


function _putcatalog() {
	$this->_out('/Type /Catalog');
	$this->_out('/Pages 1 0 R');
	if($this->ZoomMode=='fullpage')	$this->_out('/OpenAction [3 0 R /Fit]');
	elseif($this->ZoomMode=='fullwidth') $this->_out('/OpenAction [3 0 R /FitH null]');
	elseif($this->ZoomMode=='real')	$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
	elseif(!is_string($this->ZoomMode))	$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
	if($this->LayoutMode=='single')	$this->_out('/PageLayout /SinglePage');
	elseif($this->LayoutMode=='continuous')	$this->_out('/PageLayout /OneColumn');
	elseif($this->LayoutMode=='two') {
	  if ($this->mirrorMargins) { $this->_out('/PageLayout /TwoColumnRight'); }
	  else { $this->_out('/PageLayout /TwoColumnLeft'); }
	}
	if(is_int(strpos($this->DisplayPreferences,'FullScreen'))) $this->_out('/PageMode /FullScreen');


  if($this->DisplayPreferences || $this->directionality == 'rtl' || $this->mirrorMargins) {
	$this->_out('/ViewerPreferences<<');
	if(is_int(strpos($this->DisplayPreferences,'HideMenubar'))) $this->_out('/HideMenubar true');
	if(is_int(strpos($this->DisplayPreferences,'HideToolbar'))) $this->_out('/HideToolbar true');
	if(is_int(strpos($this->DisplayPreferences,'HideWindowUI'))) $this->_out('/HideWindowUI true');
	if(is_int(strpos($this->DisplayPreferences,'DisplayDocTitle'))) $this->_out('/DisplayDocTitle true');
	if(is_int(strpos($this->DisplayPreferences,'CenterWindow'))) $this->_out('/CenterWindow true');
	if(is_int(strpos($this->DisplayPreferences,'FitWindow'))) $this->_out('/FitWindow true');
	if($this->directionality == 'rtl') $this->_out('/Direction /R2L');
	// mPDF 4.0
	if($this->mirrorMargins) {
		// if ($this->DefOrientation=='P') $this->_out('/Duplex /DuplexFlipShortEdge');
		$this->_out('/Duplex /DuplexFlipLongEdge');	// PDF v1.7+
	}
	$this->_out('>>');
  }
}

// Inactive function left for backwards compatability
function SetUserRights($enable=true, $annots="", $form="", $signature="") {
	// Does nothing
}

function _enddoc() {
	$this->_putpages();
	$this->_putresources();
	//Info
	$this->_newobj();
	$this->_out('<<');
	$this->_putinfo();
	$this->_out('>>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<<');
	$this->_putcatalog();
	$this->_out('>>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1; $i <= $this->n ; $i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<<');
	$this->_puttrailer();
	$this->_out('>>');
	$this->_out('startxref');
	$this->_out($o);
	$this->_out('%%EOF');
	$this->state=3;
}

function _beginpage($orientation,$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0,$mgtfp=0,$mgbfp=0) {
	$this->page++;
	$this->pages[$this->page]='';
	$this->state=2;
	$resetHTMLHeadersrequired = false;

	//Page orientation
	if(!$orientation)
		$orientation=$this->DefOrientation;
	else
	{
		$orientation=strtoupper(substr($orientation,0,1));
		if($orientation!=$this->DefOrientation)
			$this->OrientationChanges[$this->page]=true;
	}
	if($orientation!=$this->CurOrientation)
	{
		//Change orientation
		if($orientation=='P')
		{
			$this->wPt=$this->fwPt;
			$this->hPt=$this->fhPt;
			$this->w=$this->fw;
			$this->h=$this->fh;
		   if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P') {
			$this->tMargin = $this->orig_tMargin;
			$this->bMargin = $this->orig_bMargin;
			$this->DeflMargin = $this->orig_lMargin;
			$this->DefrMargin = $this->orig_rMargin;
			$this->margin_header = $this->orig_hMargin;
			$this->margin_footer = $this->orig_fMargin;
		   }
		}
		else
		{
			$this->wPt=$this->fhPt;
			$this->hPt=$this->fwPt;
			$this->w=$this->fh;
			$this->h=$this->fw;
		   if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P') {
			$this->tMargin = $this->orig_lMargin;
			$this->bMargin = $this->orig_rMargin;
			$this->DeflMargin = $this->orig_bMargin;
			$this->DefrMargin = $this->orig_tMargin;
			$this->margin_header = $this->orig_hMargin;
			$this->margin_footer = $this->orig_fMargin;
		   }

		}
		$this->CurOrientation=$orientation;
		$this->ResetMargins();
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$this->PageBreakTrigger=$this->h-$this->bMargin;
	}

	$this->pageDim[$this->page]['w']=$this->w ;
	$this->pageDim[$this->page]['h']=$this->h ;

	// If Page Margins are re-defined
	// strlen()>0 is used to pick up (integer) 0, (string) '0', or set value
	if ((strlen($mgl)>0 && $this->DeflMargin != $mgl) || (strlen($mgr)>0 && $this->DefrMargin != $mgr) || (strlen($mgt)>0 && $this->tMargin != $mgt) || (strlen($mgb)>0 && $this->bMargin != $mgb) || (strlen($mgh)>0 && $this->margin_header!=$mgh) || (strlen($mgf)>0 && $this->margin_footer!=$mgf)) {
		if (strlen($mgl)>0)  $this->DeflMargin = $mgl;
		if (strlen($mgr)>0)  $this->DefrMargin = $mgr;
		if (strlen($mgt)>0)  $this->tMargin = $mgt;
		if (strlen($mgb)>0)  $this->bMargin = $mgb;
		if (strlen($mgh)>0)  $this->margin_header=$mgh;
		if (strlen($mgf)>0)  $this->margin_footer=$mgf;
		$this->ResetMargins();
		$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
	}

	// Change Top/Bottom margins for "first page"
	if (isset($this->page_box['first_page_top_margin']) && $this->page_box['first_page_top_margin']) {	// next page of new @page definition
		$this->tMargin -= $this->page_box['first_page_top_margin'];
	}
	if (isset($this->page_box['first_page_bottom_margin']) && $this->page_box['first_page_bottom_margin']) {	// next page of new @page definition
		$this->bMargin -= $this->page_box['first_page_bottom_margin'];
	}
	$this->page_box['first_page_top_margin']=0;
	$this->page_box['first_page_bottom_margin']=0;
	if (isset($this->page_box['start_page']) && $this->page_box['start_page']==$this->page) {	// first page of new @page definition
		$this->tMargin += $mgtfp;
		$this->bMargin += $mgbfp;
		$this->page_box['first_page_top_margin']=$mgtfp;
		$this->page_box['first_page_bottom_margin']=$mgbfp;
	}

	// Moved in v1.4 to allow for changes in page orientation (Sets lMargin, rMargin, MarginCorrection)
	$this->ResetMargins();
	$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);

	// Reset column top margin
	$this->y0 = $this->tMargin;

	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
	$this->FontFamily='';

	// HEADERS AND FOOTERS
	if ($ohvalue<0 || strtoupper($ohvalue)=='OFF') { 
		$this->HTMLHeader = ''; 
		$this->headerDetails['odd'] = array(); 
	}
	else if ($ohname && $ohvalue>0) {
		if (isset($this->pageheaders[$ohname])) { $this->headerDetails['odd'] = $this->pageheaders[$ohname]; } 
		else { $this->headerDetails['odd'] = array(); }
		$this->HTMLHeader = ''; 
	}

	if ($ehvalue<0 || strtoupper($ehvalue)=='OFF') { 
		$this->HTMLHeaderE = ''; 
		$this->headerDetails['even'] = array(); 
	}
	else if ($ehname && $ehvalue>0) {
		if (isset($this->pageheaders[$ehname])) { $this->headerDetails['even'] = $this->pageheaders[$ehname]; }
		else { $this->headerDetails['even'] = array(); }
		$this->HTMLHeaderE = ''; 
	}

	if ($ofvalue<0 || strtoupper($ofvalue)=='OFF') { 
		$this->HTMLFooter = ''; 
		$this->footerDetails['odd'] = array(); 
	}
	else if ($ofname && $ofvalue>0) {
		if (isset($this->pagefooters[$ofname])) { $this->footerDetails['odd'] = $this->pagefooters[$ofname]; }
		else { $this->footerDetails['odd'] = array(); }
		$this->HTMLFooter = ''; 
	}

	if ($efvalue<0 || strtoupper($efvalue)=='OFF') { 
		$this->HTMLFooterE = ''; 
		$this->footerDetails['even'] = array(); 
	}
	else if ($efname && $efvalue>0) {
		if (isset($this->pagefooters[$efname])) { $this->footerDetails['even'] = $this->pagefooters[$efname]; } 
		else { $this->footerDetails['even'] = array(); }
		$this->HTMLFooterE = ''; 
	}


	// mPDF 4.0
	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->_setAutoHeaderHeight($this->headerDetails['even'], $this->HTMLHeaderE);
		$this->_setAutoFooterHeight($this->footerDetails['even'], $this->HTMLFooterE);
	}
	else {	// ODD or DEFAULT
		$this->_setAutoHeaderHeight($this->headerDetails['odd'], $this->HTMLHeader);
		$this->_setAutoFooterHeight($this->footerDetails['odd'], $this->HTMLFooter);
	}
	// Reset column top margin
	$this->y0 = $this->tMargin;

	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
}


// mPDF 4.0
function _setAutoHeaderHeight(&$det, &$htmlh) {
  if ($this->setAutoTopMargin=='pad') {
	if ($htmlh['h']) { $h = $htmlh['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'H'); }
	else { $h = 0; }
	$this->tMargin = $this->margin_header + $h + $this->orig_tMargin;
  }
  else if ($this->setAutoTopMargin=='stretch') {
	if ($htmlh['h']) { $h = $htmlh['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'H'); }
	else { $h = 0; }
	$this->tMargin = max($this->orig_tMargin, $this->margin_header + $h + $this->autoMarginPadding);
  }
}

// mPDF 4.0
function _setAutoFooterHeight(&$det, &$htmlf) {
  if ($this->setAutoBottomMargin=='pad') {
	if ($htmlf['h']) { $h = $htmlf['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'F'); }
	else { $h = 0; }
	$this->bMargin = $this->margin_footer + $h + $this->orig_bMargin;
	$this->PageBreakTrigger=$this->h-$this->bMargin ;
  }
  else if ($this->setAutoBottomMargin=='stretch') {
	if ($htmlf['h']) { $h = $htmlf['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'F'); }
	else { $h = 0; }
	$this->bMargin = max($this->orig_bMargin, $this->margin_footer + $h + $this->autoMarginPadding);
	$this->PageBreakTrigger=$this->h-$this->bMargin ;
  }
}
// mPDF 4.0
function _getHFHeight(&$det,$end) {
	$h = 0;
	if(count($det)) {
		foreach(array('L','C','R') AS $pos) {
		  if (isset($det[$pos]['content']) && $det[$pos]['content']) {
			if (isset($det[$pos]['font-size']) && $det[$pos]['font-size']) { $hfsz = $det[$pos]['font-size']; }
			else { $hfsz = $this->default_font_size; }
			$h = max($h,$hfsz/$this->k);
		  }
		}
		if ($det['line'] && $end=='H') { $h += $h/$this->k*$this->header_line_spacing; }
		else if ($det['line'] && $end=='F') { $h += $h/$this->k*$this->footer_line_spacing; }
   	}
	return $h;
}


function _endpage() {
	$this->printfloatbuffer();

	//End of page contents
	$this->state=1;
}

function _newobj() {
	//Begin a new object
	$this->n++;
	$this->offsets[$this->n]=strlen($this->buffer);
	$this->_out($this->n.' 0 obj');
}

function _dounderline($x,$y,$txt) {
	// mPDF 4.0 changed to line instead of rectangle
	// Now print line exactly where $y secifies - called from Text() and Cell() - adjust  position there
	// WORD SPACING
      $w =($this->GetStringWidth($txt)*$this->k) + ($this->charspacing * mb_strlen( $txt, $this->mb_enc )) 
		 + ( $this->ws * mb_substr_count( $txt, ' ', $this->mb_enc ));
	//Draw a line
	return sprintf('%.3f %.3f m %.3f %.3f l S',$x*$this->k,($this->h-$y)*$this->k,($x*$this->k)+$w,($this->h-$y)*$this->k);
}

// mPDF 3.0
function _parsephp($file) {
	list($accessfile, $isTemp) = $this->_get_local_file($file);
	if (!$accessfile) { 
		if ($this->showImageErrors) $this->Error("PHP parser: unable to open file : ".$file); 
		else return -1; 
	}
	// mPDF 4.0
	$type = '';
	if (function_exists('curl_init')) { 
		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_NOBODY, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_URL, $accessfile);
		$contents = curl_exec($c);
		curl_close($c);
		if ($isTemp) { unlink($accessfile); } 
		preg_match('/Content-Type:\s*image\/(\w+)/i',$contents,$m);
		$type = strtolower(trim($m[1]));
	}
	// mPDF 4.0
	if (!function_exists('curl_init') || !$type) {
		$fh = @fopen($accessfile, "rb");
		if ($fh) {
			$hdr = fread($fh, 10);
			fclose($fh);
			if ($isTemp) { unlink($accessfile); } 
			if (substr($hdr, 6, 4)== 'JFIF') { 
				$type = 'jpeg'; 
			}
			else if (substr($hdr, 0, 6)== "GIF87a" || substr($hdr, 0, 6)== "GIF89a") { 
				$type = 'gif';
			}
			else if (substr($hdr, 0, 8)== $this->chrs[137].'PNG'.$this->chrs[13].$this->chrs[10].$this->chrs[26].$this->chrs[10]) { 
				$type = 'png';
			}
			else {
				if ($this->showImageErrors) $this->Error("PHP parser error: unable to determine file type: ".$accessfile); 
				return -1; 
			}
		}
		else {
			if ($this->showImageErrors) $this->Error("PHP parser error: cURL is not enabled and unable to open file: ".$accessfile); 
			return -1; 
		}
	}
	$res = -1;
	if ($type == 'jpeg') { $res = $this->_parsejpg($file); }
	else if ($type == 'gif') { $res = $this->_parsegif($file); }
	else if ($type == 'png') { $res = $this->_parsepng($file); }
	// mPDF 3.0
	else {
		if ($this->showImageErrors) $this->Error("PHP parser: file type not recognised from php Header: ".$file); 
		else return -1; 
	}
	// mPDF 3.0 - Try again PNG
	if ($type=='png' && $res < -1) { 
		$res=$this->_parsepng2($file, $res); 	// mPDF 4.0
		if ($res['UID']) { $file = $res['UID']; }	// A unique alpha blended PNG created
	}
	return $res; 
}


function _parsejpg($file) {
	list($accessfile, $isTemp) = $this->_get_local_file($file);
 
	if (!$accessfile) { 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error("JPG parser: unable to open file : ".$file); 
		else return -1; 
	}

	$a=GetImageSize($accessfile);

	//Extract info from a JPEG file
	if(!$a) { 
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Missing or incorrect JPEG image file: '.$file); 
		else return -1; 
	}
	if($a[2]!=2) { 
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Not a JPEG file: '.$file); 
		else return -1; 
	}
	if(!isset($a['channels']) or $a['channels']==3) { $colspace='DeviceRGB'; }	
	elseif($a['channels']==4) { $colspace='DeviceCMYK'; }
	else { $colspace='DeviceGray'; }

	$bpc=isset($a['bits']) ? $a['bits'] : 8;

	//Read whole file
	$data=file_get_contents($accessfile);
	if ($isTemp) { unlink($accessfile); } 
	return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
}

// mPDF 4.0 - Try again PNG
function _parsepng2($file, $problem) {
	list($accessfile, $isTemp) = $this->_get_local_file($file);
	if (!$accessfile) { 
		if ($this->showImageErrors) $this->Error("PNG parser (2): unable to open file : ".$file); 
		else return -1; 
	}
	$im = imagecreatefrompng($accessfile);
	$w = imagesx($im);
	$h = imagesy($im);
	if ($isTemp) { unlink($accessfile); } 
	if ($im) {
	  // DokuWiki
	   $tempfile = $tmpalphadir.'_tempImgPNG'.RAND(1,10000).'.png';
	  // DokuWiki
	   if ($problem == -3) {	// Alpha channel set
		$imgalpha = imagecreate($w, $h);
		// generate gray scale pallete
		for ($c = 0; $c < 256; ++$c) {
			ImageColorAllocate($imgalpha, $c, $c, $c);
		}
		// extract alpha channel
		for ($xpx = 0; $xpx < $w; ++$xpx) {
			for ($ypx = 0; $ypx < $h; ++$ypx) {
				$colorindex = imagecolorat($im, $xpx, $ypx);
				$col = imagecolorsforindex($im, $colorindex);
				$gammacorr = 2.2;	// gamma correction
				$gamma = (pow((((127 - $col['alpha']) * 255 / 127) / 255), $gammacorr) * 255);
				imagesetpixel($imgalpha, $xpx, $ypx, $gamma);
			}
		}
		// create temp alpha file
	  // DokuWiki
    $tempfile_alpha = $tmpalphadir.'_tempMskPNG'.RAND(1,10000).'.png';
	  // DokuWiki
    
		imagepng($imgalpha, $tempfile_alpha);
		imagedestroy($imgalpha);
		// extract image without alpha channel
		$imgplain = imagecreatetruecolor($w, $h);
		imagecopy($imgplain, $im, 0, 0, 0, 0, $w, $h);
		// create temp image file
		imagepng($imgplain, $tempfile);
		imagedestroy($imgplain);
		// embed mask image
		$minfo = $this->_parsepng($tempfile_alpha);
		$imgmask = count($this->images)+1;
		$minfo['cs'] = 'DeviceGray';
		$minfo['i']=$imgmask ;
		$this->images[$tempfile_alpha] = $minfo;
		unlink($tempfile_alpha);
		// embed image, masked with previously embedded mask
		$info = $this->_parsepng($tempfile);
		$info['masked'] = $imgmask;
		unlink($tempfile);
		return $info;
	   }
	   else { 	// No alpha/transparency set
		imagealphablending($im, false);
		imagesavealpha($im, false); 
		imageinterlace($im, false);
		imagepng($im, $tempfile );
		imagedestroy($im);
		$ret = $this->_parsepng($tempfile ) ;
		if (isset($UID) && $UID) { $ret['UID'] = $tempfile; }	// A unique alpha blended image created
		unlink($tempfile ); 
		return $ret;
	   }
	}
	else {
		if ($this->showImageErrors) $this->Error("PNG parser (2): PNG error: ".$file); 
		else return -1; 
	}
}

function _parsepng($file) {
	list($accessfile, $isTemp) = $this->_get_local_file($file);

	if (!$accessfile) { 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error("PNG parser: unable to open file : ".$file); 
		else return -1; 
	}

	$f=@fopen($accessfile,'rb');

	//Extract info from a PNG file
	if (!$f) { 
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error("PNG parser: unable to open file - access-name: $accessfile - filename: ".$file); 
		else return -1; 
	}

	//Check signature
	if(fread($f,8)!=$this->chrs[137].'PNG'.$this->chrs[13].$this->chrs[10].$this->chrs[26].$this->chrs[10]) { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Not a PNG file: '.$file); 
		else return -1; 
	}
	//Read header chunk
	fread($f,4);
	if(fread($f,4)!='IHDR') { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Incorrect PNG file (no IHDR block found): '.$file); 
		else return -1; 
	}
	$w=$this->_freadint($f);
	$h=$this->_freadint($f);
	$bpc=$this->ords[fread($f,1)];
	$errbpc = false;	// mPDF 4.0
	if($bpc>8) { 
		if ($this->showImageErrors===2) {
			fclose($f);
			if ($isTemp) { unlink($accessfile); } 
			$this->Error('16-bit depth not supported: '.$file); 
		}
		else $errbpc = true;	// Don't return here - check for alpha	// mPDF 4.0
	}
	$ct=$this->ords[fread($f,1)];
	if($ct==0) { $colspace='DeviceGray'; }
	elseif($ct==2) { $colspace='DeviceRGB'; }
	elseif($ct==3) { $colspace='Indexed'; }
	else { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors===2) $this->Error('Alpha channel not supported: '.$file); 
		else return -3; 
	}
	if ($errbpc) { 	// mPDF 4.0
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		return -2; 
	}
	if($this->ords[fread($f,1)]!=0) { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors===2) $this->Error('Unknown compression method: '.$file); 
		else return -2; 
	}
	if($this->ords[fread($f,1)]!=0) { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors===2) $this->Error('Unknown PNG filter method: '.$file); 
		else return -2; 
	}
	if($this->ords[fread($f,1)]!=0) { 
		fclose($f);
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors===2) $this->Error('PNG Interlacing not supported: '.$file); 
		else return -2; 
	}
	fread($f,4);
	$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
	//Scan chunks looking for palette, transparency and image data
	$pal='';
	$trns='';
	$data='';
	do
	{
		$n=$this->_freadint($f);
		$type=fread($f,4);
		if($type=='PLTE') {
			//Read palette
			$pal=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='tRNS') {
			//Read transparency info
			$t=fread($f,$n);
			if($ct==0) $trns=array($this->ords[substr($t,1,1)]);
			elseif($ct==2) $trns=array($this->ords[substr($t,1,1)],$this->ords[substr($t,3,1)],$this->ords[substr($t,5,1)]);
			else
			{
				$pos=strpos($t,$this->chrs[0]);
				if(is_int($pos)) $trns=array($pos);
			}
			fread($f,4);
		}
		elseif($type=='IDAT') {
			$rem = $n;
			while ($rem > 0) {
			  $x = fread($f, min($rem, 2048));
			  $data.= $x;
			  $rem -= strlen($x);
			}
			fread($f,4);
		}
		elseif($type=='IEND') { break; }
		else if (preg_match('/[a-zA-Z]{4}/',$type)) { fread($f,$n+4); }
		else {
			fclose($f);
			if ($isTemp) { unlink($accessfile); } 
			// mPDF 3.0
			if ($this->showImageErrors) $this->Error("PNG parser: unable to parse file data: ".$file); 
			else return -1; 
		}
	}
	while($n);
	fclose($f);
	if ($isTemp) { unlink($accessfile); } 
	if($colspace=='Indexed' and empty($pal)) {
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Missing palette in '.$file);
		else return -1; 
	}
	return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
}



function _parsegif($file) { //EDITEI - GIF support is now included 
	list($accessfile, $isTemp) = $this->_get_local_file($file);
 
	if (!$accessfile) { 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error("GIF parser: unable to open file : ".$file); 
		else return -1; 
	}

	// mPDF 4.0
	if (function_exists('gd_info')) { $gd = gd_info(); }
	else {$gd = array(); }

	if (isset($gd['GIF Read Support']) && $gd['GIF Read Support']) {
		$im = @imagecreatefromgif($accessfile);
		if ($im) {
      // DokuWiki
			$tempfile = $tmpalphadir.'_tempImgPNG'.RAND(1,10000).'.png';
      // DokuWiki
			imagealphablending($im, false);
			imagesavealpha($im, false); 
			imageinterlace($im, false);
			imagepng($im, $tempfile);
			$info = $this->_parsepng($tempfile);
			imagedestroy($im);
			unlink($tempfile);
		}
		if ($isTemp) { unlink($accessfile); } 
		return $info;
	}

	//Function by Jerome Fenal
	require_once(_MPDF_PATH .'classes/gif.php'); //GIF class in pure PHP from Yamasoft (http://www.yamasoft.com/php-gif.zip)

	$h=0;
	$w=0;
	$gif=new CGIF();
	if (!$gif->loadFile($accessfile, 0)) { 
		if ($isTemp) { unlink($accessfile); } 
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error("GIF parser: unable to open file - access-name: $accessfile - filename: ".$file); 
		else return -1; 
	}

	if(isset($gif->m_img->m_gih->m_bLocalClr) && $gif->m_img->m_gih->m_bLocalClr) {
		$nColors = $gif->m_img->m_gih->m_nTableSize;
		$pal = $gif->m_img->m_gih->m_colorTable->toString();
		if($bgColor != -1) {
			// mPDF 4.0
			$bgColor = $gif->m_img->m_gih->m_colorTable->colorIndex($bgColor);
		}
		$colspace='Indexed';
	} elseif(isset($gif->m_gfh->m_bGlobalClr) && $gif->m_gfh->m_bGlobalClr) {
		$nColors = $gif->m_gfh->m_nTableSize;
		$pal = $gif->m_gfh->m_colorTable->toString();
		if((isset($bgColor)) and $bgColor != -1) {
			$bgColor = $gif->m_gfh->m_colorTable->colorIndex($bgColor);
		}
		$colspace='Indexed';
	} else {
		$nColors = 0;
		$bgColor = -1;
		$colspace='DeviceGray';
		$pal='';
	}

	$trns='';
	if(isset($gif->m_img->m_bTrans) && $gif->m_img->m_bTrans && ($nColors > 0)) {
		$trns=array($gif->m_img->m_nTrans);
	}

	$data=$gif->m_img->m_data;
	$w=$gif->m_gfh->m_nWidth;
	$h=$gif->m_gfh->m_nHeight;

	if ($isTemp) { unlink($accessfile); } 

	if($colspace=='Indexed' and empty($pal)) {
		// mPDF 3.0
		if ($this->showImageErrors) $this->Error('Missing palette in '.$file);
		else return -1; 
	}

	if ($this->compress) {
		$data=gzcompress($data);
		return array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'f'=>'FlateDecode', 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
	} 
	else {
		return array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
	} 
}

function _parsewmf($file) {
        $this->gdiObjectArray = array();

        $a=unpack('stest',"\1\0");
        if ($a['test']!=1)
            $this->Error('Big-endian architectures are not supported');

	  list($accessfile, $isTemp) = $this->_get_local_file($file);
 
	  if (!$accessfile) { 
		return -1; 
            $this->Error('Can\'t open WMF image file: '.$file);
	  }

        $f=@fopen($accessfile,'rb');
        if(!$f)
            $this->Error('Can\'t open WMF image file: '.$file);

        // check for Aldus placeable metafile header
        $key = unpack('Lmagic', fread($f, 4));
        $headSize = 18 - 4; // WMF header minus four bytes already read
        if ($key['magic'] == (int)0x9AC6CDD7)
            $headSize += 22; // Aldus header

        // strip headers
        fread($f, $headSize);

        // define some state variables
        $wo=null; // window origin
        $we=null; // window extent
        $polyFillMode = 0;
        $nullPen = false;
        $nullBrush = false;

        $endRecord = false;

        $data = '';

        // read the records
        while (!feof($f) && !$endRecord)
        {
            $recordInfo = unpack('Lsize/Sfunc', fread($f, 6));

            // size of record given in WORDs (= 2 bytes)
            $size = $recordInfo['size'];

            // func is number of GDI function
            $func = $recordInfo['func'];

            // parameters are read as one block and processed
            // as necessary by the case statement below.
            // the data are stored in little-endian format and are unpacked using:
            // s - signed 16-bit int
            // S - unsigned 16-bit int (or WORD)
            // L - unsigned 32-bit int (or DWORD)
            // NB. parameters to GDI functions are stored in reverse order
            // however structures are not reversed,
            // e.g. POINT { int x, int y } where x=3000 (0x0BB8) and y=-1200 (0xFB50)
            // is stored as B8 0B 50 FB
            if ($size > 3)
            {
                $parms = fread($f, 2*($size-3));
            }

            // process each record.
            // function numbers are defined in wingdi.h
            switch ($func)
            {
                case 0x020b:  // SetWindowOrg
                    // do not allow window origin to be changed
                    // after drawing has begun
                    if (!$data)
                        $wo = array_reverse(unpack('s2', $parms));
                    break;

                case 0x020c:  // SetWindowExt
                    // do not allow window extent to be changed
                    // after drawing has begun
                    if (!$data)
                        $we = array_reverse(unpack('s2', $parms));
                    break;

                case 0x02fc:  // CreateBrushIndirect
                    $brush = unpack('sstyle/Cr/Cg/Cb/Ca/Shatch', $parms);
                    $brush['type'] = 'B';
                    $this->_AddGDIObject($brush);
                    break;

                case 0x02fa:  // CreatePenIndirect
                    $pen = unpack('Sstyle/swidth/sdummy/Cr/Cg/Cb/Ca', $parms);

                    // convert width from twips to user unit
                    $pen['width'] /= (20 * $this->k);
                    $pen['type'] = 'P';
                    $this->_AddGDIObject($pen);
                    break;

                // MUST create other GDI objects even if we don't handle them
                // otherwise object numbering will get out of sequence
                case 0x06fe: // CreateBitmap
                case 0x02fd: // CreateBitmapIndirect
                case 0x00f8: // CreateBrush
                case 0x02fb: // CreateFontIndirect
                case 0x00f7: // CreatePalette
                case 0x01f9: // CreatePatternBrush
                case 0x06ff: // CreateRegion
                case 0x0142: // DibCreatePatternBrush
                    $dummyObject = array('type'=>'D');
                    $this->_AddGDIObject($dummyObject);
                    break;

                case 0x0106:  // SetPolyFillMode
                    $polyFillMode = unpack('smode', $parms);
                    $polyFillMode = $polyFillMode['mode'];
                    break;

                case 0x01f0:  // DeleteObject
                    $idx = unpack('Sidx', $parms);
                    $idx = $idx['idx'];
                    $this->_DeleteGDIObject($idx);
                    break;

                case 0x012d:  // SelectObject
                    $idx = unpack('Sidx', $parms);
                    $idx = $idx['idx'];
                    $obj = $this->_GetGDIObject($idx);

                    switch ($obj['type'])
                    {
                        case 'B':
                            $nullBrush = false;

                            if ($obj['style'] == 1) // BS_NULL, BS_HOLLOW
                            {
                                $nullBrush = true;
                            }
                            else
                            {
                                $data .= sprintf("%.3f %.3f %.3f rg\n",$obj['r']/255,$obj['g']/255,$obj['b']/255);
                            }
                            break;

                        case 'P':
                            $nullPen = false;
                            $dashArray = array(); 

                            // dash parameters are my own - feel free to change them
                            switch ($obj['style'])
                            {
                                case 0: // PS_SOLID
                                    break;
                                case 1: // PS_DASH
                                    $dashArray = array(3,1);
                                    break;
                                case 2: // PS_DOT
                                    $dashArray = array(0.5,0.5);
                                    break;
                                case 3: // PS_DASHDOT
                                    $dashArray = array(2,1,0.5,1);
                                    break;
                                case 4: // PS_DASHDOTDOT
                                    $dashArray = array(2,1,0.5,1,0.5,1);
                                    break;
                                case 5: // PS_NULL
                                    $nullPen = true;
                                    break;
                            }

                            if (!$nullPen)
                            {
                                $data .= sprintf("%.3f %.3f %.3f RG\n",$obj['r']/255,$obj['g']/255,$obj['b']/255);
                                $data .= sprintf("%.3f w\n",$obj['width']*$this->k);
                            }

                            if (!empty($dashArray))
                            {
                                $s = '[';
                                for ($i=0; $i<count($dashArray);$i++)
                                {
                                    $s .= $dashArray[$i] * $this->k;
                                    if ($i != count($dashArray)-1)
                                        $s .= ' ';
                                }
                                $s .= '] 0 d';
                                $data .= $s."\n";
                            }

                            break;
                    }
                    break;

                case 0x0325: // Polyline
                case 0x0324: // Polygon
                    $coords = unpack('s'.($size-3), $parms);
                    $numpoints = $coords[1];

                    for ($i = $numpoints; $i > 0; $i--)
                    {
                        $px = $coords[2*$i];
                        $py = $coords[2*$i+1];

                        if ($i < $numpoints)
                            $data .= $this->_LineTo($px, $py);
                        else
                            $data .= $this->_MoveTo($px, $py);
                    }

                    if ($func == 0x0325)
                    {
                        $op = 's';
                    }
                    else if ($func == 0x0324)
                    {
                        if ($nullPen)
                        {
                            if ($nullBrush)
                                $op = 'n';  // no op
                            else
                                $op = 'f';  // fill
                        }
                        else
                        {
                            if ($nullBrush)
                                $op = 's';  // stroke
                            else
                                $op = 'b';  // stroke and fill
                        }

                        if ($polyFillMode==1 && ($op=='b' || $op=='f')) 
                            $op .= '*';  // use even-odd fill rule
                    }

                    $data .= $op."\n";
                    break;

                case 0x0538: // PolyPolygon
                    $coords = unpack('s'.($size-3), $parms);

                    $numpolygons = $coords[1];

                    $adjustment = $numpolygons;

                    for ($j = 1; $j <= $numpolygons; $j++)
                    {
                        $numpoints = $coords[$j + 1];

                        for ($i = $numpoints; $i > 0; $i--)
                        {
                            $px = $coords[2*$i   + $adjustment];
                            $py = $coords[2*$i+1 + $adjustment];

                            if ($i == $numpoints)
                                $data .= $this->_MoveTo($px, $py);
                            else
                                $data .= $this->_LineTo($px, $py);
                        }

                        $adjustment += $numpoints * 2;
                    }

                    if ($nullPen)
                    {
                        if ($nullBrush)
                            $op = 'n';  // no op
                        else
                            $op = 'f';  // fill
                    }
                    else
                    {
                        if ($nullBrush)
                            $op = 's';  // stroke
                        else
                            $op = 'b';  // stroke and fill
                    }

                    if ($polyFillMode==1 && ($op=='b' || $op=='f')) 
                        $op .= '*';  // use even-odd fill rule

                    $data .= $op."\n";

                    break;

                case 0x0000:
                    $endRecord = true;
                    break;
            }
        }

        fclose($f);
	  if ($isTemp) { unlink($accessfile); } 
        return array('x'=>$wo[0],'y'=>$wo[1],'w'=>$we[0],'h'=>$we[1],'data'=>$data);
}

function _MoveTo($x, $y) {
        return "$x $y m\n";
}

// a line must have been started using _MoveTo() first
function _LineTo($x, $y) {
        return "$x $y l\n";
}

function _AddGDIObject($obj) {
        // find next available slot
        $idx = 0;
        if (!empty($this->gdiObjectArray))
        {
            $empty = false;
            $i = 0;

            while (!$empty)
            {
                $empty = !isset($this->gdiObjectArray[$i]);
                $i++;
            }
            $idx = $i-1;
        }

        $this->gdiObjectArray[$idx] = $obj;
}

function _GetGDIObject($idx) {
        return $this->gdiObjectArray[$idx];
}

function _DeleteGDIObject($idx) {
        unset($this->gdiObjectArray[$idx]);
}

function _putformobjects() {
        reset($this->formobjects);
        while(list($file,$info)=each($this->formobjects))
        {
            $this->_newobj();
            $this->formobjects[$file]['n']=$this->n;
            $this->_out('<</Type /XObject');
            $this->_out('/Subtype /Form');
            $this->_out('/BBox ['.$info['x'].' '.$info['y'].' '.($info['w']+$info['x']).' '.($info['h']+$info['y']).']');
            if ($this->compress)
                $this->_out('/Filter /FlateDecode');
            $data=($this->compress) ? gzcompress($info['data']) : $info['data'];
            $this->_out('/Length '.strlen($data).'>>');
            $this->_putstream($data);

            unset($this->formobjects[$file]['data']);
            $this->_out('endobj');
        }
}
// END of WMF functions


function _freadint($f)
{
	//Read a 4-byte integer from file
	$i=$this->ords[fread($f,1)]<<24;
	$i+=$this->ords[fread($f,1)]<<16;
	$i+=$this->ords[fread($f,1)]<<8;
	$i+=$this->ords[fread($f,1)];
	return $i;
}

function _UTF16BEtextstring($s) {
	$s = $this->UTF8ToUTF16BE($s, true);
	return '('. $this->_escape($s).')';
}

function _textstring($s) {
	return '('. $this->_escape($s).')';
}


function _escape($s)
{
	// the chr(13) substitution fixes the Bugs item #1421290.
	return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', $this->chrs[13] => '\r'));
}

function _putstream($s) {
	$this->_out('stream');
	$this->_out($s);
	$this->_out('endstream');
}


function _out($s,$ln=true) {
	if($this->state==2) {
	   if ($this->bufferoutput) {
		$this->headerbuffer.= $s."\n";
	   }
	   else if ($this->table_rotate && !$this->processingHeader && !$this->processingFooter) {
		// Captures eveything in buffer for rotated tables; 
		$this->tablebuffer[] = array(
		's' => $s,							// Text string to output  
		'x' => $this->x, 						// x when printed  
		'y' => $this->y,					 	// y when printed (after column break) 
		);
	   }
	   else if ($this->kwt && !$this->processingHeader && !$this->processingFooter) {
		// Captures eveything in buffer for keep-with-table (h1-6); 
		$this->kwt_buffer[] = array(
		's' => $s,							// Text string to output 
		'x' => $this->x, 						// x when printed  
		'y' => $this->y,					 	// y when printed  
		);
	   }
	   else if (($this->keep_block_together) && (!$this->processingHeader) && (!$this->processingFooter)) {
		// Captures eveything in buffer; 
		if (preg_match('/q \d+\.\d\d+ 0 0 (\d+\.\d\d+) \d+\.\d\d+ \d+\.\d\d+ cm \/I\d+ Do Q/',$s,$m)) {	// Image data
			$h = ($m[1]/$this->k);
			// Update/overwrite the lowest bottom of printing y value for Keep together block
			$this->ktBlock[$this->page]['bottom_margin'] = $this->y+$h;
		}
		else { 	// Td Text Set in Cell()
			if (isset($this->ktBlock[$this->page]['bottom_margin'])) { $h = $this->ktBlock[$this->page]['bottom_margin'] - $this->y; }
			else { $h = 0; }
		}
		if ($h < 0) { $h = -$h; }
		$this->divbuffer[] = array(
		'page' => $this->page,
		's' => $s,							// Text string to output 
		'x' => $this->x, 						// x when printed 
		'y' => $this->y,					 	// y when printed (after column break)
		'h' => $h						 	// actual y at bottom when printed = y+h
		);
	   }
	   else {
		$this->pages[$this->page] .= $s.($ln == true ? "\n" : '');
	   }

	}
	else {
		$this->buffer .= $s.($ln == true ? "\n" : '');
	}
}



function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*$this->k;
		$cy=($this->h-$y)*$this->k;
		$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.3f %.3f cm 1 0 0 1 %.3f %.3f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}

// From Invoice
function RoundedRect($x, $y, $w, $h, $r, $style = '')
{
	$k = $this->k;
	$hp = $this->h;
	if($style=='F')
		$op='f';
	elseif($style=='FD' or $style=='DF')
		$op='B';
	else
		$op='S';
	$MyArc = 4/3 * (sqrt(2) - 1);
	$this->_out(sprintf('%.3f %.3f m',($x+$r)*$k,($hp-$y)*$k ));
	$xc = $x+$w-$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.3f %.3f l', $xc*$k,($hp-$y)*$k ));

	$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
	$xc = $x+$w-$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.3f %.3f l',($x+$w)*$k,($hp-$yc)*$k));
	$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
	$xc = $x+$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.3f %.3f l',$xc*$k,($hp-($y+$h))*$k));
	$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
	$xc = $x+$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.3f %.3f l',($x)*$k,($hp-$yc)*$k ));
	$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
	$this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
{
	$h = $this->h;
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f c ', $x1*$this->k, ($h-$y1)*$this->k,
						$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
}




//====================================================



// Label and number of invoice/estimate





// Converts UTF-8 strings to codepoints array.<br>
function UTF8StringToArray($str) {
			$unicode = array(); // array containing unicode values
			$bytes  = array(); // array containing single character byte sequences
			$numbytes  = 1; // number of octetc needed to represent the UTF-8 character
			
			$str .= ""; // force $str to be a string
			$length = strlen($str);
			
			for($i = 0; $i < $length; $i++) {
				$char = $this->ords[substr($str,$i,1)]; // get one string character at time
				if(count($bytes) == 0) { // get starting octect
					if ($char <= 0x7F) {
						$unicode[] = $char; // use the character "as is" because is ASCII
						$numbytes = 1;
					} elseif (($char >> 0x05) == 0x06) { // 2 bytes character (0x06 = 110 BIN)
						$bytes[] = ($char - 0xC0) << 0x06; 
						$numbytes = 2;
					} elseif (($char >> 0x04) == 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
						$bytes[] = ($char - 0xE0) << 0x0C; 
						$numbytes = 3;
					} elseif (($char >> 0x03) == 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
						$bytes[] = ($char - 0xF0) << 0x12; 
						$numbytes = 4;
					} else {
						// use replacement character for other invalid sequences
						$unicode[] = 0xFFFD;
						$bytes = array();
						$numbytes = 1;
					}
				} elseif (($char >> 0x06) == 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
					$bytes[] = $char - 0x80;
					if (count($bytes) == $numbytes) {
						// compose UTF-8 bytes to a single unicode value
						$char = $bytes[0];
						for($j = 1; $j < $numbytes; $j++) {
							$char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
						}
						if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
							// The definition of UTF-8 prohibits encoding character numbers between
							// U+D800 and U+DFFF, which are reserved for use with the UTF-16
							// encoding form (as surrogate pairs) and do not directly represent
							// characters.
							$unicode[] = 0xFFFD; // use replacement character
						}
						else {
							$unicode[] = $char; // add char to array
						}
						// reset data for next char
						$bytes = array(); 
						$numbytes = 1;
					}
				} else {
					// use replacement character for other invalid sequences
					$unicode[] = 0xFFFD;
					$bytes = array();
					$numbytes = 1;
				}
			}
			return $unicode;
}


// mPDF 4.0 Convert utf-8 string to <HHHHHH> for Font Subsets
function UTF8toSubset($str) {
	$ret = '<';
	// mPDF 4.0
	$str = preg_replace('/'.preg_quote($this->aliasNbPg,'/').'/', chr(7), $str );
	$str = preg_replace('/'.preg_quote($this->aliasNbPgGp,'/').'/', chr(8), $str );
	$unicode = $this->UTF8StringToArray($str);
	$orig_fid = $this->CurrentFont['subsetfontids'][0];
	$last_fid = $this->CurrentFont['subsetfontids'][0];
	foreach($unicode as $c) {
	   // mPDF 4.0
	   if ($c == 7 || $c == 8) { 
			if ($orig_fid != $last_fid) {
				$ret .= '> Tj /F'.$orig_fid.' '.$this->FontSizePt.' Tf <';
				$last_fid = $orig_fid;
			}
			if ($c == 7) { $ret .= $this->aliasNbPg; }
			else { $ret .= $this->aliasNbPgGp; }
			continue;
	   }
	   for ($i=0; $i<99; $i++) {
		// return c as decimal char
		$init = array_search($c, $this->CurrentFont['subsets'][$i]);
		if ($init!==false) {
			if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
				$ret .= '> Tj /F'.$this->CurrentFont['subsetfontids'][$i].' '.$this->FontSizePt.' Tf <';
				$last_fid = $this->CurrentFont['subsetfontids'][$i];
			}
			$ret .= sprintf("%02s", strtoupper(dechex($init)));
			break;
		}
		else if (count($this->CurrentFont['subsets'][$i]) < 255) {
			$n = count($this->CurrentFont['subsets'][$i]);
			$this->CurrentFont['subsets'][$i][$n] = $c;
			if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
				$ret .= '> Tj /F'.$this->CurrentFont['subsetfontids'][$i].' '.$this->FontSizePt.' Tf <';
				$last_fid = $this->CurrentFont['subsetfontids'][$i];
			}
			$ret .= sprintf("%02s", strtoupper(dechex($n)));
			break;
		}
		else if (!isset($this->CurrentFont['subsets'][($i+1)])) {
			$this->CurrentFont['subsets'][($i+1)] = range(0,32);
			$new_fid = count($this->fonts)+$this->extraFontSubsets+1;
			$this->CurrentFont['subsetfontids'][($i+1)] = $new_fid;
			$this->extraFontSubsets++;
		}
	   }
	}
	$ret .= '>';
	if ($last_fid != $orig_fid) {
		$ret .= ' Tj /F'.$orig_fid.' '.$this->FontSizePt.' Tf <> ';
	}
	return $ret;
}


// Converts UTF-8 strings to UTF16-BE.
function UTF8ToUTF16BE($str, $setbom=true) {
	$outstr = ""; // string to be returned
	if ($setbom) {
		$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
	}
	$outstr .= mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
	return $outstr;
}




function _getfontpath() {
	// mPDF 4.0  Depracated. Use MPDF_FONTPATH
	return defined('MPDF_FONTPATH') ? MPDF_FONTPATH : '';
}




// ====================================================
// ====================================================




//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
function SetAutoFont($af = AUTOFONT_ALL) {
	if (!$this->is_MB) { return false; }
	if (!$af && $af !== 0) { $af = AUTOFONT_ALL; }
	$this->autoFontGroups = $af;
	if ($this->autoFontGroups ) { 
		$this->useSubstitutions = false; 
		$this->useLang = true;
		// mPDF 4.0
	}
}


function SetDefaultFont($font) {
	// Disallow embedded fonts to be used as defaults except in win-1252
	if ($this->codepage != 'win-1252') {
		if (strtolower($font) == 'times') { $font = 'serif'; }
		if (strtolower($font) == 'courier') { $font = 'monospace'; }
		if ((strtolower($font) == 'arial') || (strtolower($font) == 'helvetica')) { $font = 'sans-serif'; }
	}
  	$font = $this->SetFont($font);	// returns substituted font if necessary
	$this->default_font = $font;
	$this->original_default_font = $font;
}

function SetDefaultFontSize($fontsize) {
	$this->default_font_size = $fontsize;
	$this->original_default_font_size = $fontsize;
	$this->SetFontSize($fontsize);
	$this->defaultCSS['BODY']['FONT-SIZE'] = $fontsize . 'pt';
}

function SetDirectionality($dir='ltr') {
		$this->directionality = 'ltr'; 
		$this->defaultAlign = 'L';
		$this->defaultTableAlign = 'L';
}

function reverse_align(&$align) {
	if (strtolower($align) == 'right') { $align = 'left'; }
	else if (strtolower($align) == 'left') { $align = 'right'; }
	if (strtoupper($align) == 'R') { $align = 'L'; }
	else if (strtoupper($align) == 'L') { $align = 'R'; }
}


// Added to set line-height-correction
function SetLineHeightCorrection($val) {
	if ($val > 0) { $this->default_lineheight_correction = $val; }
	else { $this->default_lineheight_correction = 1.2; }
}

// Added to Set the lineheight - either to named fontsize(pts) or default
function SetLineHeight($FontPt='',$spacing = '') {
   if ($spacing > 0) { 
	if ($FontPt) { $this->lineheight = (($FontPt/2.834) *$spacing); }
	else { $this->lineheight = (($this->FontSizePt/2.834) *$spacing); }
   }
   else {
	if ($FontPt) { $this->lineheight = (($FontPt/2.834) *$this->default_lineheight_correction); }
	else { $this->lineheight = (($this->FontSizePt/2.834) *$this->default_lineheight_correction); }
   }
}

function SetBasePath($str='') {
  // mPDF 4.0
  if (!$str) { 
	if ( isset($_SERVER['HTTP_HOST']) ) { $host = $_SERVER['HTTP_HOST']; }
	else if ( isset($_SERVER['SERVER_NAME']) ) { $host = $_SERVER['SERVER_NAME']; }
	else { $host = ''; }
	if ($_SERVER['SCRIPT_NAME']) { $currentPath = dirname($_SERVER['SCRIPT_NAME']); }
	else { $currentPath = dirname($_SERVER['PHP_SELF']); }
	$currentPath = str_replace("\\","/",$currentPath);
	if ($currentPath == '/') { $currentPath = ''; }
	if ($host) { $currpath = 'http://' . $host . $currentPath .'/'; }
	else { $currpath = ''; }
	$this->basepath = $currpath; 
	$this->basepathIsLocal = true; 
	return; 
  }
  $str = preg_replace('/\?.*/','',$str);
  if (!preg_match('/(http|https|ftp):\/\/.*\//i',$str)) { $str .= '/'; } 
  $str .= 'xxx';	// in case $str ends in / e.g. http://www.bbc.co.uk/
  $this->basepath = dirname($str) . "/";	// returns e.g. e.g. http://www.google.com/dir1/dir2/dir3/
  $this->basepath = str_replace("\\","/",$this->basepath); //If on Windows
  $tr = parse_url($this->basepath);
  if (isset($tr['host']) && ($tr['host'] == $host)) { $this->basepathIsLocal = true; }
  else { $this->basepathIsLocal = false; }
}

// mPDF 4.0
function GetFullPath(&$path,$basepath='') {
	// When parsing CSS need to pass temporary basepath - so links are relative to current stylesheet
	if (!$basepath) { $basepath = $this->basepath; }
	//Fix path value
	$path = str_replace("\\","/",$path); //If on Windows
	//Get link info and obtain its absolute path
	$regexp = '|^./|';	// Inadvertently corrects "./path/etc" and "//www.domain.com/etc"
	$path = preg_replace($regexp,'',$path);

	if(substr($path,0,1) == '#') { return; }
	if (stristr($path,"mailto:") !== false) { return; }
	if (strpos($path,"../") !== false ) { //It is a Relative Link
		$backtrackamount = substr_count($path,"../");
		$maxbacktrack = substr_count($basepath,"/") - 1;
		$filepath = str_replace("../",'',$path);
		$path = $basepath;
		//If it is an invalid relative link, then make it go to directory root
		if ($backtrackamount > $maxbacktrack) $backtrackamount = $maxbacktrack;
		//Backtrack some directories
		for( $i = 0 ; $i < $backtrackamount + 1 ; $i++ ) $path = substr( $path, 0 , strrpos($path,"/") );
		$path = $path . "/" . $filepath; //Make it an absolute path
	}
	else if( strpos($path,":/") === false || strpos($path,":/") > 10) { //It is a Local Link
		if (substr($path,0,1) == "/") { 
			$tr = parse_url($basepath);
			$root = $tr['scheme'].'://'.$tr['host'];
			$path = $root . $path; 
		}
		else { $path = $basepath . $path; }
	}
	//Do nothing if it is an Absolute Link
}

// checks if file exists locally or remote
function _file_exists($srcpath) {
		if ($fh = @fopen($srcpath,"rb")) { fclose($fh); return true; }
		// If local file try using local path (? quicker, but also allowed even if allow_url_fopen false)
		if ($this->basepathIsLocal) {
			$tr = parse_url($srcpath);
			$lp=getenv("SCRIPT_NAME");
			$ap=realpath($lp);
			$ap=str_replace("\\","/",$ap);
			$docroot=substr($ap,0,strpos($ap,$lp));

			// WriteHTML parses all paths to full URLs; may be local file name from calling ->Image() directly
			if ($tr['scheme'] && $tr['host'] && $_SERVER["DOCUMENT_ROOT"] ) { 
				$localsrcpath = $_SERVER["DOCUMENT_ROOT"] . $tr['path']; 
			}
			// DOCUMENT_ROOT is not returned on IIS
			else if ($docroot) {
				$localsrcpath = $docroot . $tr['path'];
			}
			else { $localsrcpath = $srcpath; }
			if (file_exists($localsrcpath)) { return true; }
		}
		// if not use full URL
		else if (!ini_get('allow_url_fopen') && function_exists("curl_init")) {
			$ch = curl_init($srcpath);
			curl_setopt($ch, CURLOPT_HEADER, 0);
      		curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
			$test = curl_exec($ch);
			curl_close($ch);
			if ($test) { return true; }
		}
		return false;
}

// Used for external CSS files
function _get_file($path) {
	// If local file try using local path (? quicker, but also allowed even if allow_url_fopen false)
	$contents = '';
	$contents = @file_get_contents($path);
	if ($contents) { return $contents; }
	if ($this->basepathIsLocal) {
		$tr = parse_url($path);
		$lp=getenv("SCRIPT_NAME");
		$ap=realpath($lp);
		$ap=str_replace("\\","/",$ap);
		$docroot=substr($ap,0,strpos($ap,$lp));
		// WriteHTML parses all paths to full URLs; may be local file name from calling ->Image() directly
		if ($tr['scheme'] && $tr['host'] && $_SERVER["DOCUMENT_ROOT"] ) { 
			$localpath = $_SERVER["DOCUMENT_ROOT"] . $tr['path']; 
		}
		// DOCUMENT_ROOT is not returned on IIS
		else if ($docroot) {
			$localpath = $docroot . $tr['path'];
		}
		else { $localpath = $path; }
		$contents = @file_get_contents($localpath);
	}
	// if not use full URL
	else if (!$contents && !ini_get('allow_url_fopen') && function_exists("curl_init"))  {
		$ch = curl_init($path);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
		$contents = curl_exec($ch);
		curl_close($ch);
	}
	return $contents;
}
// returns name of local path, or URI if can be accessed, or copies file to temporary local file and returns filename
// returns array of filename to access, and true/false if temporary local file created
// called by _parsegif, _parsejpg and _parsepng
function _get_local_file($file) {
		if ($fh = @fopen($file,"rb")) { fclose($fh); return array($file,false); }
		// If local file try using local path (? quicker, but also allowed even if allow_url_fopen false)
		if ($this->basepathIsLocal) {
			$tr = parse_url($file);
			$lp=getenv("SCRIPT_NAME");
			$ap=realpath($lp);
			$ap=str_replace("\\","/",$ap);
			$docroot=substr($ap,0,strpos($ap,$lp));
			// WriteHTML parses all paths to full URLs; may be local file name from calling ->Image() directly
			if ($tr['scheme'] && $tr['host'] && $_SERVER["DOCUMENT_ROOT"] ) { 
				$localfile = $_SERVER["DOCUMENT_ROOT"] . $tr['path']; 
			}
			// DOCUMENT_ROOT is not returned on IIS
			else if ($docroot) {
				$localfile = $docroot . $tr['path'];
			}
			else { $localfile = $file; }
			if (file_exists($localfile)) { return array($localfile,false); }
		}
		// if not use full URL
		else if (!ini_get('allow_url_fopen') && function_exists("curl_init") && preg_match('/^http.*?\/([^\/]*)$/',$file,$match)) {
			$localfile = '_tmpImage_'.$match[1];
			$ch = curl_init($file);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$lFile = fopen( $localfile, 'wb' );
      		curl_setopt ( $ch , CURLOPT_FILE , $lFile );
			$data = curl_exec($ch);
			curl_close($ch);
			fclose( $lFile );
			return array($localfile, true); 
		}
		return array(false,false);
}




function ShowNOIMG_GIF($opt=true)
{
  $this->shownoimg=$opt;
}

function UseCSS($opt=true)
{
  $this->usecss=$opt;
}

function UseTableHeader($opt=true)
{
  $this->usetableheader=$opt;
}

function UsePRE($opt=true)
{
  $this->usepre=$opt;
}

// Edited mPDF 3.0 - added offset & extras (prefix/suffix)
function docPageNum($num = 0, $extras = false) {
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgno = $num;
	$suppress = 0;
	$offset = 0;
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgno = $num - $psarr['from'] + 1 + $offset; 
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
	}
	if ($suppress) { return ''; }
	if ($type=='A') { $ppgno = $this->dec2alpha($ppgno,true); }
	else if ($type=='a') { $ppgno = $this->dec2alpha($ppgno,false);}
	else if ($type=='I') { $ppgno = $this->dec2roman($ppgno,true); }
	else if ($type=='i') { $ppgno = $this->dec2roman($ppgno,false); }
	if ($extras) { $ppgno = $this->pagenumPrefix . $ppgno . $this->pagenumSuffix; }
	return $ppgno;
}

// mPDF 3.0
function docPageSettings($num = 0) {
	// Retruns current type (numberstyle), suppression state for this page number; 
	// reset is only returned if set for this page number
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgno = $num;
	$suppress = 0;
	$offset = 0;
	$reset = '';
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgno = $num - $psarr['from'] + 1 + $offset; 
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
		if ($num == $psarr['from']) { $reset = $psarr['reset']; }
	}
	if ($suppress) { $suppress = 'on'; }
	else { $suppress = 'off'; }
	return array($type, $suppress, $reset);
}

function docPageNumTotal($num = 0, $extras = false) {
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgstart = 1;
	$ppgend = count($this->pages)+1; 
	$suppress = 0;
	$offset = 0;
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgstart = $psarr['from'] + $offset; 
				$ppgend = count($this->pages)+1 + $offset; 
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
		if ($num < $psarr['from']) {
			if ($psarr['reset']) { 
				$ppgend = $psarr['from'] + $offset; 
				break;
			}
		}
	}
	if ($suppress) { return ''; }
	$ppgno = $ppgend-$ppgstart+$offset; 
	if ($extras) { $ppgno = $this->nbpgPrefix . $ppgno . $this->nbpgSuffix; }
	return $ppgno;
}

function RestartDocTemplate() {
	$this->docTemplateStart = $this->page;
}



//Page header
function Header($content='') {

	$this->cMarginL = 0;
	$this->cMarginR = 0;

	if ($this->docTemplate) {
		$pagecount = $this->SetSourceFile($this->docTemplate);
		if (($this->page - $this->docTemplateStart) > $pagecount) {
			if ($this->docTemplateContinue) { 
				$tplIdx = $this->ImportPage($pagecount);
				$this->UseTemplate($tplIdx);
			}
		}
		else {
			$tplIdx = $this->ImportPage(($this->page - $this->docTemplateStart));
			$this->UseTemplate($tplIdx);
		}
	}
	if ($this->pageTemplate) {
		$this->UseTemplate($this->pageTemplate);
	}

  $this->processingHeader=true;
  $h = $this->headerDetails;
  if(count($h)) {

	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out(sprintf('q 0 -1 1 0 0 %.3f cm ',($this->h*$this->k)));
		$yadj = $this->w - $this->h;
		$headerpgwidth = $this->h - $this->orig_lMargin - $this->orig_rMargin;
		if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$headerlmargin = $this->orig_rMargin;
		}
		else {
			$headerlmargin = $this->orig_lMargin;
		}
	}
	else { 
		$yadj = 0; 
		$headerpgwidth = $this->pgwidth;
		$headerlmargin = $this->lMargin;
	}

	$this->y = $this->margin_header - $yadj ;
	$this->SetTextColor(0);
    	$this->SUP = false;
	$this->SUB = false;
	$this->bullet = false;

	// only show pagenumber if numbering on
	// mPDF 3.0 Add PageNum prefix/suffix
	$pgno = $this->docPageNum($this->page, true); 

	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$side = 'even';
	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS = DEFAULT
			$side = 'odd';
	}
	$maxfontheight = 0;
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
	  }
	}
	// LEFT-CENTER-RIGHT
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		$hd = str_replace('{PAGENO}',$pgno,$h[$side][$pos]['content']);
		// mPDF 3.0
		$hd = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->aliasNbPgGp.$this->nbpgSuffix,$hd);	// {nbpg}
		$hd = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$hd);
		if (isset($h[$side][$pos]['font-family']) && $h[$side][$pos]['font-family']) { $hff = $h[$side][$pos]['font-family']; }
		// mPDF 3.0 original_ in case pagebreak in middle of table
		else { $hff = $this->original_default_font; }
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		// mPDF 3.0 original_ in case pagebreak in middle of table
		else { $hfsz = $this->original_default_font_size; }	// pts
		$maxfontheight = max($maxfontheight,$hfsz);
		$hfst = '';
		if (isset($h[$side][$pos]['font-style']) && $h[$side][$pos]['font-style']) { 
			$hfst = $h[$side][$pos]['font-style'];
		}
		if (isset($h[$side][$pos]['color']) && $h[$side][$pos]['color']) { 
			$hfcol = $h[$side][$pos]['color']; 
			$cor = $this->ConvertColor($hfcol);
			if ($cor) { $this->SetTextColor($cor['R'],$cor['G'],$cor['B']); }
		}
		else { $hfcol = ''; }
		// mPDF 3.0 Force output
		$this->SetFont($hff,$hfst,$hfsz,true,true);
		$this->x = $headerlmargin ;
		$this->y = $this->margin_header - $yadj ;

		$hd = $this->purify_utf8_text($hd);
		if ($this->text_input_as_HTML) {
			$hd = $this->all_entities_to_utf8($hd);
		}
		// CONVERT CODEPAGE
		if (!$this->is_MB) { $hd = mb_convert_encoding($hd,$this->mb_enc,'UTF-8'); }
		// DIRECTIONALITY RTL
		// mPDF 4.0 Font-specific ligature substitution for Indic fonts
		$align = $pos;
		if ($pos!='L' && (stripos($hd,$this->aliasNbPg)!==false || stripos($hd,$this->aliasNbPgGp)!==false)) { 
			if (stripos($hd,$this->aliasNbPgGp)!==false) { $type= 'nbpggp'; } else { $type= 'nbpg'; }
			$this->_out('{mpdfheader'.$type.' '.$pos.' ff='.$hff.' fs='.$hfst.' fz='.$hfsz.'}'); 
			$this->Cell($headerpgwidth ,$maxfontheight/$this->k ,$hd,0,0,$align,0,'',0,0,0,'M');
			$this->_out('Q');
		}
		else { 
			$this->Cell($headerpgwidth ,$maxfontheight/$this->k ,$hd,0,0,$align,0,'',0,0,0,'M');
		}
		if ($hfcol) { $this->SetTextColor(0); }
	  }
	}
	//Return Font to normal
	$this->SetFont($this->default_font,'',$this->original_default_font_size);
	// LINE
	if (isset($h[$side]['line']) && $h[$side]['line']) { 
		$this->SetLineWidth(0.1);
		$this->SetDrawColor(0);
		$this->Line($headerlmargin , $this->margin_header + ($maxfontheight*(1+$this->header_line_spacing)/$this->k) - $yadj , $headerlmargin + $headerpgwidth, $this->margin_header + ($maxfontheight*(1+$this->header_line_spacing)/$this->k) - $yadj  );
	}
	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out('Q');
	}
  }
  $this->SetY($this->tMargin);

  $this->processingHeader=false;
}



// mPDF 4.0
function TableHeaderFooter($content='',$tablestartpage='',$tablestartcolumn ='',$horf = 'H') {
  if((($this->usetableheader && $horf=='H') || $horf=='F') && !empty($content)) {	// mPDF 3.0

	$table = &$this->table[1][1];
	// Advance down page by half width of top border
	// mPDF 4.0
	if ($horf=='H') { // Only if header
		if ($table['borders_separate']) { $adv = $table['border_spacing_V']/2 + $table['border_details']['T']['w'] + $table['padding']['T'];  }
		else { $adv = $table['max_cell_border_width']['T'] /2 ; }
		if ($adv) { 
		   if ($this->table_rotate) {
			$this->y += ($adv);
		   }
		   else {
			$this->DivLn($adv,$this->blklvl,true); 
		   }
		}
	}

   // mPDF 4.0
   if ($horf=='F') { // Table Footer
	$firstrow = count($table['cells']) - $this->tablefooternrows;
	$lastrow = count($table['cells']) - 1;
   }
   else { 	// Table Header
	$firstrow = 0;
	$lastrow = $this->tableheadernrows - 1;
   }

   $topy = $content[$firstrow][0]['y']-$this->y;

   // mPDF 4.0
   for ($i=$firstrow ; $i<=$lastrow; $i++) {

    $y = $this->y;


    $colctr = 0;
    foreach($content[$i] as $tablehf)
    {
	$colctr++;
	$y = $tablehf['y'] - $topy;

      $this->y = $y;
      //Set some cell values
      $x = $tablehf['x'];
	if (($this->mirrorMargins) && ($tablestartpage == 'ODD') && (($this->page)%2==0)) {	// EVEN
		$x = $x +$this->MarginCorrection;
	}
	else if (($this->mirrorMargins) && ($tablestartpage == 'EVEN') && (($this->page)%2==1)) {	// ODD
		$x = $x +$this->MarginCorrection;
	}

      $w = $tablehf['w'];
      $h = $tablehf['h'];
      $va = $tablehf['va'];
      $R = $tablehf['R'];
      $mih = $tablehf['mih'];
      $fill = $tablehf['bgcolor'];
      $border = $tablehf['border'];
      $border_details = $tablehf['border_details'];
      $padding = $tablehf['padding'];
	$this->tabletheadjustfinished = true;

      $textbuffer = $tablehf['textbuffer'];

      $align = $tablehf['a'];
      //Align
      $this->divalign=$align;
	$this->x = $x;

	if ($table['borders_separate']) { 
		 $tablefill = isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
  					: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0);
		 if ($tablefill) {
  				$color = $this->ConvertColor($tablefill);
  				if ($color) $this->SetFillColor($color['R'],$color['G'],$color['B']);
				$xadj = ($table['border_spacing_H']/2);
				$yadj = ($table['border_spacing_V']/2);
				$wadj = $table['border_spacing_H'];
				$hadj = $table['border_spacing_V'];
				// mPDF 4.0
 			   	if ($i == $firstrow && $horf=='H') {		// Top
					$yadj += $table['padding']['T'];
					$hadj += $table['padding']['T'];
			   	}
			   	if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i+$tablehf['rowspan']) == ($lastrow+1))  || (!isset($tablehf['rowspan']) && ($i+1) == ($lastrow+1))) && $horf=='F') {	// Bottom
					$hadj += $table['padding']['B'];
			   	}
			   	if ($colctr == 1) {		// Left
					$xadj += $table['padding']['L'];
					$wadj += $table['padding']['L'];
			   	}
			   	if ($colctr == count($content[$i]) ) {	// Right
					$wadj += $table['padding']['R'];
			   	}
				$this->Rect($x - $xadj, $y - $yadj, $w + $wadj, $h + $hadj, 'F');
		 }
	}

	// TABLE BORDER - if separate
 	if ($table['borders_separate'] && $table['border']) { 
			$halfspaceL = $table['padding']['L'] + ($table['border_spacing_H']/2);
			$halfspaceR = $table['padding']['R'] + ($table['border_spacing_H']/2);
			$halfspaceT = $table['padding']['T'] + ($table['border_spacing_V']/2);
			$halfspaceB = $table['padding']['B'] + ($table['border_spacing_V']/2);
			$tbx = $x;
			$tby = $y;
			$tbw = $w;
			$tbh = $h;
			$tab_bord = 0;
			// mPDF 4.0
			$corner = '';
 			if ($i == $firstrow && $horf=='H') {		// Top
				$tby -= $halfspaceT + ($table['border_details']['T']['w']/2);
				$tbh += $halfspaceT + ($table['border_details']['T']['w']/2);
				$this->setBorder($tab_bord , _BORDER_TOP); 
				$corner .= 'T';
			}
			   	if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i+$tablehf['rowspan']) == ($lastrow+1))  || (!isset($tablehf['rowspan']) && ($i+1) == ($lastrow+1))) && $horf=='F') {	// Bottom
				$tbh += $halfspaceB + ($table['border_details']['B']['w']/2);
				$this->setBorder($tab_bord , _BORDER_BOTTOM); 
				$corner .= 'B';
			}
			if ($colctr == 1) {	// Top Left
				$tbx -= $halfspaceL + ($table['border_details']['L']['w']/2);
				$tbw += $halfspaceL + ($table['border_details']['L']['w']/2);
				$this->setBorder($tab_bord , _BORDER_LEFT); 
				$corner .= 'L';
			}
			else if ($colctr == count($content[$i])) {	// Right
				$tbw += $halfspaceR + ($table['border_details']['R']['w']/2);
				$this->setBorder($tab_bord , _BORDER_RIGHT); 
				$corner .= 'R';
			}
			// mPDF 3.0
			$this->_tableRect($tbx, $tby, $tbw, $tbh, $tab_bord , $table['border_details'], false, $table['borders_separate'], 'table', $corner, $table['border_spacing_V'], $table['border_spacing_H'] );
	}

	if ($table['empty_cells']!='hide' || !empty($textbuffer) || !$table['borders_separate']) { $paintcell = true; }
	else { $paintcell = false; } 

	//Vertical align
	if ($R && INTVAL($R) > 0 && isset($va) && $va!='B') { $va='B';}

	if (!isset($va) || empty($va) || $va=='M') $this->y += ($h-$mih)/2;
      elseif (isset($va) && $va=='B') $this->y += $h-$mih;
	if ($fill && $paintcell)
      {
 		$color = $this->ConvertColor($fill);
 		if ($color) $this->SetFillColor($color['R'],$color['G'],$color['B']);
 		if ($table['borders_separate']) { 
 			$this->Rect($x+ ($table['border_spacing_H']/2), $y+ ($table['border_spacing_V']/2), $w- $table['border_spacing_H'], $h- $table['border_spacing_V'], 'F');
		}
 		else { 
	 		$this->Rect($x, $y, $w, $h, 'F');
		}
	}



   	//Border
 	if ($table['borders_separate'] && $paintcell) { 
 		$this->_tableRect($x+ ($table['border_spacing_H']/2)+($border_details['L']['w'] /2), $y+ ($table['border_spacing_V']/2)+($border_details['T']['w'] /2), $w-$table['border_spacing_H']-($border_details['L']['w'] /2)-($border_details['R']['w'] /2), $h- $table['border_spacing_V']-($border_details['T']['w'] /2)-($border_details['B']['w']/2), $border, $border_details, false, $table['borders_separate']);
	}
 	else if ($paintcell) { 
		$this->_tableRect($x, $y, $w, $h, $border, $border_details, true, $table['borders_separate']);  	// true causes buffer
	}

 	//Print cell content
      $this->divheight = $this->table_lineheight*$this->lineheight;
      if (!empty($textbuffer)) {

		if ($R) {
					$cellPtSize = $textbuffer[0][11] / $this->shrin_k;
					$cellFontHeight = ($cellPtSize/$this->k);
					$opx = $this->x;
					$opy = $this->y;
					$angle = INTVAL($R);
					// Only allow 45 - 90 degrees (when bottom-aligned) or -90
					if ($angle > 90) { $angle = 90; }
					else if ($angle > 0 && (isset($va) && $va!='B')) { $angle = 90; }
					else if ($angle > 0 && $angle <45) { $angle = 45; }
					else if ($angle < 0) { $angle = -90; }
					$offset = ((sin(deg2rad($angle))) * 0.37 * $cellFontHeight);
					if (isset($align) && $align =='R') { 
						$this->x += ($w) + ($offset) - ($cellFontHeight/3) - ($padding['R'] + $border_details['R']['w']); 
					}
					else if (!isset($align ) || $align =='C') { 
						$this->x += ($w/2) + ($offset); 
					}
					else { 
						$this->x += ($offset) + ($cellFontHeight/3)+($padding['L'] + $border_details['L']['w']); 
					}
					$str = ltrim(implode(' ',$tablehf['text']));
					$str = $this->mb_rtrim($str ,$this->mb_enc);

					if (!isset($va) || $va=='M') { 
						$this->y -= ($h-$mih)/2; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += (($h-$mih)/2)+($padding['T'] + $border_details['T']['w']) + ($mih-($padding['T'] + $border_details['T']['w']+$border_details['B']['w']+$padding['B'])); }
						else if ($angle < 0) { $this->y += (($h-$mih)/2)+($padding['T'] + $border_details['T']['w']); }
					}
					else if (isset($va) && $va=='B') { 
						$this->y -= $h-$mih; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += $h-($border_details['B']['w']+$padding['B']); }
						else if ($angle < 0) { $this->y += $h-$mih+($padding['T'] + $border_details['T']['w']); }
					}
					else if (isset($va) && $va=='T') { 
						if ($angle > 0) { $this->y += $mih-($border_details['B']['w']+$padding['B']); }
						else if ($angle < 0) { $this->y += ($padding['T'] + $border_details['T']['w']); }
					}

					$this->Rotate($angle,$this->x,$this->y);
					$s_fs = $this->FontSizePt;
					$s_f = $this->FontFamily;	// mPDF 3.0
					$s_st = $this->FontStyle;	// mPDF 3.0
					$this->SetFont($textbuffer[0][4],$textbuffer[0][2],$cellPtSize,true,true);
					$this->Text($this->x,$this->y,$str);
					$this->Rotate(0);
					$this->SetFont($s_f,$s_st,$s_fs,true,true);
					$this->x = $opx;
					$this->y = $opy;
		}
		else {
			if ($table['borders_separate']) {	// NB twice border width
				$xadj = $border_details['L']['w'] + $padding['L'] +($table['border_spacing_H']/2);
				$wadj = $border_details['L']['w'] + $border_details['R']['w'] + $padding['L'] +$padding['R'] + $table['border_spacing_H'];
				$yadj = $border_details['T']['w'] + $padding['T'] + ($table['border_spacing_H']/2);
			}
			else {
				$xadj = $border_details['L']['w']/2 + $padding['L'];
				$wadj = ($border_details['L']['w'] + $border_details['R']['w'])/2 + $padding['L'] + $padding['R'];
				$yadj = $border_details['T']['w']/2 + $padding['T'];
			}

			$this->divwidth=$w-($wadj);
			$this->x += $xadj;
			$this->y += $yadj;
			$this->printbuffer($textbuffer,'',true);
		}

	}
      $textbuffer = array();
     }
     $this->y = $y + $h; //Update y coordinate
   }

  }//end of 'if usetableheader ...'
}


function DefHeaderByName($name,$arr) {
	if (!$name) { $name = '_default'; }
	$this->pageheaders[$name] = $arr;
}

function DefFooterByName($name,$arr) {
	if (!$name) { $name = '_default'; }
	$this->pagefooters[$name] = $arr;
}

function SetHeaderByName($name,$side='O',$write=false) {
	if (!$name) { $name = '_default'; }
	if ($side=='E') { $this->headerDetails['even'] = $this->pageheaders[$name]; }
	else { $this->headerDetails['odd'] = $this->pageheaders[$name]; }
	if ($write) { $this->Header(); }
}

function SetFooterByName($name,$side='O') {
	if (!$name) { $name = '_default'; }
	if ($side=='E') { $this->footerDetails['even'] = $this->pagefooters[$name]; }
	else { $this->footerDetails['odd'] = $this->pagefooters[$name]; }
}



function SetHeader($Harray=array(),$side='',$write=false) {
  if (is_string($Harray)) {
    if (strlen($Harray)==0) {
	if ($side=='O') { $this->headerDetails['odd'] = array(); }
	else if ($side=='E') { $this->headerDetails['even'] = array(); }
	else { $this->headerDetails = array(); }
   }
   else if (strpos($Harray,'|') || strpos($Harray,'|')===0) {
	$hdet = explode('|',$Harray);
	$this->headerDetails = array (
  		'odd' => array (
	'L' => array ('content' => $hdet[0], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'C' => array ('content' => $hdet[1], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'R' => array ('content' => $hdet[2], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
  		),
  		'even' => array (
	'R' => array ('content' => $hdet[0], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'C' => array ('content' => $hdet[1], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'L' => array ('content' => $hdet[2], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
		)
	);
    }
    else {
	$this->headerDetails = array (
  		'odd' => array (
	'R' => array ('content' => $Harray, 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
  		),
  		'even' => array (
	'L' => array ('content' => $Harray, 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
		)
	);
    }
  }
  else if (is_array($Harray)) {
	if ($side=='O') { $this->headerDetails['odd'] = $Harray; }
	else if ($side=='E') { $this->headerDetails['even'] = $Harray; }
	else { $this->headerDetails = $Harray; }
  }

  if ($write) { 
	$save_y = $this->y;
	$this->Header();
	$this->SetY($save_y) ; 
  }
}

function SetFooter($Farray=array(),$side='') {
  if (is_string($Farray)) {
    if (strlen($Farray)==0) {
	if ($side=='O') { $this->footerDetails['odd'] = array(); }
	else if ($side=='E') { $this->footerDetails['even'] = array(); }
	else { $this->footerDetails = array(); }
    }
    else if (strpos($Farray,'|') || strpos($Farray,'|')===0) {
	$fdet = explode('|',$Farray);
	$this->footerDetails = array (
		'odd' => array (
	'L' => array ('content' => $fdet[0], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'C' => array ('content' => $fdet[1], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'R' => array ('content' => $fdet[2], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		),
		'even' => array (
	'R' => array ('content' => $fdet[0], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'C' => array ('content' => $fdet[1], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'L' => array ('content' => $fdet[2], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		)
	);
    }
    else {
	$this->footerDetails = array (
		'odd' => array (
	'R' => array ('content' => $Farray, 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		),
		'even' => array (
	'L' => array ('content' => $Farray, 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		)
	);
    }
  }
  else if (is_array($Farray)) {
	if ($side=='O') { $this->footerDetails['odd'] = $Farray; }
	else if ($side=='E') { $this->footerDetails['even'] = $Farray; }
	else { $this->footerDetails = $Farray; }
  }
}



//Page footer
function Footer() {
  // PAGED MEDIA - CROP / CROSS MARKS from @PAGE
  if ($this->show_marks == 'CROP') {
	// Show TICK MARKS
	$this->SetLineWidth(0.1);	// = 0.1 mm
	$this->SetDrawColor(0);
	$l = 18;	// Default length in mm of crop line
	$m = 8;	// Distance of crop mark from margin in mm
	$b = 8;	// Non-printable border at edge of paper sheet in mm
	$ax1 = $b;
	$bx = $this->page_box['outer_width_LR'] - $m;
	$ax = max($ax1, $bx-$l);
	$cx1 = $this->w - $b;
	$dx = $this->w - $this->page_box['outer_width_LR'] + $m;
	$cx = min($cx1, $dx+$l);
	$ay1 = $b;
	$by = $this->page_box['outer_width_TB'] - $m;
	$ay = max($ay1, $by-$l);
	$cy1 = $this->h - $b;
	$dy = $this->h - $this->page_box['outer_width_TB'] + $m;
	$cy = min($cy1, $dy+$l);

	$this->Line($ax, $this->page_box['outer_width_TB'], $bx, $this->page_box['outer_width_TB']);
	$this->Line($cx, $this->page_box['outer_width_TB'], $dx, $this->page_box['outer_width_TB']);
	$this->Line($ax, $this->h - $this->page_box['outer_width_TB'], $bx, $this->h - $this->page_box['outer_width_TB']);
	$this->Line($cx, $this->h - $this->page_box['outer_width_TB'], $dx, $this->h - $this->page_box['outer_width_TB']);
	$this->Line($this->page_box['outer_width_LR'], $ay, $this->page_box['outer_width_LR'], $by);
	$this->Line($this->page_box['outer_width_LR'], $cy, $this->page_box['outer_width_LR'], $dy);
	$this->Line($this->w - $this->page_box['outer_width_LR'], $ay, $this->w - $this->page_box['outer_width_LR'], $by);
	$this->Line($this->w - $this->page_box['outer_width_LR'], $cy, $this->w - $this->page_box['outer_width_LR'], $dy);
  }
  if ($this->show_marks == 'CROSS') {
	$this->SetLineWidth(0.1);	// = 0.1 mm
	$this->SetDrawColor(0);
	$l = 14 /2;	// longer length of the cross line (half)
	$w = 6 /2;	// shorter width of the cross line (half)
	$r = 1.2;	// radius of circle
	$m = 10;	// Distance of cross mark from margin in mm
	$x1 = $this->page_box['outer_width_LR'] - $m;
	$x2 = $this->w - $this->page_box['outer_width_LR'] + $m;
	$y1 = $this->page_box['outer_width_TB'] - $m;
	$y2 = $this->h - $this->page_box['outer_width_TB'] + $m;
	// Left
	$this->Circle($x1, $this->h/2, $r, 'S') ;
	$this->Line($x1-$w, $this->h/2, $x1+$w, $this->h/2);
	$this->Line($x1, $this->h/2-$l, $x1, $this->h/2+$l);
	// Right
	$this->Circle($x2, $this->h/2, $r, 'S') ;
	$this->Line($x2-$w, $this->h/2, $x2+$w, $this->h/2);
	$this->Line($x2, $this->h/2-$l, $x2, $this->h/2+$l);
	// Top
	$this->Circle($this->w/2, $y1, $r, 'S') ;
	$this->Line($this->w/2, $y1-$w, $this->w/2, $y1+$w);
	$this->Line($this->w/2-$l, $y1, $this->w/2+$l, $y1);
	// Bottom
	$this->Circle($this->w/2, $y2, $r, 'S') ;
	$this->Line($this->w/2, $y2-$w, $this->w/2, $y2+$w);
	$this->Line($this->w/2-$l, $y2, $this->w/2+$l, $y2);
  }


	// If @page set non-HTML headers/footers named, they were not read until later in the HTML code - so now set them
	if ($this->page==1) {
		if ($this->firstPageBoxHeader) {
			$this->headerDetails['odd'] = $this->pageheaders[$this->firstPageBoxHeader]; 
  			$this->Header();
		}
		if ($this->firstPageBoxHeaderEven) {
			$this->headerDetails['even'] = $this->pageheaders[$this->firstPageBoxHeaderEven];
		}
		if ($this->firstPageBoxFooter) {
			$this->footerDetails['odd'] = $this->pagefooters[$this->firstPageBoxFooter];
		}
		if ($this->firstPageBoxFooterEven) {
			$this->footerDetails['even'] = $this->pagefooters[$this->firstPageBoxFooterEven]; 
		}
		$this->firstPageBoxHeader='';
		$this->firstPageBoxHeaderEven='';
		$this->firstPageBoxFooter='';
		$this->firstPageBoxFooterEven='';
	}




  $this->processingHeader=true;
  $this->ResetMargins();	// necessary after columns
  $this->pgwidth = $this->w - $this->lMargin - $this->rMargin;

  $h = $this->footerDetails;
  if(count($h)) {

	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out(sprintf('q 0 -1 1 0 0 %.3f cm ',($this->h*$this->k)));
		$headerpgwidth = $this->h - $this->orig_lMargin - $this->orig_rMargin;
		if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$headerlmargin = $this->orig_rMargin;
		}
		else {
			$headerlmargin = $this->orig_lMargin;
		}
	}
	else { 
		$yadj = 0; 
		$headerpgwidth = $this->pgwidth;
		$headerlmargin = $this->lMargin;
	}
	$this->SetY(-$this->margin_footer);

	$this->SetTextColor(0);
    	$this->SUP = false;
	$this->SUB = false;
	$this->bullet = false;

	// only show pagenumber if numbering on
	// mPDF 3.0 Add PageNum prefix/suffix
	$pgno = $this->docPageNum($this->page, true); 

	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$side = 'even';
	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS = DEFAULT
			$side = 'odd';
	}
	$maxfontheight = 0;
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
	  }
	}
	// LEFT-CENTER-RIGHT
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		$hd = str_replace('{PAGENO}',$pgno,$h[$side][$pos]['content']);
		// mPDF 3.0
		$hd = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->aliasNbPgGp.$this->nbpgSuffix,$hd);	// {nbpg}
		$hd = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$hd);
		if (isset($h[$side][$pos]['font-family']) && $h[$side][$pos]['font-family']) { $hff = $h[$side][$pos]['font-family']; }
		// mPDF 3.0 original_ in case pagebreak in middle of table
		else { $hff = $this->original_default_font; }
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		// mPDF 3.0 original_ in case pagebreak in middle of table
		else { $hfsz = $this->original_default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
		if (isset($h[$side][$pos]['font-style']) && $h[$side][$pos]['font-style']) { $hfst = $h[$side][$pos]['font-style']; }
		else { $hfst = ''; }
		if (isset($h[$side][$pos]['color']) && $h[$side][$pos]['color']) { 
			$hfcol = $h[$side][$pos]['color']; 
			$cor = $this->ConvertColor($hfcol);
			if ($cor) { $this->SetTextColor($cor['R'],$cor['G'],$cor['B']); }
		}
		else { $hfcol = ''; }
		// mPDF 3.0 Force output
		$this->SetFont($hff,$hfst,$hfsz,true,true);
		$this->x = $headerlmargin ;
		// mPDF 4.0
//		$this->y = $this->h - $this->margin_footer - ($maxfontheight/$this->k * 0.5) - 0.5;
		$this->y = $this->h - $this->margin_footer - ($maxfontheight/$this->k);
		$hd = $this->purify_utf8_text($hd);
		if ($this->text_input_as_HTML) {
			$hd = $this->all_entities_to_utf8($hd);
		}
		// CONVERT CODEPAGE
		if (!$this->is_MB) { $hd = mb_convert_encoding($hd,$this->mb_enc,'UTF-8'); }
		// DIRECTIONALITY RTL
		// mPDF 4.0 Font-specific ligature substitution for Indic fonts
		$align = $pos;
		if ($this->directionality == 'rtl') { 
			if ($pos == 'L') { $align = 'R'; }
			else if ($pos == 'R') { $align = 'L'; }
		}

		if ($pos!='L' && (stripos($hd,$this->aliasNbPg)!==false || stripos($hd,$this->aliasNbPgGp)!==false)) { 
			if (stripos($hd,$this->aliasNbPgGp)!==false) { $type= 'nbpggp'; } else { $type= 'nbpg'; }
			$this->_out('{mpdfheader'.$type.' '.$pos.' ff='.$hff.' fs='.$hfst.' fz='.$hfsz.'}'); 
			// mPDF 4.0
			$this->Cell($headerpgwidth ,$maxfontheight/$this->k ,$hd,0,0,$align,0,'',0,0,0,'M');
			$this->_out('Q');
		}
		else { 
			// mPDF 4.0
			$this->Cell($headerpgwidth ,$maxfontheight/$this->k ,$hd,0,0,$align,0,'',0,0,0,'M');
		}
		if ($hfcol) { $this->SetTextColor(0); }
	  }
	}
	// Return Font to normal
	$this->SetFont($this->default_font,'',$this->original_default_font_size);

	// LINE
	// mPDF 4.0
	if (isset($h[$side]['line']) && $h[$side]['line']) { 
		$this->SetLineWidth(0.1);
		$this->SetDrawColor(0);
		// mPDF 4.0
		$this->Line($headerlmargin , $this->y-($maxfontheight*($this->footer_line_spacing)/$this->k), $headerlmargin +$headerpgwidth, $this->y-($maxfontheight*($this->footer_line_spacing)/$this->k));
	}
	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out('Q');
	}
  }
  $this->processingHeader=false;

}




///////////////////
/// HTML parser ///
///////////////////
function WriteHTML($html,$sub=0,$init=true,$close=true) {
				// $sub ADDED - 0 = default; 1=headerCSS only; 2=HTML body (parts) only; 3 - HTML parses only
				// 4 - writes HTML headers
				// $close Leaves buffers etc. in current state, so that it can continue a block etc.
				// $init - Clears and sets buffers to Top level block etc.

	if ($init) {
		$this->headerbuffer='';
		$this->textbuffer = array();
		// mPDF 4.0 Fixed Pos block
		$this->fixedPosBlockSave = array();
	}

	if ($sub == 1) { $html = '<style> '.$html.' </style>'; }	// stylesheet only

	if ($this->allow_charset_conversion) {
		if ($sub < 1) { 
			$this->ReadCharset($html); 
		}
		if ($this->charset_in) { 
			$success = iconv($this->charset_in,'UTF-8//TRANSLIT',$html); 
			if ($success) { $html = $success; }
		}
	}

	$html = $this->purify_utf8($html,false);
	if ($init) {
		$this->blklvl = 0;
		$this->lastblocklevelchange = 0;
		$this->blk = array();
		// mPDF 4.0
		$this->initialiseBlock($this->blk[0]);
		$this->blk[0]['width'] =& $this->pgwidth;
		$this->blk[0]['inner_width'] =& $this->pgwidth;
		// mPDF 3.0
		$this->blk[0]['blockContext'] = $this->blockContext;
	}

	if ($sub < 2) { 
		$this->ReadMetaTags($html); 

		// default stylesheet now in mPDF.css - read on initialising class
		$html = $this->ReadCSS($html); 
		// SET Blocklevel[0] CSS if defined in <body> or from default
		$properties = $this->MergeCSS('BLOCK','BODY','');

		if ($sub == 1) { $this->setCSS($properties,'','BODY'); return ''; }


		// allow in-line CSS for body tag to be parsed // Get <body> tag inline CSS
		if (preg_match('/<body([^>]*)>(.*?)<\/body>/ism',$html,$m) || preg_match('/<body([^>]*)>(.*)$/ism',$html,$m)) { 
			$html = $m[2]; 
			// mPDF 3.0 - Tiling Patterns
			// Changed to allow style="background: url('bg.jpg')"
			if (preg_match('/style=[\"](.*?)[\"]/ism',$m[1],$mm) || preg_match('/style=[\'](.*?)[\']/ism',$m[1],$mm)) { 
				$zproperties = $this->readInlineCSS($mm[1]); 
				$properties = $this->array_merge_recursive_unique($properties,$zproperties); 
			}
			if (isset($html_lang) && $html_lang) { $properties['LANG'] = $html_lang; }
		}
		$this->setCSS($properties,'','BODY'); 
		if (!isset($this->CSS['BODY'])) { $this->CSS['BODY'] = array(); }

		// mPDF 4.0 Not required(?) and messing up text-align for RTL
//		$this->CSS['BODY'] = $this->array_merge_recursive_unique($this->CSS['BODY'], $properties); 

		$pm = $this->GetPagedMediaCSS('');
		$this->page_box['changed'] = false;

		$this->page_box['last_name'] = $this->page_box['name'] = '';
		// If page-box is set
		if ($this->state==0 && $pm['ISSET']) {
			$this->page_box['CSS'] = $pm;
			// mPDF 3.0
			list($pborientation,$pbmgl,$pbmgr,$pbmgt,$pbmgb,$pbmgh,$pbmgf,$pbmgtfp,$pbmgbfp,$ohname,$ehname,$ofname,$efname,$bg) = $this->SetPagedMediaCSS();
			$this->CurOrientation = $this->DefOrientation = $pborientation; 
			$this->DeflMargin = $pbmgl; 
			$this->DefrMargin = $pbmgr; 
			$this->tMargin = $pbmgt;
			$this->bMargin = $pbmgb;
			$this->margin_header = $pbmgh; 
			$this->margin_footer = $pbmgf; 

			if ($ohname && !preg_match('/^html_(.*)$/i',$ohname)) $this->firstPageBoxHeader = $ohname;
			if ($ehname && !preg_match('/^html_(.*)$/i',$ehname)) $this->firstPageBoxHeaderEven = $ehname;
			if ($ofname && !preg_match('/^html_(.*)$/i',$ofname)) $this->firstPageBoxFooter = $ofname;
			if ($efname && !preg_match('/^html_(.*)$/i',$efname)) $this->firstPageBoxFooterEven = $efname;

			$this->SetMargins($this->DeflMargin,$this->DefrMargin,$this->tMargin);	// sets l r t margin
			$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);	// sets $this->bMargin & PageBreakTrigger
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin ;
			$this->x = $this->lMargin;
			$this->y = $this->tMargin;

			// mPDF 3.0 - Background color
			if (isset($bg['BACKGROUND-COLOR'])) {
				$cor = $this->ConvertColor($bg['BACKGROUND-COLOR']);
				if ($cor) { 
					$this->bodyBackgroundColor = $cor; 
					$this->bodyBackgroundImage = false; 
					$this->bodyBackgroundGradient = false; 
				}
			}


			$this->page_box['changed'] = true;
		}

	}

	if($this->state==0 && $sub!=1 && $sub!=3 && $sub!=4) {
		$this->AddPage($this->CurOrientation);
	}

	$parseonly = false; 
	$this->bufferoutput = false; 
	if ($sub == 3) { 
		$parseonly = true; 
		// Close any open block tags
		for ($b= $this->blklvl;$b>0;$b--) { $this->CloseTag($this->blk[$b]['tag']); }
		// Output any text left in buffer
		if (count($this->textbuffer)) { $this->printbuffer($this->textbuffer); }
		$this->textbuffer=array();
	} 
	else if ($sub == 4) { 
		// Close any open block tags
		for ($b= $this->blklvl;$b>0;$b--) { $this->CloseTag($this->blk[$b]['tag']); }
		// Output any text left in buffer
		if (count($this->textbuffer)) { $this->printbuffer($this->textbuffer); }
		$this->bufferoutput = true; 
		$this->textbuffer=array();
		$this->headerbuffer='';
		$properties = $this->MergeCSS('BLOCK','BODY','');
		$this->setCSS($properties,'','BODY'); 
	} 

	mb_internal_encoding('UTF-8'); 

	$html = $this->AdjustHTML($html,$this->directionality,$this->usepre, $this->tabSpaces); //Try to make HTML look more like XHTML

	if ($this->autoFontGroups) { $html = $this->AutoFont($html); }
	$html = preg_replace('/<htmlpageheader.*?<\/htmlpageheader>/si','',$html);
	$html = preg_replace('/<htmlpagefooter.*?<\/htmlpagefooter>/si','',$html);


	$html=str_replace('<?','< ',$html); //Fix '<?XML' bug from HTML code generated by MS Word
	$html = $this->SubstituteChars($html);

	// mPDF 4.0

	// Don't allow non-breaking spaces that are converted to substituted chars or will break anyway and mess up table width calc.
	$html = str_replace('<tta>160</tta>',$this->chrs[32],$html); 
	$html = str_replace('</tta><tta>','|',$html); 
	$html = str_replace('</tts><tts>','|',$html); 
	$html = str_replace('</ttz><ttz>','|',$html); 

	//Add new supported tags in the DisableTags function
	$html=strip_tags($html,$this->enabledtags); //remove all unsupported tags, but the ones inside the 'enabledtags' string

	//Explode the string in order to parse the HTML code
	$a=preg_split('/<(.*?)>/ms',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	// ? more accurate regexp that allows e.g. <a name="Silly <name>">
	// if changing - also change in fn.SubstituteChars()
	// $a = preg_split ('/<((?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+)>/ms', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

	if ($this->mb_enc) { 
		mb_internal_encoding($this->mb_enc); 
	}

	foreach($a as $i => $e) {

		if($i%2==0) {
		//TEXT
			if ($this->blk[$this->blklvl]['hide']) { continue; }

			// mPDF 4.0
			if ($this->inFixedPosBlock) { $this->fixedPosBlock .= $e; continue; }	// *CSS-POSITION*
			if (strlen($e) == 0) { continue; }

			$e = strcode2utf($e);	
			$e = $this->lesser_entity_decode($e);
				// mPDF 4.0 Removed rtlAsArabicFarsi

			// mPDF 4.0 Font-specific ligature substitution for Indic fonts

			// CONVERT CODEPAGE
			if (!$this->is_MB) { $e = mb_convert_encoding($e,$this->mb_enc,'UTF-8'); }
			if (!$this->isCJK) {
				if ($this->toupper) { $e = strtoupper($e); }
				if ($this->tolower) { $e = strtolower($e); }
			}
			if (($this->tts) || ($this->ttz) || ($this->tta)) {
				$es = explode('|',$e);
				$e = '';
				foreach($es AS $val) {
					$e .= $this->chrs[$val];
				}
			}
			//Adjust lineheight
      		//$this->SetLineHeight($this->FontSizePt); //should be inside printbuffer? // does nothing


			// TABLE
			else if ($this->tableLevel) {
				if ($this->tdbegin) {
     				   if (($this->ignorefollowingspaces) and !$this->ispre) { $e = ltrim($e); }
				   if ($e || $e==='0') {
					// mPDF 3.0
				      if (($this->blockjustfinished || $this->listjustfinished) && $this->cell[$this->row][$this->col]['s']>0) {
	  					$this->cell[$this->row][$this->col]['textbuffer'][] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
  						$this->cell[$this->row][$this->col]['text'][] = "\n";
						if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
							$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
						}
						elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
							$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];  
						}
						$this->cell[$this->row][$this->col]['s'] = 0;// reset
				      }
					// mPDF 3.0
					$this->blockjustfinished=false;
					$this->listjustfinished=false;

	  				$this->cell[$this->row][$this->col]['textbuffer'][] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
  					$this->cell[$this->row][$this->col]['text'][] = $e;
            			if (!isset($this->cell[$this->row][$this->col]['R']) || !$this->cell[$this->row][$this->col]['R']) {
						if (isset($this->cell[$this->row][$this->col]['s'])) { 
							$this->cell[$this->row][$this->col]['s'] += $this->GetStringWidth($e); 
						}
						else { $this->cell[$this->row][$this->col]['s'] = $this->GetStringWidth($e); }
					}
					if ($this->tableLevel==1 && $this->useGraphs) { 
						$this->graphs[$this->currentGraphId]['data'][$this->row][$this->col] = $e;
					}
					$this->nestedtablejustfinished = false;
					// mPDF 3.0
					$this->linebreakjustfinished=false;
				   }
				}
			}
			// ALL ELSE
			else {
     				if ($this->ignorefollowingspaces and !$this->ispre) { $e = ltrim($e); }
				if ($e || $e==='0') $this->textbuffer[] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize); 
			}
		}


		else { // TAG **
		   // mPDF 4.0
		   if(substr($e,0,1)=='/') { // END TAG
		    // Check for tags where HTML specifies optional end tags,
    		    // and/or does not allow nesting e.g. P inside P, or 
		    $endtag = strtoupper(substr($e,1));
			// mPDF 4.0
		    if($this->blk[$this->blklvl]['hide']) { 
			if (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags)) { 
				unset($this->blk[$this->blklvl]);
				$this->blklvl--; 
			}
			continue; 
		    }

		    if ($this->inFixedPosBlock) { 
			if (in_array($endtag, $this->outerblocktags) || in_array($endtag, $this->innerblocktags)) { $this->fixedPosBlockDepth--; }
			if ($this->fixedPosBlockDepth == 0) { 
				$this->fixedPosBlockSave[] = array($this->fixedPosBlock, $this->fixedPosBlockBBox, $this->page);
				$this->fixedPosBlock = '';
				$this->inFixedPosBlock = false;
				continue; 
			}
			$this->fixedPosBlock .= '<'.$e.'>'; 
			continue; 
		    }
		    if ($this->allow_html_optional_endtags && !$parseonly) {
			if (($endtag == 'DIV' || $endtag =='FORM' || $endtag =='CENTER') && $this->lastoptionaltag == 'P') { $this->CloseTag($this->lastoptionaltag ); }
			if ($this->lastoptionaltag == 'LI' && $endtag == 'OL') { $this->CloseTag($this->lastoptionaltag ); }
			if ($this->lastoptionaltag == 'LI' && $endtag == 'UL') { $this->CloseTag($this->lastoptionaltag ); }
			if ($this->lastoptionaltag == 'DD' && $endtag == 'DL') { $this->CloseTag($this->lastoptionaltag ); }
			if ($this->lastoptionaltag == 'DT' && $endtag == 'DL') { $this->CloseTag($this->lastoptionaltag ); }
			if ($this->lastoptionaltag == 'OPTION' && $endtag == 'SELECT') { $this->CloseTag($this->lastoptionaltag ); }
			if ($endtag == 'TABLE') {
				if ($this->lastoptionaltag == 'THEAD' || $this->lastoptionaltag == 'TBODY' || $this->lastoptionaltag == 'TFOOT') { 
					$this->CloseTag($this->lastoptionaltag);
				}
				if ($this->lastoptionaltag == 'TR') { $this->CloseTag('TR'); }
				if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') { $this->CloseTag($this->lastoptionaltag ); $this->CloseTag('TR'); }
			}
			if ($endtag == 'THEAD' || $endtag == 'TBODY' || $endtag == 'TFOOT') { 
				if ($this->lastoptionaltag == 'TR') { $this->CloseTag('TR'); }
				if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') { $this->CloseTag($this->lastoptionaltag ); $this->CloseTag('TR'); }
			}
			if ($endtag == 'TR') {
				if ($this->lastoptionaltag == 'TD' || $this->lastoptionaltag == 'TH') { $this->CloseTag($this->lastoptionaltag ); }
			}
		    }
		    $this->CloseTag($endtag); 
		   }

		   else {	// OPENING TAG
			if($this->blk[$this->blklvl]['hide']) { 
				if (strpos($e,' ')) { $te = strtoupper(substr($e,0,strpos($e,' '))); }
				else { $te = strtoupper($e); } 
				if (in_array($te, $this->outerblocktags) || in_array($te, $this->innerblocktags)) { 
					$this->blklvl++; 	
					$this->blk[$this->blklvl]['hide']=true;
				}
				continue; 
			}

			if ($this->inFixedPosBlock) { 
				if (strpos($e,' ')) { $te = strtoupper(substr($e,0,strpos($e,' '))); }
				else { $te = strtoupper($e); } 
				$this->fixedPosBlock .= '<'.$e.'>'; 
				if (in_array($te, $this->outerblocktags) || in_array($te, $this->innerblocktags)) { $this->fixedPosBlockDepth++; }
				continue; 
			}
			$regexp = '|=\'(.*?)\'|s'; // eliminate single quotes, if any
      		$e = preg_replace($regexp,"=\"\$1\"",$e);
			// changes anykey=anyvalue to anykey="anyvalue" (only do this inside tags)
			if (substr($e,0,10)!='pageheader' && substr($e,0,10)!='pagefooter') {
				$regexp = '| (\\w+?)=([^\\s>"]+)|si'; 
	      		$e = preg_replace($regexp," \$1=\"\$2\"",$e);
			}

      		//Fix path values, if needed
			if ((stristr($e,"href=") !== false) or (stristr($e,"src=") !== false) ) {
				$regexp = '/ (href|src)="(.*?)"/i';
				preg_match($regexp,$e,$auxiliararray);
				if (isset($auxiliararray[2])) { $path = $auxiliararray[2]; }
				else { $path = ''; }
				// mPDF 3.0
				if (trim($path) != '') { 
					$this->GetFullPath($path); // mPDF 4.0
					$regexp = '/ (href|src)="(.*?)"/i';
					$e = preg_replace($regexp,' \\1="'.$path.'"',$e);
				}
			}//END of Fix path values


			//Extract attributes
			$contents=array();
			// mPDF 3.0 - Tiling Patterns
			// Changed to allow style="background: url('bg.jpg')"
			preg_match_all('/\\S*=["][^"]*["]/',$e,$contents1);
			preg_match_all('/\\S*=[\'][^\']*[\']/',$e,$contents2);
			$contents = array_merge($contents1, $contents2);
			preg_match('/\\S+/',$e,$a2);
			$tag=strtoupper($a2[0]);
			$attr=array();
			if (!empty($contents)) {
				foreach($contents[0] as $v) {
					// mPDF 3.0 - Tiling Patterns
					// Changed to allow style="background: url('bg.jpg')"
 					if(preg_match('/^([^=]*)=["]?([^"]*)["]?$/',$v,$a3) || preg_match('/^([^=]*)=[\']?([^\']*)[\']?$/',$v,$a3)) {
 						if (strtoupper($a3[1])=='ID' || strtoupper($a3[1])=='STYLE' || strtoupper($a3[1])=='CLASS') {
   							$attr[strtoupper($a3[1])]=trim(strtoupper($a3[2]));
						}
						// includes header-style-right etc. used for <pageheader>
 						else if (preg_match('/^(HEADER|FOOTER)-STYLE/i',$a3[1])) {
   							$attr[strtoupper($a3[1])]=trim(strtoupper($a3[2]));
						}
						else {
    							$attr[strtoupper($a3[1])]=trim($a3[2]);
						}
     					}
  				}
			}
			$this->OpenTag($tag,$attr);
			// mPDF 4.0 Fixed Pos block
			if ($this->inFixedPosBlock) { 
				$this->fixedPosBlockBBox = array($tag,$attr, $this->x, $this->y); 
				$this->fixedPosBlock = ''; 
				$this->fixedPosBlockDepth = 1; 
			}
		   }

		} // end TAG
	} //end of	foreach($a as $i=>$e)

	if ($close) {

		// Close any open block tags
		for ($b= $this->blklvl;$b>0;$b--) { $this->CloseTag($this->blk[$b]['tag']); }

		// Output any text left in buffer
		if (count($this->textbuffer) && !$parseonly) { $this->printbuffer($this->textbuffer); }
		if (!$parseonly) $this->textbuffer=array();

		// If ended with a float, need to move to end page
		$currpos = $this->page*1000 + $this->y;
		if (isset($this->blk[$this->blklvl]['float_endpos']) && $this->blk[$this->blklvl]['float_endpos'] > $currpos) {
			$old_page = $this->page;
			$new_page = intval($this->blk[$this->blklvl]['float_endpos'] /1000);
			if ($old_page != $new_page) {
				$s = $this->PrintPageBackgrounds();
				// Writes after the marker so not overwritten later by page background etc.
				$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
				$this->pageBackgrounds = array();
				$this->page = $new_page;
				$this->ResetMargins();
				$this->Reset();
				$this->pageoutput[$this->page] = array();
			}
			$this->y = (($this->blk[$this->blklvl]['float_endpos'] *1000) % 1000000)/1000;	// mod changes operands to integers before processing
		}

		$this->printfloatbuffer();

		//Create Internal Links, if needed
		if (!empty($this->internallink) ) {
			foreach($this->internallink as $k=>$v) {
				if (strpos($k,"#") !== false ) { continue; } //ignore
				$ypos = $v['Y'];
				$pagenum = $v['PAGE'];
				$sharp = "#";
				while (array_key_exists($sharp.$k,$this->internallink)) {
					$internallink = $this->internallink[$sharp.$k];
					$this->SetLink($internallink,$ypos,$pagenum);
					$sharp .= "#";
				}
			}
		}

		$this->linemaxfontsize = '';
		$this->lineheight_correction = $this->default_lineheight_correction;

		$this->bufferoutput = false; 

		// mPDF 4.0 Fixed Pos block
		if (count($this->fixedPosBlockSave) && $sub != 4) {
		  foreach($this->fixedPosBlockSave AS $fpbs) {
			$old_page = $this->page;
			$this->page = $fpbs[2];
			$this->WriteFixedPosHTML($fpbs[0], 0, 0, 100, 100,'auto', $fpbs[1]);  // 0,0,10,10 are overwritten by bbox
			$this->page = $old_page;
		  }
		}

	}
}

// mPDF 4.0
function WriteFixedPosHTML($html='',$x, $y, $w, $h, $overflow='visible', $bounding=array()) {
	// $overflow can be 'hidden', 'visible' or 'auto' - 'auto' causes autofit to size
	// Annotations disabled
	// Links do work
	// Will always go on current page (or start Page 1 if required)
	// Probably INCOMPATIBLE WITH keep with table, columns etc.
	// Called externally or interally via <div style="position: [fixed|absolute]">
	// When used internally, $x $y $w $h and $overflow are all overridden by $bounding

	$overflow = strtolower($overflow);
	if($this->state==0) { 
		$this->AddPage($this->CurOrientation);
	}
	$save_y = $this->y;
	$save_x = $this->x;
	// mPDF 4.1		// Allows Image in fixedposdiv to be full height
	$this->fullImageHeight = $this->h;
	$save_cols = false;
	$this->writingHTMLheader = true;	// a FIX to stop pagebreaks etc.
	$this->writingHTMLfooter = true;
	$this->InFooter = true;	// suppresses autopagebreaks
	$save_bgs = $this->pageBackgrounds;

	if ($w > $this->w) { $x = 0; $w = $this->w; }
	if ($h > $this->h) { $y = 0; $h = $this->h; }
	if ($x > $this->w) { $x = $this->w - $w; }
	if ($y > $this->h) { $y = $this->h - $h; }

	if (!empty($bounding)) {	
		// $cont_ containing block = full physical page (position: absolute) or page inside margins (position: fixed)
		// $bbox_ Bounding box is the <div> which is positioned absolutely/fixed 
		// top/left/right/bottom/width/height/background*/border*/padding*/margin* are taken from bounding
		// font*[family/size/style/weight]/line-height/text*[align/decoration/transform/indent]/color are transferred to $inner
		// as an enclosing <div> (after having checked ID/CLASS)
		// $x, $y, $w, $h are inside of $bbox_ = containing box for $inner_
		// $inner_ InnerHTML is the contents of that block to be output 
		$tag = $bounding[0];
		$attr = $bounding[1];

		$orig_x0 = $bounding[2];
		$orig_y0 = $bounding[3];

		// As in WriteHTML() initialising
		$this->blklvl = 0;
		$this->lastblocklevelchange = 0;
		$this->blk = array();
		$this->initialiseBlock($this->blk[0]);

		$this->blk[0]['width'] =& $this->pgwidth;
		$this->blk[0]['inner_width'] =& $this->pgwidth;

		$this->blk[0]['blockContext'] = $this->blockContext;

		$properties = $this->MergeCSS('BLOCK','BODY','');
		$this->setCSS($properties,'','BODY'); 
		$this->blklvl = 1;
		$this->initialiseBlock($this->blk[1]);
		$this->blk[1]['tag'] = $tag;
		$this->blk[1]['attr'] = $attr;

		$this->Reset();
		$p = $this->MergeCSS('BLOCK',$tag,$attr);
		if (isset($p['OVERFLOW'])) { $overflow = strtolower($p['OVERFLOW']); }
		if (strtolower($p['POSITION']) == 'fixed') {
			$cont_w = $this->pgwidth;	// $this->blk[0]['inner_width'];
			$cont_h = $this->h - $this->tMargin - $this->bMargin;
			$cont_x = $this->lMargin;
			$cont_y = $this->tMargin;
		}
		else {
			$cont_w = $this->w;	// ABSOLUTE;
			$cont_h = $this->h;
			$cont_x = 0;
			$cont_y = 0;
		}

		// Pass on in-line properties to the innerhtml
		$css = '';
		if (isset($p['TEXT-ALIGN'])) { $css .= 'text-align: '.strtolower($p['TEXT-ALIGN']).'; '; }
		if (isset($p['TEXT-TRANSFORM'])) { $css .= 'text-transform: '.strtolower($p['TEXT-TRANSFORM']).'; '; }
		if (isset($p['TEXT-INDENT'])) { $css .= 'text-indent: '.strtolower($p['TEXT-INDENT']).'; '; }
		if (isset($p['TEXT-DECORATION'])) { $css .= 'text-decoration: '.strtolower($p['TEXT-DECORATION']).'; '; }
		if (isset($p['FONT-FAMILY'])) { $css .= 'font-family: '.strtolower($p['FONT-FAMILY']).'; '; }
		if (isset($p['FONT-STYLE'])) { $css .= 'font-style: '.strtolower($p['FONT-STYLE']).'; '; }
		if (isset($p['FONT-WEIGHT'])) { $css .= 'font-weight: '.strtolower($p['FONT-WEIGHT']).'; '; }
		if (isset($p['FONT-SIZE'])) { $css .= 'font-size: '.strtolower($p['FONT-SIZE']).'; '; }
		if (isset($p['LINE-HEIGHT'])) { $css .= 'line-height: '.strtolower($p['LINE-HEIGHT']).'; '; }
		if (isset($p['COLOR'])) { $css .= 'color: '.strtolower($p['COLOR']).'; '; }
		if ($css) {
			$html = '<div style="'.$css.'">'.$html.'</div>';
		}

		// Copy over (only) the properties to set for border and background
		$pb = array();
		$pb['MARGIN-TOP'] = $p['MARGIN-TOP']; 
		$pb['MARGIN-RIGHT'] = $p['MARGIN-RIGHT']; 
		$pb['MARGIN-BOTTOM'] = $p['MARGIN-BOTTOM']; 
		$pb['MARGIN-LEFT'] = $p['MARGIN-LEFT']; 
		$pb['PADDING-TOP'] = $p['PADDING-TOP']; 
		$pb['PADDING-RIGHT'] = $p['PADDING-RIGHT']; 
		$pb['PADDING-BOTTOM'] = $p['PADDING-BOTTOM']; 
		$pb['PADDING-LEFT'] = $p['PADDING-LEFT']; 
		$pb['BORDER-TOP'] = $p['BORDER-TOP']; 
		$pb['BORDER-RIGHT'] = $p['BORDER-RIGHT']; 
		$pb['BORDER-BOTTOM'] = $p['BORDER-BOTTOM']; 
		$pb['BORDER-LEFT'] = $p['BORDER-LEFT']; 
		if (isset($p['BACKGROUND-COLOR'])) { $pb['BACKGROUND-COLOR'] = $p['BACKGROUND-COLOR']; }

		$this->setCSS($pb,'BLOCK',$tag);
		//================================================================
		$bbox_dir = $p['DIR']; 
		$bbox_br = $this->blk[1]['border_right']['w'];
		$bbox_bl = $this->blk[1]['border_left']['w'];
		$bbox_bt = $this->blk[1]['border_top']['w'];
		$bbox_bb = $this->blk[1]['border_bottom']['w'];
		$bbox_pr = $this->blk[1]['padding_right'];
		$bbox_pl = $this->blk[1]['padding_left'];
		$bbox_pt = $this->blk[1]['padding_top'];
		$bbox_pb = $this->blk[1]['padding_bottom'];
		$bbox_mr = $this->blk[1]['margin_right'];
		if (strtolower($p['MARGIN-RIGHT'])=='auto') { $bbox_mr = 'auto'; }
		$bbox_ml = $this->blk[1]['margin_left'];
		if (strtolower($p['MARGIN-LEFT'])=='auto') { $bbox_ml = 'auto'; }
		$bbox_mt = $this->blk[1]['margin_top'];
		if (strtolower($p['MARGIN-TOP'])=='auto') { $bbox_mt = 'auto'; }
		$bbox_mb = $this->blk[1]['margin_bottom'];
 		if (strtolower($p['MARGIN-BOTTOM'])=='auto') { $bbox_mb = 'auto'; }
		if (isset($p['LEFT']) && strtolower($p['LEFT'])!='auto') { $bbox_left = $this->ConvertSize($p['LEFT'], $cont_w, $this->FontSize,false); }
		else { $bbox_left = 'auto'; }
 		if (isset($p['TOP']) && strtolower($p['TOP'])!='auto') { $bbox_top = $this->ConvertSize($p['TOP'], $cont_h, $this->FontSize,false); }
		else { $bbox_top = 'auto'; }
 		if (isset($p['RIGHT']) && strtolower($p['RIGHT'])!='auto') { $bbox_right = $this->ConvertSize($p['RIGHT'], $cont_w, $this->FontSize,false); }
		else { $bbox_right = 'auto'; }
 		if (isset($p['BOTTOM']) && strtolower($p['BOTTOM'])!='auto') { $bbox_bottom = $this->ConvertSize($p['BOTTOM'], $cont_h, $this->FontSize,false); }
		else { $bbox_bottom = 'auto'; }
 		if (isset($p['WIDTH']) && strtolower($p['WIDTH'])!='auto') { $inner_w = $this->ConvertSize($p['WIDTH'], $cont_w, $this->FontSize,false); }
		else { $inner_w = 'auto'; }
 		if (isset($p['HEIGHT']) && strtolower($p['HEIGHT'])!='auto') { $inner_h = $this->ConvertSize($p['HEIGHT'], $cont_h, $this->FontSize,false); }
		else { $inner_h = 'auto'; }
		//================================================================
		// Algorithm from CSS2.1  See http://www.w3.org/TR/CSS21/visudet.html#abs-non-replaced-height
		if ($bbox_top==='auto' && $inner_h==='auto' && $bbox_bottom==='auto') {
			if ($bbox_mt==='auto') { $bbox_mt = 0; }
			if ($bbox_mb==='auto') { $bbox_mb = 0; }
			$bbox_top = $orig_y0 - $bbox_mt - $cont_y;
			// solve for $bbox_bottom when content_h known - $inner_h=='auto' && $bbox_bottom=='auto'
		}
		else if ($bbox_top!=='auto' && $inner_h!=='auto' && $bbox_bottom!=='auto') {
			if ($bbox_mt==='auto' && $bbox_mb==='auto') {
				$x = $cont_h - $bbox_top - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_bottom;
				$bbox_mt = $bbox_mb = ($x/2);
			}
			else if ($bbox_mt==='auto') {
				$bbox_mt = $cont_h - $bbox_top - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mb - $bbox_bottom;
			}
			else if ($bbox_mb==='auto') {
				$bbox_mb = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_bottom;
			}
			else {
				$bbox_bottom = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mt;
			}
		}
		else {
		  if ($bbox_mt==='auto') { $bbox_mt = 0; }
		  if ($bbox_mb==='auto') { $bbox_mb = 0; }
		  if ($bbox_top==='auto' && $inner_h==='auto' && $bbox_bottom!=='auto') {
			// solve for $bbox_top when content_h known - $inner_h=='auto' && $bbox_top =='auto'
		  }
		  else if ($bbox_top==='auto' && $bbox_bottom==='auto' && $inner_h!=='auto') {
			$bbox_top = $orig_y0 - $bbox_mt - $cont_y;
			$bbox_bottom = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mt;
		  }
		  else if ($inner_h==='auto' && $bbox_bottom==='auto' && $bbox_top!=='auto') {
			// solve for $bbox_bottom when content_h known - $inner_h=='auto' && $bbox_bottom=='auto'
		  }
		  else if ($bbox_top==='auto' && $inner_h!=='auto' && $bbox_bottom!=='auto') {
			$bbox_top = $cont_h - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mt - $bbox_bottom;
		  }
		  else if ($inner_h==='auto' && $bbox_top!=='auto' && $bbox_bottom!=='auto') {
			$inner_h = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $bbox_pb - $bbox_bb - $bbox_mt - $bbox_bottom;
		  }
		  else if ($bbox_bottom==='auto' && $bbox_top!=='auto' && $inner_h!=='auto') {
			$bbox_bottom = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mt;
		  }
		}

		// THEN DO SAME FOR WIDTH
		// http://www.w3.org/TR/CSS21/visudet.html#abs-non-replaced-width
		if ($bbox_left==='auto' && $inner_w==='auto' && $bbox_right==='auto') {
			if ($bbox_ml==='auto') { $bbox_ml = 0; }
			if ($bbox_mr==='auto') { $bbox_mr = 0; }
			// IF containing element RTL, should set $bbox_right
			$bbox_left = $orig_x0 - $bbox_ml - $cont_x;
			// solve for $bbox_right when content_w known - $inner_w=='auto' && $bbox_right=='auto'
		}
		else if ($bbox_left!=='auto' && $inner_w!=='auto' && $bbox_right!=='auto') {
			if ($bbox_ml==='auto' && $bbox_mr==='auto') {
				$x = $cont_w - $bbox_left - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_right;
				$bbox_ml = $bbox_mr = ($x/2);
			}
			else if ($bbox_ml==='auto') {
				$bbox_ml = $cont_w - $bbox_left - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_mr - $bbox_right;
			}
			else if ($bbox_mr==='auto') {
				$bbox_mr = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_right;
			}
			else {
				$bbox_right = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml;
			}
		}
		else {
		  if ($bbox_ml==='auto') { $bbox_ml = 0; }
		  if ($bbox_mr==='auto') { $bbox_mr = 0; }
		  if ($bbox_left==='auto' && $inner_w==='auto' && $bbox_right!=='auto') {
			// solve for $bbox_left when content_w known - $inner_w=='auto' && $bbox_left =='auto'
		  }
		  else if ($bbox_left==='auto' && $bbox_right==='auto' && $inner_w!=='auto') {
			// IF containing element RTL, should set $bbox_right
			$bbox_left = $orig_x0 - $bbox_ml - $cont_x;
			$bbox_right = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml;
		  }
		  else if ($inner_w==='auto' && $bbox_right==='auto' && $bbox_left!=='auto') {
			// solve for $bbox_right when content_w known - $inner_w=='auto' && $bbox_right=='auto'
		  }
		  else if ($bbox_left==='auto' && $inner_w!=='auto' && $bbox_right!=='auto') {
			$bbox_left = $cont_w - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml - $bbox_right;
		  }
		  else if ($inner_w==='auto' && $bbox_left!=='auto' && $bbox_right!=='auto') {
			$inner_w = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $bbox_pr - $bbox_br - $bbox_ml - $bbox_right;
		  }
		  else if ($bbox_right==='auto' && $bbox_left!=='auto' && $inner_w!=='auto') {
			$bbox_right = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml;
		  }
		}

		//================================================================
		//================================================================

		//================================================================
		$y = $cont_y + $bbox_top + $bbox_mt + $bbox_bt + $bbox_pt;
		$h = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $bbox_pb - $bbox_bb - $bbox_mb - $bbox_bottom;
		$x = $cont_x + $bbox_left + $bbox_ml + $bbox_bl + $bbox_pl;
		$w = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $bbox_pr - $bbox_br - $bbox_mr - $bbox_right;
		// Set (temporary) values for x y w h to do first paint, if values are auto
		if ($inner_h==='auto' && $bbox_top==='auto') {
			$y = $cont_y + $bbox_mt + $bbox_bt + $bbox_pt;
			$h = $cont_h - ($bbox_bottom + $bbox_mt + $bbox_mb + $bbox_bt + $bbox_bb + $bbox_pt + $bbox_pb);
		}
		else if ($inner_h==='auto' && $bbox_bottom==='auto') {
			$y = $cont_y + $bbox_top + $bbox_mt + $bbox_bt + $bbox_pt;
			$h = $cont_h - ($bbox_top + $bbox_mt + $bbox_mb + $bbox_bt + $bbox_bb + $bbox_pt + $bbox_pb);
		}
		if ($inner_w==='auto' && $bbox_left==='auto') {
			$x = $cont_x + $bbox_ml + $bbox_bl + $bbox_pl;
			$w = $cont_w - ($bbox_right + $bbox_ml + $bbox_mr + $bbox_bl + $bbox_br + $bbox_pl + $bbox_pr);
		}
		else if ($inner_w==='auto' && $bbox_right==='auto') {
			$x = $cont_x + $bbox_left + $bbox_ml + $bbox_bl + $bbox_pl;
			$w = $cont_w - ($bbox_left + $bbox_ml + $bbox_mr + $bbox_bl + $bbox_br + $bbox_pl + $bbox_pr);
		}
		$bbox_y = $cont_y + $bbox_top + $bbox_mt;
		$bbox_x = $cont_x + $bbox_left + $bbox_ml;
		$saved_block1 = $this->blk[1];
		unset($p);
		unset($pb);
		//================================================================
		if ($inner_w==='auto') { // do a first write
			$this->lMargin=$x;
			$this->rMargin=$this->w - $w - $x;
			// SET POSITION & FONT VALUES
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
			$this->pageoutput[$this->page]=array();
			$this->x = $x;
			$this->y = $y;
			$this->HTMLheaderPageLinks = array();
			$this->pageBackgrounds = array();
			$this->maxPosR = 0;		// **********************
			$this->maxPosL = $this->w;	// **********************For RTL
			$this->WriteHTML($html , 4);
			$inner_w = $this->maxPosR - $this->lMargin;
			if ($bbox_right==='auto') {
				$bbox_right = $cont_w - $bbox_left - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml;
			}
			else if ($bbox_left==='auto') {
				$bbox_left = $cont_w - $bbox_ml - $bbox_bl - $bbox_pl - $inner_w - $bbox_pr - $bbox_br - $bbox_ml - $bbox_right;
				$bbox_x = $cont_x + $bbox_left + $bbox_ml ;
				$inner_x = $bbox_x + $bbox_bl + $bbox_pl;
				$x = $inner_x;
			}
			$w = $inner_w;
			$bbox_y = $cont_y + $bbox_top + $bbox_mt;
			$bbox_x = $cont_x + $bbox_left + $bbox_ml;
		}

		if ($inner_h==='auto') { // do a first write
			$this->lMargin=$x;
			$this->rMargin=$this->w - $w - $x;
			// SET POSITION & FONT VALUES
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
			$this->pageoutput[$this->page]=array();
			$this->x = $x;
			$this->y = $y;
			$this->HTMLheaderPageLinks = array();
			$this->pageBackgrounds = array();
			$this->WriteHTML($html , 4);
			$inner_h = $this->y - $y;
			if ($overflow!='hidden' && $overflow!='visible') {	// constrained
				if (($this->y + $bbox_pb + $bbox_bb) > ($cont_y + $cont_h)) {
					$adj = ($this->y + $bbox_pb + $bbox_bb) - ($cont_y + $cont_h);
					$inner_h -= $adj;
				}
			}
			if ($bbox_bottom==='auto') {
				$bbox_bottom = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mb;
			}
			else if ($bbox_top==='auto') {
				$bbox_top = $cont_h - $bbox_mt - $bbox_bt - $bbox_pt - $inner_h - $bbox_pb - $bbox_bb - $bbox_mb - $bbox_bottom;
				if ($overflow!='hidden' && $overflow!='visible') {	// constrained
					if ($bbox_top < 0) {
						$bbox_top = 0;
						$inner_h = $cont_h - $bbox_top - $bbox_mt - $bbox_bt - $bbox_pt - $bbox_pb - $bbox_bb - $bbox_mb - $bbox_bottom;
					}
				}
				$bbox_y = $cont_y + $bbox_top + $bbox_mt;
				$inner_y = $bbox_y + $bbox_bt + $bbox_pt;
				$y = $inner_y;
			}
			$h = $inner_h;
			$bbox_y = $cont_y + $bbox_top + $bbox_mt;
			$bbox_x = $cont_x + $bbox_left + $bbox_ml;
		}
		$inner_w = $w;
		$inner_h = $h;

	}


	$this->lMargin=$x;
	$this->rMargin=$this->w - $w - $x;
	// SET POSITION & FONT VALUES
	$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
	$this->pageoutput[$this->page]=array();
	$this->x = $x;
	$this->y = $y;
	$this->HTMLheaderPageLinks = array();
	$this->pageBackgrounds = array();
	$this->WriteHTML($html , 4);	// parameter 4 saves output to $this->headerbuffer
	$actual_h = $this->y - $y;
	$use_w = $w;
	$use_h = $h;
	$ratio = $actual_h / $use_w;
	if ($overflow!='hidden' && $overflow!='visible') {
		$target = $h/$w;
		if (($ratio / $target ) > 1) {
			$nl = CEIL($actual_h / $this->lineheight);
			$l = $use_w * $nl;
			$est_w = sqrt(($l * $this->lineheight) / $target) * 0.8;
			$use_w += ($est_w - $use_w) - ($w/100);
		}
		while(($ratio / $target ) > 1) {
			$this->x = $x;
			$this->y = $y;

			$use_w += ($w/100);

			$use_h = $use_w * $target ;
			$this->rMargin=$this->w - $use_w - $x;
			$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
			$this->HTMLheaderPageLinks = array();
			$this->pageBackgrounds = array();
			$this->WriteHTML($html , 4);	// parameter 4 saves output to $this->headerbuffer
			$actual_h = $this->y - $y;
			$ratio = $actual_h / $use_w;
		}
	}
	$shrink_f = $w/$use_w;

	//================================================================

	$this->pages[$this->page] .= '___BEFORE_BORDERS___';
	$block_s = $this->PrintPageBackgrounds();	// Save to print later inside clipping path
	$this->pageBackgrounds = array();

	//================================================================
	if (!empty($bounding)) {	
		// WHEN HEIGHT // BOTTOM EDGE IS KNOWN and $this->y is set to the bottom
		// Re-instate saved $this->blk[1]
		$this->blk[1] = $saved_block1;

		// These are only needed when painting border/background
		$this->blk[1]['width'] = $bbox_w = $cont_w - $bbox_left - $bbox_ml - $bbox_mr - $bbox_right;
		$this->blk[1]['x0'] = $bbox_x;
		$this->blk[1]['y0'] = $bbox_y;
		$this->blk[1]['startpage'] = $this->page;
		$this->blk[1]['y1'] = $bbox_y + $bbox_bt + $bbox_pt + $inner_h + $bbox_pb + $bbox_bb ;
		$this->PaintDivBB('',0,1);	// Prints borders and sets backgrounds in $this->pageBackgrounds 
	}

	//================================================================
	$s = $this->PrintPageBackgrounds();
	$this->pages[$this->page] = preg_replace('/___BEFORE_BORDERS___/', "\n".$s."\n", $this->pages[$this->page]);
	$this->pageBackgrounds = array();
	//================================================================

	// Clipping Output
	if ($overflow=='hidden') {
		//Bounding rectangle to clip
		$clip_y1 = $this->y;
		if (!empty($bounding) && ($this->y + $bbox_pb + $bbox_bb) > ($bbox_y + $bbox_bt + $bbox_pt + $inner_h + $bbox_pb + $bbox_bb )) {
			$clip_y1 = ($bbox_y + $bbox_bt + $bbox_pt + $inner_h + $bbox_pb + $bbox_bb ) - ($bbox_pb + $bbox_bb);
		}
		//$op = 'W* n';	// Clipping
		$op = 'W n';	// Clipping alternative mode
		$this->_out("q");
		$ch = $clip_y1 - $y;
		$this->_out(sprintf('%.3f %.3f %.3f %.3f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$ch*$this->k,$op));
		if (!empty($block_s)) {
			$tmp = "q\n".sprintf('%.3f %.3f %.3f %.3f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$ch*$this->k,$op);
			$tmp .= "\n".$block_s."\nQ";
			$block_s = $tmp ;
		}
	}


	if (!empty($block_s)) {
		if ($shrink_f != 1) {	// i.e. autofit has resized the box
			$tmp = "q\n".$this->transformScale(($shrink_f*100),($shrink_f*100), $x, $y, true);
			$tmp .= "\n".$block_s."\nQ";
			$block_s = $tmp ;
		}

		$this->_out($block_s);
	}




	//================================================================

	if ($shrink_f != 1) {	// i.e. autofit has resized the box
		$this->StartTransform();
		$this->transformScale(($shrink_f*100),($shrink_f*100), $x, $y);
	}

	$this->_out($this->headerbuffer);

	if ($shrink_f != 1) {	// i.e. autofit has resized the box
		$this->StopTransform();
	}

	if ($overflow=='hidden') {
		//End clipping
		$this->_out("Q");
	}


	// Page Links
	foreach($this->HTMLheaderPageLinks AS $lk) {
		if ($shrink_f != 1) { 	// i.e. autofit has resized the box
			$lx1 = (($lk[0]/$this->k)-$x);
			$lx2 = $x + ($lx1 * $shrink_f);
			$lk[0] = $lx2*$this->k;
			$ly1 = (($this->h-($lk[1]/$this->k))-$y);
			$ly2 = $y + ($ly1 * $shrink_f);
			$lk[1] = ($this->h-$ly2)*$this->k;
			$lk[2] *= $shrink_f;	// width
			$lk[3] *= $shrink_f;	// height
		}
		$this->PageLinks[$this->page][]=$lk;
	}
	// Restore
	$this->headerbuffer = '';
	$this->HTMLheaderPageLinks = array();
	$this->pageBackgrounds = $save_bgs;
	$this->writingHTMLheader = false;
	// mPDF 4.0
	$this->writingHTMLfooter = false;
	// mPDF 4.1
	$this->fullImageHeight = false;
	$this->ResetMargins();
	$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
	$this->SetXY($save_x,$save_y) ; 
	$this->InFooter = false;	// turns back on autopagebreaks
	$this->pageoutput[$this->page]=array();
	$this->pageoutput[$this->page]['Font']='';
}


// mPDF 4.0
function initialiseBlock(&$blk) {
	$blk['margin_top'] = 0;
	$blk['margin_left'] = 0;
	$blk['margin_bottom'] = 0;
	$blk['margin_right'] = 0;
	$blk['padding_top'] = 0;
	$blk['padding_left'] = 0;
	$blk['padding_bottom'] = 0;
	$blk['padding_right'] = 0;
	$blk['border_top']['w'] = 0;
	$blk['border_left']['w'] = 0;
	$blk['border_bottom']['w'] = 0;
	$blk['border_right']['w'] = 0;
	$blk['hide'] = false; 
	$blk['outer_left_margin'] = 0; 
	$blk['outer_right_margin'] = 0; 
	$blk['cascadeCSS'] = array(); 
	$blk['block-align'] = false; 
	$blk['bgcolor'] = false; 
	$blk['page_break_after_avoid'] = false; 
	$blk['keep_block_together'] = false; 
	$blk['float'] = false; 
	$blk['line_height'] = ''; 
	$blk['margin_collapse'] = false; 
}

// mPDF 4.0
function border_details($bd) {
	$prop = preg_split('/\s+/',trim($bd));
	// mPDF 4.0
	if (isset($this->blk[$this->blklvl]['inner_width'])) { $refw = $this->blk[$this->blklvl]['inner_width']; }
	else if (isset($this->blk[$this->blklvl-1]['inner_width'])) { $refw = $this->blk[$this->blklvl-1]['inner_width']; }
	else { $refw = $this->w; }
	if ( count($prop) == 1 ) { 
		$bsize = $this->ConvertSize($prop[0],$refw,$this->FontSize,false);
		if ($bsize > 0) {
			return array('s' => 1, 'w' => $bsize, 'c' => array('R'=>0,'G'=>0,'B'=>0), 'style'=>'solid');
		}
		else { return array('w' => 0, 's' => 0); }	// mPDF 3.0
	}
	// mPDF 4.0
	else if (count($prop) == 2 ) { 
		// 1px solid 
		if (in_array($prop[1],$this->borderstyles) || $prop[1] == 'none' || $prop[1] == 'hidden' ) { $prop[2] = ''; }
		// solid #000000 
		else if (in_array($prop[0],$this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden' ) { $prop[0] = ''; $prop[1] = $prop[0]; $prop[2] = $prop[1]; }
		// 1px #000000 
		else { $prop[1] = ''; $prop[2] = $prop[1]; }
	}
	else if ( count($prop) == 3 ) {
		// Change #000000 1px solid to 1px solid #000000 (proper)
		if (substr($prop[0],0,1) == '#') { $tmp = $prop[0]; $prop[0] = $prop[1]; $prop[1] = $prop[2]; $prop[2] = $tmp; }
		// Change solid #000000 1px to 1px solid #000000 (proper)
		else if (substr($prop[0],1,1) == '#') { $tmp = $prop[1]; $prop[0] = $prop[2]; $prop[1] = $prop[0]; $prop[2] = $tmp; }
		// Change solid 1px #000000 to 1px solid #000000 (proper)
		else if (in_array($prop[0],$this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden' ) { 
			$tmp = $prop[0]; $prop[0] = $prop[1]; $prop[1] = $tmp; 
		}
	}
	else { return array(); } 
	// Size
	$bsize = $this->ConvertSize($prop[0],$refw,$this->FontSize,false);
	//color
	$coul = $this->ConvertColor($prop[2]);	// returns array
	// Style
	$prop[1] = strtolower($prop[1]);
	if (in_array($prop[1],$this->borderstyles) && $bsize > 0) { $on = 1; } 
	else if ($prop[1] == 'hidden') { $on = 1; $bsize = 0; $coul = ''; } 
	else if ($prop[1] == 'none') { $on = 0; $bsize = 0; $coul = ''; } 
	else { $on = 0; $bsize = 0; $coul = ''; $prop[1] = ''; }
	return array('s' => $on, 'w' => $bsize, 'c' => $coul, 'style'=> $prop[1] );
}

// mPDF 4.0
function _fix_borderStr($bd) {
	$prop = preg_split('/\s+/',trim($bd));
	$w = '1px';
	$c = '#000000';
	$s = 'none';

	if ( count($prop) == 1 ) { 
		// solid
		if (in_array($prop[0],$this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden' ) { $s = $prop[0]; }
		// #000000
		else if (is_array($this->ConvertColor($prop[0]))) { $c = $prop[0]; }
		// 1px 
		else { $w = $prop[0]; }
	}
	else if (count($prop) == 2 ) { 
		// 1px solid 
		if (in_array($prop[1],$this->borderstyles) || $prop[1] == 'none' || $prop[1] == 'hidden' ) { $w = $prop[0]; $s = $prop[1]; }
		// solid #000000 
		else if (in_array($prop[0],$this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden' ) { $s = $prop[0]; $c = $prop[1]; }
		// 1px #000000 
		else { $w = $prop[0]; $c = $prop[1]; }
	}
	else if ( count($prop) == 3 ) {
		// Change #000000 1px solid to 1px solid #000000 (proper)
		if (substr($prop[0],0,1) == '#') { $c = $prop[0]; $w = $prop[1]; $s = $prop[2]; }
		// Change solid #000000 1px to 1px solid #000000 (proper)
		else if (substr($prop[0],1,1) == '#') { $s = $prop[0]; $c = $prop[1]; $w = $prop[2]; }
		// Change solid 1px #000000 to 1px solid #000000 (proper)
		else if (in_array($prop[0],$this->borderstyles) || $prop[0] == 'none' || $prop[0] == 'hidden' ) { 
			$s = $prop[0]; $w = $prop[1]; $c = $prop[2]; 
		}
		else { $w = $prop[0]; $s = $prop[1]; $c = $prop[2]; }
	}
	else { return ''; } 
	$s = strtolower($s);
	return $w.' '.$s.' '.$c;
}



// NEW FUNCTION FOR CSS MARGIN or PADDING called from SetCSS
function fixCSS($prop) {
	if (!is_array($prop) || (count($prop)==0)) return array(); 
	$newprop = array(); 
	foreach($prop AS $k => $v) {
		// mPDF 4.0 - FONT shorthand
		if ($k == 'FONT') {
			$s = trim($v);
			preg_match_all('/\"(.*?)\"/',$s,$ff);
			if (count($ff[1])) {
				foreach($ff[1] AS $ffp) { 
					$w = preg_split('/\s+/',$ffp);
					$s = preg_replace('/\"'.$ffp.'\"/',$w[0],$s); 
				}
			}
			preg_match_all('/\'(.*?)\'/',$s,$ff);
			if (count($ff[1])) {
				foreach($ff[1] AS $ffp) { 
					$w = preg_split('/\s+/',$ffp);
					$s = preg_replace('/\''.$ffp.'\'/',$w[0],$s); 
				}
			}
			$s = preg_replace('/\s*,\s*/',',',$s); 
			$bits = preg_split('/\s+/',$s);
			if (count($bits)>1) {
				$k = 'FONT-FAMILY'; $v = $bits[(count($bits)-1)];
				$fs = $bits[(count($bits)-2)];
				if (preg_match('/(.*?)\/(.*)/',$fs, $fsp)) { 
					$newprop['FONT-SIZE'] = $fsp[1];
					$newprop['LINE-HEIGHT'] = $fsp[2];
				}
				else { $newprop['FONT-SIZE'] = $fs; } 
				if (preg_match('/(italic|oblique)/i',$s)) { $newprop['FONT-STYLE'] = 'italic'; }
				else { $newprop['FONT-STYLE'] = 'normal'; }
				if (preg_match('/bold/i',$s)) { $newprop['FONT-WEIGHT'] = 'bold'; }
				else { $newprop['FONT-WEIGHT'] = 'normal'; }
				if (preg_match('/small-caps/i',$s)) { $newprop['TEXT-TRANSFORM'] = 'uppercase'; }
			}
		}
		if ($k == 'FONT-FAMILY') {
			$aux_fontlist = explode(",",$v);
			$fonttype = trim($aux_fontlist[0]);
			// mPDF 4.0
			$fonttype = preg_replace('/["\']*(.*?)["\']*/','\\1',$fonttype);
			$aux_fontlist = explode(" ",$fonttype);
			$fonttype = $aux_fontlist[0];
			$v = strtolower(trim($fonttype));
			if (($this->is_MB && in_array($v,$this->available_unifonts)) || 
				(!$this->is_MB && in_array($v,$this->available_fonts)) || in_array($v, array('sjis','uhc','big5','gb')) || 
				in_array($v,$this->sans_fonts) || in_array($v,$this->serif_fonts) || in_array($v,$this->mono_fonts) ) { 
				$newprop[$k] = $v; 
			}
		}
		else if ($k == 'MARGIN') {
			$tmp =  $this->expand24($v);
			$newprop['MARGIN-TOP'] = $tmp['T'];
			$newprop['MARGIN-RIGHT'] = $tmp['R'];
			$newprop['MARGIN-BOTTOM'] = $tmp['B'];
			$newprop['MARGIN-LEFT'] = $tmp['L'];
		}
		else if ($k == 'PADDING') {
			$tmp =  $this->expand24($v);
			$newprop['PADDING-TOP'] = $tmp['T'];
			$newprop['PADDING-RIGHT'] = $tmp['R'];
			$newprop['PADDING-BOTTOM'] = $tmp['B'];
			$newprop['PADDING-LEFT'] = $tmp['L'];
		}
		// mPDF 4.0
		else if ($k == 'BORDER') {
			if ($v == '1') { $v = '1px solid #000000'; }
			else { $v = $this->_fix_borderStr($v); }
			$newprop['BORDER-TOP'] = $v;
			$newprop['BORDER-RIGHT'] = $v;
			$newprop['BORDER-BOTTOM'] = $v;
			$newprop['BORDER-LEFT'] = $v;
		}
		else if ($k == 'BORDER-TOP') {
			$newprop['BORDER-TOP'] = $this->_fix_borderStr($v);
		}
		else if ($k == 'BORDER-RIGHT') {
			$newprop['BORDER-RIGHT'] = $this->_fix_borderStr($v);
		}
		else if ($k == 'BORDER-BOTTOM') {
			$newprop['BORDER-BOTTOM'] = $this->_fix_borderStr($v);
		}
		else if ($k == 'BORDER-LEFT') {
			$newprop['BORDER-LEFT'] = $this->_fix_borderStr($v);
		}
		// mPDF 4.0
		else if ($k == 'BORDER-STYLE') {
			$e = $this->expand24($v);
			$newprop['BORDER-TOP-STYLE'] = $e['T'];
			$newprop['BORDER-RIGHT-STYLE'] = $e['R'];
			$newprop['BORDER-BOTTOM-STYLE'] = $e['B'];
			$newprop['BORDER-LEFT-STYLE'] = $e['L'];
		}
		else if ($k == 'BORDER-WIDTH') {
			$e = $this->expand24($v);
			$newprop['BORDER-TOP-WIDTH'] = $e['T'];
			$newprop['BORDER-RIGHT-WIDTH'] = $e['R'];
			$newprop['BORDER-BOTTOM-WIDTH'] = $e['B'];
			$newprop['BORDER-LEFT-WIDTH'] = $e['L'];
		}
		else if ($k == 'BORDER-COLOR') {
			$e = $this->expand24($v);
			$newprop['BORDER-TOP-COLOR'] = $e['T'];
			$newprop['BORDER-RIGHT-COLOR'] = $e['R'];
			$newprop['BORDER-BOTTOM-COLOR'] = $e['B'];
			$newprop['BORDER-LEFT-COLOR'] = $e['L'];
		}

		else if ($k == 'BORDER-SPACING') {
			$prop = preg_split('/\s+/',trim($v));
			if (count($prop) == 1 ) { 
				$newprop['BORDER-SPACING-H'] = $prop[0];
				$newprop['BORDER-SPACING-V'] = $prop[0];
			}
			else if (count($prop) == 2 ) { 
				$newprop['BORDER-SPACING-H'] = $prop[0];
				$newprop['BORDER-SPACING-V'] = $prop[1];
			}
		}
		else if ($k == 'SIZE') {
			$prop = preg_split('/\s+/',trim($v));
			if (preg_match('/(auto|portrait|landscape)/',$prop[0])) {
				$newprop['SIZE'] = strtoupper($prop[0]);
			}
			else if (count($prop) == 1 ) {
				$newprop['SIZE']['W'] = $this->ConvertSize($prop[0]);
				$newprop['SIZE']['H'] = $this->ConvertSize($prop[0]);
			}
			else if (count($prop) == 2 ) {
				$newprop['SIZE']['W'] = $this->ConvertSize($prop[0]);
				$newprop['SIZE']['H'] = $this->ConvertSize($prop[1]);
			}
		}
		// mPDF 3.0 - Tiling Patterns
		else if ($k == 'BACKGROUND') {
			$bg = $this->parseCSSbackground($v);
			if ($bg['c']) { $newprop['BACKGROUND-COLOR'] = $bg['c']; }
		}
		else { 
			$newprop[$k] = $v; 
		}
	}
	// mPDF 4.0
//	$this->_mergeBorders($newprop,$newprop);
	return $newprop;
}


// mPDF 3.0 - Tiling Patterns
function parseCSSbackground($s) {
	$s = strtolower($s);
	$bg = array('c'=>false, 'i'=>false, 'r'=>false, 'p'=>false, );
	if (preg_match('/url\(/',$s)) {
		// If color, set and strip it off
		if (preg_match('/^\s*(#[0-9a-fA-F]{3,6}|rgb\(.*?\)|[a-zA-Z]{3,})\s+(url\(.*)/',$s,$m)) { 
			$bg['c'] = $m[1]; 
			$s = $m[2];
		}
	}
	else {
		if (preg_match('/^\s*(#[0-9a-fA-F]{3,6}|rgb\(.*?\)|[a-zA-Z]{3,})/',$s,$m)) { $bg['c'] = $m[1]; }
	}
	return ($bg);
}


function expand24($mp) {
	$prop = preg_split('/\s+/',trim($mp));
	if (count($prop) == 1 ) { 
		return array('T' => $prop[0], 'R' => $prop[0], 'B' => $prop[0], 'L'=> $prop[0]);
	}
	if (count($prop) == 2 ) { 
		return array('T' => $prop[0], 'R' => $prop[1], 'B' => $prop[0], 'L'=> $prop[1]);
	}
	// mPDF 4.0
	if (count($prop) == 3 ) { 
		return array('T' => $prop[0], 'R' => $prop[1], 'B' => $prop[2], 'L'=> $prop[1]);
	}
	if (count($prop) == 4 ) { 
		return array('T' => $prop[0], 'R' => $prop[1], 'B' => $prop[2], 'L'=> $prop[3]);
	}
	return array(); 
}


// mPDF 4.0
function fixLineheight($v) {
	$lh = false;
	if (preg_match('/^[0-9\.,]*$/',$v) && $v >= 1) { $lh = $v + 0; }
	else if (preg_match('/%/',$v) && $v >= 100) { $lh = ($v + 0)/100; }
	else if (strtoupper($v) == 'NORMAL') { 
		return $this->default_lineheight_correction; 
	}
	else { 
		$tlh = $this->ConvertSize($v,$this->FontSize,$this->FontSize,true); 
		if ($tlh) { $lh = ($tlh / $this->FontSize); }
	}
	return $lh;
}



function setBorderDominance($prop, $val) {
	if (isset($prop['BORDER-LEFT']) && $prop['BORDER-LEFT']) { $this->cell_border_dominance_L = $val; }
	if (isset($prop['BORDER-RIGHT']) && $prop['BORDER-RIGHT']) { $this->cell_border_dominance_R = $val; }
	if (isset($prop['BORDER-TOP']) && $prop['BORDER-TOP']) { $this->cell_border_dominance_T = $val; }
	if (isset($prop['BORDER-BOTTOM']) && $prop['BORDER-BOTTOM']) { $this->cell_border_dominance_B = $val; }
}

// mPDF 4.0
function _mergeCSS(&$p, &$t) {
	// Save Cascading CSS e.g. "div.topic p" at this block level
	if (isset($p) && $p) {
		$carry = $p;
		if ($t) { 
			$t = $this->array_merge_recursive_unique($t, $carry); 
		}
	   	else { $t = $carry; }
	}
}

// for CSS handling
function array_merge_recursive_unique($array1, $array2) {
    $arrays = func_get_args();
    $narrays = count($arrays);
    $ret = $arrays[0];
    for ($i = 1; $i < $narrays; $i ++) {
        foreach ($arrays[$i] as $key => $value) {
            if (((string) $key) === ((string) intval($key))) { // integer or string as integer key - append
                $ret[] = $value;
            }
            else { // string key - merge
                if (is_array($value) && isset($ret[$key])) {
                    $ret[$key] = $this->array_merge_recursive_unique($ret[$key], $value);
                }
                else {
                    $ret[$key] = $value;
                }
            }
        }   
    }
    return $ret;
}


// mPDF 4.0
function _mergeFullCSS(&$p, &$t, $tag, $classes, $id) {
		$this->_mergeCSS($p[$tag], $t);
		// STYLESHEET CLASS e.g. .smallone{}  .redletter{}
		foreach($classes AS $class) {
		  $this->_mergeCSS($p['CLASS>>'.$class], $t);
		}
		// STYLESHEET CLASS e.g. #smallone{}  #redletter{}
		if (isset($id)) {
		  $this->_mergeCSS($p['ID>>'.$id], $t);
		}
		// STYLESHEET CLASS e.g. .smallone{}  .redletter{}
		foreach($classes AS $class) {
		  $this->_mergeCSS($p[$tag.'>>CLASS>>'.$class], $t);
		}
		// STYLESHEET CLASS e.g. #smallone{}  #redletter{}
		if (isset($id)) {
		  $this->_mergeCSS($p[$tag.'>>ID>>'.$id], $t);
		}
}

// mPDF 4.0
function _set_mergedCSS(&$m, &$p, $d=true, $bd=false) {
	if (isset($m)) {
		if ((isset($m['depth']) && $m['depth']>1) || $d==false) { 	// include check for 'depth'
			if ($bd) { $this->setBorderDominance($m, $bd); }	// *TABLES*
			if (is_array($m)) { 
				$p = array_merge($p,$m); 
				$this->_mergeBorders($p,$m);	// mPDF 4.0
			}
		}
	}
}

// mPDF 4.0
function _mergeBorders(&$b, &$a) {	// Merges $a['BORDER-TOP-STYLE'] to $b['BORDER-TOP'] etc.
  foreach(array('TOP','RIGHT','BOTTOM','LEFT') AS $side) {
    foreach(array('STYLE','WIDTH','COLOR') AS $el) {
	if (isset($a['BORDER-'.$side.'-'.$el])) {	// e.g. $b['BORDER-TOP-STYLE']
		$s = trim($a['BORDER-'.$side.'-'.$el]);
		if (isset($b['BORDER-'.$side])) {	// e.g. $b['BORDER-TOP']
			$p = trim($b['BORDER-'.$side]);
		}
		else { $p = ''; }
		if ($el=='STYLE') {
			if ($p) { $b['BORDER-'.$side] = preg_replace('/(\S+)\s+(\S+)\s+(\S+)/', '\\1 '.$s.' \\3', $p); }
			else { $b['BORDER-'.$side] = '0px '.$s.' #000000'; }
		}
		else if ($el=='WIDTH') {
			if ($p) { $b['BORDER-'.$side] = preg_replace('/(\S+)\s+(\S+)\s+(\S+)/', $s.' \\2 \\3', $p); }
			else { $b['BORDER-'.$side] = $s.' none #000000'; }
		}
		else if ($el=='COLOR') {
			if ($p) { $b['BORDER-'.$side] = preg_replace('/(\S+)\s+(\S+)\s+(\S+)/', '\\1 \\2 '.$s, $p); }
			else { $b['BORDER-'.$side] = '0px none '.$s; }
		}
		unset($a['BORDER-'.$side.'-'.$el]);
	}
    }
  }
}


function MergeCSS($inherit,$tag,$attr) {
	$p = array();
	$zp = array(); 

	$classes = array();
	if (isset($attr['CLASS'])) {
		$classes = preg_split('/\s+/',$attr['CLASS']);
	}
	//===============================================
	// Set Inherited properties
	if ($inherit == 'TOPTABLE') {	// $tag = TABLE
		//===============================================
		// Save Cascading CSS e.g. "div.topic p" at this block level

		if (isset($this->blk[$this->blklvl]['cascadeCSS'])) {
			$this->tablecascadeCSS[0] = $this->blk[$this->blklvl]['cascadeCSS'];
		}
		else {
			$this->tablecascadeCSS[0] = $this->cascadeCSS;
		}
	}
	//===============================================
	// Set Inherited properties
	if ($inherit == 'TOPTABLE' || $inherit == 'TABLE') {
		//Cascade everything from last level that is not an actual property, or defined by current tag/attributes
		if (isset($this->tablecascadeCSS[$this->tbCSSlvl-1]) && is_array($this->tablecascadeCSS[$this->tbCSSlvl-1])) {
		   foreach($this->tablecascadeCSS[$this->tbCSSlvl-1] AS $k=>$v) {
//			if ($k != $tag && !preg_match('/^CLASS>>('.implode('|',$classes).')$/i',$k) && !preg_match('/^'.$tag.'>>CLASS>>('.implode('|',$classes).')$/i',$k) && $k != 'ID>>'.$attr['ID'] && $k != $tag.'>>ID>>'.$attr['ID'] && (preg_match('/(ID|CLASS)>>/',$k) || preg_match('/^('.$this->allowedCSStags.')(>>.*){0,1}$/',$k))) {
				$this->tablecascadeCSS[$this->tbCSSlvl][$k] = $v;
//			}
		   }
		}
		// mPDF 4.0
		$this->_mergeFullCSS($this->cascadeCSS, $this->tablecascadeCSS[$this->tbCSSlvl], $tag, $classes, $attr['ID']);
		//===============================================
		// Cascading forward CSS e.g. "table.topic td" for this table in $this->tablecascadeCSS 
		//===============================================
		// STYLESHEET TAG e.g. table
		// mPDF 4.0
		$this->_mergeFullCSS($this->tablecascadeCSS[$this->tbCSSlvl-1], $this->tablecascadeCSS[$this->tbCSSlvl], $tag, $classes, $attr['ID']);
		//===============================================
	}
	//===============================================
	// Set Inherited properties
	if ($inherit == 'TOPLIST') {	// $tag = UL,OL
		//===============================================
		// Save Cascading CSS e.g. "div.topic p" at this block level
		if (isset($this->blk[$this->blklvl]['cascadeCSS'])) {
			$this->listcascadeCSS[0] = $this->blk[$this->blklvl]['cascadeCSS'];
		}
		else {
			$this->listcascadeCSS[0] = $this->cascadeCSS;
		}
	}
	//===============================================
	// Set Inherited properties
	if ($inherit == 'TOPLIST' || $inherit == 'LIST') {
		//Cascade everything from last level that is not an actual property, or defined by current tag/attributes
		if (isset($this->listcascadeCSS[$this->listCSSlvl-1]) && is_array($this->listcascadeCSS[$this->listCSSlvl-1])) {
		   foreach($this->listcascadeCSS[$this->listCSSlvl-1] AS $k=>$v) {
				$this->listcascadeCSS[$this->listCSSlvl][$k] = $v;
		   }
		}
		// mPDF 4.0
		$this->_mergeFullCSS($this->cascadeCSS, $this->listcascadeCSS[$this->listCSSlvl], $tag, $classes, $attr['ID']);
		//===============================================
		// Cascading forward CSS e.g. "table.topic td" for this list in $this->listcascadeCSS 
		//===============================================
		// STYLESHEET TAG e.g. table
		// mPDF 4.0
		$this->_mergeFullCSS($this->listcascadeCSS[$this->listCSSlvl-1], $this->listcascadeCSS[$this->listCSSlvl], $tag, $classes, $attr['ID']);
		//===============================================
	}
	//===============================================
	// Set Inherited properties
	if ($inherit == 'BLOCK') {
		if (isset($this->blk[$this->blklvl-1]['cascadeCSS']) && is_array($this->blk[$this->blklvl-1]['cascadeCSS'])) {
		   foreach($this->blk[$this->blklvl-1]['cascadeCSS'] AS $k=>$v) {
//			if ($k != $tag && !preg_match('/^CLASS>>('.implode('|',$classes).')$/i',$k) && !preg_match('/^'.$tag.'>>CLASS>>('.implode('|',$classes).')$/i',$k) && $k != 'ID>>'.$attr['ID'] && $k != $tag.'>>ID>>'.$attr['ID'] && (preg_match('/(ID|CLASS)>>/',$k) || preg_match('/^('.$this->allowedCSStags.')(>>.*){0,1}$/',$k))) {
				$this->blk[$this->blklvl]['cascadeCSS'][$k] = $v;
//			}

		   }
		}

		//===============================================
		// Save Cascading CSS e.g. "div.topic p" at this block level
		// mPDF 4.0
		$this->_mergeFullCSS($this->cascadeCSS, $this->blk[$this->blklvl]['cascadeCSS'], $tag, $classes, $attr['ID']);
		//===============================================
		// Cascading forward CSS
		//===============================================
		// mPDF 4.0
		$this->_mergeFullCSS($this->blk[$this->blklvl-1]['cascadeCSS'], $this->blk[$this->blklvl]['cascadeCSS'], $tag, $classes, $attr['ID']);
		//===============================================
		  // Block properties
		  if (isset($this->blk[$this->blklvl-1]['margin_collapse']) && $this->blk[$this->blklvl-1]['margin_collapse']) { $p['MARGIN-COLLAPSE'] = 'COLLAPSE'; }	// custom tag, but follows CSS principle that border-collapse is inherited
		  if (isset($this->blk[$this->blklvl-1]['line_height']) && $this->blk[$this->blklvl-1]['line_height']) { $p['LINE-HEIGHT'] = $this->blk[$this->blklvl-1]['line_height']; }	
		  if (isset($this->blk[$this->blklvl-1]['align']) && $this->blk[$this->blklvl-1]['align']) { 
			if ($this->blk[$this->blklvl-1]['align'] == 'L') { $p['TEXT-ALIGN'] = 'left'; } 
			else if ($this->blk[$this->blklvl-1]['align'] == 'J') { $p['TEXT-ALIGN'] = 'justify'; } 
			else if ($this->blk[$this->blklvl-1]['align'] == 'R') { $p['TEXT-ALIGN'] = 'right'; } 
			else if ($this->blk[$this->blklvl-1]['align'] == 'C') { $p['TEXT-ALIGN'] = 'center'; } 
		  }
		  // mPDF 3.0
		  if ($this->ColActive || $this->keep_block_together) { 
		  	if (isset($this->blk[$this->blklvl-1]['bgcolor']) && $this->blk[$this->blklvl-1]['bgcolor']) { // Doesn't officially inherit, but default value is transparent (?=inherited)
				$cor = $this->blk[$this->blklvl-1]['bgcolorarray' ];
				$p['BACKGROUND-COLOR'] = 'RGB('.$cor['R'].','.$cor['G'].','.$cor['B'].')';
			}
		  }

		// mPDF 4.0
		if (isset($this->blk[$this->blklvl-1]['text_indent']) && ($this->blk[$this->blklvl-1]['text_indent'] || $this->blk[$this->blklvl-1]['text_indent']===0)) { $p['TEXT-INDENT'] = $this->blk[$this->blklvl-1]['text_indent']; }

		// mPDF 4.0
		$biilp = $this->blk[$this->blklvl-1]['InlineProperties'];
		if (isset($biilp[ 'family' ]) && $biilp[ 'family' ]) { $p['FONT-FAMILY'] = $biilp[ 'family' ]; }
		if (isset($biilp[ 'I' ]) && $biilp[ 'I' ]) { $p['FONT-STYLE'] = 'italic'; }
		if (isset($biilp[ 'sizePt' ]) && $biilp[ 'sizePt' ]) { $p['FONT-SIZE'] = $biilp[ 'sizePt' ] . 'pt'; }
		if (isset($biilp[ 'B' ]) && $biilp[ 'B' ]) { $p['FONT-WEIGHT'] = 'bold'; }
		if (isset($biilp[ 'colorarray' ]) && $biilp[ 'colorarray' ]) { 
			$cor = $biilp[ 'colorarray' ];
			$p['COLOR'] = 'RGB('.$cor['R'].','.$cor['G'].','.$cor['B'].')';
		}
		if (isset($biilp[ 'toupper' ]) && $biilp[ 'toupper' ]) { $p['TEXT-TRANSFORM'] = 'uppercase'; }
		else if (isset($biilp[ 'tolower' ]) && $biilp[ 'tolower' ]) { $p['TEXT-TRANSFORM'] = 'lowercase'; }
			// CSS says text-decoration is not inherited, but IE7 does?? 
		if (isset($biilp[ 'underline' ]) && $biilp[ 'underline' ]) { $p['TEXT-DECORATION'] = 'underline'; }

	}
	//===============================================
	//===============================================
	// Set Inherited properties
	if ($inherit == 'LIST') {
		if ($this->listlvl == 1) {
		    // mPDF 4.0
		    $bilp = $this->blk[$this->blklvl]['InlineProperties'];
		    if (isset($bilp[ 'family' ]) && $bilp[ 'family' ]) { $p['FONT-FAMILY'] = $bilp[ 'family' ]; }
   		    if (isset($bilp[ 'I' ]) && $bilp[ 'I' ]) { $p['FONT-STYLE'] = 'italic'; }
   		    if (isset($bilp[ 'sizePt' ]) && $bilp[ 'sizePt' ]) { $p['FONT-SIZE'] = $bilp[ 'sizePt' ] . 'pt'; }
   		    if (isset($bilp[ 'B' ]) && $bilp[ 'B' ]) { $p['FONT-WEIGHT'] = 'bold'; }
   		    if (isset($bilp[ 'colorarray' ]) && $bilp[ 'colorarray' ]) { 
			$cor = $bilp[ 'colorarray' ];
			$p['COLOR'] = 'RGB('.$cor['R'].','.$cor['G'].','.$cor['B'].')';
		    }
		    if (isset($bilp[ 'toupper' ]) && $bilp[ 'toupper' ]) { $p['TEXT-TRANSFORM'] = 'uppercase'; }
		    else if (isset($bilp[ 'tolower' ]) && $bilp[ 'tolower' ]) { $p['TEXT-TRANSFORM'] = 'lowercase'; }
			// CSS says text-decoration is not inherited, but IE7 does??
		    if (isset($bilp[ 'underline' ]) && $bilp[ 'underline' ]) { $p['TEXT-DECORATION'] = 'underline'; }
		    if ($tag=='LI') { 
			$lilp = $this->InlineProperties['LIST'][$this->listlvl][$this->listoccur[$this->listlvl]];
			if (isset($lilp[ 'family' ]) && $lilp[ 'family' ]) { $p['FONT-FAMILY'] = $lilp[ 'family' ]; }
   			if (isset($lilp[ 'I' ]) && $lilp[ 'I' ]) { $p['FONT-STYLE'] = 'italic'; }
   			if (isset($lilp[ 'sizePt' ]) && $lilp[ 'sizePt' ]) { $p['FONT-SIZE'] = $lilp[ 'sizePt' ] . 'pt'; }
   			if (isset($lilp[ 'B' ]) && $lilp[ 'B' ]) { $p['FONT-WEIGHT'] = 'bold'; }
   			if (isset($lilp[ 'colorarray' ]) && $lilp[ 'colorarray' ]) { 
				$cor = $lilp[ 'colorarray' ];
				$p['COLOR'] = 'RGB('.$cor['R'].','.$cor['G'].','.$cor['B'].')';
			}
			if (isset($lilp[ 'toupper' ]) && $lilp[ 'toupper' ]) { $p['TEXT-TRANSFORM'] = 'uppercase'; }
			else if (isset($lilp[ 'tolower' ]) && $lilp[ 'tolower' ]) { $p['TEXT-TRANSFORM'] = 'lowercase'; }
			// CSS says text-decoration is not inherited, but IE7 does?? 
			if (isset($lilp[ 'underline' ]) && $lilp[ 'underline' ]) { $p['TEXT-DECORATION'] = 'underline'; }
		    }
		}
	}
	//===============================================
	//===============================================
	// DEFAULT for this TAG set in DefaultCSS
	if (isset($this->defaultCSS[$tag])) { 
			$zp = $this->fixCSS($this->defaultCSS[$tag]);
			if (is_array($zp)) { 	// Default overwrites Inherited
				$p = array_merge($p,$zp); 	// !! Note other way round !!
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// mPDF 4.0 cellPadding overwrites TD/TH default but not specific CSS set on cell
	if (($tag=='TD' || $tag=='TH') && $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cell_padding']) { 
		$p['PADDING-LEFT'] = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cell_padding'];
		$p['PADDING-RIGHT'] = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cell_padding'];
		$p['PADDING-TOP'] = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cell_padding'];
		$p['PADDING-BOTTOM'] = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cell_padding'];
	}
	//===============================================
	// STYLESHEET TAG e.g. h1  p  div  table
	if (isset($this->CSS[$tag]) && $this->CSS[$tag]) { 
			$zp = $this->CSS[$tag];
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// STYLESHEET CLASS e.g. .smallone{}  .redletter{}
	foreach($classes AS $class) {
			$zp = array();
			if (isset($this->CSS['CLASS>>'.$class]) && $this->CSS['CLASS>>'.$class]) { $zp = $this->CSS['CLASS>>'.$class]; }
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// STYLESHEET ID e.g. #smallone{}  #redletter{}
	if (isset($attr['ID']) && isset($this->CSS['ID>>'.$attr['ID']]) && $this->CSS['ID>>'.$attr['ID']]) {
			$zp = $this->CSS['ID>>'.$attr['ID']];
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// STYLESHEET CLASS e.g. p.smallone{}  div.redletter{}
	foreach($classes AS $class) {
			$zp = array();
			if (isset($this->CSS[$tag.'>>CLASS>>'.$class]) && $this->CSS[$tag.'>>CLASS>>'.$class]) { $zp = $this->CSS[$tag.'>>CLASS>>'.$class]; }
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// STYLESHEET CLASS e.g. p#smallone{}  div#redletter{}
	if (isset($attr['ID']) && isset($this->CSS[$tag.'>>ID>>'.$attr['ID']]) && $this->CSS[$tag.'>>ID>>'.$attr['ID']]) {
			$zp = $this->CSS[$tag.'>>ID>>'.$attr['ID']];
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	// Cascaded e.g. div.class p only works for block level
	if ($inherit == 'BLOCK') {
		// mPDF 4.0
		$this->_set_mergedCSS($this->blk[$this->blklvl-1]['cascadeCSS'][$tag], $p);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->blk[$this->blklvl-1]['cascadeCSS']['CLASS>>'.$class], $p);
		}
		$this->_set_mergedCSS($this->blk[$this->blklvl-1]['cascadeCSS']['ID>>'.$attr['ID']], $p);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->blk[$this->blklvl-1]['cascadeCSS'][$tag.'>>CLASS>>'.$class], $p);
		}
		$this->_set_mergedCSS($this->blk[$this->blklvl-1]['cascadeCSS'][$tag.'>>ID>>'.$attr['ID']], $p);
	}
	// mPDF 4.0
	else if ($inherit == 'INLINE') {
		// mPDF 4.0
		$this->_set_mergedCSS($this->blk[$this->blklvl]['cascadeCSS'][$tag], $p);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->blk[$this->blklvl]['cascadeCSS']['CLASS>>'.$class], $p);
		}
		$this->_set_mergedCSS($this->blk[$this->blklvl]['cascadeCSS']['ID>>'.$attr['ID']], $p);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->blk[$this->blklvl]['cascadeCSS'][$tag.'>>CLASS>>'.$class], $p);
		}
		$this->_set_mergedCSS($this->blk[$this->blklvl]['cascadeCSS'][$tag.'>>ID>>'.$attr['ID']], $p);
	}
	else if ($inherit == 'TOPTABLE' || $inherit == 'TABLE') { // NB looks at $this->tablecascadeCSS-1 for cascading CSS
		// false, 9 = don't check for 'depth' and do set border dominance
		// mPDF 4.0
		$this->_set_mergedCSS($this->tablecascadeCSS[$this->tbCSSlvl-1][$tag], $p, false, 9);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->tablecascadeCSS[$this->tbCSSlvl-1]['CLASS>>'.$class], $p, false, 9);
		}
		$this->_set_mergedCSS($this->tablecascadeCSS[$this->tbCSSlvl-1]['ID>>'.$attr['ID']], $p, false, 9);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->tablecascadeCSS[$this->tbCSSlvl-1][$tag.'>>CLASS>>'.$class], $p, false, 9);
		}
		$this->_set_mergedCSS($this->tablecascadeCSS[$this->tbCSSlvl-1][$tag.'>>ID>>'.$attr['ID']], $p, false, 9);
	}
	//===============================================
	else if ($inherit == 'TOPLIST' || $inherit == 'LIST') { // NB looks at $this->listcascadeCSS-1 for cascading CSS
		// false = don't check for 'depth' 
		// mPDF 4.0
		$this->_set_mergedCSS($this->listcascadeCSS[$this->listCSSlvl-1][$tag], $p, false);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->listcascadeCSS[$this->listCSSlvl-1]['CLASS>>'.$class], $p, false);
		}
		$this->_set_mergedCSS($this->listcascadeCSS[$this->listCSSlvl-1]['ID>>'.$attr['ID']], $p, false);
		foreach($classes AS $class) {
			$this->_set_mergedCSS($this->listcascadeCSS[$this->listCSSlvl-1][$tag.'>>CLASS>>'.$class], $p, false);
		}
		$this->_set_mergedCSS($this->listcascadeCSS[$this->listCSSlvl-1][$tag.'>>ID>>'.$attr['ID']], $p, false);
	}
	//===============================================
	//===============================================
	// INLINE STYLE e.g. style="CSS:property"
	if (isset($attr['STYLE'])) {
			$zp = $this->readInlineCSS($attr['STYLE']);
			if (is_array($zp)) { 
				// mPDF 4.0
				$p = array_merge($p,$zp); 
				$this->_mergeBorders($p,$zp);	// mPDF 4.0
			}
	}
	//===============================================
	//===============================================
	// INLINE ATTRIBUTES e.g. .. ALIGN="CENTER">
	if (isset($attr['LANG']) and $attr['LANG']!='') {
			$p['LANG'] = $attr['LANG'];
	}
	if (isset($attr['COLOR']) and $attr['COLOR']!='') {
			$p['COLOR'] = $attr['COLOR'];
	}
	if ($tag != 'INPUT') {
		if (isset($attr['WIDTH']) and $attr['WIDTH']!='') {
			$p['WIDTH'] = $attr['WIDTH'];
		}
		if (isset($attr['HEIGHT']) and $attr['HEIGHT']!='') {
			$p['HEIGHT'] = $attr['HEIGHT'];
		}
	}
	if ($tag == 'FONT') {
		if (isset($attr['FACE'])) {
			$p['FONT-FAMILY'] = $attr['FACE'];
		}
		if (isset($attr['SIZE']) and $attr['SIZE']!='') {
			$s = '';
			if ($attr['SIZE'] === '+1') { $s = '120%'; }
			else if ($attr['SIZE'] === '-1') { $s = '86%'; }
			else if ($attr['SIZE'] === '1') { $s = 'XX-SMALL'; }
			else if ($attr['SIZE'] == '2') { $s = 'X-SMALL'; }
			else if ($attr['SIZE'] == '3') { $s = 'SMALL'; }
			else if ($attr['SIZE'] == '4') { $s = 'MEDIUM'; }
			else if ($attr['SIZE'] == '5') { $s = 'LARGE'; }
			else if ($attr['SIZE'] == '6') { $s = 'X-LARGE'; }
			else if ($attr['SIZE'] == '7') { $s = 'XX-LARGE'; }
			if ($s) $p['FONT-SIZE'] = $s;
		}
	}
	if (isset($attr['VALIGN']) and $attr['VALIGN']!='') {
		$p['VERTICAL-ALIGN'] = $attr['VALIGN'];
	}
	if (isset($attr['VSPACE']) and $attr['VSPACE']!='') {
		$p['MARGIN-TOP'] = $attr['VSPACE'];
		$p['MARGIN-BOTTOM'] = $attr['VSPACE'];
	}
	if (isset($attr['HSPACE']) and $attr['HSPACE']!='') {
		$p['MARGIN-LEFT'] = $attr['HSPACE'];
		$p['MARGIN-RIGHT'] = $attr['HSPACE'];
	}
	//===============================================
	return $p;
}


function GetPagedMediaCSS($tag='') {
	$tag = strtoupper($tag);
	$first = array();
	$p = array();
	$p['SIZE'] = 'AUTO';
	$p['ISSET']=false; 

	// Uses mPDF original margins as default
	$p['MARGIN-RIGHT'] = strval($this->orig_rMargin).'mm';
	$p['MARGIN-LEFT'] = strval($this->orig_lMargin).'mm';
	$p['MARGIN-TOP'] = strval($this->orig_tMargin).'mm';
	$p['MARGIN-BOTTOM'] = strval($this->orig_bMargin).'mm';
	$p['MARGIN-HEADER'] = strval($this->orig_hMargin).'mm';
	$p['MARGIN-FOOTER'] = strval($this->orig_fMargin).'mm';


	// Basic page + selector
	if (isset($this->CSS['@PAGE'])) { $zp = $this->CSS['@PAGE']; }
	else { $zp = array(); }
	if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }
	// If right/Odd page
	if (isset($this->CSS['@PAGE>>PSEUDO>>RIGHT'])) { $zp = $this->CSS['@PAGE>>PSEUDO>>RIGHT']; }
	else { $zp = array(); }
	if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }

	// If left/Even page
	if (isset($this->CSS['@PAGE>>PSEUDO>>RIGHT'])) { $zp = $this->CSS['@PAGE>>PSEUDO>>LEFT']; }
	else { $zp = array(); }
	// Mirror the values
	if (isset($zp['MARGIN-LEFT']) || isset($zp['MARGIN-RIGHT'])) { 
		if (isset($zp['MARGIN-RIGHT'])) { $tmp = $zp['MARGIN-RIGHT']; }
		else { $tmp = '0'; }
		if (isset($zp['MARGIN-LEFT'])) { $zp['MARGIN-RIGHT'] = $zp['MARGIN-LEFT']; }
		else { $zp['MARGIN-RIGHT'] = '0'; }
		$zp['MARGIN-LEFT'] = $tmp; 
	}
	if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }

	// If first page
	if (isset($this->CSS['@PAGE>>PSEUDO>>FIRST'])) { $zp = $this->CSS['@PAGE>>PSEUDO>>FIRST']; }
	else { $zp = array(); }
	if (is_array($zp) && !empty($zp)) { $first = array_merge($p,$zp); $p['ISSET']=true; }
	if ($tag) {
		// If named page
		if (isset($this->CSS['@PAGE>>NAMED>>'.$tag])) { $zp = $this->CSS['@PAGE>>NAMED>>'.$tag]; }
		else { $zp = array(); }
		if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }

		// If named right/Odd page
		if (isset($this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>RIGHT'])) { $zp = $this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>RIGHT']; }
		else { $zp = array(); }
		if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }

		// If named left/Even page
		if (isset($this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>LEFT'])) { $zp = $this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>LEFT']; }
		else { $zp = array(); }
		if (isset($zp['MARGIN-LEFT']) || isset($zp['MARGIN-RIGHT'])) { 
			if (isset($zp['MARGIN-RIGHT'])) { $tmp = $zp['MARGIN-RIGHT']; }
			else { $tmp = '0'; }
			if (isset($zp['MARGIN-LEFT'])) { $zp['MARGIN-RIGHT'] = $zp['MARGIN-LEFT']; }
			else { $zp['MARGIN-RIGHT'] = '0'; }
			$zp['MARGIN-LEFT'] = $tmp; 
		}
		if (is_array($zp) && !empty($zp)) { $p = array_merge($p,$zp); $p['ISSET']=true; }

		// If named first page
		if (isset($this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>FIRST'])) { $zp = $this->CSS['@PAGE>>NAMED>>'.$tag.'>>PSEUDO>>FIRST']; }
		else { $zp = array(); }
		if (is_array($zp) && !empty($zp)) { $first = array_merge($first,$p,$zp); $p['ISSET']=true; }
	}
	if (isset($p['SIZE']) && is_array($p['SIZE'])) {
		if ($p['SIZE']['W'] > $p['SIZE']['H']) { $p['ORIENTATION'] = 'L'; $p['ISSET']=true; }
		else { $p['ORIENTATION'] = 'P'; }
	}
	$p['FIRST'] = $first;
	return $p;
}


function SetPagedMediaCSS() {
		$pm = $this->page_box['CSS'];	
		$bg = array();
		if (is_array($pm['SIZE'])) {
			if ($pm['SIZE']['W'] > $this->wPt/$this->k) { $pm['SIZE']['W'] = $this->wPt/$this->k; }
			if ($pm['SIZE']['H'] > $this->hPt/$this->k) { $pm['SIZE']['H'] = $this->hPt/$this->k; }
			if ($pm['ORIENTATION']==$this->DefOrientation) {
				$outer_width_LR = ($this->wPt/$this->k - $pm['SIZE']['W'])/2;
				$outer_width_TB = ($this->hPt/$this->k - $pm['SIZE']['H'])/2;
			}
			else {
				$outer_width_LR = ($this->hPt/$this->k - $pm['SIZE']['W'])/2;
				$outer_width_TB = ($this->wPt/$this->k - $pm['SIZE']['H'])/2;
			}
			$pgw = $pm['SIZE']['W'];
			$pgh = $pm['SIZE']['H'];
		}
		else {	// AUTO LANDSCAPE PORTRAIT
			$outer_width_LR = 0;
			$outer_width_TB = 0;
			if (strtoupper($pm['SIZE']) == 'AUTO') { $pm['ORIENTATION']=$this->DefOrientation; }
			else if (strtoupper($pm['SIZE']) == 'LANDSCAPE') { $pm['ORIENTATION']='L'; }
			else { $pm['ORIENTATION']='P'; }
			if ($pm['ORIENTATION']==$this->DefOrientation) {
				$pgw = $this->wPt/$this->k;
				$pgh = $this->hPt/$this->k;
			}
			else {
				$pgw = $this->hPt/$this->k;
				$pgh = $this->wPt/$this->k;
			}
		}

		$ohname = $ehname = $ofname = $efname = '';
		if (isset($pm['ODD-HEADER-NAME']) && $pm['ODD-HEADER-NAME']) { $ohname = $pm['ODD-HEADER-NAME']; }
		if (isset($pm['EVEN-HEADER-NAME']) && $pm['EVEN-HEADER-NAME']) { $ehname = $pm['EVEN-HEADER-NAME']; }
		if (isset($pm['ODD-FOOTER-NAME']) && $pm['ODD-FOOTER-NAME']) { $ofname = $pm['ODD-FOOTER-NAME']; }
		if (isset($pm['EVEN-FOOTER-NAME']) && $pm['EVEN-FOOTER-NAME']) { $efname = $pm['EVEN-FOOTER-NAME']; }

		// mPDF 3.0
		if (isset($pm['BACKGROUND-COLOR']) && $pm['BACKGROUND-COLOR']) { $bg['BACKGROUND-COLOR'] = $pm['BACKGROUND-COLOR']; }

		if (isset($pm['MARGIN-LEFT'])) { $mgl = $this->ConvertSize($pm['MARGIN-LEFT'],$pgw) + $outer_width_LR; }
		if (isset($pm['MARGIN-RIGHT'])) { $mgr = $this->ConvertSize($pm['MARGIN-RIGHT'],$pgw) + $outer_width_LR; }
		if (isset($pm['MARGIN-BOTTOM'])) { $mgb = $this->ConvertSize($pm['MARGIN-BOTTOM'],$pgh) + $outer_width_TB; }
		if (isset($pm['MARGIN-TOP'])) { $mgt = $this->ConvertSize($pm['MARGIN-TOP'],$pgh) + $outer_width_TB; }
		if (isset($pm['MARGIN-HEADER'])) { $mgh = $this->ConvertSize($pm['MARGIN-HEADER'],$pgh) + $outer_width_TB; }
		if (isset($pm['MARGIN-FOOTER'])) { $mgf = $this->ConvertSize($pm['MARGIN-FOOTER'],$pgh) + $outer_width_TB; }
		$mgtfp = 0;	// first page margintop (difference from mgt)
		$mgbfp = 0;	// first page marginbottom (difference from mgb)

		if (isset($pm['ORIENTATION']) && $pm['ORIENTATION']) { $orientation = $pm['ORIENTATION']; }
		$this->page_box['outer_width_LR'] = $outer_width_LR;
		$this->page_box['outer_width_TB'] = $outer_width_TB;
		if ($pm['FIRST']) {
			if ($pm['FIRST']['MARGIN-BOTTOM']) { $mgbfp = $this->ConvertSize($pm['FIRST']['MARGIN-BOTTOM'],$pgh) + $outer_width_TB - $mgb; }
			if ($pm['FIRST']['MARGIN-TOP']) { $mgtfp = $this->ConvertSize($pm['FIRST']['MARGIN-TOP'],$pgh) + $outer_width_TB - $mgt; }
		}
		return array($orientation,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$mgtfp,$mgbfp,$ohname,$ehname,$ofname,$efname,$bg);
}

function PreviewBlockCSS($tag,$attr) {
	// Looks ahead from current block level to a new level
	$p = array();
	$zp = array(); 
	$oldcascadeCSS = $this->blk[$this->blklvl]['cascadeCSS'];
	$classes = array();
	if (isset($attr['CLASS'])) { $classes = preg_split('/\s+/',$attr['CLASS']); }
	//===============================================
	// DEFAULT for this TAG set in DefaultCSS
	if (isset($this->defaultCSS[$tag])) { 
		$zp = $this->fixCSS($this->defaultCSS[$tag]);
		if (is_array($zp)) { $p = array_merge($zp,$p); }	// Inherited overwrites default
	}
	// STYLESHEET TAG e.g. h1  p  div  table
	if (isset($this->CSS[$tag])) { 
		$zp = $this->CSS[$tag];
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	// STYLESHEET CLASS e.g. .smallone{}  .redletter{}
	foreach($classes AS $class) {
		$zp = array(); 
		if (isset($this->CSS['CLASS>>'.$class])) { $zp = $this->CSS['CLASS>>'.$class]; }
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	// STYLESHEET ID e.g. #smallone{}  #redletter{}
	if (isset($attr['ID']) && isset($this->CSS['ID>>'.$attr['ID']])) {
		$zp = $this->CSS['ID>>'.$attr['ID']];
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	// STYLESHEET CLASS e.g. p.smallone{}  div.redletter{}
	foreach($classes AS $class) {
		$zp = array(); 
		if (isset($this->CSS[$tag.'>>CLASS>>'.$class])) { $zp = $this->CSS[$tag.'>>CLASS>>'.$class]; }
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	// STYLESHEET CLASS e.g. p#smallone{}  div#redletter{}
	if (isset($attr['ID']) && isset($this->CSS[$tag.'>>ID>>'.$attr['ID']])) {
		$zp = $this->CSS[$tag.'>>ID>>'.$attr['ID']];
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	//===============================================
	// STYLESHEET TAG e.g. div h1    div p
	// mPDF 4.0
	$this->_set_mergedCSS($oldcascadeCSS[$tag], $p);
	// STYLESHEET CLASS e.g. .smallone{}  .redletter{}
	foreach($classes AS $class) {
	  // mPDF 4.0
	  $this->_set_mergedCSS($oldcascadeCSS['CLASS>>'.$class], $p);
	}
	// STYLESHEET CLASS e.g. #smallone{}  #redletter{}
	if (isset($attr['ID'])) {
	  // mPDF 4.0
	  $this->_set_mergedCSS($oldcascadeCSS['ID>>'.$attr['ID']], $p);
	}
	// STYLESHEET CLASS e.g. div.smallone{}  p.redletter{}
	foreach($classes AS $class) {
	  // mPDF 4.0
	  $this->_set_mergedCSS($oldcascadeCSS[$tag.'>>CLASS>>'.$class], $p);
	}
	// STYLESHEET CLASS e.g. div#smallone{}  p#redletter{}
	if (isset($attr['ID'])) {
	  // mPDF 4.0
	  $this->_set_mergedCSS($oldcascadeCSS[$tag.'>>ID>>'.$attr['ID']], $p);
	}
	//===============================================
	// INLINE STYLE e.g. style="CSS:property"
	if (isset($attr['STYLE'])) {
		$zp = $this->readInlineCSS($attr['STYLE']);
		if (is_array($zp)) { $p = array_merge($p,$zp); }
	}
	//===============================================
	return $p;
}



// Added mPDF 3.0 Float DIV - CLEAR
function ClearFloats($clear, $blklvl=0) {
	list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($blklvl,true);
	$end = $currpos = ($this->page*1000 + $this->y);
	if ($clear == 'BOTH' && ($l_exists || $r_exists)) {
// mPDF 4.0		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$end = max($l_max, $r_max, $currpos);
	}
	else if ($clear == 'RIGHT' && $r_exists) {
// mPDF 4.0		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$end = max($r_max, $currpos);
	}
	else if ($clear == 'LEFT' && $l_exists ) {
// mPDF 4.0		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$end = max($l_max, $currpos);
	}
	else { return; }
	$old_page = $this->page;
	$new_page = intval($end/1000);
	if ($old_page != $new_page) {
		$s = $this->PrintPageBackgrounds();
		// Writes after the marker so not overwritten later by page background etc.
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
		$this->pageBackgrounds = array();
		$this->page = $new_page;
	}
	$this->ResetMargins();
// mPDF 4.0	$this->Reset();
	$this->pageoutput[$this->page] = array();
	$this->y = (($end*1000) % 1000000)/1000;	// mod changes operands to integers before processing
}


// Added mPDF 3.0 Float DIV
function GetFloatDivInfo($blklvl=0,$clear=false) {
	// If blklvl specified, only returns floats at that level - for ClearFloats
	$l_exists = false;
	$r_exists = false;
	$l_max = 0;
	$r_max = 0;
	$l_width = 0;
	$r_width = 0;
	if (count($this->floatDivs)) {
	  $currpos = ($this->page*1000 + $this->y);
	  foreach($this->floatDivs AS $f) {
	    if (($clear && $f['blockContext'] == $this->blk[$blklvl]['blockContext']) || (!$clear && $currpos >= $f['startpos'] && $currpos < ($f['endpos']-0.001) && $f['blklvl'] > $blklvl && $f['blockContext'] == $this->blk[$blklvl]['blockContext'])) {
		if ($f['side']=='L') {
			$l_exists= true;
			$l_max = max($l_max, $f['endpos']);
			$l_width = max($l_width , $f['w']);
		}
		if ($f['side']=='R') {
			$r_exists= true;
			$r_max = max($r_max, $f['endpos']);
			$r_width = max($r_width , $f['w']);
		}
	    }
	  }
	}
	return array($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width);
}




function OpenTag($tag,$attr)
{

  // What this gets: < $tag $attr['WIDTH']="90px" > does not get content here </closeTag here>
  // Correct tags where HTML specifies optional end tags,
  // and/or does not allow nesting e.g. P inside P, or 
  if ($this->allow_html_optional_endtags) {
    if (($tag == 'P' || $tag == 'DIV' || $tag == 'H1' || $tag == 'H2' || $tag == 'H3' || $tag == 'H4' || $tag == 'H5' || $tag == 'H6' || $tag == 'UL' || $tag == 'OL' || $tag == 'TABLE' || $tag=='PRE' || $tag=='FORM' || $tag=='ADDRESS' || $tag=='BLOCKQUOTE' || $tag=='CENTER' || $tag=='DL' || $tag == 'HR' ) && $this->lastoptionaltag == 'P') { $this->CloseTag($this->lastoptionaltag ); }
    if ($tag == 'DD' && $this->lastoptionaltag == 'DD') { $this->CloseTag($this->lastoptionaltag ); }
    if ($tag == 'DD' && $this->lastoptionaltag == 'DT') { $this->CloseTag($this->lastoptionaltag ); }
    if ($tag == 'DT' && $this->lastoptionaltag == 'DD') { $this->CloseTag($this->lastoptionaltag ); }
    if ($tag == 'DT' && $this->lastoptionaltag == 'DT') { $this->CloseTag($this->lastoptionaltag ); }
    if ($tag == 'LI' && $this->lastoptionaltag == 'LI') { $this->CloseTag($this->lastoptionaltag ); }
    if (($tag == 'TD' || $tag == 'TH') && $this->lastoptionaltag == 'TD') { $this->CloseTag($this->lastoptionaltag ); }	// *TABLES*
    if (($tag == 'TD' || $tag == 'TH') && $this->lastoptionaltag == 'TH') { $this->CloseTag($this->lastoptionaltag ); }	// *TABLES*
    if ($tag == 'TR' && $this->lastoptionaltag == 'TR') { $this->CloseTag($this->lastoptionaltag ); }	// *TABLES*
    if ($tag == 'TR' && $this->lastoptionaltag == 'TD') { $this->CloseTag($this->lastoptionaltag );  $this->CloseTag('TR'); $this->CloseTag('THEAD'); }	// *TABLES*
    if ($tag == 'TR' && $this->lastoptionaltag == 'TH') { $this->CloseTag($this->lastoptionaltag );  $this->CloseTag('TR'); $this->CloseTag('THEAD'); }	// *TABLES*
    if ($tag == 'OPTION' && $this->lastoptionaltag == 'OPTION') { $this->CloseTag($this->lastoptionaltag ); }
  }


  $align = array('left'=>'L','center'=>'C','right'=>'R','top'=>'T','text-top'=>'T','middle'=>'M','baseline'=>'M','bottom'=>'B','text-bottom'=>'B','justify'=>'J');

  $this->ignorefollowingspaces=false;

  //Opening tag
  switch($tag){


     case 'PAGEHEADER': 
     case 'PAGEFOOTER':
	$this->ignorefollowingspaces = true; 
	if ($attr['NAME']) { $pname = $attr['NAME']; }
	else { $pname = '_default'; }

		if ($tag=='PAGEHEADER') { $p = &$this->pageheaders[$pname]; }
		else { $p = &$this->pagefooters[$pname]; }

		$p['L']=array();
		$p['C']=array();
		$p['R']=array();
		$p['L']['font-style'] = ''; 
		$p['C']['font-style'] = ''; 
		$p['R']['font-style'] = ''; 

		if (isset($attr['CONTENT-LEFT'])) {
			$p['L']['content'] = $attr['CONTENT-LEFT'];
		}
		if (isset($attr['CONTENT-CENTER'])) {
			$p['C']['content'] = $attr['CONTENT-CENTER'];
		}
		if (isset($attr['CONTENT-RIGHT'])) {
			$p['R']['content'] = $attr['CONTENT-RIGHT'];
		}

		if (isset($attr['HEADER-STYLE']) || isset($attr['FOOTER-STYLE'])) {	// font-family,size,weight,style,color
			if ($tag=='PAGEHEADER') { $properties = $this->readInlineCSS($attr['HEADER-STYLE']); }
			else { $properties = $this->readInlineCSS($attr['FOOTER-STYLE']); }
			if (isset($properties['FONT-FAMILY'])) { 
				$p['L']['font-family'] = $properties['FONT-FAMILY']; 
				$p['C']['font-family'] = $properties['FONT-FAMILY']; 
				$p['R']['font-family'] = $properties['FONT-FAMILY']; 
			}
			if (isset($properties['FONT-SIZE'])) { 
				$p['L']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; 
				$p['C']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; 
				$p['R']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; 
			}
			if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT']=='BOLD') { 
				$p['L']['font-style'] = 'B'; 
				$p['C']['font-style'] = 'B'; 
				$p['R']['font-style'] = 'B'; 
			}
			if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE']=='ITALIC') { 
				$p['L']['font-style'] .= 'I'; 
				$p['C']['font-style'] .= 'I'; 
				$p['R']['font-style'] .= 'I'; 
			}
			if (isset($properties['COLOR'])) { 
				$p['L']['color'] = $properties['COLOR']; 
				$p['C']['color'] = $properties['COLOR']; 
				$p['R']['color'] = $properties['COLOR']; 
			}
		}
		if (isset($attr['HEADER-STYLE-LEFT']) || isset($attr['FOOTER-STYLE-LEFT'])) {
			if ($tag=='PAGEHEADER') { $properties = $this->readInlineCSS($attr['HEADER-STYLE-LEFT']); }
			else { $properties = $this->readInlineCSS($attr['FOOTER-STYLE-LEFT']); }
			if (isset($properties['FONT-FAMILY'])) { $p['L']['font-family'] = $properties['FONT-FAMILY']; }
			if (isset($properties['FONT-SIZE'])) { $p['L']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; }
			if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT']=='BOLD') { $p['L']['font-style'] ='B'; }
			if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE']=='ITALIC') { $p['L']['font-style'] .='I'; }
			if (isset($properties['COLOR'])) { $p['L']['color'] = $properties['COLOR']; }
		}
		if (isset($attr['HEADER-STYLE-CENTER']) || isset($attr['FOOTER-STYLE-CENTER'])) {
			if ($tag=='PAGEHEADER') { $properties = $this->readInlineCSS($attr['HEADER-STYLE-CENTER']); }
			else { $properties = $this->readInlineCSS($attr['FOOTER-STYLE-CENTER']); }
			if (isset($properties['FONT-FAMILY'])) { $p['C']['font-family'] = $properties['FONT-FAMILY']; }
			if (isset($properties['FONT-SIZE'])) { $p['C']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; }
			if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT']=='BOLD') { $p['C']['font-style'] = 'B'; }
			if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE']=='ITALIC') { $p['C']['font-style'] .= 'I'; }
			if (isset($properties['COLOR'])) { $p['C']['color'] = $properties['COLOR']; }
		}
		if (isset($attr['HEADER-STYLE-RIGHT']) || isset($attr['FOOTER-STYLE-RIGHT'])) {
			if ($tag=='PAGEHEADER') { $properties = $this->readInlineCSS($attr['HEADER-STYLE-RIGHT']); }
			else { $properties = $this->readInlineCSS($attr['FOOTER-STYLE-RIGHT']); }
			if (isset($properties['FONT-FAMILY'])) { $p['R']['font-family'] = $properties['FONT-FAMILY']; }
			if (isset($properties['FONT-SIZE'])) { $p['R']['font-size'] = $this->ConvertSize($properties['FONT-SIZE']) * $this->k; }
			if (isset($properties['FONT-WEIGHT']) && $properties['FONT-WEIGHT']=='BOLD') { $p['R']['font-style'] = 'B'; }
			if (isset($properties['FONT-STYLE']) && $properties['FONT-STYLE']=='ITALIC') { $p['R']['font-style'] .= 'I'; }
			if (isset($properties['COLOR'])) { $p['R']['color'] = $properties['COLOR']; }
		}
		if (isset($attr['LINE']) && $attr['LINE']) {	// 0|1|on|off
			if ($attr['LINE']=='1' || strtoupper($attr['LINE'])=='ON') { $lineset=1; }
			else { $lineset=0; }
			$p['line'] = $lineset;
		}
	break;



     case 'SETPAGEHEADER': 
     case 'SETPAGEFOOTER':
	$this->ignorefollowingspaces = true; 
	if (isset($attr['NAME']) && $attr['NAME']) { $pname = $attr['NAME']; }
	else { $pname = '_default'; }
	if (isset($attr['PAGE']) && $attr['PAGE']) { 	// O|odd|even|E|ALL|[blank]
		if (strtoupper($attr['PAGE'])=='O' || strtoupper($attr['PAGE'])=='ODD') { $side='odd'; }
		else if (strtoupper($attr['PAGE'])=='E' || strtoupper($attr['PAGE'])=='EVEN') { $side='even'; }
		else if (strtoupper($attr['PAGE'])=='ALL') { $side='both'; }
		else { $side='odd'; }
	}
	else { $side='odd'; }
	if (isset($attr['VALUE']) && $attr['VALUE']) { 	// -1|1|on|off
		if ($attr['VALUE']=='1' || strtoupper($attr['VALUE'])=='ON') { $set=1; }
		else if ($attr['VALUE']=='-1' || strtoupper($attr['VALUE'])=='OFF') { $set=0; }
		else { $set=1; }
	}
	else { $set=1; }
	if ($side=='odd' || $side=='both') {
		if ($set && $tag=='SETPAGEHEADER') { $this->headerDetails['odd'] = $this->pageheaders[$pname]; }
		else if ($set && $tag=='SETPAGEFOOTER') { $this->footerDetails['odd'] = $this->pagefooters[$pname]; }
		else if ($tag=='SETPAGEHEADER') { $this->headerDetails['odd'] = array(); }
		else { $this->footerDetails['odd'] = array(); }
		// mPDF 4.0
		if (!$this->mirrorMargins || ($this->page)%2!=0) {	// ODD
			if ($tag=='SETPAGEHEADER') { $this->_setAutoHeaderHeight($this->headerDetails['odd'],$this->HTMLHeader); }
			if ($tag=='SETPAGEFOOTER') { $this->_setAutoFooterHeight($this->footerDetails['odd'],$this->HTMLFooter); }
		}
	}
	if ($side=='even' || $side=='both') {
		if ($set && $tag=='SETPAGEHEADER') { $this->headerDetails['even'] = $this->pageheaders[$pname]; }
		else if ($set && $tag=='SETPAGEFOOTER') { $this->footerDetails['even'] = $this->pagefooters[$pname]; }
		else if ($tag=='SETPAGEHEADER') { $this->headerDetails['even'] = array(); }
		else { $this->footerDetails['even'] = array(); }
		// mPDF 4.0
		if ($this->mirrorMargins && ($this->page)%2==0) {	// EVEN
			if ($tag=='SETPAGEHEADER') { $this->_setAutoHeaderHeight($this->headerDetails['even'],$this->HTMLHeaderE); }
			if ($tag=='SETPAGEFOOTER') { $this->_setAutoFooterHeight($this->footerDetails['even'],$this->HTMLFooterE); }
		}
	}
	if (isset($attr['SHOW-THIS-PAGE']) && $attr['SHOW-THIS-PAGE'] && $tag=='SETPAGEHEADER') {
		$this->Header();
	}
	break;




    case 'PAGE_BREAK': //custom-tag
    case 'PAGEBREAK': //custom-tag
    case 'NEWPAGE': //custom-tag
    case 'FORMFEED': //custom-tag

	$save_blklvl = $this->blklvl;
	$save_blk = $this->blk;
	$save_silp = $this->saveInlineProperties();
	$save_spanlvl = $this->spanlvl;
	$save_ilp = $this->InlineProperties;

	// Close any open block tags
	for ($b= $this->blklvl;$b>0;$b--) { $this->CloseTag($this->blk[$b]['tag']); }
	if(!empty($this->textbuffer))  {	//Output previously buffered content
   	  	$this->printbuffer($this->textbuffer);
        	$this->textbuffer=array(); 
      }
	$this->ignorefollowingspaces = true;
	$save_cols = false;
	$mgr = $mgl = $mgt = $mgb = $mgh = $mgf = '';
	if (isset($attr['MARGIN-RIGHT'])) { $mgr = $this->ConvertSize($attr['MARGIN-RIGHT'],$this->w,$this->FontSize,false); }
	if (isset($attr['MARGIN-LEFT'])) { $mgl = $this->ConvertSize($attr['MARGIN-LEFT'],$this->w,$this->FontSize,false); }
	if (isset($attr['MARGIN-TOP'])) { $mgt = $this->ConvertSize($attr['MARGIN-TOP'],$this->w,$this->FontSize,false); }
	if (isset($attr['MARGIN-BOTTOM'])) { $mgb = $this->ConvertSize($attr['MARGIN-BOTTOM'],$this->w,$this->FontSize,false); }
	if (isset($attr['MARGIN-HEADER'])) { $mgh = $this->ConvertSize($attr['MARGIN-HEADER'],$this->w,$this->FontSize,false); }
	if (isset($attr['MARGIN-FOOTER'])) { $mgf = $this->ConvertSize($attr['MARGIN-FOOTER'],$this->w,$this->FontSize,false); }
	$ohname = $ehname = $ofname = $efname = '';
	if (isset($attr['ODD-HEADER-NAME'])) { $ohname = $attr['ODD-HEADER-NAME']; }
	if (isset($attr['EVEN-HEADER-NAME'])) { $ehname = $attr['EVEN-HEADER-NAME']; }
	if (isset($attr['ODD-FOOTER-NAME'])) { $ofname = $attr['ODD-FOOTER-NAME']; }
	if (isset($attr['EVEN-FOOTER-NAME'])) { $efname = $attr['EVEN-FOOTER-NAME']; }
	$ohvalue = $ehvalue = $ofvalue = $efvalue = 0;
	if (isset($attr['ODD-HEADER-VALUE']) && ($attr['ODD-HEADER-VALUE']=='1' || strtoupper($attr['ODD-HEADER-VALUE'])=='ON')) { $ohvalue = 1; }
	else if (isset($attr['ODD-HEADER-VALUE']) && ($attr['ODD-HEADER-VALUE']=='-1' || strtoupper($attr['ODD-HEADER-VALUE'])=='OFF')) { $ohvalue = -1; }
	if (isset($attr['EVEN-HEADER-VALUE']) && ($attr['EVEN-HEADER-VALUE']=='1' || strtoupper($attr['EVEN-HEADER-VALUE'])=='ON')) { $ehvalue = 1; }
	else if (isset($attr['EVEN-HEADER-VALUE']) && ($attr['EVEN-HEADER-VALUE']=='-1' || strtoupper($attr['EVEN-HEADER-VALUE'])=='OFF')) { $ehvalue = -1; }
	if (isset($attr['ODD-FOOTER-VALUE']) && ($attr['ODD-FOOTER-VALUE']=='1' || strtoupper($attr['ODD-FOOTER-VALUE'])=='ON')) { $ofvalue = 1; }
	else if (isset($attr['ODD-FOOTER-VALUE']) && ($attr['ODD-FOOTER-VALUE']=='-1' || strtoupper($attr['ODD-FOOTER-VALUE'])=='OFF')) { $ofvalue = -1; }
	if (isset($attr['EVEN-FOOTER-VALUE']) && ($attr['EVEN-FOOTER-VALUE']=='1' || strtoupper($attr['EVEN-FOOTER-VALUE'])=='ON')) { $efvalue = 1; }
	else if (isset($attr['EVEN-FOOTER-VALUE']) && ($attr['EVEN-FOOTER-VALUE']=='-1' || strtoupper($attr['EVEN-FOOTER-VALUE'])=='OFF')) { $efvalue = -1; }

	if (isset($attr['ORIENTATION']) && (strtoupper($attr['ORIENTATION'])=='L' || strtoupper($attr['ORIENTATION'])=='LANDSCAPE')) { $orient = 'L'; }
	else if (isset($attr['ORIENTATION']) && (strtoupper($attr['ORIENTATION'])=='P' || strtoupper($attr['ORIENTATION'])=='PORTRAIT')) { $orient = 'P'; }
	else { $orient = $this->CurOrientation; }
	$resetpagenum = '';
	$pagenumstyle = '';
	$suppress = '';
	if (isset($attr['RESETPAGENUM'])) { $resetpagenum = $attr['RESETPAGENUM']; }
	if (isset($attr['PAGENUMSTYLE'])) { $pagenumstyle = $attr['PAGENUMSTYLE']; }
	if (isset($attr['SUPPRESS'])) { $suppress = $attr['SUPPRESS']; }

	if ($tag == 'TOCPAGEBREAK') { $type = 'NEXT-ODD'; }
	else if(isset($attr['TYPE'])) { $type = strtoupper($attr['TYPE']); }
	else { $type = ''; }

	if ($type == 'E' || $type == 'EVEN') { $this->AddPage($orient,'E', $resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue); }
	else if ($type == 'O' || $type == 'ODD') { $this->AddPage($orient,'O', $resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue); }
	else if ($type == 'NEXT-ODD') { $this->AddPage($orient,'NEXT-ODD', $resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue); }
	else if ($type == 'NEXT-EVEN') { $this->AddPage($orient,'NEXT-EVEN', $resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue); }
	else { $this->AddPage($orient,'', $resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue); }


	if (($tag == 'FORMFEED' || $this->restoreBlockPagebreaks) && !$this->tableLevel && !$this->listlvl) {
		$this->blk = $save_blk;
		// Re-open block tags
		$t = $this->blk[0]['tag'];
		$a = $this->blk[0]['attr'];
		$this->blklvl = 0; 
		for ($b=0; $b<=$save_blklvl;$b++) {
			$tc = $t;
			$ac = $a;
			$t = $this->blk[$b+1]['tag'];
			$a = $this->blk[$b+1]['attr'];
			unset($this->blk[$b+1]);
			$this->OpenTag($tc,$ac); 
		}
		$this->spanlvl = $save_spanlvl;
		$this->InlineProperties = $save_ilp;
		$this->restoreInlineProperties($save_silp);
	}

	break;










    case 'BDO':
	$this->biDirectional = true;
	break;


    case 'TTZ':
	$this->ttz = true;
	$this->InlineProperties[$tag] = $this->saveInlineProperties();
	$this->setCSS(array('FONT-FAMILY'=>'zapfdingbats','FONT-WEIGHT'=>'normal','FONT-STYLE'=>'normal'),'INLINE');
	break;

    case 'TTS':
	$this->tts = true;
	$this->InlineProperties[$tag] = $this->saveInlineProperties();
	$this->setCSS(array('FONT-FAMILY'=>'symbol','FONT-WEIGHT'=>'normal','FONT-STYLE'=>'normal'),'INLINE');
	break;

    case 'TTA':
	$this->tta = true;
	$this->InlineProperties[$tag] = $this->saveInlineProperties();
	// mPDF 4.0
	if (in_array($this->FontFamily,$this->mono_fonts)) {
		$this->setCSS(array('FONT-FAMILY'=>'courier-embedded'),'INLINE');
	}
	else if (in_array($this->FontFamily,$this->serif_fonts)) { 
		$this->setCSS(array('FONT-FAMILY'=>'times-embedded'),'INLINE');
	}
	else {
		$this->setCSS(array('FONT-FAMILY'=>'helvetica-embedded'),'INLINE');
	}
	break;



    // INLINE PHRASES OR STYLES
    case 'SUB':
    case 'SUP':
    case 'ACRONYM':
    case 'BIG':
    case 'SMALL':
    case 'INS':
    case 'S':
    case 'STRIKE':
    case 'DEL':
    case 'STRONG':
    case 'CITE':
    case 'Q':
    case 'EM':
    case 'B':
    case 'I':
    case 'U':
    case 'SAMP':
    case 'CODE':
    case 'KBD':
    case 'TT':
    case 'VAR':
    case 'FONT':
    case 'SPAN':

	if ($tag == 'SPAN') {
		$this->spanlvl++;
		$this->InlineProperties['SPAN'][$this->spanlvl] = $this->saveInlineProperties();
	}
	else { 
		$this->InlineProperties[$tag] = $this->saveInlineProperties(); 
	}
	$properties = $this->MergeCSS('INLINE',$tag,$attr);	// mPDF 4.0
	if (!empty($properties)) $this->setCSS($properties,'INLINE');
	break;


    case 'A':
	if (isset($attr['NAME']) and $attr['NAME'] != '') { 
		// mPDF 3.0
		$e = '';
		if($this->tableLevel) {	// *TABLES*
			$this->cell[$this->row][$this->col]['textbuffer'][] = array($e,'','',array(),'',false,false,$attr['NAME']); 	// *TABLES*
		}	// *TABLES*
		else  {	// *TABLES*
			$this->textbuffer[] = array($e,'','',array(),'',false,false,$attr['NAME']); //an internal link (adds a space for recognition)
		}	// *TABLES*
	}
	if (isset($attr['HREF'])) { 
		$this->InlineProperties['A'] = $this->saveInlineProperties();
		$properties = $this->MergeCSS('',$tag,$attr);
		if (!empty($properties)) $this->setCSS($properties,'INLINE');
		$this->HREF=$attr['HREF'];
	}
	break;



    case 'BR':
	// Added mPDF 3.0 Float DIV - CLEAR
	if (isset($attr['STYLE'])) {
		$properties = $this->readInlineCSS($attr['STYLE']);
		if (isset($properties['CLEAR'])) { $this->ClearFloats(strtoupper($properties['CLEAR']),$this->blklvl); }	// *CSS-FLOAT*
	}


	if($this->tableLevel) {
	   // mPDF 3.0
	   if ($this->blockjustfinished || $this->listjustfinished) {
		$this->cell[$this->row][$this->col]['textbuffer'][] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
		$this->cell[$this->row][$this->col]['text'][] = "\n";
	   }

		$this->cell[$this->row][$this->col]['textbuffer'][] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
		$this->cell[$this->row][$this->col]['text'][] = "\n";
		if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
		}
		elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];  
		}
		$this->cell[$this->row][$this->col]['s'] = 0 ;// reset
	}
	else  {
		$this->textbuffer[] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
	}	// *TABLES*
	$this->ignorefollowingspaces = true; 
	$this->blockjustfinished=false;
	$this->listjustfinished=false;
	// mPDF 3.0
	$this->linebreakjustfinished=true;
	break;


	// *********** BLOCKS  ********************

	//NB $outerblocktags = array('DIV','FORM','CENTER','DL');
	//NB $innerblocktags = array('P','BLOCKQUOTE','ADDRESS','PRE',''H1','H2','H3','H4','H5','H6','DT','DD');

    case 'PRE':
	$this->ispre=true;	// ADDED - Prevents left trim of textbuffer in printbuffer()

    case 'DIV':
    case 'FORM':
    case 'CENTER':

    case 'BLOCKQUOTE':
    case 'ADDRESS': 

    case 'P':
    case 'H1':
    case 'H2':
    case 'H3':
    case 'H4':
    case 'H5':
    case 'H6':
    case 'DL':
    case 'DT':
    case 'DD':

	$p = $this->PreviewBlockCSS($tag,$attr);
	if(isset($p['DISPLAY']) && strtolower($p['DISPLAY'])=='none') { 
		$this->blklvl++;
		$this->blk[$this->blklvl]['hide'] = true; 
		return; 
	}
	// mPDF 4.0
	if ((isset($p['POSITION']) && (strtolower($p['POSITION'])=='fixed' || strtolower($p['POSITION'])=='absolute')) && $this->blklvl==0) { 
		if ($this->inFixedPosBlock) {
			die("ERROR - Cannot nest block with position:fixed or position:absolute"); 
		}
		$this->inFixedPosBlock = true;
		return;
	}
	// Start Block
	$this->ignorefollowingspaces = true; 
	$this->blockjustfinished=false;

	// mPDF 4.0
	if ($this->listlvl>0) { return; }

	$this->InlineProperties = array(); 
	$this->spanlvl = 0;
	$this->listjustfinished=false;
	$this->divbegin=true;
	// mPDF 3.0
	$this->linebreakjustfinished=false;

	if ($this->tableLevel) {
	   // mPDF 3.0
	   // If already something on the line
	   if ($this->cell[$this->row][$this->col]['s'] > 0  && !$this->nestedtablejustfinished ) {
		$this->cell[$this->row][$this->col]['textbuffer'][] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
		$this->cell[$this->row][$this->col]['text'][] = "\n";
		if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
		}
		elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
		}
		$this->cell[$this->row][$this->col]['s'] = 0 ;// reset
	   }
	   // Cannot set block properties inside table - use Bold to indicate h1-h6
	   if ($tag == 'CENTER' && $this->tdbegin) { $this->cell[$this->row][$this->col]['a'] = $align['center']; }

		$this->InlineProperties['BLOCKINTABLE'] = $this->saveInlineProperties();
		$properties = $this->MergeCSS('',$tag,$attr);
		if (!empty($properties)) $this->setCSS($properties,'INLINE');


	   break;
	}

	if ($tag == 'P' || $tag == 'DT' || $tag == 'DD') { $this->lastoptionaltag = $tag; } // Save current HTML specified optional endtag
	else { $this->lastoptionaltag = ''; }


	if ($this->lastblocklevelchange == 1) { $blockstate = 1; }	// Top margins/padding only
	else if ($this->lastblocklevelchange < 1) { $blockstate = 0; }	// NO margins/padding
	$this->printbuffer($this->textbuffer,$blockstate);
	$this->textbuffer=array();

	$this->blklvl++;
	// mPDF 4.0
	$currblk =& $this->blk[$this->blklvl];
	$this->initialiseBlock($currblk);
	$prevblk =& $this->blk[$this->blklvl-1];

	$currblk['tag'] = $tag;
	$currblk['attr'] = $attr;

	$this->Reset();
	$properties = $this->MergeCSS('BLOCK',$tag,$attr);

	if (isset($properties['PAGE'])) { $pmtag = $properties['PAGE']; } else { $pmtag = ''; }	// *CSS-PAGE*
	$pm = $this->GetPagedMediaCSS($pmtag);	// *CSS-PAGE*

	if (isset($this->page_box['name'])) { $this->page_box['last_name'] = $this->page_box['name'] ; }	// *CSS-PAGE*
	else { $this->page_box['last_name'] = ''; }	// *CSS-PAGE*
	$this->page_box['name'] = $pmtag;	// *CSS-PAGE*

	// If page-box has changed AND/OR PAGE-BREAK-BEFORE
	$save_cols = false;
	if (($pmtag && isset($this->page_box['last_name']) && $this->page_box['last_name'] != $this->page_box['name']) || (isset($properties['PAGE-BREAK-BEFORE']) && $properties['PAGE-BREAK-BEFORE'])) {
		if ($this->blklvl>1) {
			// Close any open block tags
			for ($b= $this->blklvl;$b>0;$b--) { $this->CloseTag($this->blk[$b]['tag']); }
			// Output any text left in buffer
			if (count($this->textbuffer)) { $this->printbuffer($this->textbuffer); $this->textbuffer=array(); }
		}

		if ($pmtag && isset($this->page_box['last_name']) && $this->page_box['last_name'] != $this->page_box['name']) {
			$this->page_box['changed'] = true;
			// Set orientation and page size/margins
			$this->page_box['CSS'] = $pm;
		}

		// Must Add new page if changed page properties
		if (isset($properties['PAGE-BREAK-BEFORE'])) {
			if (strtoupper($properties['PAGE-BREAK-BEFORE']) == 'RIGHT') { $this->AddPage($this->CurOrientation,'NEXT-ODD'); }
			else if (strtoupper($properties['PAGE-BREAK-BEFORE']) == 'LEFT') { $this->AddPage($this->CurOrientation,'NEXT-EVEN'); }
			else if (strtoupper($properties['PAGE-BREAK-BEFORE']) == 'ALWAYS') { $this->AddPage(); }
			else if (isset($this->page_box['changed']) && $this->page_box['changed']) { $this->AddPage(); }	// *CSS-PAGE*
		}
		else if (isset($this->page_box['changed']) && $this->page_box['changed']) { $this->AddPage(); }
		$this->page_box['changed'] = false;	// Also Set back to false in AddPage

		// if using htmlheaders, the headers need to be rewritten when new page
		// done by calling WriteHTML() within resethtmlheaders
		// so block is reset to 0 - now we need to resurrect it
		// As in WriteHTML() initialising
		$this->blklvl = 0;
		$this->lastblocklevelchange = 0;
		$this->blk = array();
		// mPDF 4.0
		$this->initialiseBlock($this->blk[0]);
		$this->blk[0]['width'] =& $this->pgwidth;
		$this->blk[0]['inner_width'] =& $this->pgwidth;
		// mPDF 3.0
		$this->blk[0]['blockContext'] = $this->blockContext;
		$properties = $this->MergeCSS('BLOCK','BODY','');
		$this->setCSS($properties,'','BODY'); 
		$this->blklvl++;
		// mPDF 4.0
		$currblk =& $this->blk[$this->blklvl];
		$prevblk =& $this->blk[$this->blklvl-1];

		$this->initialiseBlock($currblk);
		$currblk['tag'] = $tag;
		$currblk['attr'] = $attr;

		$this->Reset();
		$properties = $this->MergeCSS('BLOCK',$tag,$attr);
	}


	if (isset($properties['PAGE-BREAK-INSIDE']) && strtoupper($properties['PAGE-BREAK-INSIDE']) == 'AVOID' && !$this->ColActive && !$this->keep_block_together) {
		$currblk['keep_block_together'] = 1;
		$currblk['y00'] = $this->y;
		$this->keep_block_together = 1;
		$this->divbuffer = array();
		$this->ktLinks = array();
		$this->ktAnnots = array();
		$this->ktBlock = array();
		$this->ktReference = array();
		$this->ktBMoutlines = array();
		$this->_kttoc = array();
	}

	$this->setCSS($properties,'BLOCK',$tag); //name(id/class/style) found in the CSS array!
	$currblk['InlineProperties'] = $this->saveInlineProperties();


	if(isset($attr['ALIGN']) && $attr['ALIGN']) { $currblk['block-align'] = $align[strtolower($attr['ALIGN'])]; }


	// mPDF 4.0 height
	if (isset($properties['HEIGHT'])) { $currblk['css_set_height'] = $this->ConvertSize($properties['HEIGHT'],($this->h - $this->tMargin - $this->bMargin),$this->FontSize,false); }
	else { $currblk['css_set_height'] = false; }


	// Added mPDF 3.0 Float DIV
	if (isset($prevblk['blockContext'])) { $currblk['blockContext'] = $prevblk['blockContext'] ; }	// *CSS-FLOAT*

	if (isset($properties['CLEAR'])) { $this->ClearFloats(strtoupper($properties['CLEAR']), $this->blklvl-1); }	// *CSS-FLOAT*

	$container_w = $prevblk['inner_width'];
	$bdr = $currblk['border_right']['w'];
	$bdl = $currblk['border_left']['w'];
	$pdr = $currblk['padding_right'];
	$pdl = $currblk['padding_left'];

	if (isset($currblk['css_set_width'])) { $setwidth = $currblk['css_set_width']; }
	else { $setwidth = 0; }

	if (isset($properties['FLOAT']) && strtoupper($properties['FLOAT']) == 'RIGHT' && !$this->ColActive) {
		// Cancel Keep-Block-together
		$currblk['keep_block_together'] = false;
		$currblk['y00'] = '';
		$this->keep_block_together = 0;

		$this->blockContext++;
		$currblk['blockContext'] = $this->blockContext;

		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);

		// DIV is too narrow for text to fit!
		$maxw = $container_w - $l_width - $r_width;
		if (($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) < $this->GetStringWidth('WW')) { 
			// Too narrow to fit - try to move down past L or R float
			if ($l_max < $r_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('LEFT', $this->blklvl-1); 
			}
			else if ($r_max < $l_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)  <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('RIGHT', $this->blklvl-1); 
			}
			else { $this->ClearFloats('BOTH', $this->blklvl-1); }
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);
		}

		if ($r_exists) { $currblk['margin_right'] += $r_width; }

		$currblk['float'] = 'R';
		$currblk['float_start_y'] = $this->y;
		if ($currblk['css_set_width']) {
			$currblk['margin_left'] = $container_w - ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
			$currblk['float_width'] = ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
		}
		else {
			// *** If no width set - would need to buffer and keep track of max width, then Right-align if not full width
			// and do borders and backgrounds - For now - just set to maximum width left

			if ($l_exists) { $currblk['margin_left'] += $l_width; }
			$currblk['css_set_width'] = $container_w - ($currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr);

			$currblk['float_width'] = ($currblk['css_set_width'] + $bdl + $pdl + $bdr + $pdr + $currblk['margin_right']);
		}
	}
	else if (isset($properties['FLOAT']) && strtoupper($properties['FLOAT']) == 'LEFT' && !$this->ColActive) {
		// Cancel Keep-Block-together
		$currblk['keep_block_together'] = false;
		$currblk['y00'] = '';
		$this->keep_block_together = 0;

		$this->blockContext++;
		$currblk['blockContext'] = $this->blockContext;

		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);

		// DIV is too narrow for text to fit!
		$maxw = $container_w - $l_width - $r_width;
		if (($setwidth + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) < $this->GetStringWidth('WW')) { 
			// Too narrow to fit - try to move down past L or R float
			if ($l_max < $r_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('LEFT', $this->blklvl-1); 
			}
			else if ($r_max < $l_max && ($setwidth + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('RIGHT', $this->blklvl-1); 
			}
			else { $this->ClearFloats('BOTH', $this->blklvl-1); }
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);
		}

		if ($l_exists) { $currblk['margin_left'] += $l_width; }

		$currblk['float'] = 'L';
		$currblk['float_start_y'] = $this->y;
		if ($setwidth) {
			$currblk['margin_right'] = $container_w - ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
			$currblk['float_width'] = ($setwidth + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
		}
		else {
			// *** If no width set - would need to buffer and keep track of max width, then Right-align if not full width
			// and do borders and backgrounds - For now - just set to maximum width left

			if ($r_exists) { $currblk['margin_right'] += $r_width; }
			$currblk['css_set_width'] = $container_w - ($currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr);

			$currblk['float_width'] = ($currblk['css_set_width'] + $bdl + $pdl + $bdr + $pdr + $currblk['margin_left']);
		}
	}

	else {
		// Don't allow overlap - if floats present - adjust padding to avoid overlap with Floats
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);
		$maxw = $container_w - $l_width - $r_width;
		if (($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) > $maxw || ($maxw - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) < $this->GetStringWidth('WW')) { 
			// Too narrow to fit - try to move down past L or R float
			if ($l_max < $r_max && ($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $r_width) && (($container_w - $r_width) - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('LEFT', $this->blklvl-1); 
			}
			else if ($r_max < $l_max && ($setwidth + $currblk['margin_left'] + $currblk['margin_right'] + $bdl + $pdl + $bdr + $pdr) <= ($container_w - $l_width) && (($container_w - $l_width) - ($currblk['margin_right'] + $currblk['margin_left'] + $bdl + $pdl + $bdr + $pdr)) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('RIGHT', $this->blklvl-1); 
			}
			else { $this->ClearFloats('BOTH', $this->blklvl-1); }
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl-1);
		}
		if ($r_exists) { $currblk['padding_right'] = max(($r_width-$currblk['margin_right']-$bdr), $pdr); }
		if ($l_exists) { $currblk['padding_left'] = max(($l_width-$currblk['margin_left']-$bdl), $pdl); }
	}




	// Hanging indent - if negative indent: ensure padding is >= indent
	// mPDF 4.0
	$cbti = $this->ConvertSize($currblk['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
	if ($cbti < 0) {
	  $hangind = -($cbti);
		$currblk['padding_left'] = max($currblk['padding_left'],$hangind);
	}

	if (isset($currblk['css_set_width'])) {
	  if (isset($properties['MARGIN-LEFT']) && isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-LEFT'])=='auto' && strtolower($properties['MARGIN-RIGHT'])=='auto') { 
		  // Try to reduce margins to accomodate - if still too wide, set margin-right/left=0 (reduces width)
		  $anyextra = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);
		  if ($anyextra>0) {
			$currblk['margin_left'] = $currblk['margin_right'] = $anyextra /2;
		  }
		  else {
			$currblk['margin_left'] = $currblk['margin_right'] = 0;
		  }
	  }
	  else if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT'])=='auto') { 
		  // Try to reduce margin-left to accomodate - if still too wide, set margin-left=0 (reduces width)
		  $currblk['margin_left'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_right']);
		  if ($currblk['margin_left'] < 0) {
			$currblk['margin_left'] = 0;
		  }
	  }
	  else if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT'])=='auto') { 
		  // Try to reduce margin-right to accomodate - if still too wide, set margin-right=0 (reduces width)
		  $currblk['margin_right'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_left']);
		  if ($currblk['margin_right'] < 0) {
			$currblk['margin_right'] = 0;
		  }
	  }
	  else { 
		// Try to reduce margin-left to accomodate - if still too wide, set margin-left=0 (reduces width)
		  // Try to reduce margin-right to accomodate - if still too wide, set margin-right=0 (reduces width)
		  $currblk['margin_right'] = $prevblk['inner_width'] - ($currblk['css_set_width'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right'] + $currblk['margin_left']);
		  if ($currblk['margin_right'] < 0) {
			$currblk['margin_right'] = 0;
		  }
	  }
	}

	$currblk['outer_left_margin'] = $prevblk['outer_left_margin'] + $currblk['margin_left'] + $prevblk['border_left']['w'] + $prevblk['padding_left'];
	$currblk['outer_right_margin'] = $prevblk['outer_right_margin']  + $currblk['margin_right'] + $prevblk['border_right']['w'] + $prevblk['padding_right'];

	$currblk['width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin']);
	$currblk['inner_width'] = $currblk['width'] - ($currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);

	// Check DIV is not now too narrow to fit text
	$mw = $this->GetStringWidth('WW');
	if ($currblk['inner_width'] < $mw) {
		$currblk['padding_left'] = 0;
		$currblk['padding_right'] = 0;
		$currblk['border_left']['w'] = 0.2;
		$currblk['border_right']['w'] = 0.2;
		$currblk['margin_left'] = 0;
		$currblk['margin_right'] = 0;
		$currblk['outer_left_margin'] = $prevblk['outer_left_margin'] + $currblk['margin_left'] + $prevblk['border_left']['w'] + $prevblk['padding_left'];
		$currblk['outer_right_margin'] = $prevblk['outer_right_margin']  + $currblk['margin_right'] + $prevblk['border_right']['w'] + $prevblk['padding_right'];
		$currblk['width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin']);
		$currblk['inner_width'] = $this->pgwidth - ($currblk['outer_right_margin'] + $currblk['outer_left_margin'] + $currblk['border_left']['w'] + $currblk['padding_left'] + $currblk['border_right']['w'] + $currblk['padding_right']);
		if ($currblk['inner_width'] < $mw) { die("DIV is too narrow for text to fit!"); }
	}

	$this->x = $this->lMargin + $currblk['outer_left_margin'];


	if ($this->use_kwt && isset($attr['KEEP-WITH-TABLE']) && !$this->ColActive && !$this->keep_block_together) {
		$this->kwt = true;
		$this->kwt_y0 = $this->y;
		$this->kwt_x0 = $this->x;
		$this->kwt_height = 0;
		$this->kwt_buffer = array();
		$this->kwt_Links = array();
		$this->kwt_Annots = array();
		$this->kwt_moved = false;
		$this->kwt_saved = false;
		// mPDF 3.0
		$this->kwt_Reference = array();
		$this->kwt_BMoutlines = array();
		$this->kwt_toc = array();
	}
	else { 
		$this->kwt = false; 
	}	// *TABLES*

	//Save x,y coords in case we need to print borders...
	$currblk['y0'] = $this->y;
	$currblk['x0'] = $this->x;
	$currblk['startpage'] = $this->page;
	$this->oldy = $this->y;

	$this->lastblocklevelchange = 1 ;

	break;

    case 'HR':
	// Added mPDF 3.0 Float DIV - CLEAR
	if (isset($attr['STYLE'])) {
		$properties = $this->readInlineCSS($attr['STYLE']);
		if (isset($properties['CLEAR'])) { $this->ClearFloats(strtoupper($properties['CLEAR']),$this->blklvl); }	// *CSS-FLOAT*
	}

	$this->ignorefollowingspaces = true; 

	$objattr = array();
		// mPDF 3.0
		$objattr['margin_top'] = 0;
		$objattr['margin_bottom'] = 0;
		$objattr['margin_left'] = 0;
		$objattr['margin_right'] = 0;
		$objattr['width'] = 0;
		$objattr['height'] = 0;
		$objattr['border_top']['w'] = 0;
		$objattr['border_bottom']['w'] = 0;
		$objattr['border_left']['w'] = 0;
		$objattr['border_right']['w'] = 0;
	$properties = $this->MergeCSS('',$tag,$attr);
	if (isset($properties['MARGIN-TOP'])) { $objattr['margin_top'] = $this->ConvertSize($properties['MARGIN-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
	if (isset($properties['MARGIN-BOTTOM'])) { $objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
	if (isset($properties['WIDTH'])) { $objattr['width'] = $this->ConvertSize($properties['WIDTH'],$this->blk[$this->blklvl]['inner_width']); }
	if (isset($properties['TEXT-ALIGN'])) { $objattr['align'] = $align[strtolower($properties['TEXT-ALIGN'])]; }
	// mPDF 3.0
	if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT'])=='auto') { 
		$objattr['align'] = 'R';
	}
	if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT'])=='auto') { 
		$objattr['align'] = 'L';
		if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT'])=='auto' && isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT'])=='auto') { 
			$objattr['align'] = 'C';
		}
	}
	if (isset($properties['COLOR'])) { $objattr['color'] = $this->ConvertColor($properties['COLOR']); }
	if (isset($properties['HEIGHT'])) { $objattr['linewidth'] = $this->ConvertSize($properties['HEIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }

	if(isset($attr['WIDTH']) && $attr['WIDTH'] != '') $objattr['width'] = $this->ConvertSize($attr['WIDTH'],$this->blk[$this->blklvl]['inner_width']);
	if(isset($attr['ALIGN']) && $attr['ALIGN'] != '') $objattr['align'] = $align[strtolower($attr['ALIGN'])];
	if(isset($attr['COLOR']) && $attr['COLOR'] != '') $objattr['color'] = $this->ConvertColor($attr['COLOR']);

	if ($this->tableLevel) {
		$objattr['W-PERCENT'] = 100;
		if (isset($properties['WIDTH']) && stristr($properties['WIDTH'],'%')) { 
			$properties['WIDTH'] += 0;  //make "90%" become simply "90" 
			$objattr['W-PERCENT'] = $properties['WIDTH'];
		}
		if (isset($attr['WIDTH']) && stristr($attr['WIDTH'],'%')) { 
			$attr['WIDTH'] += 0;  //make "90%" become simply "90" 
			$objattr['W-PERCENT'] = $attr['WIDTH'];
		}
	}

	$objattr['type'] = 'hr';
	$objattr['height'] = $objattr['linewidth'] + $objattr['margin_top'] + $objattr['margin_bottom'];
	$e = "\xbb\xa4\xactype=image,objattr=".serialize($objattr)."\xbb\xa4\xac";

	// Clear properties - tidy up
	$properties = array();

	// Output it to buffers
	if ($this->tableLevel) {
		if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
		}
		elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
		}
		$this->cell[$this->row][$this->col]['s'] = 0 ;// reset
		$this->cell[$this->row][$this->col]['textbuffer'][] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
	}
	else {
		$this->textbuffer[] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
	}	// *TABLES*

	break;




	// *********** FORM ELEMENTS ********************



	// *********** GRAPH  ********************
     case 'JPGRAPH':
	if (!$this->useGraphs) { break; }
	if ($attr['TABLE']) { $gid = strtoupper($attr['TABLE']); }
	else { $gid = '0'; }
	if (!is_array($this->graphs[$gid]) || count($this->graphs[$gid])==0 ) { break; }
	include_once(_MPDF_PATH.'graph.php');
	$this->graphs[$gid]['attr'] = $attr;

	// mPDF 4.0
	if (isset($this->graphs[$gid]['attr']['WIDTH']) && $this->graphs[$gid]['attr']['WIDTH']) { 
		$this->graphs[$gid]['attr']['cWIDTH']=$this->ConvertSize($this->graphs[$gid]['attr']['WIDTH'],$pgwidth); 
	}	// mm
	if (isset($this->graphs[$gid]['attr']['HEIGHT']) && $this->graphs[$gid]['attr']['HEIGHT']) { 
		$this->graphs[$gid]['attr']['cHEIGHT']=$this->ConvertSize($this->graphs[$gid]['attr']['HEIGHT'],$pgwidth); 
	}

	$graph_img = print_graph($this->graphs[$gid],$this->blk[$this->blklvl]['inner_width']);
	if ($graph_img) { 
		$attr['SRC'] = $graph_img['file']; 
		$attr['WIDTH'] = $graph_img['w']; 
		$attr['HEIGHT'] = $graph_img['h']; 
	}
	else { break; }

	// *********** IMAGE  ********************
    case 'IMG':
	$objattr = array();
		$objattr['margin_top'] = 0;
		$objattr['margin_bottom'] = 0;
		$objattr['margin_left'] = 0;
		$objattr['margin_right'] = 0;
		// mPDF 4.0
		$objattr['padding_top'] = 0;
		$objattr['padding_bottom'] = 0;
		$objattr['padding_left'] = 0;
		$objattr['padding_right'] = 0;
		$objattr['width'] = 0;
		$objattr['height'] = 0;
		$objattr['border_top']['w'] = 0;
		$objattr['border_bottom']['w'] = 0;
		$objattr['border_left']['w'] = 0;
		$objattr['border_right']['w'] = 0;
	if(isset($attr['SRC']))	{
     		$srcpath = $attr['SRC'];
		$properties = $this->MergeCSS('',$tag,$attr);
		if(isset($properties ['DISPLAY']) && strtolower($properties ['DISPLAY'])=='none') { 
			return; 
		}
		// VSPACE and HSPACE converted to margins in MergeCSS
		if (isset($properties['MARGIN-TOP'])) { $objattr['margin_top']=$this->ConvertSize($properties['MARGIN-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['MARGIN-BOTTOM'])) { $objattr['margin_bottom'] = $this->ConvertSize($properties['MARGIN-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['MARGIN-LEFT'])) { $objattr['margin_left'] = $this->ConvertSize($properties['MARGIN-LEFT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['MARGIN-RIGHT'])) { $objattr['margin_right'] = $this->ConvertSize($properties['MARGIN-RIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }

		// mPDF 4.0
		if (isset($properties['PADDING-TOP'])) { $objattr['padding_top']=$this->ConvertSize($properties['PADDING-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['PADDING-BOTTOM'])) { $objattr['padding_bottom'] = $this->ConvertSize($properties['PADDING-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['PADDING-LEFT'])) { $objattr['padding_left'] = $this->ConvertSize($properties['PADDING-LEFT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		if (isset($properties['PADDING-RIGHT'])) { $objattr['padding_right'] = $this->ConvertSize($properties['PADDING-RIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }

		if (isset($properties['BORDER-TOP'])) { $objattr['border_top'] = $this->border_details($properties['BORDER-TOP']); }
		if (isset($properties['BORDER-BOTTOM'])) { $objattr['border_bottom'] = $this->border_details($properties['BORDER-BOTTOM']); }
		if (isset($properties['BORDER-LEFT'])) { $objattr['border_left'] = $this->border_details($properties['BORDER-LEFT']); }
		if (isset($properties['BORDER-RIGHT'])) { $objattr['border_right'] = $this->border_details($properties['BORDER-RIGHT']); }

		if (isset($properties['VERTICAL-ALIGN'])) { $objattr['vertical-align'] = $align[strtolower($properties['VERTICAL-ALIGN'])]; }
		$w = 0;
		$h = 0;
		// mPDF 4.0
		if(isset($properties['WIDTH'])) $w = $this->ConvertSize($properties['WIDTH'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		if(isset($properties['HEIGHT'])) $h = $this->ConvertSize($properties['HEIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);

		if(isset($attr['WIDTH'])) $w = $this->ConvertSize($attr['WIDTH'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		if(isset($attr['HEIGHT'])) $h = $this->ConvertSize($attr['HEIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		if (isset($properties['OPACITY']) && $properties['OPACITY'] > 0 && $properties['OPACITY'] <= 1) { $objattr['opacity'] = $properties['OPACITY']; }
		if ($this->HREF) { $objattr['link'] = $this->HREF; }	// ? this isn't used

		// mPDF 4.0
		$extraheight = $objattr['padding_top'] + $objattr['padding_bottom'] + $objattr['margin_top'] + $objattr['margin_bottom'] + $objattr['border_top']['w'] + $objattr['border_bottom']['w'];
		$extrawidth = $objattr['padding_left'] + $objattr['padding_right'] + $objattr['margin_left'] + $objattr['margin_right'] + $objattr['border_left']['w'] + $objattr['border_right']['w'];
		// Image file
		// Check if file is available
		$found_img = $this->_file_exists($srcpath);
		$imageset = false;
		while(!$imageset) {
			if (!$found_img) {
				if(!$this->shownoimg) break;
				$srcpath = str_replace("\\","/",dirname(__FILE__)) . "/";
				$srcpath .= 'includes/no_image.jpg';
				$w = (14 * 0.2645); 	// 14 x 16px
				$h = (16 * 0.2645); 	// 14 x 16px
				$imageset = true;
			}
			// Gets Image Info
			//First use of image, get info
			$pos=strrpos($srcpath,'.');
			if(!$pos) { 
				$found_img = false; 
				// mPDF 3.0
				if ($this->showImageErrors) $this->Error('Image file has no extension and no type was specified: '.$srcpath);
				else continue; 
			}
			$qpos=strrpos($srcpath,'?');
			if ($qpos) {  $itype=substr($srcpath,$pos+1,$qpos-$pos-1); }
			else { $itype=substr($srcpath,$pos+1); }

			$itype=strtolower($itype);
			if(isset($this->images[$srcpath])) { $info=$this->images[$srcpath]; $imageset = true; }
			else if (isset($this->formobjects[$srcpath])) { $info=$this->formobjects[$srcpath]; $imageset = true; }	// *IMAGES-WMF*
			else {
				// mPDF 4.1
				$mqr=ini_get("magic_quotes_runtime");
				if ($mqr) { set_magic_quotes_runtime(0); }
				$info = -1;
				if($itype=='jpg' or $itype=='jpeg')	$info=$this->_parsejpg($srcpath);
				elseif($itype=='png') $info=$this->_parsepng($srcpath);
				elseif($itype=='gif') $info=$this->_parsegif($srcpath);
				elseif($itype=='wmf') $info=$this->_parsewmf($srcpath); 	// *IMAGES-WMF*
				else { 
					//Allow for additional formats
					$mtd='_parse'.$itype;
					if(method_exists($this,$mtd)) { $info=$this->$mtd($srcpath); }
					else { 
						// mPDF 3.0
						$info=$this->_parsephp($srcpath); 
						if (!is_array($info) && $info < 0) { 
							if ($this->showImageErrors) $this->Error('Unsupported image type: '.$itype);
							else $info = -1; 
						}
					}
				}
				// mPDF 3.0 - Try again PNG
				if ($itype=='png' && $info < -1) { 
					$info=$this->_parsepng2($srcpath, $info); 
					if (isset($info['UID']) && $info['UID']) { $srcpath = $info['UID']; }	// A unique alpha blended PNG created
				}
				// mPDF 4.1
				if ($mqr) { set_magic_quotes_runtime($mqr); }
				if (!is_array($info) && $info < 0) { 
					$found_img = false; 
					continue; 
				}
            		else if ($itype=='wmf') { 
	            		$info['i']=count($this->formobjects)+1;
	            		$this->formobjects[$srcpath]=$info;
					$imageset = true;
				}
				else {
					$info['i']=count($this->images)+1;
					$this->images[$srcpath]=$info;
					$imageset = true;
				}
			}
		}

		$objattr['file'] = $srcpath;
		//Default width and height calculation if needed
		if($w==0 and $h==0) {
      	      if ($itype=='wmf') { 
				// WMF units are twips (1/20pt)
				// divide by 20 to get points
				// divide by k to get user units
				$w = abs($info['w'])/(20*$this->k);
				$h = abs($info['h']) / (20*$this->k);
			}
			else {
				//Put image at default dpi
				$w=($info['w']/$this->k) * (72/$this->img_dpi);
				$h=($info['h']/$this->k) * (72/$this->img_dpi);
			}
		}
		// IF WIDTH OR HEIGHT SPECIFIED
		if($w==0)  $w=abs($h*$info['w']/$info['h']); 
		if($h==0)	$h=abs($w*$info['h']/$info['w']);

		// Resize to maximum dimensions of page
		$maxWidth = $this->blk[$this->blklvl]['inner_width'];
   		$maxHeight = $this->h - ($this->tMargin + $this->bMargin + 10) ;	// mPDF 4.1
		if ($this->fullImageHeight) { $maxHeight = $this->fullImageHeight; }	// mPDF 4.1
		if ($w + $extrawidth > $maxWidth ) {
			$w = $maxWidth - $extrawidth;
			$h=abs($w*$info['h']/$info['w']);
		}

		if ($h + $extraheight > $maxHeight ) {
			$h = $maxHeight - $extraheight;
			$w=abs($h*$info['w']/$info['h']);
		}
		$objattr['type'] = 'image';
		$objattr['itype'] = $itype;
		$objattr['orig_h'] = $info['h'];
		$objattr['orig_w'] = $info['w'];
		if ($itype=='wmf') {
			$objattr['wmf_x'] = $info['x'];
			$objattr['wmf_y'] = $info['y'];
		}
		$objattr['height'] = $h + $extraheight;
		$objattr['width'] = $w + $extrawidth;
		$objattr['image_height'] = $h;
		$objattr['image_width'] = $w;
		if (!$this->ColActive && !$this->tableLevel && !$this->listlvl && !$this->kwt && !$this->keep_block_together) {
		  if (isset($properties['FLOAT']) && (strtoupper($properties['FLOAT']) == 'RIGHT' || strtoupper($properties['FLOAT']) == 'LEFT')) {
			$objattr['float'] = substr(strtoupper($properties['FLOAT']),0,1);
		  }
		}

		$e = "\xbb\xa4\xactype=image,objattr=".serialize($objattr)."\xbb\xa4\xac";

		// Clear properties - tidy up
		$properties = array();

		// Output it to buffers
		if ($this->tableLevel) {
			$this->cell[$this->row][$this->col]['textbuffer'][] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
			$this->cell[$this->row][$this->col]['s'] += $objattr['width'] ;
		}
		else {
			$this->textbuffer[] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);

		}	// *TABLES*
	}
	break;



    case 'TABLE': // TABLE-BEGIN
	$this->tdbegin = false;
	$this->lastoptionaltag = '';
	// Disable vertical justification in columns
	if ($this->lastblocklevelchange == 1) { $blockstate = 1; }	// Top margins/padding only
	else if ($this->lastblocklevelchange < 1) { $blockstate = 0; }	// NO margins/padding
	// called from block after new div e.g. <div> ... <table> ...    Outputs block top margin/border and padding
	if (count($this->textbuffer) == 0 && $this->lastblocklevelchange == 1 && !$this->tableLevel && !$this->kwt) {
		$this->newFlowingBlock( $this->blk[$this->blklvl]['width'],$this->lineheight,'',false,false,1,true);	// true = newblock
		$this->finishFlowingBlock(true);	// true = END of flowing block
	}
	else if (!$this->tableLevel && count($this->textbuffer)) { $this->printbuffer($this->textbuffer,$blockstate); }
	//else if (!$this->tableLevel) { $this->printbuffer($this->textbuffer,$blockstate); }

	$this->textbuffer=array();
	$this->lastblocklevelchange = -1;
	if ($this->tableLevel) {	// i.e. now a nested table coming...
		// Save current level table
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['baseProperties']= $this->base_table_properties;
	//	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['tablecascadeCSS'] = $this->tablecascadeCSS;
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = $this->cell;
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currrow'] = $this->row;
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currcol'] = $this->col;
	}
	$this->tableLevel++;
	$this->tbCSSlvl++;

	// mPDF 3.0
	if (isset($this->tbctr[$this->tableLevel])) { $this->tbctr[$this->tableLevel]++; }
	else { $this->tbctr[$this->tableLevel] = 1; }

	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['level'] = $this->tableLevel;
	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['levelid'] = $this->tbctr[$this->tableLevel];

	if ($this->tableLevel > $this->innermostTableLevel) { $this->innermostTableLevel = $this->tableLevel; }
	if ($this->tableLevel > 1) { 
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nestedpos'] = array($this->row,$this->col,$this->tbctr[($this->tableLevel-1)]); 
	}
	//++++++++++++++++++++++++++++

	$this->cell = array();
	$this->col=-1; //int
	$this->row=-1; //int
	$table = &$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]];
 
	// mPDF 3.0
	$table['bgcolor'] = false;
	$table['va'] = false;
	$table['txta'] = false;
	$table['topntail'] = false;
	$table['thead-underline'] = false;
	$table['border'] = false;
	$table['border_details']['R']['w'] = 0;
	$table['border_details']['L']['w'] = 0;
	$table['border_details']['T']['w'] = 0;
	$table['border_details']['B']['w'] = 0;
	$table['border_details']['R']['style'] = '';
	$table['border_details']['L']['style'] = '';
	$table['border_details']['T']['style'] = '';
	$table['border_details']['B']['style'] = '';
	$table['max_cell_border_width']['R'] = 0;
	$table['max_cell_border_width']['L'] = 0;
	$table['max_cell_border_width']['T'] = 0;
	$table['max_cell_border_width']['B'] = 0;
	$table['padding']['L'] = false;
	$table['padding']['R'] = false;
	$table['padding']['T'] = false;
	$table['padding']['B'] = false;
	$table['margin']['L'] = false;
	$table['margin']['R'] = false;
	$table['margin']['T'] = false;
	$table['margin']['B'] = false;
	$table['a'] = false;
	$table['border_spacing_H'] = false;
	$table['border_spacing_V'] = false;

	$this->Reset();
	$this->table_lineheight = $this->default_lineheight_correction; 
	$this->InlineProperties = array();
	$this->spanlvl = 0;
	$table['nc'] = $table['nr'] = 0;
	$this->tablethead = 0;
	$this->tabletfoot = 0;
	$this->tabletheadjustfinished = false;

	// mPDF 3.0
	if ($this->tableLevel==1) { $this->tableheadernrows = 0; $this->tablefooternrows = 0; $this->usetableheader = false; }	// mPDF 4.0

	if ($this->tableLevel ==1) $this->base_table_properties = array();

		// ADDED CSS FUNCIONS FOR TABLE 
		if ($this->tbCSSlvl==1) {
			$properties = $this->MergeCSS('TOPTABLE',$tag,$attr);
		}
		else {
			$properties = $this->MergeCSS('TABLE',$tag,$attr);
		}
		$w = '';
		if (isset($properties['WIDTH'])) { $w = $properties['WIDTH']; }

		if (isset($properties['BACKGROUND-COLOR'])) { $table['bgcolor'][-1] = $properties['BACKGROUND-COLOR'];	}
		else if (isset($properties['BACKGROUND'])) { $table['bgcolor'][-1] = $properties['BACKGROUND'];	}
		if (isset($properties['VERTICAL-ALIGN'])) { $table['va'] = $align[strtolower($properties['VERTICAL-ALIGN'])]; }
		if (isset($properties['TEXT-ALIGN'])) { $table['txta'] = $align[strtolower($properties['TEXT-ALIGN'])]; }
		if (isset($properties['AUTOSIZE']) && $properties['AUTOSIZE'] && $this->tableLevel ==1)	{ 
			$this->shrink_this_table_to_fit = $properties['AUTOSIZE']; 
			if ($this->shrink_this_table_to_fit < 1) { $this->shrink_this_table_to_fit = 0; }
		}
		if (isset($properties['ROTATE']) && $properties['ROTATE'] && $this->tableLevel ==1)	{ 
			$this->table_rotate = $properties['ROTATE']; 
		}
		if (isset($properties['TOPNTAIL'])) { $table['topntail'] = $properties['TOPNTAIL']; }
		if (isset($properties['THEAD-UNDERLINE'])) { $table['thead-underline'] = $properties['THEAD-UNDERLINE']; }

		if (isset($properties['BORDER'])) { 
			$bord = $this->border_details($properties['BORDER']);
			if ($bord['s']) {
				$table['border'] = _BORDER_ALL;
				$table['border_details']['R'] = $bord;
				$table['border_details']['L'] = $bord;
				$table['border_details']['T'] = $bord;
				$table['border_details']['B'] = $bord;
			}
		}
		if (isset($properties['BORDER-RIGHT'])) { 
			$table['border_details']['R'] = $this->border_details($properties['BORDER-RIGHT']);
		  $this->setBorder($table['border'], _BORDER_RIGHT, $table['border_details']['R']['s']); 
		}
		if (isset($properties['BORDER-LEFT'])) { 
			$table['border_details']['L'] = $this->border_details($properties['BORDER-LEFT']);
		  $this->setBorder($table['border'], _BORDER_LEFT, $table['border_details']['L']['s']); 
		}
		if (isset($properties['BORDER-BOTTOM'])) { 
			$table['border_details']['B'] = $this->border_details($properties['BORDER-BOTTOM']);
			$this->setBorder($table['border'], _BORDER_BOTTOM, $table['border_details']['B']['s']); 
		}
		if (isset($properties['BORDER-TOP'])) { 
			$table['border_details']['T'] = $this->border_details($properties['BORDER-TOP']);
			$this->setBorder($table['border'], _BORDER_TOP, $table['border_details']['T']['s']); 
		}
		if (isset($table['border'])){ 
			  $this->table_border_css_set = 1;
		}
		else {
		  $this->table_border_css_set = 0;
		}

		if (isset($properties['FONT-FAMILY'])) { 
		   if (!$this->isCJK) { 
			$this->default_font = $properties['FONT-FAMILY'];
			$this->SetFont($this->default_font,'',0,false);
			$this->base_table_properties['FONT-FAMILY'] = $properties['FONT-FAMILY'];
		   }
		}
		if (isset($properties['FONT-SIZE'])) { 
		   $mmsize = $this->ConvertSize($properties['FONT-SIZE'],$this->default_font_size/$this->k);
		   if ($mmsize) {
			$this->default_font_size = $mmsize*(72/25.4);
   			$this->SetFontSize($this->default_font_size,false);
			$this->base_table_properties['FONT-SIZE'] = $properties['FONT-SIZE'];
		   }
		}

		if (isset($properties['FONT-WEIGHT'])) {
			if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD')	{ $this->base_table_properties['FONT-WEIGHT'] = 'BOLD'; }
		}
		if (isset($properties['FONT-STYLE'])) {
			if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') { $this->base_table_properties['FONT-STYLE'] = 'ITALIC'; }
		}
		if (isset($properties['COLOR'])) {
			$this->base_table_properties['COLOR'] = $properties['COLOR'];
		}


		if (isset($properties['PADDING-LEFT'])) { 
			$table['padding']['L'] = $this->ConvertSize($properties['PADDING-LEFT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-RIGHT'])) { 
			$table['padding']['R'] = $this->ConvertSize($properties['PADDING-RIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-TOP'])) { 
			$table['padding']['T'] = $this->ConvertSize($properties['PADDING-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-BOTTOM'])) { 
			$table['padding']['B'] = $this->ConvertSize($properties['PADDING-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}

		if (isset($properties['MARGIN-TOP'])) { 
			$table['margin']['T'] = $this->ConvertSize($properties['MARGIN-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}

		if (isset($properties['MARGIN-BOTTOM'])) { 
			$table['margin']['B'] = $this->ConvertSize($properties['MARGIN-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}
		if (isset($properties['MARGIN-TOP'])) { 
			$table['margin']['L'] = $this->ConvertSize($properties['MARGIN-LEFT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}

		if (isset($properties['MARGIN-BOTTOM'])) { 
			$table['margin']['R'] = $this->ConvertSize($properties['MARGIN-RIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}
		if (isset($properties['MARGIN-LEFT']) && isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-LEFT'])=='auto' && strtolower($properties['MARGIN-RIGHT'])=='auto') { 
			$table['a'] = 'C'; 
		}
		else if (isset($properties['MARGIN-LEFT']) && strtolower($properties['MARGIN-LEFT'])=='auto') { 
			$table['a'] = 'R'; 
		}
		else if (isset($properties['MARGIN-RIGHT']) && strtolower($properties['MARGIN-RIGHT'])=='auto') { 
			$table['a'] = 'L'; 
		}

		// mPDF 4.0
		if (isset($properties['LINE-HEIGHT'])) { 
			$this->table_lineheight = $this->fixLineheight($properties['LINE-HEIGHT']);
			if (!$this->table_lineheight) { $this->table_lineheight = $this->default_lineheight_correction; }
		}

		if (isset($properties['BORDER-COLLAPSE']) && strtoupper($properties['BORDER-COLLAPSE'])=='SEPARATE') { 
			$table['borders_separate'] = true; 
		}
		else { 
			$table['borders_separate'] = false; 
		}

		if (isset($properties['BORDER-SPACING-H'])) { 
			$table['border_spacing_H'] = $this->ConvertSize($properties['BORDER-SPACING-H'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}
		if (isset($properties['BORDER-SPACING-V'])) { 
			$table['border_spacing_V'] = $this->ConvertSize($properties['BORDER-SPACING-V'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		}

		if (isset($properties['EMPTY-CELLS'])) { 
			$table['empty_cells'] = strtolower($properties['EMPTY-CELLS']); 	// 'hide'  or 'show'
		}
		else { $table['empty_cells'] = ''; } 

		if (isset($properties['PAGE-BREAK-INSIDE']) && strtoupper($properties['PAGE-BREAK-INSIDE'])=='AVOID' && $this->tableLevel==1) { 
			$this->table_keep_together = true; 
		}
		else if ($this->tableLevel==1) { 
			$this->table_keep_together = false; 
		}

	$properties = array();

	if (!$table['borders_separate']) { $table['border_spacing_H'] = $table['border_spacing_V'] = 0; }	
	else if (isset($attr['CELLSPACING'])) { 
		$table['border_spacing_H'] = $table['border_spacing_V'] = $this->ConvertSize($attr['CELLSPACING'],$this->blk[$this->blklvl]['inner_width']); 
	}


	if (isset($attr['CELLPADDING'])) {
		$table['cell_padding'] = $attr['CELLPADDING'];
	}
	else {
		$table['cell_padding'] = false;
	}

	if (isset($attr['BORDER'])) {
		  $this->table_border_attr_set = 1;
		if ($attr['BORDER']=='1') {
			$bord = $this->border_details('#000000 1px solid');
		   if ($bord['s']) {
			$table['border'] = _BORDER_ALL;
			$table['border_details']['R'] = $bord;
			$table['border_details']['L'] = $bord;
			$table['border_details']['T'] = $bord;
			$table['border_details']['B'] = $bord;
		   }
		}
	}
	else {
	  $this->table_border_attr_set = 0;
	}
	if (isset($attr['REPEAT_HEADER']) and $attr['REPEAT_HEADER'] == true) { $this->UseTableHeader(true); } 


	if (isset($attr['ALIGN'])) { $table['a']	= $align[strtolower($attr['ALIGN'])]; }
	if (!$table['a']) { $table['a'] = $this->defaultTableAlign; }	// mPDF 4.0
	if (isset($attr['BGCOLOR'])) { $table['bgcolor'][-1]	= $attr['BGCOLOR']; }
	// mPDF 4.0 This does not work
	// if (isset($attr['HEIGHT'])) { $table['h']	= $this->ConvertSize($attr['HEIGHT'],$this->blk[$this->blklvl]['inner_width']); }

	if (isset($attr['WIDTH']) && $attr['WIDTH']) { $w = $attr['WIDTH']; }
	if ($w) { // set here or earlier in $properties
		$maxwidth = $this->blk[$this->blklvl]['inner_width'];
		if ($table['borders_separate']) { 
			$tblblw = $table['margin']['L'] + $table['margin']['R'] + $table['border_details']['L']['w']/2 + $table['border_details']['R']['w']/2;
		}
		else { 
			$tblblw = $table['margin']['L'] + $table['margin']['R'] + $table['max_cell_border_width']['L']/2 + $table['max_cell_border_width']['R']/2;
		}
		if (strpos($w,'%') && $this->tableLevel == 1 && !$this->ignore_table_percents ) { 
			// % needs to be of inner box without table margins etc.
			$maxwidth -= $tblblw ;
			$wmm = $this->ConvertSize($w,$maxwidth,$this->FontSize,false);
			$table['w'] = $wmm + $tblblw ;
		}
		if (strpos($w,'%') && $this->tableLevel > 1 && !$this->ignore_table_percents && $this->keep_table_proportions) { 
			$table['wpercent'] = $w + 0; 	// makes 80% -> 80
		}
		if (!strpos($w,'%') && !$this->ignore_table_widths ) {
			$wmm = $this->ConvertSize($w,$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
			$table['w'] = $wmm + $tblblw ;
		}
		if (!$this->keep_table_proportions) {
			if (isset($table['w']) && $table['w'] > $this->blk[$this->blklvl]['inner_width']) { $table['w'] = $this->blk[$this->blklvl]['inner_width']; }
		}
	}


	if (isset($attr['AUTOSIZE']) && $this->tableLevel==1)	{ 
		$this->shrink_this_table_to_fit = $attr['AUTOSIZE']; 
		if ($this->shrink_this_table_to_fit < 1) { $this->shrink_this_table_to_fit = 0; }
	}
	if (isset($attr['ROTATE']) && $this->tableLevel==1)	{ 
		$this->table_rotate = $attr['ROTATE']; 
	}

	//++++++++++++++++++++++++++++
	// keeping block together on one page
	// Autosize is now forced therefore keep block together disabled
	if ($this->keep_block_together) {
		$this->keep_block_together = 0;
		$this->printdivbuffer();
		$this->blk[$this->blklvl]['keep_block_together'] = 0;
	}
	if ($this->table_rotate) {
		$this->tbrot_Links = array();
		$this->tbrot_Annots = array();
		// mPDF 3.0
		$this->tbrot_Reference = array();
		$this->tbrot_BMoutlines = array();
		$this->tbrot_toc = array();
	}

	if ($this->kwt) {
		if ($this->table_rotate) { $this->table_keep_together = true; }
		$this->kwt = false;
		$this->kwt_saved = true;
	}

	if ($this->tableLevel==1 && $this->useGraphs) { 
		if (isset($attr['ID']) && $attr['ID']) { $this->currentGraphId = strtoupper($attr['ID']); }
		else { $this->currentGraphId = '0'; }
		$this->graphs[$this->currentGraphId] = array();
	}

	//++++++++++++++++++++++++++++
	$this->plainCell_properties = array();


	break;



    case 'THEAD':
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
	$this->tbCSSlvl++;
	$this->tablethead = 1;
	$this->UseTableHeader(true);
	$properties = $this->MergeCSS('TABLE',$tag,$attr);
	if (isset($properties['FONT-WEIGHT'])) {
		if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD')	{ $this->thead_font_weight = 'B'; }
		else { $this->thead_font_weight = ''; }
	}

	if (isset($properties['FONT-STYLE'])) {
		if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') { $this->thead_font_style = 'I'; }
		else { $this->thead_font_style = ''; }
	}

	if (isset($properties['VERTICAL-ALIGN'])) {
		$this->thead_valign_default = $properties['VERTICAL-ALIGN'];
	}
	if (isset($properties['TEXT-ALIGN'])) {
		$this->thead_textalign_default = $properties['TEXT-ALIGN'];
	}
	$properties = array();
	break;


    case 'TFOOT':
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
	$this->tbCSSlvl++;
	// mPDF 4.0
	$this->tabletfoot = 1; 
	// mPDF 3.0
	$this->tablethead = 0;
	$properties = $this->MergeCSS('TABLE',$tag,$attr);
	if (isset($properties['FONT-WEIGHT'])) {
		if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD')	{ $this->tfoot_font_weight = 'B'; }
		else { $this->tfoot_font_weight = ''; }
	}

	if (isset($properties['FONT-STYLE'])) {
		if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') { $this->tfoot_font_style = 'I'; }
		else { $this->tfoot_font_style = ''; }
	}

	if (isset($properties['VERTICAL-ALIGN'])) {
		$this->tfoot_valign_default = $properties['VERTICAL-ALIGN'];
	}
	if (isset($properties['TEXT-ALIGN'])) {
		$this->tfoot_textalign_default = $properties['TEXT-ALIGN'];
	}
	$properties = array();
	break;


    case 'TBODY':
	// mPDF 3.0
	$this->tablethead = 0;
	$this->tabletfoot = 0;	// mPDF 4.0
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
	$this->tbCSSlvl++;
	$this->MergeCSS('TABLE',$tag,$attr);
	break;


    case 'TR':
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
	$this->tbCSSlvl++;
	$this->row++;
	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr']++;
	$this->col = -1;
	$properties = $this->MergeCSS('TABLE',$tag,$attr);
	if (isset($properties['BACKGROUND-COLOR'])) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$this->row] = $properties['BACKGROUND-COLOR']; }
	else if (isset($properties['BACKGROUND'])) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$this->row] = $properties['BACKGROUND']; }
	if (isset($properties['TEXT-ROTATE'])) {
		$this->trow_text_rotate = $properties['TEXT-ROTATE'];
	}
	if (isset($attr['TEXT-ROTATE'])) $this->trow_text_rotate = $attr['TEXT-ROTATE'];

	if (isset($attr['BGCOLOR'])) $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['bgcolor'][$this->row]	= $attr['BGCOLOR'];
	if ($this->tablethead) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_thead'][$this->row] = true; }
	// mPDF 4.0
	if ($this->tabletfoot) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$this->row] = true; }
	$properties = array();
	break;



    case 'TH':
    case 'TD':
	$this->ignorefollowingspaces = true; 
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
	$this->tbCSSlvl++;
	$this->InlineProperties = array();
	$this->spanlvl = 0;
	$this->tdbegin = true;
	$this->col++;
	while (isset($this->cell[$this->row][$this->col])) { $this->col++; }
	//Update number column
	if ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] < $this->col+1) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] = $this->col+1; }
	$this->cell[$this->row][$this->col] = array();
	$this->cell[$this->row][$this->col]['text'] = array();
	$this->cell[$this->row][$this->col]['s'] = 0 ;

	$table = &$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]];
	$cell = &$this->cell[$this->row][$this->col];

	// mPDF 3.0
	$cell['bgcolor'] = false;
	$cell['a'] = false;
	$cell['bgcolor'] = false;
	$cell['border'] = false;
	$cell['border_details']['R']['w'] = 0;
	$cell['border_details']['L']['w'] = 0;
	$cell['border_details']['T']['w'] = 0;
	$cell['border_details']['B']['w'] = 0;
	$cell['border_details']['mbw']['B']['L'] = 0;
	$cell['border_details']['mbw']['B']['R'] = 0;
	$cell['border_details']['mbw']['R']['T'] = 0;
	$cell['border_details']['mbw']['R']['B'] = 0;
	$cell['border_details']['mbw']['T']['L'] = 0;
	$cell['border_details']['mbw']['T']['R'] = 0;
	$cell['border_details']['mbw']['L']['T'] = 0;
	$cell['border_details']['mbw']['L']['B'] = 0;
	$cell['border_details']['R']['style'] = '';
	$cell['border_details']['L']['style'] = '';
	$cell['border_details']['T']['style'] = '';
	$cell['border_details']['B']['style'] = '';
	$cell['padding']['L'] = false;
	$cell['padding']['R'] = false;
	$cell['padding']['T'] = false;
	$cell['padding']['B'] = false;
	$cell['R'] = false;
	$cell['nowrap'] = false;


	// INHERITED TABLE PROPERTIES (or ROW for BGCOLOR)
	// If cell bgcolor not set specifically, set to TR row bgcolor (if set)
	if ((!isset($cell['bgcolor'])) && isset($table['bgcolor'][$this->row])) {
		$cell['bgcolor'] = $table['bgcolor'][$this->row];
	}
	else if (isset($table['bgcolor'][-1])) { $cell['bgcolor'] = $table['bgcolor'][-1]; }

	if ($table['va']) { $cell['va'] = $table['va']; }
	if ($table['txta']) { $cell['a'] = $table['txta']; }
	if ($this->table_border_attr_set) {
	  if ($table['border_details']) {
		$cell['border_details']['R'] = $table['border_details']['R'];
		$cell['border_details']['L'] = $table['border_details']['L'];
		$cell['border_details']['T'] = $table['border_details']['T'];
		$cell['border_details']['B'] = $table['border_details']['B'];
		$cell['border'] = $table['border']; 
		$cell['border_details']['L']['dom'] = 1; 
		$cell['border_details']['R']['dom'] = 1; 
		$cell['border_details']['T']['dom'] = 1; 
		$cell['border_details']['B']['dom'] = 1; 
	  }
	} 

	// INHERITED THEAD CSS Properties
	if ($this->tablethead) { 
		if ($this->thead_valign_default) $cell['va'] = $align[strtolower($this->thead_valign_default)]; 
		if ($this->thead_textalign_default) $cell['a'] = $align[strtolower($this->thead_textalign_default)]; 
		if ($this->thead_font_weight == 'B') { $this->SetStyle('B',true); }
		if ($this->thead_font_style == 'I') { $this->SetStyle('I',true); }
	}

	// INHERITED TFOOT CSS Properties
	if ($this->tabletfoot) { 
		if ($this->tfoot_valign_default) $cell['va'] = $align[strtolower($this->tfoot_valign_default)]; 
		if ($this->tfoot_textalign_default) $cell['a'] = $align[strtolower($this->tfoot_textalign_default)]; 
		if ($this->tfoot_font_weight == 'B') { $this->SetStyle('B',true); }
		if ($this->tfoot_font_style == 'I') { $this->SetStyle('I',true); }
	}


	if ($this->trow_text_rotate) {
		$cell['R'] = $this->trow_text_rotate; 
	}

		$this->cell_border_dominance_L = 0; 
		$this->cell_border_dominance_R = 0; 
		$this->cell_border_dominance_T = 0; 
		$this->cell_border_dominance_B = 0; 

		$properties = $this->MergeCSS('TABLE',$tag,$attr);
		$properties = $this->array_merge_recursive_unique($this->base_table_properties, $properties);
		if (isset($properties['BACKGROUND-COLOR'])) { $cell['bgcolor'] = $properties['BACKGROUND-COLOR']; }
		else if (isset($properties['BACKGROUND'])) { $cell['bgcolor'] = $properties['BACKGROUND']; }



		if (isset($properties['VERTICAL-ALIGN'])) { $cell['va']=$align[strtolower($properties['VERTICAL-ALIGN'])]; }
		if (isset($properties['TEXT-ALIGN'])) { $cell['a'] = $align[strtolower($properties['TEXT-ALIGN'])]; }

		if (isset($properties['TEXT-ROTATE']) && $properties['TEXT-ROTATE'])	{ 
			$cell['R'] = $properties['TEXT-ROTATE']; 
		}
		if (isset($properties['BORDER'])) { 
			$bord = $this->border_details($properties['BORDER']);
			if ($bord['s']) {
				$cell['border'] = _BORDER_ALL;
				$cell['border_details']['R'] = $bord;
				$cell['border_details']['L'] = $bord;
				$cell['border_details']['T'] = $bord;
				$cell['border_details']['B'] = $bord;
				$cell['border_details']['L']['dom'] = $this->cell_border_dominance_L; 
				$cell['border_details']['R']['dom'] = $this->cell_border_dominance_R; 
				$cell['border_details']['T']['dom'] = $this->cell_border_dominance_T; 
				$cell['border_details']['B']['dom'] = $this->cell_border_dominance_B; 
			}
		}

		if (isset($properties['BORDER-RIGHT']) && $properties['BORDER-RIGHT']) { 
			$cell['border_details']['R'] = $this->border_details($properties['BORDER-RIGHT']);
			$this->setBorder($cell['border'], _BORDER_RIGHT, $cell['border_details']['R']['s']); 
			$cell['border_details']['R']['dom'] = $this->cell_border_dominance_R; 
		}
		if (isset($properties['BORDER-LEFT']) && $properties['BORDER-LEFT']) { 
			$cell['border_details']['L'] = $this->border_details($properties['BORDER-LEFT']);
			$this->setBorder($cell['border'], _BORDER_LEFT, $cell['border_details']['L']['s']); 
			$cell['border_details']['L']['dom'] = $this->cell_border_dominance_L; 
		}
		if (isset($properties['BORDER-BOTTOM']) && $properties['BORDER-BOTTOM']) { 
			$cell['border_details']['B'] = $this->border_details($properties['BORDER-BOTTOM']);
			$this->setBorder($cell['border'], _BORDER_BOTTOM, $cell['border_details']['B']['s']); 
			$cell['border_details']['B']['dom'] = $this->cell_border_dominance_B; 
		}
		if (isset($properties['BORDER-TOP']) && $properties['BORDER-TOP']) { 
			$cell['border_details']['T'] = $this->border_details($properties['BORDER-TOP']);
			$this->setBorder($cell['border'], _BORDER_TOP, $cell['border_details']['T']['s']); 
			$cell['border_details']['T']['dom'] = $this->cell_border_dominance_T; 
		}

		// mPDF 4.0
		if (isset($properties['PADDING-LEFT'])) { 
			$cell['padding']['L'] = $this->ConvertSize($properties['PADDING-LEFT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-RIGHT'])) { 
			$cell['padding']['R'] = $this->ConvertSize($properties['PADDING-RIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-BOTTOM'])) { 
			$cell['padding']['B'] = $this->ConvertSize($properties['PADDING-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}
		if (isset($properties['PADDING-TOP'])) { 
			$cell['padding']['T'] = $this->ConvertSize($properties['PADDING-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);
		}

		$w = '';
		if (isset($properties['WIDTH'])) { $w = $properties['WIDTH']; }
		if (isset($attr['WIDTH'])) { $w = $attr['WIDTH']; }
		if ($w) { 
			if (strpos($w,'%') && !$this->ignore_table_percents ) { $cell['wpercent'] = $w + 0; }	// makes 80% -> 80
			else if (!strpos($w,'%') && !$this->ignore_table_widths ) { $cell['w'] = $this->ConvertSize($w,$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
		}

		// mPDF 4.0
		if (isset($properties['HEIGHT'])) { $cell['h']	= $this->ConvertSize($properties['HEIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }

		if (isset($properties['COLOR'])) {
		  $cor = $this->ConvertColor($properties['COLOR']);
		  if ($cor) { 
			$this->colorarray = $cor;
			$this->SetTextColor($cor['R'],$cor['G'],$cor['B']);
			$this->issetcolor=true;
		  }
		}
		if (isset($properties['FONT-FAMILY'])) {
		   if (!$this->isCJK) { 
			$this->SetFont($properties['FONT-FAMILY'],'',0,false);
		   }
		}
		if (isset($properties['FONT-SIZE'])) { 
		   $mmsize = $this->ConvertSize($properties['FONT-SIZE'],$this->default_font_size/$this->k);
		   if ($mmsize) {
  			$this->SetFontSize($mmsize*(72/25.4),false);
		   }
		}
		if (isset($properties['FONT-WEIGHT'])) {
			if (strtoupper($properties['FONT-WEIGHT']) == 'BOLD')	{ $this->SetStyle('B',true); }
		}
		if (isset($properties['FONT-STYLE'])) {
			if (strtoupper($properties['FONT-STYLE']) == 'ITALIC') { $this->SetStyle('I',true); }
		}
		if (isset($properties['WHITE-SPACE'])) {
			if (strtoupper($properties['WHITE-SPACE']) == 'NOWRAP') { $cell['nowrap']= 1; }
		}
		$properties = array();


	if (isset($attr['HEIGHT'])) $cell['h'] = $this->ConvertSize($attr['HEIGHT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false);

	if (isset($attr['ALIGN'])) $cell['a'] = $align[strtolower($attr['ALIGN'])];
	if (isset($attr['VALIGN'])) $cell['va'] = $align[strtolower($attr['VALIGN'])];

	if (isset($attr['BGCOLOR'])) $cell['bgcolor'] = $attr['BGCOLOR'];

	$cs = $rs = 1;
	if (isset($attr['COLSPAN']) && $attr['COLSPAN']>1)	$cs = $cell['colspan']	= $attr['COLSPAN'];
	if ($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] < $this->col+$cs) { 
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'] = $this->col+$cs; 
		for($l=$this->col; $l < $this->col+$cs ;$l++) {
			if ($l-$this->col) $this->cell[$this->row][$l] = 0;
		}
	}
	if (isset($attr['ROWSPAN']) && $attr['ROWSPAN']>1)	$rs = $cell['rowspan']	= $attr['ROWSPAN'];
	for ($k=$this->row ; $k < $this->row+$rs ;$k++) {
		for($l=$this->col; $l < $this->col+$cs ;$l++) {
			if ($k-$this->row || $l-$this->col)	$this->cell[$k][$l] = 0;
		}
	}
	if (isset($attr['TEXT-ROTATE']))	{ 
		$cell['R'] = $attr['TEXT-ROTATE']; 
	}
	if (isset($attr['NOWRAP']) && $attr['NOWRAP']) $cell['nowrap']= 1;
	unset($cell );
	break;



    // *********** LISTS ********************

    case 'OL':
    case 'UL':
	$this->listjustfinished = false;
	// mPDF 3.0
	$this->linebreakjustfinished=false;
	$this->lastoptionaltag = ''; // Save current HTML specified optional endtag
	$this->listCSSlvl++;
	if((!$this->tableLevel) && ($this->listlvl == 0)) {
		if ($this->lastblocklevelchange == 1) { $blockstate = 1; }	// Top margins/padding only
		else if ($this->lastblocklevelchange < 1) { $blockstate = 0; }	// NO margins/padding
		// called from block after new div e.g. <div> ... <ol> ...    Outputs block top margin/border and padding
		if (count($this->textbuffer) == 0 && $this->lastblocklevelchange == 1 && !$this->tableLevel && !$this->kwt) {
			$this->newFlowingBlock( $this->blk[$this->blklvl]['width'],$this->lineheight,'',false,false,1,true);	// true = newblock
			$this->finishFlowingBlock(true);	// true = END of flowing block
		}
		else if (count($this->textbuffer)) { $this->printbuffer($this->textbuffer,$blockstate); }
		$this->textbuffer=array();
		$this->lastblocklevelchange = -1;
	}
	// ol and ul types are mixed here
	if ($this->listlvl == 0) {
		$this->list_indent = array();
		$this->list_align = array();
		$this->list_lineheight = array();
		$this->InlineProperties['LIST'] = array();
		$this->InlineProperties['LISTITEM'] = array();
	}

	// A simple list for inside a table
	if($this->tableLevel) {
		$this->list_indent[$this->listlvl] = 0;	// mm default indent for each level
		if ($tag == 'OL') $this->listtype = '1';
		else if ($tag == 'UL') $this->listtype = 'disc';
      	if ($this->listlvl > 0) {
			$this->listlist[$this->listlvl]['MAXNUM'] = $this->listnum; //save previous lvl's maxnum
		}
		$this->listlvl++;
		$this->listnum = 0; // reset
		$this->listlist[$this->listlvl] = array('TYPE'=>$this->listtype,'MAXNUM'=>$this->listnum);
		break;
	}

	if ($this->listCSSlvl==1) {
		$properties = $this->MergeCSS('TOPLIST',$tag,$attr);
	}
	else {
		$properties = $this->MergeCSS('LIST',$tag,$attr);
	}
	if (!empty($properties)) $this->setCSS($properties,'INLINE');
	// List-type
	// mPDF 4.0
	$this->listtype = '';
	if (isset($attr['TYPE']) && $attr['TYPE']) { $this->listtype = $attr['TYPE']; }
	else if (isset($properties['LIST-STYLE-TYPE'])) { 
		// mPDF 4.0
		$this->listtype = $this->_getListStyle($properties['LIST-STYLE-TYPE']);
	}
	else if (isset($properties['LIST-STYLE'])) { 
		// mPDF 4.0
		$this->listtype = $this->_getListStyle($properties['LIST-STYLE']);
	}
	if (!$this->listtype) {
		if ($tag == 'OL') $this->listtype = '1';
		if ($tag == 'UL') {
			// mPDF 4.0
			if ($this->listlvl % 3 == 0) $this->listtype = 'disc';
			elseif ($this->listlvl % 3 == 1) $this->listtype = 'circle';
			else $this->listtype = 'square';
		}
	}

      if ($this->listlvl == 0) {
	  $this->inherit_lineheight = 0;
        $this->listlvl++; // first depth level
        $this->listnum = 0; // reset
        $occur = $this->listoccur[$this->listlvl] = 1;
        $this->listlist[$this->listlvl][1] = array('TYPE'=>$this->listtype,'MAXNUM'=>$this->listnum);
      }
      else {
        if (!empty($this->textbuffer))
        {
		$this->listitem[] = array($this->listlvl,$this->listnum,$this->textbuffer,$this->listoccur[$this->listlvl],$this->listitemtype);
		$this->listnum++;
        }
	  // Save current lineheight to inherit
	  $this->textbuffer = array();
  	  $occur = $this->listoccur[$this->listlvl];
        $this->listlist[$this->listlvl][$occur]['MAXNUM'] = $this->listnum; //save previous lvl's maxnum
        $this->listlvl++;
        $this->listnum = 0; // reset

	// mPDF 3.0
        if (!isset($this->listoccur[$this->listlvl]) || $this->listoccur[$this->listlvl] == 0) $this->listoccur[$this->listlvl] = 1;
        else $this->listoccur[$this->listlvl]++;
  	  $occur = $this->listoccur[$this->listlvl];
        $this->listlist[$this->listlvl][$occur] = array('TYPE'=>$this->listtype,'MAXNUM'=>$this->listnum);
      }


	// TOP LEVEL ONLY
	if ($this->listlvl == 1) {
	   if (isset($properties['MARGIN-TOP'])) { 
		$this->DivLn($this->ConvertSize($properties['MARGIN-TOP'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false),$this->blklvl,true,1); 	// collapsible
	   }
	   if (isset($properties['MARGIN-BOTTOM'])) { 
		$this->list_margin_bottom = $this->ConvertSize($properties['MARGIN-BOTTOM'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
	   }
	   // mPDF 4.0
	   if (isset($this->blk[$this->blklvl]['line_height'])) {
		$this->list_lineheight[$this->listlvl][$occur] = $this->blk[$this->blklvl]['line_height'];
	   }
	}
	$this->list_indent[$this->listlvl][$occur] = 5;	// mm default indent for each level
	if (isset($properties['TEXT-INDENT'])) { $this->list_indent[$this->listlvl][$occur] = $this->ConvertSize($properties['TEXT-INDENT'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); }
	if (isset($properties['TEXT-ALIGN'])) { $this->list_align[$this->listlvl][$occur] = $align[strtolower($properties['TEXT-ALIGN'])]; }

	// mPDF 4.0
	if (isset($properties['LINE-HEIGHT'])) { 
		$this->list_lineheight[$this->listlvl][$occur] = $this->fixLineheight($properties['LINE-HEIGHT']);
		
	}
	if (!isset($this->list_lineheight[$this->listlvl][$occur]) || !$this->list_lineheight[$this->listlvl][$occur]) { 
		$this->list_lineheight[$this->listlvl][$occur] = $this->default_lineheight_correction; 
	}


	$this->InlineProperties['LIST'][$this->listlvl][$occur] = $this->saveInlineProperties();
	$properties = array();
     break;



    case 'LI':
	// Start Block
	$this->lastoptionaltag = $tag; // Save current HTML specified optional endtag
      $this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
	// A simple list for inside a table
	if($this->tableLevel) {
	   $this->blockjustfinished=false;

	   // If already something in the Cell
	   if ((isset($this->cell[$this->row][$this->col]['maxs']) && $this->cell[$this->row][$this->col]['maxs'] > 0 ) || $this->cell[$this->row][$this->col]['s'] > 0 ) {
			$this->cell[$this->row][$this->col]['textbuffer'][] = array("\n",$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
			$this->cell[$this->row][$this->col]['text'][] = "\n";
			if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
				$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s'];
			}
			elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
				$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
			}
			$this->cell[$this->row][$this->col]['s'] = 0 ;
		}
		if ($this->listlvl == 0) { //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
			$this->listlvl++; // first depth level
			$this->listnum = 0; // reset
			$this->listlist[$this->listlvl] = array('TYPE'=>'disc','MAXNUM'=>$this->listnum);
		}
		$this->listnum++;
		switch($this->listlist[$this->listlvl]['TYPE']) {
		case 'A':
			$blt = $this->dec2alpha($this->listnum,true).$this->list_number_suffix;
			break;
		case 'a':
			$blt = $this->dec2alpha($this->listnum,false).$this->list_number_suffix;
			break;
		case 'I':
			$blt = $this->dec2roman($this->listnum,true).$this->list_number_suffix;
			break;
		case 'i':
			$blt = $this->dec2roman($this->listnum,false).$this->list_number_suffix;
			break;
		case '1':
			$blt = $this->listnum.$this->list_number_suffix;
            	break;
		default:
			$blt = '-';
			break;
		}

		// mPDF 3.0 - change to &nbsp; spaces
			$ls = str_repeat(chr(160).chr(160),($this->listlvl-1)*2) . $blt . ' '; 

		$this->cell[$this->row][$this->col]['textbuffer'][] = array($ls,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
		$this->cell[$this->row][$this->col]['text'][] = $ls;
		$this->cell[$this->row][$this->col]['s'] += $this->GetStringWidth($ls);
		break;
	}
	//Observation: </LI> is ignored
	if ($this->listlvl == 0) { //in case of malformed HTML code. Example:(...)</p><li>Content</li><p>Paragraph1</p>(...)
	//First of all, skip a line
		$this->listlvl++; // first depth level
		$this->listnum = 0; // reset
		$this->listoccur[$this->listlvl] = 1;
		$this->listlist[$this->listlvl][1] = array('TYPE'=>'disc','MAXNUM'=>$this->listnum);
	}
	if ($this->listnum == 0) {
		$this->listnum++;
		$this->textbuffer = array();
	}
	else {
		if (!empty($this->textbuffer)) {
			$this->listitem[] = array($this->listlvl,$this->listnum,$this->textbuffer,$this->listoccur[$this->listlvl],$this->listitemtype);
			$this->listnum++;
		}
		$this->textbuffer = array();
      }

	$this->listCSSlvl++;
	$properties = $this->MergeCSS('LIST',$tag,$attr);
	if (!empty($properties)) $this->setCSS($properties,'INLINE');
	$this->InlineProperties['LISTITEM'][$this->listlvl][$this->listoccur[$this->listlvl]][$this->listnum] = $this->saveInlineProperties();

	// List-type
	if (isset($attr['TYPE']) && $attr['TYPE']) { $this->listitemtype = $attr['TYPE']; }
	else if (isset($properties['LIST-STYLE-TYPE'])) { 
		// mPDF 4.0
		$this->listitemtype = $this->_getListStyle($properties['LIST-STYLE-TYPE']);
	}
	else if (isset($properties['LIST-STYLE'])) { 
		// mPDF 4.0
		$this->listitemtype = $this->_getListStyle($properties['LIST-STYLE']);
	}
	else $this->listitemtype = '';

      break;

  }//end of switch
}

// mPDF 4.0
function _getListStyle($ls) {
	if (stristr($ls,'disc')) { return 'disc'; }
	else if (stristr($ls,'circle')) { return 'circle'; }
	else if (stristr($ls,'square')) { return 'square'; }
	else if (stristr($ls,'decimal')) { return '1'; }
	else if (stristr($ls,'lower-roman')) { return 'i'; }
	else if (stristr($ls,'upper-roman')) { return 'I'; }
	else if (stristr($ls,'lower-latin')|| stristr($ls,'lower-alpha')) { return 'a'; }
	else if (stristr($ls,'upper-latin') || stristr($ls,'upper-alpha')) { return 'A'; }
	else if (stristr($ls,'none')) { return 'none'; }
	else { return ''; }
}



function CloseTag($tag)
{
	$this->ignorefollowingspaces = false; //Eliminate exceeding left-side spaces
    //Closing tag
    if($tag=='OPTION') { $this->selectoption['ACTIVE'] = false; 	$this->lastoptionaltag = ''; }

    if($tag=='TTS' or $tag=='TTA' or $tag=='TTZ') {
	if ($this->InlineProperties[$tag]) { $this->restoreInlineProperties($this->InlineProperties[$tag]); }
	unset($this->InlineProperties[$tag]);
	$ltag = strtolower($tag);
	$this->$ltag = false;
    }


    if($tag=='FONT' || $tag=='SPAN' || $tag=='CODE' || $tag=='KBD' || $tag=='SAMP' || $tag=='TT' || $tag=='VAR' 
	|| $tag=='INS' || $tag=='STRONG' || $tag=='CITE' || $tag=='SUB' || $tag=='SUP' || $tag=='S' || $tag=='STRIKE' || $tag=='DEL'
	|| $tag=='Q' || $tag=='EM' || $tag=='B' || $tag=='I' || $tag=='U' | $tag=='SMALL' || $tag=='BIG' || $tag=='ACRONYM') {

	if ($tag == 'SPAN') {
		if (isset($this->InlineProperties['SPAN'][$this->spanlvl]) && $this->InlineProperties['SPAN'][$this->spanlvl]) { $this->restoreInlineProperties($this->InlineProperties['SPAN'][$this->spanlvl]); }
		unset($this->InlineProperties['SPAN'][$this->spanlvl]);
		$this->spanlvl--;
	}
	else { 
		if (isset($this->InlineProperties[$tag]) && $this->InlineProperties[$tag]) { $this->restoreInlineProperties($this->InlineProperties[$tag]); }
		unset($this->InlineProperties[$tag]);
	}
    }


    if($tag=='A') {
	$this->HREF=''; 
	if (isset($this->InlineProperties['A'])) { $this->restoreInlineProperties($this->InlineProperties['A']); }
	unset($this->InlineProperties['A']);
    }





	// *********** BLOCKS ********************

    if($tag=='P' || $tag=='DIV' || $tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4' || $tag=='H5' || $tag=='H6' || $tag=='PRE' 
	 || $tag=='FORM' || $tag=='ADDRESS' || $tag=='BLOCKQUOTE' || $tag=='CENTER' || $tag=='DT'  || $tag=='DD'  || $tag=='DL' ) { 

	$this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
	$this->blockjustfinished=true;

	// mPDF 4.0
	if ($this->listlvl>0) { return; }

	if($this->tableLevel) {
		// mPDF 3.0
		if ($this->linebreakjustfinished) { $this->blockjustfinished=false; }
		// mPDF 4.0
		if (isset($this->InlineProperties['BLOCKINTABLE'])) { 
			if ($this->InlineProperties['BLOCKINTABLE']) { $this->restoreInlineProperties($this->InlineProperties['BLOCKINTABLE']); }
			unset($this->InlineProperties['BLOCKINTABLE']);
		}
		return;
	}
	$this->lastoptionaltag = '';
	$this->divbegin=false;
	// mPDF 3.0
	$this->linebreakjustfinished=false;

	$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

	// If float contained in a float, need to extend bottom to allow for it
	$currpos = $this->page*1000 + $this->y;
	if (isset($this->blk[$this->blklvl]['float_endpos']) && $this->blk[$this->blklvl]['float_endpos'] > $currpos) {
		$old_page = $this->page;
		$new_page = intval($this->blk[$this->blklvl]['float_endpos'] /1000);
		if ($old_page != $new_page) {
			$s = $this->PrintPageBackgrounds();
			// Writes after the marker so not overwritten later by page background etc.
			$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
			$this->pageBackgrounds = array();
			$this->page = $new_page;
			$this->ResetMargins();
			$this->Reset();
			$this->pageoutput[$this->page] = array();
		}
		$this->y = (($this->blk[$this->blklvl]['float_endpos'] *1000) % 1000000)/1000;	// mod changes operands to integers before processing
	}

	//Print content
	if ($this->lastblocklevelchange == 1) { $blockstate = 3; }	// Top & bottom margins/padding
	else if ($this->lastblocklevelchange == -1) { $blockstate = 2; }	// Bottom margins/padding only
	else { $blockstate = 0; }	// mPDF 3.0
	// called from after e.g. </table> </div> </div> ...    Outputs block margin/border and padding
	if (count($this->textbuffer) && $this->textbuffer[count($this->textbuffer)-1]) {
	  // mPDF 3.0 ...as long as not special content
	  if (substr($this->textbuffer[count($this->textbuffer)-1][0],0,3) != "\xbb\xa4\xac") {	// not special content
		$this->textbuffer[count($this->textbuffer)-1][0] = preg_replace('/[ ]+$/', '', $this->textbuffer[count($this->textbuffer)-1][0]);
	  }
	}
	if (count($this->textbuffer) == 0 && $this->lastblocklevelchange != 0) {
		$this->newFlowingBlock( $this->blk[$this->blklvl]['width'],$this->lineheight,'',false,false,2,true);	// true = newblock
		$this->finishFlowingBlock(true);	// true = END of flowing block
		$this->PaintDivBB('',$blockstate);
	}
	else {
		$this->printbuffer($this->textbuffer,$blockstate); 
	}


	$this->textbuffer=array();

	if ($this->blk[$this->blklvl]['keep_block_together']) {
		$this->printdivbuffer(); 
	}

	if ($this->kwt) {
		$this->kwt_height = $this->y - $this->kwt_y0;
	}

	$this->printfloatbuffer();

	if($tag=='PRE') { $this->ispre=false; }

	if ($this->blk[$this->blklvl]['float'] == 'R') {
		// If width not set, here would need to adjust and output buffer
		$s = $this->PrintPageBackgrounds();
		// Writes after the marker so not overwritten later by page background etc.
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
		$this->pageBackgrounds = array();
		$this->Reset();
		$this->pageoutput[$this->page] = array();

		for($i=($this->blklvl-1); $i >= 0; $i--) {
			if (isset($this->blk[$i]['float_endpos'])) { $this->blk[$i]['float_endpos'] = max($this->blk[$i]['float_endpos'], ($this->page*1000 + $this->y)); }
			else { $this->blk[$i]['float_endpos'] = $this->page*1000 + $this->y; }
		}

		$this->floatDivs[] = array(
			'side'=>'R',
			'startpage'=>$this->blk[$this->blklvl]['startpage'] ,
			'y0'=>$this->blk[$this->blklvl]['float_start_y'] ,
			'startpos'=> ($this->blk[$this->blklvl]['startpage']*1000 + $this->blk[$this->blklvl]['float_start_y']),
			'endpage'=>$this->page ,
			'y1'=>$this->y ,
			'endpos'=> ($this->page*1000 + $this->y),
			'w'=> $this->blk[$this->blklvl]['float_width'],
			'blklvl'=>$this->blklvl,
			'blockContext' => $this->blk[$this->blklvl-1]['blockContext']
		);

		$this->y = $this->blk[$this->blklvl]['float_start_y'] ;
		$this->page = $this->blk[$this->blklvl]['startpage'] ;
		$this->ResetMargins();
		$this->pageoutput[$this->page] = array();
	}
	if ($this->blk[$this->blklvl]['float'] == 'L') {
		// If width not set, here would need to adjust and output buffer
		$s = $this->PrintPageBackgrounds();
		// Writes after the marker so not overwritten later by page background etc.
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
		$this->pageBackgrounds = array();
		$this->Reset();
		$this->pageoutput[$this->page] = array();

		for($i=($this->blklvl-1); $i >= 0; $i--) {
			if (isset($this->blk[$i]['float_endpos'])) { $this->blk[$i]['float_endpos'] = max($this->blk[$i]['float_endpos'], ($this->page*1000 + $this->y)); }
			else { $this->blk[$i]['float_endpos'] = $this->page*1000 + $this->y; }
		}

		$this->floatDivs[] = array(
			'side'=>'L',
			'startpage'=>$this->blk[$this->blklvl]['startpage'] ,
			'y0'=>$this->blk[$this->blklvl]['float_start_y'] ,
			'startpos'=> ($this->blk[$this->blklvl]['startpage']*1000 + $this->blk[$this->blklvl]['float_start_y']),
			'endpage'=>$this->page ,
			'y1'=>$this->y ,
			'endpos'=> ($this->page*1000 + $this->y),
			'w'=> $this->blk[$this->blklvl]['float_width'],
			'blklvl'=>$this->blklvl,
			'blockContext' => $this->blk[$this->blklvl-1]['blockContext']
		);

		$this->y = $this->blk[$this->blklvl]['float_start_y'] ;
		$this->page = $this->blk[$this->blklvl]['startpage'] ;
		$this->ResetMargins();
		$this->pageoutput[$this->page] = array();
	}

	//Reset values
	$this->Reset();

	if ($this->blklvl > 0) {	// ==0 SHOULDN'T HAPPEN - NOT XHTML 
	   if ($this->blk[$this->blklvl]['tag'] == $tag) {
		unset($this->blk[$this->blklvl]);
		$this->blklvl--;
	   }
	   //else { echo $tag; exit; }	// debug - forces error if incorrectly nested html tags
	}

	$this->lastblocklevelchange = -1 ;
	// Reset Inline-type properties
	if (isset($this->blk[$this->blklvl]['InlineProperties'])) { $this->restoreInlineProperties($this->blk[$this->blklvl]['InlineProperties']); }

	$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

    }



    if($tag=='TH') $this->SetStyle('B',false);

    if(($tag=='TH' or $tag=='TD') && $this->tableLevel) {
	$this->lastoptionaltag = 'TR';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
	$this->tdbegin = false;

	// Added for correct calculation of cell column width - otherwise misses the last line if not end </p> etc.
	if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
		$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
	}
	elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
		$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
	}

	// Remove last <br> if at end of cell
	if (isset($this->cell[$this->row][$this->col]['textbuffer'])) { $ntb = count($this->cell[$this->row][$this->col]['textbuffer']); }
	else { $ntb = 0; }
	// mPDF 3.0 ... but only the last one
	if ($ntb>1 && $this->cell[$this->row][$this->col]['textbuffer'][$ntb-1][0] == "\n") {
		unset($this->cell[$this->row][$this->col]['textbuffer'][$ntb-1]);
	}

	if ($this->tablethead) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_thead'][$this->row] = true; }
	// mPDF 3.0
	if ($this->usetableheader && ($this->row == 0  || $this->tablethead) && $this->tableLevel==1) {
		$this->tableheadernrows = max($this->tableheadernrows, ($this->row+1));
	}
	// mPDF 4.0
	if ($this->tabletfoot) { $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$this->row] = true; }
	if ($this->tabletfoot && $this->tableLevel==1) {
		$this->tablefooternrows = max($this->tablefooternrows, ($this->row+1 - $this->tableheadernrows));
	}
	$this->Reset();
    }

    if($tag=='TR' && $this->tableLevel) {
	$this->lastoptionaltag = '';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
	$this->trow_text_rotate = '';
	$this->tabletheadjustfinished = false;
   }

    if($tag=='TBODY') {
	$this->lastoptionaltag = '';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
    }

    if($tag=='THEAD') {
	$this->lastoptionaltag = '';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
	$this->tablethead = 0;
	$this->tabletheadjustfinished = true;
	$this->thead_font_weight = '';
	$this->SetStyle('B',false);
	$this->thead_font_style = '';
	$this->SetStyle('I',false);

	$this->thead_valign_default = '';
	$this->thead_textalign_default = '';
    }

    if($tag=='TFOOT') {
	$this->lastoptionaltag = '';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
	$this->tabletfoot = 0;	// mPDF 4.0
	$this->tfoot_font_weight = '';
	$this->SetStyle('B',false);
	$this->tfoot_font_style = '';
	$this->SetStyle('I',false);

	$this->tfoot_valign_default = '';
	$this->tfoot_textalign_default = '';
    }



    if($tag=='TABLE') { // TABLE-END (
	$this->lastoptionaltag = '';
	unset($this->tablecascadeCSS[$this->tbCSSlvl]);
	$this->tbCSSlvl--;
	$this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = $this->cell;
	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['wc'] = array_pad(array(),$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nc'],array('miw'=>0,'maw'=>0));
	$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['hr'] = array_pad(array(),$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['nr'],0);

	// mPDF 4.0  Move TABLE FOOTER TFOOT to end of table 
	if (isset($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot']) && count($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'])) {
		$tfrows = array();
		foreach($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'] AS $r=>$val) {
			if ($val) { $tfrows[] = $r; }
		}
		$temp = array();
		$temptf = array();
		foreach($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] AS $k=>$row) {
			if (in_array($k,$tfrows)) {
				$temptf[] = $row;
			}
			else {
				$temp[] = $row;
			}
		}
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'] = array();
		for($i=count($temp) ; $i<(count($temp)+count($temptf)); $i++) {
			$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['is_tfoot'][$i] = true;
		}
		// Update nestedpos row references
		if (count($this->table[($this->tableLevel+1)])) {
		  foreach($this->table[($this->tableLevel+1)] AS $nid=>$nested) {
			$this->table[($this->tableLevel+1)][$nid]['nestedpos'][0] -= count($temptf);
		  }
		} 
		$this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'] = array_merge($temp, $temptf);
	}


	// Fix Borders *********************************************
	$this->_fixTableBorders($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]);


	if ($this->table_rotate <> 0) {
		$this->tablebuffer = array();
		// Max width for rotated table
		$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5);
		$this->tbrot_maxh = $this->blk[$this->blklvl]['inner_width'] ;		// Max width for rotated table
		$this->tbrot_align = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['a'] ;
	}
	$this->shrin_k = 1;

	if (!$this->shrink_this_table_to_fit) { $this->shrink_this_table_to_fit = $this->shrink_tables_to_fit; }

	if ($this->tableLevel>1) {
		// deal with nested table

		$this->_tableColumnWidth($this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]],true);

		$tmiw = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['miw'];
		$tmaw = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['maw'];
		$tl = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['tl'];

		// Go down to lower table level
		$this->tableLevel--;

		// Reset lower level table
		$this->base_table_properties = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['baseProperties'];
		$this->cell = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['cells'];
		$this->row = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currrow'];
		$this->col = $this->table[$this->tableLevel][$this->tbctr[$this->tableLevel]]['currcol'];

		$objattr['type'] = 'nestedtable';
		$objattr['nestedcontent'] = $this->tbctr[($this->tableLevel+1)];
		$objattr['table'] = $this->tbctr[$this->tableLevel];
		$objattr['row'] = $this->row;
		$objattr['col'] = $this->col;
		$objattr['level'] = $this->tableLevel;
		$e = "\xbb\xa4\xactype=nestedtable,objattr=".serialize($objattr)."\xbb\xa4\xac";
		$this->cell[$this->row][$this->col]['textbuffer'][] = array($e,$this->HREF,$this->currentfontstyle,$this->colorarray,$this->currentfontfamily,$this->SUP,$this->SUB,'',$this->strike,$this->outlineparam,$this->spanbgcolorarray,$this->currentfontsize);
		$this->cell[$this->row][$this->col]['s'] += $tl ;
		if (!isset($this->cell[$this->row][$this->col]['maxs'])) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
		}
		elseif($this->cell[$this->row][$this->col]['maxs'] < $this->cell[$this->row][$this->col]['s']) {
			$this->cell[$this->row][$this->col]['maxs'] = $this->cell[$this->row][$this->col]['s']; 
		}
		$this->cell[$this->row][$this->col]['s'] = 0;// reset
		if ((isset($this->cell[$this->row][$this->col]['nestedmaw']) && $this->cell[$this->row][$this->col]['nestedmaw'] < $tmaw) || !isset($this->cell[$this->row][$this->col]['nestedmaw'])) { $this->cell[$this->row][$this->col]['nestedmaw'] = $tmaw ; }
		if ((isset($this->cell[$this->row][$this->col]['nestedmiw']) && $this->cell[$this->row][$this->col]['nestedmiw'] < $tmiw) || !isset($this->cell[$this->row][$this->col]['nestedmiw'])) { $this->cell[$this->row][$this->col]['nestedmiw'] = $tmiw ; }
		$this->cell[$this->row][$this->col]['nestedcontent'][] = $this->tbctr[($this->tableLevel+1)];
		$this->tdbegin = true;
		$this->nestedtablejustfinished = true;
		$this->ignorefollowingspaces = true;
		return;
	}
	$this->cMarginL = 0;
	$this->cMarginR = 0;
	$this->cMarginT = 0;
	$this->cMarginB = 0;
	$this->cellPaddingL = 0;
	$this->cellPaddingR = 0;
	$this->cellPaddingT = 0;
	$this->cellPaddingB = 0;

	if (!$this->kwt_saved) { $this->kwt_height = 0; }

	list($check,$tablemiw) = $this->_tableColumnWidth($this->table[1][1],true);

	$save_table = $this->table;
	$reset_to_minimum_width = false;
	$added_page = false;

	if ($check > 1) {	
		if ($check > $this->shrink_this_table_to_fit && $this->table_rotate) { 
				$this->AddPage($this->CurOrientation);
				$added_page = true;
				$this->kwt_moved = true; 
				$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				// mPDF 3.0
				//$check = $tablemiw/$this->tbrot_maxw; 	// undo any shrink
				$check = 1; 	// undo any shrink
		}
		$reset_to_minimum_width = true;
	}

	if ($reset_to_minimum_width) {

		$this->shrin_k = $check;

 		$this->default_font_size /= $this->shrin_k;
		$this->SetFontSize($this->default_font_size, false );

		$this->shrinkTable($this->table[1][1],$this->shrin_k);

		$this->_tableColumnWidth($this->table[1][1],false);	// repeat

		// Starting at $this->innermostTableLevel
		// Shrink table values - and redo columnWidth
		for($lvl=2;$lvl<=$this->innermostTableLevel;$lvl++) {
			for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
				$this->shrinkTable($this->table[$lvl][$nid],$this->shrin_k);
				$this->_tableColumnWidth($this->table[$lvl][$nid],false);
			}
		}
	}


	// Set table cell widths for top level table
	// Use $shrin_k to resize but don't change again
	$this->SetLineHeight('',$this->table_lineheight);

	// Top level table
	$this->_tableWidth($this->table[1][1]);

	// Now work through any nested tables setting child table[w'] = parent cell['w']
	// Now do nested tables _tableWidth
	for($lvl=2;$lvl<=$this->innermostTableLevel;$lvl++) {
		for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
			// HERE set child table width = cell width

			list($parentrow, $parentcol, $parentnid) = $this->table[$lvl][$nid]['nestedpos'];
			if (isset($this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan']) && $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan']> 1) {
			   $parentwidth = 0;
			   for($cs=0;$cs<$this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan'] ; $cs++) {
				$parentwidth += $this->table[($lvl-1)][$parentnid]['wc'][$parentcol+$cs]; 
			   }
			}
			else { $parentwidth = $this->table[($lvl-1)][$parentnid]['wc'][$parentcol]; }


			//$parentwidth -= ALLOW FOR PADDING ETC.in parent cell
			if ($this->table[$lvl-1][$parentnid]['borders_separate']) {
			  $parentwidth -= $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['L']['w']
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['R']['w']
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['L']
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['R']
				+ $this->table[($lvl-1)][$parentnid]['border_spacing_H'];
			}
			else {
			  $parentwidth -= $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['L']['w']/2
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['R']['w']/2
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['L']
				+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['R'];
			}
			if (isset($this->table[$lvl][$nid]['wpercent']) && $this->table[$lvl][$nid]['wpercent'] && $lvl>1) {
				$this->table[$lvl][$nid]['w'] = $parentwidth;
			}
			else if ($parentwidth > $this->table[$lvl][$nid]['maw']) {
				$this->table[$lvl][$nid]['w'] = $this->table[$lvl][$nid]['maw'];
			}
			else {
				$this->table[$lvl][$nid]['w'] = $parentwidth;
			}

			$this->_tableWidth($this->table[$lvl][$nid]);
		}
	}

	// Starting at $this->innermostTableLevel
	// Cascade back up nested tables: setting heights back up the tree
	for($lvl=$this->innermostTableLevel;$lvl>0;$lvl--) {
		for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
			list($tableheight,$maxrowheight,$fullpage,$remainingpage) = $this->_tableHeight($this->table[$lvl][$nid]);
		}
	}

	$recalculate = 1;
	// RESIZING ALGORITHM
	if ($maxrowheight > $fullpage) { 
		$recalculate = $this->tbsqrt($maxrowheight / $fullpage, 1); 
	}
	else if ($this->table_rotate) {	// NB $remainingpage == $fullpage == the width of the page
		if ($tableheight > $remainingpage) { 
			// If can fit on remainder of page whilst respecting autsize value..
			if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, 1)) <= $this->shrink_this_table_to_fit) {
				$recalculate = $this->tbsqrt($tableheight / $remainingpage, 1); 
			}
			else if (!$added_page) {
				$this->AddPage($this->CurOrientation);
				$added_page = true;
				$this->kwt_moved = true; 
				$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				// mPDF 3.0 added 0.001 to force it to recalculate
				$recalculate = (1 / $this->shrin_k) + 0.001; 	// undo any shrink
			}
		}
	}
	else if ($this->table_keep_together) {
		if ($tableheight > $fullpage) { 
			if (($this->shrin_k * $this->tbsqrt($tableheight / $fullpage, 1)) <= $this->shrink_this_table_to_fit) {
				$recalculate = $this->tbsqrt($tableheight / $fullpage, 1); 
			}
			else {
				$this->AddPage($this->CurOrientation);
				$added_page = true;
				$this->kwt_moved = true; 
				$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				$recalculate = $this->tbsqrt($tableheight / $fullpage, 1); 
			}
		}
		else if ($tableheight > $remainingpage) { 
			// If can fit on remainder of page whilst respecting autsize value..
			if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, 1)) <= $this->shrink_this_table_to_fit) {
				$recalculate = $this->tbsqrt($tableheight / $remainingpage, 1); 
			}
			else {
				$this->AddPage($this->CurOrientation);
				$added_page = true;
				$this->kwt_moved = true; 
				$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				// mPDF 3.0 added 0.001 to force it to recalculate
				$recalculate = (1 / $this->shrin_k) + 0.001; 	// undo any shrink
			}
		}
	}
	else { $recalculate = 1; }

	if ($recalculate > $this->shrink_this_table_to_fit) { $recalculate = $this->shrink_this_table_to_fit; }

	$iteration = 2;

	// RECALCULATE
	while($recalculate <> 1) {
		$this->shrin_k1 = $recalculate ;
		$this->shrin_k *= $recalculate ;
 		$this->default_font_size /= ($this->shrin_k1) ;
		$this->SetFontSize($this->default_font_size, false );
		$this->SetLineHeight('',$this->table_lineheight);
		$this->table = $save_table;
		if ($this->shrin_k <> 1) { $this->shrinkTable($this->table[1][1],$this->shrin_k); }
		$this->_tableColumnWidth($this->table[1][1],false);	// repeat

		// Starting at $this->innermostTableLevel
		// Shrink table values - and redo columnWidth
		for($lvl=2;$lvl<=$this->innermostTableLevel;$lvl++) {
			for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
				if ($this->shrin_k <> 1) { $this->shrinkTable($this->table[$lvl][$nid],$this->shrin_k); }
				$this->_tableColumnWidth($this->table[$lvl][$nid],false);
			}
		}
		// Set table cell widths for top level table

		// Top level table
		$this->_tableWidth($this->table[1][1]);

		// Now work through any nested tables setting child table[w'] = parent cell['w']
		// Now do nested tables _tableWidth
		for($lvl=2;$lvl<=$this->innermostTableLevel;$lvl++) {
			for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
				// HERE set child table width = cell width

				list($parentrow, $parentcol, $parentnid) = $this->table[$lvl][$nid]['nestedpos'];
				if (isset($this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan']) && $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan']> 1) {
				   $parentwidth = 0;
				   for($cs=0;$cs<$this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['colspan'] ; $cs++) {
					$parentwidth += $this->table[($lvl-1)][$parentnid]['wc'][$parentcol+$cs]; 
				   }
				}
				else { $parentwidth = $this->table[($lvl-1)][$parentnid]['wc'][$parentcol]; }

				//$parentwidth -= ALLOW FOR PADDING ETC.in parent cell
				if ($this->table[$lvl-1][$parentnid]['borders_separate']) {
				  $parentwidth -= $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['L']['w']
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['R']['w']
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['L']
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['R']
					+ $this->table[($lvl-1)][$parentnid]['border_spacing_H'];
				}
				else {
				  $parentwidth -= ($this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['L']['w']
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['border_details']['R']['w']) /2
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['L']
					+ $this->table[($lvl-1)][$parentnid]['cells'][$parentrow][$parentcol]['padding']['R'];
				}
	
				if (isset($this->table[$lvl][$nid]['wpercent']) && $this->table[$lvl][$nid]['wpercent'] && $lvl>1) {
					$this->table[$lvl][$nid]['w'] = $parentwidth;
				}
				else if ($parentwidth > $this->table[$lvl][$nid]['maw']) {
					$this->table[$lvl][$nid]['w'] = $this->table[$lvl][$nid]['maw'] ;
				}
				else {
					$this->table[$lvl][$nid]['w'] = $parentwidth;
				}
				$this->_tableWidth($this->table[$lvl][$nid]);
			}
		}
	

		// Starting at $this->innermostTableLevel
		// Cascade back up nested tables: setting heights back up the tree
		for($lvl=$this->innermostTableLevel;$lvl>0;$lvl--) {
			for ($nid=1; $nid<=$this->tbctr[$lvl]; $nid++) {
				list($tableheight,$maxrowheight,$fullpage,$remainingpage) = $this->_tableHeight($this->table[$lvl][$nid]);
			}
		}

		// RESIZING ALGORITHM

		if ($maxrowheight > $fullpage) { $recalculate = $this->tbsqrt($maxrowheight / $fullpage, $iteration); $iteration++; }
		else if ($this->table_rotate && $tableheight > $remainingpage && !$added_page) { 
			// If can fit on remainder of page whilst respecting autosize value..
			if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, $iteration)) <= $this->shrink_this_table_to_fit) {
				$recalculate = $this->tbsqrt($tableheight / $remainingpage, $iteration); $iteration++; 
			}
			else {
				if (!$added_page) { 
					$this->AddPage($this->CurOrientation); 
					$added_page = true;
					$this->kwt_moved = true; 
					$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				}
				// mPDF 3.0 added 0.001 to force it to recalculate
				$recalculate = (1 / $this->shrin_k) + 0.001; 	// undo any shrink
			}
		}
		else if ($this->table_keep_together) {
			if ($tableheight > $fullpage) { 
				if (($this->shrin_k * $this->tbsqrt($tableheight / $fullpage, $iteration)) <= $this->shrink_this_table_to_fit) {
					$recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration); $iteration++; 
				}
				else {
				   if (!$added_page) { 
					$this->AddPage($this->CurOrientation);
					$added_page = true;
					$this->kwt_moved = true; 
					$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
				   }
				   $recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration); $iteration++; 
				}
			}
			else if ($tableheight > $remainingpage) { 
				// If can fit on remainder of page whilst respecting autosize value..
				if (($this->shrin_k * $this->tbsqrt($tableheight / $remainingpage, $iteration)) <= $this->shrink_this_table_to_fit) {
					$recalculate = $this->tbsqrt($tableheight / $remainingpage, $iteration);  $iteration++; 
				}
				else {
					if (!$added_page) { 
						$this->AddPage($this->CurOrientation); 
						$added_page = true;
						$this->kwt_moved = true; 
						$this->tbrot_maxw = $this->h - ($this->y + $this->bMargin + 5) - $this->kwt_height;
					}
					// mPDF 4.0 
					//$recalculate = $this->tbsqrt($tableheight / $fullpage, $iteration); $iteration++; 
					$recalculate = (1 / $this->shrin_k) + 0.001; 	// undo any shrink
				}
			}
			else { $recalculate = 1; }
		}
		else { $recalculate = 1; }

	}

	// keep-with-table: if page has advanced, print out buffer now, else done in fn. _Tablewrite()
	if ($this->kwt_saved && $this->kwt_moved) {
		$this->printkwtbuffer();
		$this->kwt_moved = false;
		$this->kwt_saved = false;
	}

	// Recursively writes all tables starting at top level
	$this->_tableWrite($this->table[1][1]);

	if ($this->table_rotate && count($this->tablebuffer)) {
		$this->PageBreakTrigger=$this->h-$this->bMargin;
		$save_tr = $this->table_rotate;
		$save_y = $this->y;
		$this->table_rotate = 0;
		$this->y = $this->tbrot_y0;
		$h = 	$this->tbrot_w;
		$this->DivLn($h,$this->blklvl,true);

		$this->table_rotate = $save_tr;
		$this->y = $save_y;

		$this->printtablebuffer();
	}

	$this->table_rotate = 0;	// flag used for table rotation

	$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];

	//Reset values

	// Keep-with-table
	$this->kwt = false;
	$this->kwt_y0 = 0;
	$this->kwt_x0 = 0;
	$this->kwt_height = 0;
	$this->kwt_buffer = array();
	$this->kwt_Links = array();
	$this->kwt_Annots = array();
	$this->kwt_moved = false;
	$this->kwt_saved = false;
	// mPDF 3.0
	$this->kwt_Reference = array();
	$this->kwt_BMoutlines = array();
	$this->kwt_toc = array();

	$this->shrin_k = 1;
	$this->shrink_this_table_to_fit = 0;

	unset($this->table);
	$this->table=array(); //array 
	$this->tableLevel=0;
	$this->tbctr=array();
	$this->innermostTableLevel=0;
	$this->tbCSSlvl = 0;
	$this->tablecascadeCSS = array();

	unset($this->cell);
	$this->cell=array(); //array 

	$this->col=-1; //int
	$this->row=-1; //int
	$this->Reset();

 	$this->cellPaddingL = 0;
 	$this->cellPaddingT = 0;
 	$this->cellPaddingR = 0;
 	$this->cellPaddingB = 0;
 	$this->cMarginL = 0;
 	$this->cMarginT = 0;
 	$this->cMarginR = 0;
 	$this->cMarginB = 0;
 	$this->default_font_size = $this->original_default_font_size;
	$this->default_font = $this->original_default_font;
   	$this->SetFontSize($this->default_font_size, false);
	$this->SetFont($this->default_font,'',0,false);
	$this->SetLineHeight();
	if (isset($this->blk[$this->blklvl]['InlineProperties'])) { $this->restoreInlineProperties($this->blk[$this->blklvl]['InlineProperties']);}

    }


	// *********** LISTS ********************

    if($tag=='LI') { 
	$this->lastoptionaltag = ''; 
	unset($this->listcascadeCSS[$this->listCSSlvl]);
	$this->listCSSlvl--;
	if (isset($this->listoccur[$this->listlvl]) && isset($this->InlineProperties['LIST'][$this->listlvl][$this->listoccur[$this->listlvl]])) { $this->restoreInlineProperties($this->InlineProperties['LIST'][$this->listlvl][$this->listoccur[$this->listlvl]]); } 
    }


    if(($tag=='UL') or ($tag=='OL')) {
      $this->ignorefollowingspaces = true; //Eliminate exceeding left-side spaces
	unset($this->listcascadeCSS[$this->listCSSlvl]);
	$this->listCSSlvl--;

	$this->lastoptionaltag = '';
	// A simple list for inside a table
	if($this->tableLevel) {
		$this->listlist[$this->listlvl]['MAXNUM'] = $this->listnum; //save previous lvl's maxnum
		unset($this->listlist[$this->listlvl]);
		$this->listlvl--;
		if (isset($this->listlist[$this->listlvl]['MAXNUM'])) { $this->listnum = $this->listlist[$this->listlvl]['MAXNUM']; } // restore previous levels
		if ($this->listlvl == 0) { $this->listjustfinished = true; }
		return;
	}

	if ($this->listlvl > 1) { // returning one level
		$this->listjustfinished=true;
		if (!empty($this->textbuffer)) { 
			$this->listitem[] = array($this->listlvl,$this->listnum,$this->textbuffer,$this->listoccur[$this->listlvl],$this->listitemtype);
		}
		// mPDF 3.0
		else { 
			$this->listnum--;
		}

		$this->textbuffer = array();
		$occur = $this->listoccur[$this->listlvl]; 
		$this->listlist[$this->listlvl][$occur]['MAXNUM'] = $this->listnum; //save previous lvl's maxnum
		$this->listlvl--;
		$occur = $this->listoccur[$this->listlvl];
		$this->listnum = $this->listlist[$this->listlvl][$occur]['MAXNUM']; // recover previous level's number
		$this->listtype = $this->listlist[$this->listlvl][$occur]['TYPE']; // recover previous level's type
		if ($this->InlineProperties['LIST'][$this->listlvl][$occur]) { $this->restoreInlineProperties($this->InlineProperties['LIST'][$this->listlvl][$occur]); }

	}
	else { // We are closing the last OL/UL tag
		if (!empty($this->textbuffer)) {
			$this->listitem[] = array($this->listlvl,$this->listnum,$this->textbuffer,$this->listoccur[$this->listlvl],$this->listitemtype);
		}
		// mPDF 3.0
		else { 
			$this->listnum--;
		}

		$occur = $this->listoccur[$this->listlvl]; 
		$this->listlist[$this->listlvl][$occur]['MAXNUM'] = $this->listnum;
		$this->textbuffer = array();
		$this->listlvl--;

		$this->printlistbuffer();
		unset($this->InlineProperties['LIST']);
		// SPACING AFTER LIST (Top level only)
		$this->Ln(0);
		if ($this->list_margin_bottom) {
			$this->DivLn($this->list_margin_bottom,$this->blklvl,true,1); 	// collapsible
		}
		if (isset($this->blk[$this->blklvl]['InlineProperties'])) { $this->restoreInlineProperties($this->blk[$this->blklvl]['InlineProperties']);}
		$this->listjustfinished = true; 
		$this->listCSSlvl = 0;
		$this->listcascadeCSS = array();
	}
    }


}


// This function determines the shrink factor when resizing tables
// val is the table_height / page_height_available
// returns a scaling factor used as $shrin_k to resize the table
// Overcompensating will be quicker but may unnecessarily shrink table too much
// Undercompensating means it will reiterate more times (taking more processing time)
function tbsqrt($val, $iteration=3) {
	$k = 4;	// Alters number of iterations until it returns $val itself - Must be > 2
	// Probably best guess and most accurate
	if ($iteration==1) return sqrt($val);
	// Faster than using sqrt (because it won't undercompensate), and gives reasonable results
	//return 1+(($val-1)/2);
	if (2-(($iteration-2)/($k-2)) == 0) { return $val; }
	return 1+(($val-1)/(2-(($iteration-2)/($k-2))));
}


function printlistbuffer() {
    //Save x coordinate
    $x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];
    $this->cMarginL = 0;
    $this->cMarginR = 0;
    $currIndentLvl = -1;
    $lastIndent = array();
    $bak_page = $this->page;
    $indent = 0;  // mPDF 3.0
    foreach($this->listitem as $item)
    {
	// COLS
	$oldcolumn = $this->CurrCol;

	  $this->bulletarray = array();
        //Get list's buffered data
        $this->listlvl = $lvl = $item[0];
        $num = $item[1];
        $this->textbuffer = $item[2];
        $occur = $item[3];
	  if ($item[4]) { $type = $item[4]; }	// listitemtype
	  else { $type = $this->listlist[$lvl][$occur]['TYPE']; }
        $maxnum = $this->listlist[$lvl][$occur]['MAXNUM'];
	  $this->restoreInlineProperties($this->InlineProperties['LIST'][$lvl][$occur]);
	  $this->SetFont($this->FontFamily,$this->FontStyle,$this->FontSizePt,true,true);	// force to write
	  $clh = $this->FontSize;

	  // Set the bullet fontsize
	  $bullfs = $this->InlineProperties['LISTITEM'][$lvl][$occur][$num]['size'];

        $space_width = $this->GetStringWidth(' ') * 1.5;

        //Set default width & height values
        $this->divwidth = $this->blk[$this->blklvl]['inner_width'];
        $this->divheight = $this->lineheight;
	  $typefont = $this->FontFamily;
        switch($type) //Format type
        {
          case 'none':
		  $type = '';
  	        $blt_width = 0;
             break;
          case 'A':
              $anum = $this->dec2alpha($num,true);
              $maxnum = $this->dec2alpha($maxnum,true);
		  if ($this->directionality == 'rtl') { $type = $this->list_number_suffix . $anum; }
		  else { $type = $anum . $this->list_number_suffix; }
  	        $blt_width = $this->GetStringWidth(str_repeat('W',strlen($maxnum)).$this->list_number_suffix);
             break;
          case 'a':
              $anum = $this->dec2alpha($num,false);
              $maxnum = $this->dec2alpha($maxnum,false);
		  if ($this->directionality == 'rtl') { $type = $this->list_number_suffix . $anum; }
		  else { $type = $anum . $this->list_number_suffix; }
 	        $blt_width = $this->GetStringWidth(str_repeat('m',strlen($maxnum)).$this->list_number_suffix);
             break;
          case 'I':
              $anum = $this->dec2roman($num,true);
		  if ($this->directionality == 'rtl') { $type = $this->list_number_suffix . $anum; }
		  else { $type = $anum . $this->list_number_suffix; }
		  // mPDF 4.0
		  if ($maxnum>87) { $bbit = 87; }
		  else if ($maxnum>86) { $bbit = 86; }
		  else if ($maxnum>37) { $bbit = 38; }
		  else if ($maxnum>36) { $bbit = 37; }
		  else if ($maxnum>27) { $bbit = 28; }
		  else if ($maxnum>26) { $bbit = 27; }
		  else if ($maxnum>17) { $bbit = 18; }
		  else if ($maxnum>16) { $bbit = 17; }
		  else if ($maxnum>7) { $bbit = 8; }
		  else if ($maxnum>6) { $bbit = 7; }
		  else if ($maxnum>3) { $bbit = 4; }
		  else { $bbit = $maxnum; }
              $maxlnum = $this->dec2roman($bbit,true);
	        $blt_width = $this->GetStringWidth($maxlnum.$this->list_number_suffix);
              break;
          case 'i':
              $anum = $this->dec2roman($num,false);
		  if ($this->directionality == 'rtl') { $type = $this->list_number_suffix . $anum; }
		  else { $type = $anum . $this->list_number_suffix; }
		  // mPDF 4.0
		  if ($maxnum>87) { $bbit = 87; }
		  else if ($maxnum>86) { $bbit = 86; }
		  else if ($maxnum>37) { $bbit = 38; }
		  else if ($maxnum>36) { $bbit = 37; }
		  else if ($maxnum>27) { $bbit = 28; }
		  else if ($maxnum>26) { $bbit = 27; }
		  else if ($maxnum>17) { $bbit = 18; }
		  else if ($maxnum>16) { $bbit = 17; }
		  else if ($maxnum>7) { $bbit = 8; }
		  else if ($maxnum>6) { $bbit = 7; }
		  else if ($maxnum>3) { $bbit = 4; }
		  else { $bbit = $maxnum; }
              $maxlnum = $this->dec2roman($bbit,false);
		  // mPDF 4.0
	        $blt_width = $this->GetStringWidth($maxlnum.$this->list_number_suffix);
              break;
          case 'disc':
              $type = $this->chrs[108]; // bullet disc in Zapfdingbats  'l'
		  $typefont = 'zapfdingbats';
		  $blt_width = (0.791 * $this->FontSize/2.5); 
              break;
          case 'circle':
              $type = $this->chrs[109]; // circle in Zapfdingbats   'm'
		  $typefont = 'zapfdingbats';
		  $blt_width = (0.873 * $this->FontSize/2.5); 
              break;
          case 'square':
              $type = $this->chrs[110]; //black square in Zapfdingbats font   'n'
		  $typefont = 'zapfdingbats';
		  $blt_width = (0.761 * $this->FontSize/2.5); 
              break;
          case '1':
	    default:
		  if ($this->directionality == 'rtl') { $type = $this->list_number_suffix . $num; }
		  else { $type = $num . $this->list_number_suffix; }
	        $blt_width = $this->GetStringWidth(str_repeat('5',strlen($maxnum)).$this->list_number_suffix);
              break;
        }

	if ($currIndentLvl < $lvl) {
		if ($lvl > 1 || $this->list_indent_first_level) { 
			$indent += $this->list_indent[$lvl][$occur]; 
			$lastIndent[$lvl] = $this->list_indent[$lvl][$occur];
		}
	}
	else if ($currIndentLvl > $lvl) {
	  while ($currIndentLvl > $lvl) {
		$indent -= $lastIndent[$currIndentLvl];
		$currIndentLvl--;
	  }
	}
	$currIndentLvl = $lvl;

	// mPDF 3.0
	if (isset($this->list_align[$this->listlvl][$occur])) { $this->divalign = $this->list_align[$this->listlvl][$occur]; }
	else { $this->divalign = ''; }

	  // mPDF 4.0
	  if ($this->list_align_style == 'L') { $lalign = 'L'; }
	  else { $lalign = 'R';  }
        $this->divwidth = $this->blk[$this->blklvl]['width'] - ($indent + $blt_width + $space_width) ;
	  $xb =  $this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] - $blt_width - $space_width; 
        //Output bullet
	  $this->bulletarray = array('w'=>$blt_width,'h'=>$clh,'txt'=>$type,'x'=>$xb,'align'=>$lalign,'font'=>$typefont,'level'=>$lvl, 'occur'=>$occur, 'num'=>$num, 'fontsize'=>$bullfs );
	  $this->x = $x + $indent + $blt_width + $space_width;

      //Print content
  	$this->printbuffer($this->textbuffer,'',false,true);
      $this->textbuffer=array();

	// Added to correct for OddEven Margins
   	if  ($this->page != $bak_page) {
		$x=$x +$this->MarginCorrection;
		$bak_page = $this->page;
	}

    }

    //Reset all used values
    $this->listoccur = array();
    $this->listitem = array();
    $this->listlist = array();
    $this->listlvl = 0;
    $this->listnum = 0;
    $this->listtype = '';
    $this->textbuffer = array();
    $this->divwidth = 0;
    $this->divheight = 0;
    $this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];
}


function printbuffer($arrayaux,$blockstate=0,$is_table=false,$is_list=false)
{
// $blockstate = 0;	// NO margins/padding
// $blockstate = 1;	// Top margins/padding only
// $blockstate = 2;	// Bottom margins/padding only
// $blockstate = 3;	// Top & bottom margins/padding
	$this->spanbgcolorarray = array();
	$this->spanbgcolor = false;
	$paint_ht_corr = 0;

	if (count($this->floatDivs)) {
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
		if (($this->blk[$this->blklvl]['inner_width']-$l_width-$r_width) < $this->GetStringWidth('WW')) {
			// Too narrow to fit - try to move down past L or R float
			if ($l_max < $r_max && ($this->blk[$this->blklvl]['inner_width']-$r_width) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('LEFT', $this->blklvl); 
			}
			else if ($r_max < $l_max && ($this->blk[$this->blklvl]['inner_width']-$l_width) > $this->GetStringWidth('WW')) {
				$this->ClearFloats('RIGHT', $this->blklvl); 
			}
			else { $this->ClearFloats('BOTH', $this->blklvl); }
		}
	}

    	$bak_y = $this->y;
	$bak_x = $this->x;
	$align = '';
	if (!$is_table && !$is_list) {
		if (isset($this->blk[$this->blklvl]['align']) && $this->blk[$this->blklvl]['align']) { $align = $this->blk[$this->blklvl]['align']; }
		// Block-align is set by e.g. <.. align="center"> Takes priority for this block but not inherited
		if (isset($this->blk[$this->blklvl]['block-align']) && $this->blk[$this->blklvl]['block-align']) { $align = $this->blk[$this->blklvl]['block-align']; }
		$this->divwidth = $this->blk[$this->blklvl]['width'];
	}
	else {
		$align = $this->divalign;
	}
	$oldpage = $this->page;

	// ADDED for Out of Block now done as Flowing Block
	if ($this->divwidth == 0) { 
		$this->divwidth = $this->pgwidth; 
	}

	if (!$is_table && !$is_list) { $this->SetLineHeight($this->FontSizePt,$this->blk[$this->blklvl]['line_height']); }
	$this->divheight = $this->lineheight;
	$old_height = $this->divheight;

    // As a failsafe - if font has been set but not output to page
    $this->SetFont($this->default_font,'',$this->default_font_size,true,true);	// force output to page

    $array_size = count($arrayaux);
    $this->newFlowingBlock( $this->divwidth,$this->divheight,$align,$is_table,$is_list,$blockstate,true);	// true = newblock

	// Added - Otherwise <div><div><p> did not output top margins/padding for 1st/2nd div
    if ($array_size == 0) { $this->finishFlowingBlock(true); }	// true = END of flowing block

    for($i=0;$i < $array_size; $i++)
    {

	// COLS
	$oldcolumn = $this->CurrCol;

      $vetor = $arrayaux[$i];
      if ($i == 0 and $vetor[0] != "\n" and !$this->ispre) {
		$vetor[0] = ltrim($vetor[0]);
	}

	// FIXED TO ALLOW IT TO SHOW '0' 
      if (empty($vetor[0]) && !($vetor[0]==='0') && empty($vetor[7])) { //Ignore empty text and not carrying an internal link
		//Check if it is the last element. If so then finish printing the block
	     	if ($i == ($array_size-1)) { $this->finishFlowingBlock(true); }	// true = END of flowing block
		continue;
	}


      //Activating buffer properties
      if(isset($vetor[11]) and $vetor[11] != '') { 	 // Font Size
		if ($is_table && $this->shrin_k) {
			$this->SetFontSize($vetor[11]/$this->shrin_k,false); 
		}
		else {
			$this->SetFontSize($vetor[11],false); 
		}
	}
      if(isset($vetor[10]) and !empty($vetor[10])) //Background color
      {
		$this->spanbgcolorarray = $vetor[10];
		$this->spanbgcolor = true;
      }
      if(isset($vetor[9]) and !empty($vetor[9])) // Outline parameters
      {
          $cor = $vetor[9]['COLOR'];
          $outlinewidth = $vetor[9]['WIDTH'];
          $this->SetTextOutline($outlinewidth,$cor['R'],$cor['G'],$cor['B']);
          $this->outline_on = true;
      }
      if(isset($vetor[8]) and $vetor[8] === true) // strike-through the text
      {
          $this->strike = true;
      }
      if(isset($vetor[7]) and $vetor[7] != '') // internal link: <a name="anyvalue">
      {
	  if ($this->ColActive) { $ily = $this->y0; } else { $ily = $this->y; }	// use top of columns
        $this->internallink[$vetor[7]] = array("Y"=>$ily,"PAGE"=>$this->page );
        if (empty($vetor[0])) { //Ignore empty text
		//Check if it is the last element. If so then finish printing the block
      	if ($i == ($array_size-1)) { $this->finishFlowingBlock(true); }	// true = END of flowing block
		continue;
	  }
      }
      if(isset($vetor[6]) and $vetor[6] === true) // Subscript 
      {
  		$this->SUB = true;
      }
      if(isset($vetor[5]) and $vetor[5] === true) // Superscript
      {
		$this->SUP = true;
      }
      if(isset($vetor[4]) and $vetor[4] != '') {  // Font Family
		$font = $this->SetFont($vetor[4],$this->FontStyle,0,false); 
	}
      if (!empty($vetor[3])) //Font Color
      {
		$cor = $vetor[3];
		$this->SetTextColor($cor['R'],$cor['G'],$cor['B']);
      }
      if(isset($vetor[2]) and $vetor[2] != '') //Bold,Italic,Underline styles
      {
          if (strpos($vetor[2],"B") !== false) $this->SetStyle('B',true);
          if (strpos($vetor[2],"I") !== false) $this->SetStyle('I',true);
          if (strpos($vetor[2],"U") !== false) $this->SetStyle('U',true); 
      }
      if(isset($vetor[1]) and $vetor[1] != '') //LINK
      {
	  // mPDF 3.0
        if (strpos($vetor[1],".") === false && strpos($vetor[1],"@") !== 0) //assuming every external link has a dot indicating extension (e.g: .html .txt .zip www.somewhere.com etc.) 
        {
          //Repeated reference to same anchor?
          while(array_key_exists($vetor[1],$this->internallink)) $vetor[1]="#".$vetor[1];
          $this->internallink[$vetor[1]] = $this->AddLink();
          $vetor[1] = $this->internallink[$vetor[1]];
        }
        $this->HREF = $vetor[1];					// HREF link style set here ******
      }

	// SPECIAL CONTENT - IMAGES & FORM OBJECTS
      //Print-out special content
	// mPDF 3.0
      if (substr($vetor[0],0,3) == "\xbb\xa4\xac") { //identifier has been identified!
	  // mPDF 4.0
	  $objattr = $this->_getObjAttr($vetor[0]);

	  if ($objattr['type'] == 'nestedtable') {
		$level = $objattr['level'];
		$table = &$this->table[$level][$objattr['table']];
		$cell = &$table['cells'][$objattr['row']][$objattr['col']];
		if ($objattr['nestedcontent']) {
            	$this->finishFlowingBlock();
			$save_dw = $this->divwidth ;
			$save_buffer = $this->cellBorderBuffer;
			$this->cellBorderBuffer = array();
			$ncx = $this->x;

			//$w = $cell['w'];	// parent cell width	// mPDF 3.0 ? not needed

			list($dummyx,$w) = $this->_tableGetWidth($table, $objattr['row'], $objattr['col']);
			$ntw = $this->table[($level+1)][$objattr['nestedcontent']]['w'];	// nested table width
			if ($table['borders_separate']) { 
				$innerw = $w - $cell['border_details']['L']['w'] - $cell['border_details']['R']['w'] - $cell['padding']['L'] - $cell['padding']['R'] - $table['border_spacing_H'];
			}
			else {
				$innerw = $w - $cell['border_details']['L']['w']/2 - $cell['border_details']['R']['w']/2 - $cell['padding']['L'] - $cell['padding']['R'];
			}
			if ($cell['a']=='C' || $this->table[($level+1)][$objattr['nestedcontent']]['a']=='C') { 
				$ncx += ($innerw-$ntw)/2; 
			}
			elseif ($cell['a']=='R' || $this->table[($level+1)][$objattr['nestedcontent']]['a']=='R') {
				$ncx += $innerw- $ntw; 
			} 
			$this->x = $ncx ;

			$this->_tableWrite($this->table[($level+1)][$objattr['nestedcontent']]);
			$this->cellBorderBuffer = $save_buffer;
			$this->x = $bak_x ;
			$this->divwidth  = $save_dw;
			$this->newFlowingBlock( $this->divwidth,$this->divheight,$align,$is_table,$is_list,$blockstate,false); 
		}
	  }
	  else {
		if ($is_table) {	// *TABLES*
			$maxWidth = $this->divwidth; 	// *TABLES*
		}	// *TABLES*
		else {	// *TABLES*
			$maxWidth = $this->divwidth - ($this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_right'] + $this->blk[$this->blklvl]['border_right']['w']); 
		}	// *TABLES*

		// If float (already) exists at this level
		if (isset($this->floatmargins['R']) && $this->y <= $this->floatmargins['R']['y1'] && $this->y >= $this->floatmargins['R']['y0']) { $maxWidth -= $this->floatmargins['R']['w']; }
		if (isset($this->floatmargins['L']) && $this->y <= $this->floatmargins['L']['y1'] && $this->y >= $this->floatmargins['L']['y0']) { $maxWidth -= $this->floatmargins['L']['w']; }


		list($skipln) = $this->inlineObject($objattr['type'], '', $this->y, $objattr,$this->lMargin, ($this->flowingBlockAttr['contentWidth']/$this->k), $maxWidth, $this->flowingBlockAttr['height'], false, $is_table);
		//  1 -> New line needed because of width
		// -1 -> Will fit width on line but NEW PAGE REQUIRED because of height
		// -2 -> Will not fit on line therefore needs new line but thus NEW PAGE REQUIRED

		$iby = $this->y;
		$oldpage = $this->page;
		if (($skipln == 1 || $skipln == -2) && !isset($objattr['float'])) {
            	$this->finishFlowingBlock();
	           	$this->newFlowingBlock( $this->divwidth,$this->divheight,$align,$is_table,$is_list,$blockstate,false); //false=newblock
		}
		$thispage = $this->page;

		if ($skipln <0 && $this->AcceptPageBreak() && $thispage==$oldpage) { 	// the previous lines can already have triggered page break

			$this->AddPage($this->CurOrientation); 

	  		// Added to correct Images already set on line before page advanced
			// i.e. if second inline image on line is higher than first and forces new page
			if (count($this->objectbuffer)) {
				$yadj = $iby - $this->y;
				foreach($this->objectbuffer AS $ib=>$val) {
					if ($this->objectbuffer[$ib]['OUTER-Y'] ) $this->objectbuffer[$ib]['OUTER-Y'] -= $yadj;
					if ($this->objectbuffer[$ib]['BORDER-Y']) $this->objectbuffer[$ib]['BORDER-Y'] -= $yadj;
					if ($this->objectbuffer[$ib]['INNER-Y']) $this->objectbuffer[$ib]['INNER-Y'] -= $yadj;
				}
			}
		}


	  	// Added to correct for OddEven Margins
   	  	if  ($this->page != $oldpage) {
			$bak_x += $this->MarginCorrection;
			$oldpage = $this->page;
				$y = $this->tMargin - $paint_ht_corr ;
				$this->oldy = $this->tMargin - $paint_ht_corr ;
				$old_height = 0;
		}
		$this->x = $bak_x;

		if ($objattr['type'] == 'image' && isset($objattr['float'])) { 
		  $fy = $this->y;

		  // DIV TOP MARGIN/BORDER/PADDING
		  if ($this->flowingBlockAttr['newblock'] && ($this->flowingBlockAttr['blockstate']==1 || $this->flowingBlockAttr['blockstate']==3) && $this->flowingBlockAttr['lineCount']== 0) { 
			$fy += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		  }

		  if ($objattr['float']=='R') {
			$fx = $this->w - $this->rMargin - $objattr['width'] - ($this->blk[$this->blklvl]['outer_right_margin'] + $this->blk[$this->blklvl]['border_right']['w'] + $this->blk[$this->blklvl]['padding_right']);


		  }
		  else if ($objattr['float']=='L') {
			$fx = $this->lMargin + ($this->blk[$this->blklvl]['outer_left_margin'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_left']);
		  }
		  $w = $objattr['width'];
		  $h = abs($objattr['height']); 

		  $widthLeft = $maxWidth - ($this->flowingBlockAttr['contentWidth']/$this->k);
		  $maxHeight = $this->h - ($this->tMargin + $this->margin_header + $this->bMargin + 10) ;
		  // For Images
		  $extraWidth = ($objattr['border_left']['w'] + $objattr['border_right']['w'] + $objattr['margin_left']+ $objattr['margin_right']);
		  $extraHeight = ($objattr['border_top']['w'] + $objattr['border_bottom']['w'] + $objattr['margin_top']+ $objattr['margin_bottom']);

		  if ($objattr['itype'] == 'wmf') {
		  	$file = $objattr['file'];
 		  	$info=$this->formobjects[$file];
		  }
		  else {
		  	$file = $objattr['file'];
		  	$info=$this->images[$file];
		  }
		  // Automatically resize to width remaining - ********** If > maxWidth *******
//		  if ($w > $widthLeft) {
//		  	$w = $widthLeft ;
//		  	$h=abs($w*$info['h']/$info['w']);
//		  }
		  $img_w = $w - $extraWidth ;
		  $img_h = $h - $extraHeight ;
		  if ($objattr['border_left']['w']) {
		  	$objattr['BORDER-WIDTH'] = $img_w + (($objattr['border_left']['w'] + $objattr['border_right']['w'])/2) ;
		  	$objattr['BORDER-HEIGHT'] = $img_h + (($objattr['border_top']['w'] + $objattr['border_bottom']['w'])/2) ;
		  	$objattr['BORDER-X'] = $fx + $objattr['margin_left'] + (($objattr['border_left']['w'])/2) ;
		  	$objattr['BORDER-Y'] = $fy + $objattr['margin_top'] + (($objattr['border_top']['w'])/2) ;
		  }
		  $objattr['INNER-WIDTH'] = $img_w;
		  $objattr['INNER-HEIGHT'] = $img_h;
		  $objattr['INNER-X'] = $fx + $objattr['margin_left'] + ($objattr['border_left']['w']);
		  $objattr['INNER-Y'] = $fy + $objattr['margin_top'] + ($objattr['border_top']['w']) ;
		  $objattr['ID'] = $info['i'];
		  $objattr['OUTER-WIDTH'] = $w;
		  $objattr['OUTER-HEIGHT'] = $h;
		  $objattr['OUTER-X'] = $fx;
		  $objattr['OUTER-Y'] = $fy;
		  if ($objattr['float']=='R') {
			// If R float already exists at this level
		 	$this->floatmargins['R']['skipline'] = false;  // mPDF 3.0
			if (isset($this->floatmargins['R']['y1']) && $this->floatmargins['R']['y1'] > 0 && $fy < $this->floatmargins['R']['y1']) {
				$this->WriteFlowingBlock($vetor[0]); 
			}
			// If L float already exists at this level
			else if (isset($this->floatmargins['L']['y1']) && $this->floatmargins['L']['y1'] > 0 && $fy < $this->floatmargins['L']['y1']) {
				// Final check distance between floats is not now too narrow to fit text
				$mw = $this->GetStringWidth('WW');
				if (($this->blk[$this->blklvl]['inner_width'] - $w - $this->floatmargins['L']['w']) < $mw) {
					$this->WriteFlowingBlock($vetor[0]); 
				}
				else {
		  			$this->floatmargins['R']['x'] = $fx;
		  			$this->floatmargins['R']['w'] = $w;
		  			$this->floatmargins['R']['y0'] = $fy;
		  			$this->floatmargins['R']['y1'] = $fy + $h;
		 			if ($skipln == 1) {
		 			 	$this->floatmargins['R']['skipline'] = true;
		 			 	$this->floatmargins['R']['id'] = count($this->floatbuffer)+0;
						$objattr['skipline'] = true;
					}
					$this->floatbuffer[] = $objattr;
				}
			}
			else {
		  		$this->floatmargins['R']['x'] = $fx;
		  		$this->floatmargins['R']['w'] = $w;
		  		$this->floatmargins['R']['y0'] = $fy;
		  		$this->floatmargins['R']['y1'] = $fy + $h;
		 		if ($skipln == 1) {
		 		 	$this->floatmargins['R']['skipline'] = true;
		 		 	$this->floatmargins['R']['id'] = count($this->floatbuffer)+0;
					$objattr['skipline'] = true;
				}
				$this->floatbuffer[] = $objattr;
			}
		  }
		  else if ($objattr['float']=='L') {
			// If L float already exists at this level
		 	$this->floatmargins['L']['skipline'] = false;  // mPDF 3.0
			if (isset($this->floatmargins['L']['y1']) && $this->floatmargins['L']['y1'] > 0 && $fy < $this->floatmargins['L']['y1']) {
		 		$this->floatmargins['L']['skipline'] = false;  // mPDF 3.0
				$this->WriteFlowingBlock($vetor[0]); 
			}
			// If R float already exists at this level
			else if (isset($this->floatmargins['R']['y1']) && $this->floatmargins['R']['y1'] > 0 && $fy < $this->floatmargins['R']['y1']) {
				// Final check distance between floats is not now too narrow to fit text
				$mw = $this->GetStringWidth('WW');
				if (($this->blk[$this->blklvl]['inner_width'] - $w - $this->floatmargins['R']['w']) < $mw) {
					$this->WriteFlowingBlock($vetor[0]); 
				}
				else {
		  			$this->floatmargins['L']['x'] = $fx + $w;
		  			$this->floatmargins['L']['w'] = $w;
		  			$this->floatmargins['L']['y0'] = $fy;
		  			$this->floatmargins['L']['y1'] = $fy + $h;
		 			if ($skipln == 1) {
		 			 	$this->floatmargins['L']['skipline'] = true;
		 			 	$this->floatmargins['L']['id'] = count($this->floatbuffer)+0;
						$objattr['skipline'] = true;
					}
					$this->floatbuffer[] = $objattr;
				}
			}
			else {
		  		$this->floatmargins['L']['x'] = $fx + $w;
		  		$this->floatmargins['L']['w'] = $w;
		  		$this->floatmargins['L']['y0'] = $fy;
		  		$this->floatmargins['L']['y1'] = $fy + $h;
		 		if ($skipln == 1) {
		 		 	$this->floatmargins['L']['skipline'] = true;
		 		 	$this->floatmargins['L']['id'] = count($this->floatbuffer)+0;
					$objattr['skipline'] = true;
				}
				$this->floatbuffer[] = $objattr;
			}
		  }
		}
		else {
			$this->WriteFlowingBlock($vetor[0]); 
		}
	  }	// *TABLES*

      }	// END If special content

      else { //THE text
	  if ($this->tableLevel) { $paint_ht_corr = 0; }	// To move the y up when new column/page started if div border needed
	  else { $paint_ht_corr = $this->blk[$this->blklvl]['border_top']['w']; }

        if ($vetor[0] == "\n") { //We are reading a <BR> now turned into newline ("\n")
		if ($this->flowingBlockAttr['content']) {
			$this->finishFlowingBlock();
		}
		else if ($is_table) {
			$this->y+= ($this->table_lineheight * $this->FontSize);
		}
		else if (!$is_table) {
			$this->DivLn($this->lineheight); 
		}
	  	// Added to correct for OddEven Margins
   	  	if  ($this->page != $oldpage) {
			$bak_x=$bak_x +$this->MarginCorrection;
			$oldpage = $this->page;
				$y = $this->tMargin - $paint_ht_corr ;
				$this->oldy = $this->tMargin - $paint_ht_corr ;
				$old_height = 0;
		}
		$this->x = $bak_x;
		$this->newFlowingBlock( $this->divwidth,$this->divheight,$align,$is_table,$is_list,$blockstate,false);	// false = newblock
         }
          else {
		$this->WriteFlowingBlock( $vetor[0]);

		  // Added to correct for OddEven Margins
   		  if  ($this->page != $oldpage) {
			$bak_x=$bak_x +$this->MarginCorrection;
			$this->x = $bak_x;
			$oldpage = $this->page;
				$y = $this->tMargin - $paint_ht_corr ;
				$this->oldy = $this->tMargin - $paint_ht_corr ;
				$old_height = 0;
		  }
	    }


      }

      //Check if it is the last element. If so then finish printing the block
      if ($i == ($array_size-1)) {
		$this->finishFlowingBlock(true);	// true = END of flowing block
		  // Added to correct for OddEven Margins
   		  if  ($this->page != $oldpage) {
			$bak_x += $this->MarginCorrection;
			$this->x = $bak_x;
			$oldpage = $this->page;
				$y = $this->tMargin - $paint_ht_corr ;
				$this->oldy = $this->tMargin - $paint_ht_corr ;
				$old_height = 0;
		  }


	}


	// RESETTING VALUES
	$this->SetTextColor(0);
	$this->SetDrawColor(0);
	$this->SetFillColor(255);
	$this->colorarray = array();
	$this->spanbgcolorarray = array();
	$this->spanbgcolor = false;
	$this->issetcolor = false;
	$this->HREF = '';
	$this->outlineparam = array();
	$this->SetTextOutline(false);
      $this->outline_on = false;
	$this->SUP = false;
	$this->SUB = false;

	$this->strike = false;

	$this->currentfontfamily = '';  
	$this->currentfontsize = '';  
	$this->currentfontstyle = '';  
	if ($this->tableLevel) {	// *TABLES*
		$this->SetLineHeight('',$this->table_lineheight);	// *TABLES*
	}	// *TABLES*
	else if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {	// *TABLES*
		$this->SetLineHeight('',$this->blk[$this->blklvl]['line_height']);	// sets default line height
	}	// *TABLES*
	$this->SetStyle('B',false);
	$this->SetStyle('I',false);
	$this->SetStyle('U',false);
	$this->toupper = false;
	$this->tolower = false;
	$this->SetDash(); //restore to no dash
	$this->dash_on = false;
	$this->dotted_on = false;

    }//end of for(i=0;i<arraysize;i++)



    // PAINT DIV BORDER	// DISABLED IN COLUMNS AS DOESN'T WORK WHEN BROKEN ACROSS COLS??
    // mPDF 3.0 Border OR background
    if ((isset($this->blk[$this->blklvl]['border']) || isset($this->blk[$this->blklvl]['bgcolor'])) && $blockstate  && ($this->y != $this->oldy)) {
	$bottom_y = $this->y;	// Does not include Bottom Margin

	if (isset($this->blk[$this->blklvl]['startpage']) && $this->blk[$this->blklvl]['startpage'] != $this->page) {
		$this->PaintDivBB('pagetop',$blockstate);
	}
	// mPDF 3.0
	else if ($blockstate != 1) {
		$this->PaintDivBB('',$blockstate);
	}
	$this->y = $bottom_y; 
	$this->x = $bak_x;
    }

    // Reset Font
    $this->SetFontSize($this->default_font_size,false); 


}

// mPDF 4.0
function _setDashBorder($style, $div, $cp, $side) {
	if ($style == 'dashed' && (($side=='L' || $side=='R') || ($side=='T' && $div != 'pagetop' && !$cp) || ($side=='B' && $div!='pagebottom') )) {
		$dashsize = 2;	// final dash will be this + 1*linewidth
		$dashsizek = 1.5;	// ratio of Dash/Blank
		$this->SetDash($dashsize,($dashsize/$dashsizek)+($this->LineWidth*2));
	}
	else if ($style == 'dotted' || ($side=='T' && ($div == 'pagetop' || $cp)) || ($side=='B' && $div == 'pagebottom')) {
  		//Round join and cap
  		$this->_out("\n".'1 J'."\n".'1 j'."\n");
		$this->SetDash(0.001,($this->LineWidth*3));
	}
}

// mPDF 4.0
function _setBorderLine($b, $k=1) {
	$this->SetLineWidth($b['w']/$k);
	$this->SetDrawColor($b['c']['R'],$b['c']['G'],$b['c']['B']);
}

// mPDF 3.0 Borders & Background
function PaintDivBB($divider='',$blockstate=0,$blvl=0) {
	// Borders are done elsewhere in columns - messes up the repositioning in printcolumnbuffer
	$save_y = $this->y;
	if (!$blvl) { $blvl = $this->blklvl; }

	$x0 = $x1 = $y0 = $y1 = 0; 

	// Added mPDF 3.0 Float DIV
	if (isset($this->blk[$blvl]['bb_painted'][$this->page]) && $this->blk[$blvl]['bb_painted'][$this->page]) { return; }	// *CSS-FLOAT*

	if (isset($this->blk[$blvl]['x0'])) { $x0 = $this->blk[$blvl]['x0']; }	// left
	if (isset($this->blk[$blvl]['y1'])) { $y1 = $this->blk[$blvl]['y1']; }	// bottom

	// Added mPDF 3.0 Float DIV - ensures backgrounds/borders are drawn to bottom of page
	if ($y1==0) { 
		if ($divider=='pagebottom') { $y1 = $this->h-$this->bMargin; }
		else { $y1 = $this->y; }
	}

	// mPDF 3.0
	if (isset($this->blk[$blvl]['startpage']) && $this->blk[$blvl]['startpage'] != $this->page) { $continuingpage = true; } else { $continuingpage = false; } 

	if (isset($this->blk[$blvl]['y0'])) { $y0 = $this->blk[$blvl]['y0']; }
	$h = $y1 - $y0;
	$w = $this->blk[$blvl]['width'];
	$x1 = $x0 + $w;

	// Set border-widths as used here
	$border_top = $this->blk[$blvl]['border_top']['w'];
	$border_bottom = $this->blk[$blvl]['border_bottom']['w'];
	$border_left = $this->blk[$blvl]['border_left']['w'];
	$border_right = $this->blk[$blvl]['border_right']['w'];
	if (!$this->blk[$blvl]['border_top'] || $divider == 'pagetop' || $continuingpage) {
		$border_top = 0;
	}
	if (!$this->blk[$blvl]['border_bottom'] || $blockstate == 1 || $divider == 'pagebottom') { 
		$border_bottom = 0;
	}

		$brTL_H = 0;
		$brTL_V = 0;
		$brTR_H = 0;
		$brTR_V = 0;
		$brBL_H = 0;
		$brBL_V = 0;
		$brBR_H = 0;
		$brBR_V = 0;

	if (!$this->blk[$blvl]['border_top'] || $divider == 'pagetop' || $continuingpage || $this->keep_block_together) {
		$brTL_H = 0;
		$brTL_V = 0;
		$brTR_H = 0;
		$brTR_V = 0;
	}
	if (!$this->blk[$blvl]['border_bottom'] || $blockstate == 1 || $divider == 'pagebottom' || $this->keep_block_together) { 
		$brBL_H = 0;
		$brBL_V = 0;
		$brBR_H = 0;
		$brBR_V = 0;
	}


		// BORDERS
		if (isset($this->blk[$blvl]['y0']) && $this->blk[$blvl]['y0']) { $y0 = $this->blk[$blvl]['y0']; }
		$h = $y1 - $y0;
		$w = $this->blk[$blvl]['width'];

		//if ($this->blk[$blvl]['border_top']) {
		// Reinstate line above for dotted line divider when block border crosses a page
		if ($this->blk[$blvl]['border_top'] && $divider != 'pagetop' && !$continuingpage) {
			$tbd = $this->blk[$blvl]['border_top'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],$divider,$continuingpage,'T'); }
					$this->_out(sprintf('%.3f %.3f m ',($x0 + $w - ($border_top/2))*$this->k, ($this->h-($y0 + ($border_top/2)))*$this->k));
					$this->_out(sprintf('%.3f %.3f l ',($x0 + ($border_top/2))*$this->k, ($this->h-($y0 + ($border_top/2)))*$this->k));
				$this->_out('S');


				// Reset Corners and Dash off
  				$this->_out("\n".'2 J'."\n".'2 j'."\n");
				$this->SetDash(); 

			}
		}
		//if ($this->blk[$blvl]['border_bottom'] && $blockstate != 1) { 
		// Reinstate line above for dotted line divider when block border crosses a page
		if ($this->blk[$blvl]['border_bottom'] && $blockstate != 1 && $divider != 'pagebottom') { 
			$tbd = $this->blk[$blvl]['border_bottom'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],$divider,$continuingpage,'B'); }
					$this->_out(sprintf('%.3f %.3f m ',($x0 + ($border_bottom/2))*$this->k, ($this->h-($y0 + $h - ($border_bottom/2)))*$this->k));
					$this->_out(sprintf('%.3f %.3f l ',($x0 + $w - ($border_bottom/2) )*$this->k, ($this->h-($y0 + $h - ($border_bottom/2)))*$this->k));
				$this->_out('S');

				// Reset Corners and Dash off
  				$this->_out("\n".'2 J'."\n".'2 j'."\n");
				$this->SetDash(); 

			}
		}
		if ($this->blk[$blvl]['border_left']) { 
			$tbd = $this->blk[$blvl]['border_left'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],$divider,$continuingpage,'L'); }
					$this->_out(sprintf('%.3f %.3f m ',($x0 + ($border_left/2))*$this->k, ($this->h-($y0 + ($border_left/2)))*$this->k));
					$this->_out(sprintf('%.3f %.3f l ',($x0 + ($border_left/2))*$this->k, ($this->h-($y0 + $h - ($border_left/2)) )*$this->k));
				$this->_out('S');

				// Reset Corners and Dash off
  				$this->_out("\n".'2 J'."\n".'2 j'."\n");
				$this->SetDash(); 

			}
		}
		if ($this->blk[$blvl]['border_right']) { 
			$tbd = $this->blk[$blvl]['border_right'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],$divider,$continuingpage,'R'); }
					$this->_out(sprintf('%.3f %.3f m ',($x0 + $w - ($border_right/2))*$this->k, ($this->h-($y0 + $h - ($border_right/2)))*$this->k));
					$this->_out(sprintf('%.3f %.3f l ',($x0 + $w - ($border_right/2))*$this->k, ($this->h-($y0 + ($border_right/2)) )*$this->k));
				$this->_out('S');

				// Reset Corners and Dash off
  				$this->_out("\n".'2 J'."\n".'2 j'."\n");
				$this->SetDash(); 

			}
		}
		$this->SetDash(); 
	$this->y = $save_y; 


	// BACKGROUNDS are disabled in columns/kbt/headers - messes up the repositioning in printcolumnbuffer
	// mPDF 4.0 Added kwt
	if ($this->ColActive || $this->kwt || $this->keep_block_together) { return ; }

	$bgx0 = $x0;
	$bgx1 = $x1;
	$bgy0 = $y0;
	$bgy1 = $y1;

		$brbgTL_H = $brTL_H;
		$brbgTL_V = $brTL_V;
		$brbgTR_H = $brTR_H;
		$brbgTR_V = $brTR_V;
		$brbgBL_H = $brBL_H;
		$brbgBL_V = $brBL_V;
		$brbgBR_H = $brBR_H;
		$brbgBR_V = $brBR_V;

	// Set clipping path
	$s = ' q 0 w ';	// Line width=0
	$s .= sprintf('%.3f %.3f m ', ($bgx0+$brbgTL_H )*$this->k, ($this->h-$bgy0)*$this->k);	// start point TL before the arc
	$s .= sprintf('%.3f %.3f l ', ($bgx0)*$this->k, ($this->h-($bgy1-$brbgBL_V ))*$this->k);	// line to BL
	$s .= sprintf('%.3f %.3f l ', ($bgx1-$brbgBR_H )*$this->k, ($this->h-($bgy1))*$this->k);	// line to BR
	$s .= sprintf('%.3f %.3f l ', ($bgx1)*$this->k, ($this->h-($bgy0+$brbgTR_V))*$this->k);	// line to TR
	$s .= sprintf('%.3f %.3f l ', ($bgx0+$brbgTL_H )*$this->k, ($this->h-$bgy0)*$this->k);	// line to TL
	$s .= ' W n ';	// Ends path no-op & Sets the clipping path

	if ($this->blk[$blvl]['bgcolor']) { 
		$this->pageBackgrounds[$blvl][] = array('x'=>$x0, 'y'=>$y0, 'w'=>$w, 'h'=>$h, 'col'=>$this->blk[$blvl]['bgcolorarray'], 'clippath'=>$s);
	}


	// Added mPDF 3.0 Float DIV
	$this->blk[$blvl]['bb_painted'][$this->page] = true;

}




function PaintDivLnBorder($state=0,$blvl=0,$h) {
	// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom

	$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y + $h; 

	$save_y = $this->y;

	$w = $this->blk[$blvl]['width'];
	$x0 = $this->x;				// left
	$y0 = $this->y;				// top
	$x1 = $this->x + $w;			// bottom
	$y1 = $this->y + $h;			// bottom

		if ($this->blk[$blvl]['border_top'] && ($state==1 || $state==3)) {
			$tbd = $this->blk[$blvl]['border_top'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				$this->y = $y0 + ($tbd['w']/2);
				// mPDF 4.0
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'',$continuingpage,'T'); }
				$this->Line($x0 + ($tbd['w']/2) , $this->y , $x0 + $w - ($tbd['w']/2), $this->y);
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}

		if ($this->blk[$blvl]['border_left']) { 
			$tbd = $this->blk[$blvl]['border_left'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				$this->y = $y0 + ($tbd['w']/2);
				// mPDF 4.0
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'',$continuingpage,'L'); }
				$this->Line($x0 + ($tbd['w']/2), $this->y, $x0 + ($tbd['w']/2), $y0 + $h -($tbd['w']/2));
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		if ($this->blk[$blvl]['border_right']) { 
			$tbd = $this->blk[$blvl]['border_right'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
			 	$this->y = $y0 + ($tbd['w']/2);
				// mPDF 4.0
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'',$continuingpage,'R'); }
				$this->Line($x0 + $w - ($tbd['w']/2), $this->y, $x0 + $w - ($tbd['w']/2), $y0 + $h - ($tbd['w']/2));
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		if ($this->blk[$blvl]['border_bottom'] && $state > 1) { 
			$tbd = $this->blk[$blvl]['border_bottom'];
			if (isset($tbd['s']) && $tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd);
				$this->y = $y0 + $h - ($tbd['w']/2);
				// mPDF 4.0
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'',$continuingpage,'B'); }
				$this->Line($x0 + ($tbd['w']/2) , $this->y, $x0 + $w - ($tbd['w']/2), $this->y);
				$this->y += $tbd['w'];
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		$this->SetDash(); 


	$this->y = $save_y; 
}


function PaintImgBorder($objattr,$is_table) {
	// Borders are disabled in columns - messes up the repositioning in printcolumnbuffer
	if ($is_table) { $k = $this->shrin_k; } else { $k = 1; }
		$h = $objattr['BORDER-HEIGHT'];
		$w = $objattr['BORDER-WIDTH'];
		$x0 = $objattr['BORDER-X'];
		$y0 = $objattr['BORDER-Y'];

		// BORDERS
		if ($objattr['border_top']) { 
			$tbd = $objattr['border_top'];
			if ($tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd,$k);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'','','T'); }
				$this->Line($x0, $y0, $x0 + $w, $y0);
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		if ($objattr['border_left']) { 
			$tbd = $objattr['border_left'];
			if ($tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd,$k);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'','','L'); }
				$this->Line($x0, $y0, $x0, $y0 + $h);
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		if ($objattr['border_right']) { 
			$tbd = $objattr['border_right'];
			if ($tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd,$k);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'','','R'); }
				$this->Line($x0 + $w, $y0, $x0 + $w, $y0 + $h);
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		if ($objattr['border_bottom']) { 
			$tbd = $objattr['border_bottom'];
			if ($tbd['s']) {
				// mPDF 4.0
				$this->_setBorderLine($tbd,$k);
				if ($tbd['style']=='dotted' || $tbd['style']=='dashed') { $this->_setDashBorder($tbd['style'],'','','B'); }
				$this->Line($x0, $y0 + $h, $x0 + $w, $y0 + $h);
				// Reset Corners and Dash off
  				$this->_out('2 J 2 j');
				$this->SetDash(); 

			}
		}
		$this->SetDash(); 

}





function Reset()
{
	$this->SetTextColor(0);
	$this->colorarray = array();
	$this->issetcolor = false;

	$this->SetDrawColor(0);

	$this->SetFillColor(255);
	$this->spanbgcolorarray = array();
	$this->spanbgcolor = false;

	$this->SetStyle('B',false);
	$this->SetStyle('I',false);
	$this->SetStyle('U',false);

	$this->HREF = '';
	$this->outlineparam = array();
      $this->outline_on = false;
	$this->SetTextOutline(false);

	$this->SUP = false;
	$this->SUB = false;
	$this->strike = false;

	$this->SetFont($this->default_font,'',0,false);
	$this->SetFontSize($this->default_font_size,false);

	$this->currentfontfamily = '';  
	$this->currentfontsize = '';  
	if ($this->tableLevel) {	// *TABLES*
		$this->SetLineHeight('',$this->table_lineheight);	// *TABLES*
	}	// *TABLES*
	// mPDF 3.0
	else if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {	// *TABLES*
		$this->SetLineHeight('',$this->blk[$this->blklvl]['line_height']);	// sets default line height
	}	// *TABLES*
	$this->toupper = false;
	$this->tolower = false;
	$this->SetDash(); //restore to no dash
	$this->dash_on = false;
	$this->dotted_on = false;
	$this->divwidth = 0;
	$this->divheight = 0;
	$this->divalign = $this->defaultAlign;
	$this->divrevert = false;
	$this->oldy = -1;

	$bodystyle = array();
	if (isset($this->CSS['BODY']['FONT-STYLE'])) { $bodystyle['FONT-STYLE'] = $this->CSS['BODY']['FONT-STYLE']; }
	if (isset($this->CSS['BODY']['FONT-WEIGHT'])) { $bodystyle['FONT-WEIGHT'] = $this->CSS['BODY']['FONT-WEIGHT']; }
	if (isset($this->CSS['BODY']['COLOR'])) { $bodystyle['COLOR'] = $this->CSS['BODY']['COLOR']; }
	if (isset($bodystyle)) { $this->setCSS($bodystyle,'BLOCK','BODY'); }

}

function ReadMetaTags($html)
{
	// changes anykey=anyvalue to anykey="anyvalue" (only do this when this happens inside tags)
	$regexp = '/ (\\w+?)=([^\\s>"]+)/si'; 
 	$html = preg_replace($regexp," \$1=\"\$2\"",$html);

	if (preg_match('/<title>(.*?)<\/title>/si',$html,$m)) {
		$this->SetTitle($m[1]); 
	}

  preg_match_all('/<meta [^>]*?(name|content)="([^>]*?)" [^>]*?(name|content)="([^>]*?)".*?>/si',$html,$aux);
  $firstattr = $aux[1];
  $secondattr = $aux[3];
  for( $i = 0 ; $i < count($aux[0]) ; $i++)
  {

     $name = ( strtoupper($firstattr[$i]) == "NAME" )? strtoupper($aux[2][$i]) : strtoupper($aux[4][$i]);
     $content = ( strtoupper($firstattr[$i]) == "CONTENT" )? $aux[2][$i] : $aux[4][$i];
     switch($name)
     {
       case "KEYWORDS": $this->SetKeywords($content); break;
       case "AUTHOR": $this->SetAuthor($content); break;
       case "DESCRIPTION": $this->SetSubject($content); break;
     }
  }
}


function ReadCharset($html)
{
	// Charset conversion
	if ($this->allow_charset_conversion) {
	   if (preg_match('/<head.*charset=([^\'\"\s]*).*<\/head>/si',$html,$m)) {
		if (strtoupper($m[1]) != 'UTF-8') {
			$this->charset_in = strtoupper($m[1]); 
		}
	   }
	}

}

//////////////////
/// CSS parser ///
//////////////////
//////////////////
/// CSS parser ///
//////////////////
//////////////////
/// CSS parser ///
//////////////////

function ReadDefaultCSS($CSSstr) {
    $CSS = array();
    $CSSstr = preg_replace('|/\*.*?\*/|s',' ',$CSSstr);
    $CSSstr = preg_replace('/[\s\n\r\t\f]/s',' ',$CSSstr);

    $CSSstr = preg_replace('/(<\!\-\-|\-\->)/s',' ',$CSSstr);
    if ($CSSstr ) {
	preg_match_all('/(.*?)\{(.*?)\}/',$CSSstr,$styles);
	for($i=0; $i < count($styles[1]) ; $i++)  {
	 	$stylestr= trim($styles[2][$i]);
		$stylearr = explode(';',$stylestr);
		foreach($stylearr AS $sta) {
		   if (trim($sta)) {
			// mPDF 3.0
			// Changed to allow style="background: url('http://www.bpm1.com/bg.jpg')"
			list($property,$value) = explode(':',$sta,2);
			$property = trim($property);
			$value = preg_replace('/\s*!important/i','',$value);
			$value = trim($value);
			if ($property && ($value || $value==='0')) {
	  			$classproperties[strtoupper($property)] = $value;
			}
		   }
		}
		$classproperties = $this->fixCSS($classproperties);
		$tagstr = strtoupper(trim($styles[1][$i]));
		$tagarr = explode(',',$tagstr);
		foreach($tagarr AS $tg) {
		  $tags = preg_split('/\s+/',trim($tg));
		  $level = count($tags);
		  if ($level == 1) {		// e.g. p or .class or #id or p.class or p#id
		     $t = trim($tags[0]);
		     if ($t) {
			$tag = '';
			if (preg_match('/^('.$this->allowedCSStags.')$/',$t)) { $tag= $t; }
			if ($this->CSS[$tag] && $tag) { $CSS[$tag] = $this->array_merge_recursive_unique($CSS[$tag], $classproperties); }
			else if ($tag) { $CSS[$tag] = $classproperties; }
		     }
		  }
		}
  		$properties = array();
  		$values = array();
  		$classproperties = array();
	}

    } // end of if
    return $CSS;
}


function ReadCSS($html) {
//
// This supports:  .class {...} / #id { .... }
// p {...}  h1[-h6] {...}  a {...}  table {...}   thead {...}  th {...}  td {...}  hr {...}
// body {...} sets default font and fontsize
// It supports some cascaded CSS e.g. div.topic table.type1 td
// Does not support non-block level e.g. a#hover { ... }


	// Edit this if you want to exclude stylsheets for different media e.g. print
	$html = preg_replace('/<link[^>]*media=(\'|\")(aural|braille|handheld)(\'|\").*?>/is','',$html);
	$html = preg_replace('/<style[^>]*media=(\'|\")(aural|braille|handheld)(\'|\").*?<\/style>/is','',$html);
	if ($this->disablePrintCSS) {
		$html = preg_replace('/<link[^>]*media=(\'|\")print(\'|\").*?>/is','',$html);
		$html = preg_replace('/<style[^>]*media=(\'|\")print(\'|\").*?<\/style>/is','',$html);
	}
	// mPDF 4.0
	// Remove Comment tags <!--  --> inside CSS as <style> in HTML document
	preg_match_all('/<style.*?>(.*?)<\/style>/si',$html,$m);
	if (count($m[1])) { 
		for($i=0;$i<count($m[1]);$i++) {
			// Remove comment tags 
			$sub = preg_replace('/(<\!\-\-|\-\->)/s',' ',$m[1][$i]);
			$html = preg_replace('/'.preg_quote($m[1][$i], '/').'/si', $sub, $html); 
		}
	}
	// mPDF 4.0
	$html = preg_replace('/<!--mpdf/i','',$html); // mPDF 3.0 changed from ereg_
	$html = preg_replace('/mpdf-->/i','',$html); // mPDF 3.0 changed from ereg_
	$html = preg_replace('/<\!\-\-.*?\-\->/s',' ',$html);

	$match = 0; // no match for instance
	$regexp = ''; // This helps debugging: showing what is the REAL string being processed
	$CSSext = array(); 

	//CSS inside external files
	// mPDF 3.0 Allow single or double quotes
	// mPDF 4.0 Allow >1 space or extra text after link
	$regexp = '/<link[^>]*rel=["\']stylesheet["\'][^>]*href=["\']([^>"\']*)["\'].*?>/si'; // EDIT mPDF 4.0
	$x = preg_match_all($regexp,$html,$cxt);
	if ($x) { 
		$match += $x; 
		$CSSext = $cxt[1];
	}

	// mPDF 3.0 Allow single or double quotes
	$regexp = '/<link[^>]*href=["\']([^>"\']*)["\'][^>]*?rel=["\']stylesheet["\'].*?>/si';  // EDIT mPDF 4.0
	$x = preg_match_all($regexp,$html,$cxt);
	if ($x) { 
		$match += $x; 
		$CSSext = array_merge($CSSext,$cxt[1]);
	}

	// look for @import stylesheets
	$regexp = '/@import url\([\'\"]{0,1}([^\)]*?\.css)[\'\"]{0,1}\)/si'; // EDIT mPDF 4.0
	$x = preg_match_all($regexp,$html,$cxt);
	if ($x) { 
		$match += $x; 
		$CSSext = array_merge($CSSext,$cxt[1]);
	}
	// mPDF 4.0
	// look for @import without the url()
	$regexp = '/@import [\'\"]{0,1}([^;]*?\.css)[\'\"]{0,1}/si';
	$x = preg_match_all($regexp,$html,$cxt);
	if ($x) { 
		$match += $x; 
		$CSSext = array_merge($CSSext,$cxt[1]);
	}

  $ind = 0;
  $CSSstr = '';

  // Edited mPDF v1.4
  if (!is_array($this->cascadeCSS)) $this->cascadeCSS = array();

    while($match){
	$path = $CSSext[$ind];
	$this->GetFullPath($path); // mPDF 4.0
	$CSSextblock = $this->_get_file($path);
	if ($CSSextblock) {
		// mPDF 4.0
		// look for embedded @import stylesheets in other stylesheets
		// and fix url paths (including background-images) relative to stylesheet
		$regexpem = '/@import url\([\'\"]{0,1}(.*?\.css)[\'\"]{0,1}\)/si';
		$xem = preg_match_all($regexpem,$CSSextblock,$cxtem);
		$cssBasePath = preg_replace('/\/[^\/]*$/','',$path) . '/';
		if ($xem) { 
		   foreach($cxtem[1] AS $cxtembedded) {
			// path is relative to original stlyesheet!!
			$this->GetFullPath($cxtembedded, $cssBasePath );
			$match++; 
			$CSSext[] = $cxtembedded;
		   }
		}
		$regexpem = '/(background[^;]*url\s*\(\s*[\'\"]{0,1})([^\)\'\"]*)([\'\"]{0,1}\s*\))/si';
		$xem = preg_match_all($regexpem,$CSSextblock,$cxtem);
		if ($xem) { 
		   for ($i=0;$i<count($cxtem[0]);$i++) {
			// path is relative to original stlyesheet!!
			$embedded = $cxtem[2][$i];
			$this->GetFullPath($embedded, $cssBasePath );
			$CSSextblock = preg_replace('/'.preg_quote($cxtem[0][$i],'/').'/', ($cxtem[1][$i].$embedded.$cxtem[3][$i]), $CSSextblock);
		   }
		}
		$CSSstr .= ' '.$CSSextblock;
	}	

	$match--;
	$ind++;
    } //end of match
    $match = 0; // reset value, if needed
	// CSS as <style> in HTML document
    $regexp = '/<style.*?>(.*?)<\/style>/si'; 
    $match = preg_match_all($regexp,$html,$CSSblock);
    if ($match) {
		// mPDF 4.0
		$tmpCSSstr = implode(' ',$CSSblock[1]);
		$regexpem = '/(background[^;]*url\s*\(\s*[\'\"]{0,1})([^\)\'\"]*)([\'\"]{0,1}\s*\))/si';
		$xem = preg_match_all($regexpem,$tmpCSSstr ,$cxtem);
		if ($xem) { 
		   for ($i=0;$i<count($cxtem[0]);$i++) {
			$embedded = $cxtem[2][$i];
			$this->GetFullPath($embedded);
			$tmpCSSstr = preg_replace('/'.preg_quote($cxtem[0][$i],'/').'/', ($cxtem[1][$i].$embedded.$cxtem[3][$i]), $tmpCSSstr );
		   }
		}
		$CSSstr .= ' '.$tmpCSSstr;
    }
    // Remove comments
    $CSSstr = preg_replace('|/\*.*?\*/|s',' ',$CSSstr);
    $CSSstr = preg_replace('/[\s\n\r\t\f]/s',' ',$CSSstr);

    $CSSstr = preg_replace('/(<\!\-\-|\-\->)/s',' ',$CSSstr);
    if ($CSSstr ) {
	preg_match_all('/(.*?)\{(.*?)\}/',$CSSstr,$styles);
	for($i=0; $i < count($styles[1]) ; $i++)  {
		// SET array e.g. $classproperties['COLOR'] = '#ffffff';
	 	$stylestr= trim($styles[2][$i]);
		$stylearr = explode(';',$stylestr);
		foreach($stylearr AS $sta) {
		   if (trim($sta)) { 
			// mPDF 3.0
			// Changed to allow style="background: url('http://www.bpm1.com/bg.jpg')"
			list($property,$value) = explode(':',$sta,2);
			$property = trim($property);
			$value = preg_replace('/\s*!important/i','',$value);
			$value = trim($value);
			if ($property && ($value || $value==='0')) {
	  			$classproperties[strtoupper($property)] = $value;
			}
		   }
		}
		$classproperties = $this->fixCSS($classproperties);
		$tagstr = strtoupper(trim($styles[1][$i]));
		$tagarr = explode(',',$tagstr);
		$pageselectors = false;	// used to turn on $this->mirrorMargins
		foreach($tagarr AS $tg) {
		  $tags = preg_split('/\s+/',trim($tg));
		  $level = count($tags);
		  $t = '';
		  $t2 = '';
		  $t3 = '';
		  if (trim($tags[0])=='@PAGE') {
			if (isset($tags[0])) { $t = trim($tags[0]); }
			if (isset($tags[1])) { $t2 = trim($tags[1]); }
			if (isset($tags[2])) { $t3 = trim($tags[2]); }
			$tag = '';
			if ($level==1) { $tag = $t; }
			else if ($level==2 && preg_match('/^[:](.*)$/',$t2,$m)) { 
				$tag = $t.'>>PSEUDO>>'.$m[1]; 
				if ($m[1]=='LEFT' || $m[1]=='RIGHT') { $pageselectors = true; }	// used to turn on $this->mirrorMargins 
			}
			else if ($level==2) { $tag = $t.'>>NAMED>>'.$t2; }
			else if ($level==3 && preg_match('/^[:](.*)$/',$t3,$m)) { 
				$tag = $t.'>>NAMED>>'.$t2.'>>PSEUDO>>'.$m[1]; 
				if ($m[1]=='LEFT' || $m[1]=='RIGHT') { $pageselectors = true; }	// used to turn on $this->mirrorMargins
			}
			if (isset($this->CSS[$tag]) && $tag) { $this->CSS[$tag] = $this->array_merge_recursive_unique($this->CSS[$tag], $classproperties); }
			else if ($tag) { $this->CSS[$tag] = $classproperties; }
		  }

		  else if ($level == 1) {		// e.g. p or .class or #id or p.class or p#id
		     if (isset($tags[0])) { $t = trim($tags[0]); }
		     if ($t) {
			$tag = '';
			if (preg_match('/^[.](.*)$/',$t,$m)) { $tag = 'CLASS>>'.$m[1]; }
			else if (preg_match('/^[#](.*)$/',$t,$m)) { $tag = 'ID>>'.$m[1]; }
			else if (preg_match('/^('.$this->allowedCSStags.')[.](.*)$/',$t,$m)) { $tag = $m[1].'>>CLASS>>'.$m[2]; }
			else if (preg_match('/^('.$this->allowedCSStags.')[#](.*)$/',$t,$m)) { $tag = $m[1].'>>ID>>'.$m[2]; }
			else if (preg_match('/^('.$this->allowedCSStags.')$/',$t)) { $tag= $t; }

			if (isset($this->CSS[$tag]) && $tag) { $this->CSS[$tag] = $this->array_merge_recursive_unique($this->CSS[$tag], $classproperties); }
			else if ($tag) { $this->CSS[$tag] = $classproperties; }
		     }
		  }
		  else {
		   $tmp = array();
		   for($n=0;$n<$level;$n++) {
		     if (isset($tags[$n])) { $t = trim($tags[$n]); }
		     else { $t = ''; }	// mPDF 4.0
		     if ($t) {
			$tag = '';
			if (preg_match('/^[.](.*)$/',$t,$m)) { $tag = 'CLASS>>'.$m[1]; }
			else if (preg_match('/^[#](.*)$/',$t,$m)) { $tag = 'ID>>'.$m[1]; }
			else if (preg_match('/^('.$this->allowedCSStags.')[.](.*)$/',$t,$m)) { $tag = $m[1].'>>CLASS>>'.$m[2]; }
			else if (preg_match('/^('.$this->allowedCSStags.')[#](.*)$/',$t,$m)) { $tag = $m[1].'>>ID>>'.$m[2]; }
			else if (preg_match('/^('.$this->allowedCSStags.')$/',$t)) { $tag= $t; }

			if ($tag) $tmp[] = $tag;
			// mPDF 4.0
			else { break; }
		    }
		   }
		   // mPDF 4.0
		   if ($tag) {
			   $x = &$this->cascadeCSS; 
			   foreach($tmp AS $tp) { $x = &$x[$tp]; }
			   $x = $this->array_merge_recursive_unique($x, $classproperties); 
			   $x['depth'] = $level;
		   }
		  }
		}
		if ($pageselectors) { $this->mirrorMargins = true; }
  		$properties = array();
  		$values = array();
  		$classproperties = array();
	}

    } // end of if
    //Remove CSS (tags and content), if any
    $regexp = '/<style.*?>(.*?)<\/style>/si'; // it can be <style> or <style type="txt/css"> 
    $html = preg_replace($regexp,'',$html);
//print_r($this->CSS); exit;
//print_r($this->cascadeCSS); exit;
    return $html;
}

function readInlineCSS($html)
{
	//Fix incomplete CSS code
	$size = strlen($html)-1;
	if (substr($html,$size,1) != ';') $html .= ';';
	//Make CSS[Name-of-the-class] = array(key => value)
	$regexp = '|\\s*?(\\S+?):(.+?);|i';
	preg_match_all( $regexp, $html, $styleinfo);
	$properties = $styleinfo[1];
	$values = $styleinfo[2];
	//Array-properties and Array-values must have the SAME SIZE!
	$classproperties = array();
	for($i = 0; $i < count($properties) ; $i++) $classproperties[strtoupper($properties[$i])] = trim($values[$i]);
	return $this->fixCSS($classproperties);
}



function setCSS($arrayaux,$type='',$tag='')	// type= INLINE | BLOCK // tag= BODY
{
	if (!is_array($arrayaux)) return; //Removes PHP Warning
	// Set font size first so that e.g. MARGIN 0.83em works on font size for this element
	if (isset($arrayaux['FONT-SIZE'])) {
		$v = $arrayaux['FONT-SIZE'];
		if(is_numeric(substr($v,0,1))) {
			$mmsize = $this->ConvertSize($v,$this->FontSize);
			$this->SetFontSize( $mmsize*(72/25.4),false ); //Get size in points (pt)
		}
		else{
  			$v = strtoupper($v);
			// mPDF 4.0
			if (isset($this->fontsizes[$v])) { 
				$this->SetFontSize( $this->fontsizes[$v]* $this->default_font_size,false);
			}
		}
		if ($tag == 'BODY') { $this->SetDefaultFontSize($this->FontSizePt); }
	}



	// FOR INLINE and BLOCK OR 'BODY'
	if (isset($arrayaux['FONT-FAMILY'])) {
		$v = $arrayaux['FONT-FAMILY'];
		//If it is a font list, get all font types
		$aux_fontlist = explode(",",$v);
		$fonttype = $aux_fontlist[0];
		$fonttype = strtolower(trim($fonttype));
		if(($fonttype == 'helvetica') || ($fonttype == 'arial')) { $fonttype = 'sans-serif'; }
		else if($fonttype == 'helvetica-embedded')  { $fonttype = 'helvetica'; }
		// mPDF 4.0
		else if($fonttype == 'courier-embedded')  { $fonttype = 'courier'; }
		else if($fonttype == 'times-embedded')  { $fonttype = 'times'; }
		else if($fonttype == 'times')  { $fonttype = 'serif'; }
		else if($fonttype == 'courier')  { $fonttype = 'monospace'; }
		if ($tag == 'BODY') { 
			$this->SetDefaultFont($fonttype); 
		}
		$this->SetFont($fonttype,$this->currentfontstyle,0,false);
	}
	else { 
		$this->SetFont($this->currentfontfamily,$this->currentfontstyle,0,false); 
	}

   foreach($arrayaux as $k => $v) {
	if ($type != 'INLINE' && $tag != 'BODY') {
	  switch($k){
		// BORDERS
		case 'BORDER-TOP':
			$this->blk[$this->blklvl]['border_top'] = $this->border_details($v);
			if ($this->blk[$this->blklvl]['border_top']['s']) { $this->blk[$this->blklvl]['border'] = 1; }
			break;
		case 'BORDER-BOTTOM':
			$this->blk[$this->blklvl]['border_bottom'] = $this->border_details($v);
			if ($this->blk[$this->blklvl]['border_bottom']['s']) { $this->blk[$this->blklvl]['border'] = 1; }
			break;
		case 'BORDER-LEFT':
			$this->blk[$this->blklvl]['border_left'] = $this->border_details($v);
			if ($this->blk[$this->blklvl]['border_left']['s']) { $this->blk[$this->blklvl]['border'] = 1; }
			break;
		case 'BORDER-RIGHT':
			$this->blk[$this->blklvl]['border_right'] = $this->border_details($v);
			if ($this->blk[$this->blklvl]['border_right']['s']) { $this->blk[$this->blklvl]['border'] = 1; }
			break;

		// PADDING
		case 'PADDING-TOP':
			$this->blk[$this->blklvl]['padding_top'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'PADDING-BOTTOM':
			$this->blk[$this->blklvl]['padding_bottom'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'PADDING-LEFT':
			$this->blk[$this->blklvl]['padding_left'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'PADDING-RIGHT':
			$this->blk[$this->blklvl]['padding_right'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;

		// MARGINS
		case 'MARGIN-TOP':
			$this->blk[$this->blklvl]['margin_top'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'MARGIN-BOTTOM':
			$this->blk[$this->blklvl]['margin_bottom'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'MARGIN-LEFT':
			$this->blk[$this->blklvl]['margin_left'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;
		case 'MARGIN-RIGHT':
			$this->blk[$this->blklvl]['margin_right'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false);
			break;


		case 'BACKGROUND-CLIP':
			if (strtoupper($v) == 'PADDING-BOX') { $this->blk[$this->blklvl]['background_clip'] = 'padding-box'; }
			break;

		case 'PAGE-BREAK-AFTER':
			if (strtoupper($v) == 'AVOID') { $this->blk[$this->blklvl]['page_break_after_avoid'] = true; }
			break;

		case 'WIDTH':
			// mPDF 4.0 Allow em support and avoid 'auto'
			if (strtoupper($v) != 'AUTO') { 
				$this->blk[$this->blklvl]['css_set_width'] = $this->ConvertSize($v,$this->blk[$this->blklvl-1]['inner_width'],$this->FontSize,false); 
			}
			break;

		case 'TEXT-INDENT':
			// mPDF 4.0 Left as raw value (may include 1% or 2em)
			$this->blk[$this->blklvl]['text_indent'] = $v;
			break;

	  }//end of switch($k)
	}


	if ($type != 'INLINE') {	// includes BODY tag
	  switch($k){

		case 'MARGIN-COLLAPSE':	// Custom tag to collapse margins at top and bottom of page
			if (strtoupper($v) == 'COLLAPSE') { $this->blk[$this->blklvl]['margin_collapse'] = true; }
			break;

		case 'LINE-HEIGHT':	
			// mPDF 4.0
			$this->blk[$this->blklvl]['line_height'] = $this->fixLineheight($v);
			if (!$this->blk[$this->blklvl]['line_height'] ) { $this->blk[$this->blklvl]['line_height'] = $this->default_lineheight_correction; }
			break;

		case 'TEXT-ALIGN': //left right center justify
			switch (strtoupper($v)) {
				case 'LEFT': 
                        $this->blk[$this->blklvl]['align']="L";
                        break;
				case 'CENTER': 
                        $this->blk[$this->blklvl]['align']="C";
                        break;
				case 'RIGHT': 
                        $this->blk[$this->blklvl]['align']="R";
                        break;
				case 'JUSTIFY': 
                        $this->blk[$this->blklvl]['align']="J";
                        break;
			}
			break;

	  }//end of switch($k)
	}


	// FOR INLINE and BLOCK
	  switch($k){
		case 'TEXT-ALIGN': //left right center justify
			if (strtoupper($v) == 'NOJUSTIFY' && $this->blk[$this->blklvl]['align']=="J") {
                        $this->blk[$this->blklvl]['align']="";
			}
			break;
		// bgcolor only - to stay consistent with original html2fpdf
		case 'BACKGROUND': 
		case 'BACKGROUND-COLOR': 
			$cor = $this->ConvertColor($v);
			if ($cor) { 
			   // mPDF 3.0
			   if ($tag  == 'BODY') {
				$this->bodyBackgroundColor = $cor;
			   }
			   else if ($type == 'INLINE') {
				$this->spanbgcolorarray = $cor;
				$this->spanbgcolor = true;
			   }
			   else {
				$this->blk[$this->blklvl]['bgcolorarray'] = $cor;
				$this->blk[$this->blklvl]['bgcolor'] = true;
			   }
			}
			else if ($type != 'INLINE') {
		  		// mPDF 3.0
		  		if ($this->ColActive || $this->keep_block_together) { 
					$this->blk[$this->blklvl]['bgcolorarray'] = $this->blk[$this->blklvl-1]['bgcolorarray'] ;
					$this->blk[$this->blklvl]['bgcolor'] = $this->blk[$this->blklvl-1]['bgcolor'] ;
				}
			}
			break;



		case 'FONT-STYLE': // italic normal oblique
			switch (strtoupper($v)) {
				case 'ITALIC': 
				case 'OBLIQUE': 
            			$this->SetStyle('I',true);
					break;
				case 'NORMAL': 
            			$this->SetStyle('I',false);
					break;
			}
			break;

		case 'FONT-WEIGHT': // normal bold //Does not support: bolder, lighter, 100..900(step value=100)
			switch (strtoupper($v))	{
				case 'BOLD': 
            			$this->SetStyle('B',true);
					break;
				case 'NORMAL': 
            			$this->SetStyle('B',false);
					break;
			}
			break;

		case 'VERTICAL-ALIGN': //super and sub only dealt with here e.g. <SUB> and <SUP>
			switch (strtoupper($v)) {
				case 'SUPER': 
                        $this->SUP=true;
                        break;
				case 'SUB': 
                        $this->SUB=true;
                        break;
			}
			break;

		case 'TEXT-DECORATION': // none underline line-through (strikeout) //Does not support: overline, blink
			if (stristr($v,'LINE-THROUGH')) {
					$this->strike = true;
			}
			if (stristr($v,'UNDERLINE')) {
            			$this->SetStyle('U',true);
			}
			break;

		case 'TEXT-TRANSFORM': // none uppercase lowercase //Does not support: capitalize
			switch (strtoupper($v)) { //Not working 100%
				case 'UPPERCASE':
					$this->toupper=true;
					break;
				case 'LOWERCASE':
 					$this->tolower=true;
					break;
				case 'NONE': break;
			}
			break;

		case 'OUTLINE-WIDTH': 
			switch(strtoupper($v)) {
				case 'THIN': $v = '0.03em'; break;
				case 'MEDIUM': $v = '0.05em'; break;
				case 'THICK': $v = '0.07em'; break;
			}
			$this->outlineparam['WIDTH'] = $this->ConvertSize($v,$this->blk[$this->blklvl]['inner_width'],$this->FontSize);
			break;

		case 'OUTLINE-COLOR': 
			if (strtoupper($v) == 'INVERT') {
			   if ($this->colorarray) {
				$cor = $this->colorarray;
				$this->outlineparam['COLOR'] = array('R'=> (255-$cor['R']), 'G'=> (255-$cor['G']), 'B'=> (255-$cor['B']));
			   }
			   else {
				$this->outlineparam['COLOR'] = array('R'=> 255, 'G'=> 255, 'B'=> 255);
			   }
			}
			else { 
		  	  $cor = $this->ConvertColor($v);
			  if ($cor) { $this->outlineparam['COLOR'] = $cor ; }	  
			}
			break;

		case 'COLOR': // font color
		  $cor = $this->ConvertColor($v);
			if ($cor) { 
				$this->colorarray = $cor;
				$this->SetTextColor($cor['R'],$cor['G'],$cor['B']);
				$this->issetcolor = true;
			}
		  break;

		case 'DIR': 
			$this->biDirectional = true;
			break;

	  }//end of switch($k)


   }//end of foreach
}

function SetStyle($tag,$enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
  //Fix some SetStyle misuse
	if ($this->$tag < 0) $this->$tag = 0;
	if ($this->$tag > 1) $this->$tag = 1;
	foreach(array('B','I','U') as $s) {
		if($this->$s>0) {
			$style.=$s;
		}
	}
	$this->currentfontstyle=$style;
	$this->SetFont('',$style,0,false);
}

function GetStyle()
{
	$style='';
	foreach(array('B','I','U') as $s) {
		if($this->$s>0) {
			$style.=$s;
		}
	}
	return($style);
}


function DisableTags($str='')
{
  if ($str == '') //enable all tags
  {
    //Insert new supported tags in the long string below.
	///////////////////////////////////////////////////////
	// Added custom tags <indexentry>
    $this->enabledtags = "<span><s><strike><del><bdo><big><small><ins><cite><acronym><font><sup><sub><b><u><i><a><strong><em><code><samp><tt><kbd><var><q><table><thead><tfoot><tbody><tr><th><td><ol><ul><li><dl><dt><dd><form><input><select><textarea><option><div><p><h1><h2><h3><h4><h5><h6><pre><center><blockquote><address><hr><img><br><indexentry><indexinsert><bookmark><watermarktext><watermarkimage><tts><ttz><tta><column_break><columnbreak><newcolumn><newpage><page_break><pagebreak><formfeed><columns><toc><tocentry><tocpagebreak><pageheader><pagefooter><setpageheader><setpagefooter><sethtmlpageheader><sethtmlpagefooter><annotation><template><jpgraph><barcode>";
  }
  else
  {
    $str = explode(",",$str);
    foreach($str as $v) $this->enabledtags = str_replace(trim($v),'',$this->enabledtags);
  }
}



function TableWordWrap($maxwidth, $forcewrap = 0, $textbuffer = '', $returnarray=false)
{
   $biggestword=0;//EDITEI
   $toonarrow=false;//EDITEI

   $curlyquote = mb_convert_encoding("\xe2\x80\x9e",$this->mb_enc,'UTF-8');
   $curlylowquote = mb_convert_encoding("\xe2\x80\x9d",$this->mb_enc,'UTF-8');

   // mPDF 3.0 Don't use ltrim as this gets rid of \n - new line from <br>
   //$textbuffer[0][0] = ltrim($textbuffer[0][0]);
   $textbuffer[0][0] = preg_replace('/^[ ]*/','',$textbuffer[0][0]);

   if ((count($textbuffer) == 0) or ((count($textbuffer) == 1) && ($textbuffer[0][0] == ''))) { return 0; }

   $text = '';
   $lh = 0; 
   $ch = 0;
   $width = 0;
   $ln = 1;	// Counts line number
   $mxw = $this->GetStringWidth('W');	// Keep tabs on Maxwidth of actual text
   foreach ($textbuffer as $cctr=>$chunk) {
		$line = $chunk[0];

		//IMAGE
      	if (substr($line,0,3) == "\xbb\xa4\xac") { //identifier has been identified!
	  		// mPDF 4.0
	  		$objattr = $this->_getObjAttr($line);
			if ($objattr['type'] == 'nestedtable') {
				$text = "";
				$width = 0;
				$ch += $lh;
				$level = $objattr['level'];
				$ih = $this->table[($level+1)][$objattr['nestedcontent']]['h'];	// nested table width
				$ch += $ih;
				$lh = 0; // Reset lineheight
				$ln++;
				continue;
			}

			list($skipln,$iw,$ih) = $this->inlineObject($specialcontent['type'],0,0, $objattr, $this->lMargin,$width,$maxwidth,$lh,false,true);
			if ($objattr['type']=='image') { 
				$lh = $ih;
			}

			if ($objattr['type'] == 'hr') {
				$text = "";
				$width = 0;
				$ch += $lh;
				$ch += $ih;
				$lh = 0; // Reset lineheight
				$ln++;
				continue;
			}
			if ($skipln==1 || $skipln==-2) {
				// Finish last line
				$text = "";
				$width = 0;
				$ch += $lh;
				$ln++;
				$lh = 0; // Reset lineheight
			}
			$lh = max($lh,$ih);
			$width += $iw;
			continue;
		}

		// SET FONT SIZE/STYLE from $chunk[n]
		// FONTSIZE
	      if(isset($chunk[11]) and $chunk[11] != '') { 
		   if ($this->shrin_k) {
			$this->SetFontSize($chunk[11]/$this->shrin_k,false); 
		   }
		   else {
			$this->SetFontSize($chunk[11],false); 
		   }
		}

		$fh = $this->table_lineheight*$this->FontSize; // Reset lineheight
		$lh = MAX($lh,$fh);

		// mPDF 3.0 Moved to after set FontSize
		if ($line == "\n") {
			$text = "";
			$width = 0;
			// mPDF 3.0
			if ($lh==0) { $lh = $this->table_lineheight*$this->FontSize; }
			$ch += $lh;
			$ln++;
			$lh = 0; // Reset lineheight
			// mPDF 3.0 Reset FontSize
			if(isset($chunk[11]) and $chunk[11] != '') { 
				$this->SetFontSize($this->default_font_size,false);
			}
			continue;
		}

		// FONTFAMILY
	      if(isset($chunk[4]) and $chunk[4] != '') { $font = $this->SetFont($chunk[4],$this->FontStyle,0,false); }

		// FONT STYLE B I U
	      if(isset($chunk[2]) and $chunk[2] != '') {
	          if (strpos($chunk[2],"B") !== false) $this->SetStyle('B',true); 

	          if (strpos($chunk[2],"I") !== false) $this->SetStyle('I',true); 

	      }

		$space = $this->GetStringWidth(' ');

	if (mb_substr($line,0,1,$this->mb_enc ) == ' ') { 	// line (chunk) starts with a space
		$width += $space;
		$text .= ' ';
	}

	if (mb_substr($line,(mb_strlen($line,$this->mb_enc )-1),1,$this->mb_enc ) == ' ') { $lsend = true; }	// line (chunk) ends with a space
	else { $lsend = false; }
	$line= ltrim($line);
	$line= $this->mb_rtrim($line, $this->mb_enc);
	if ($line == '') { continue; }

		$words = explode(' ', $line);

	foreach ($words as $word) {
		$word = $this->mb_rtrim($word, $this->mb_enc);
		$word = ltrim($word);
		$wordwidth = $this->GetStringWidth($word);


		//maxwidth is insufficient for one word
		if ($wordwidth > $maxwidth + 0.0001) {
			while($wordwidth > $maxwidth) {
				$chw = 0;	// check width
				for ( $i = 0; $i < mb_strlen($word, $this->mb_enc ); $i++ ) {
					$chw = $this->GetStringWidth(mb_substr($word,0,$i+1,$this->mb_enc ));
					if ($chw > $maxwidth) {
						if ($text) {
							$ch += $lh;
							$lh = $fh ; // Reset lineheight
							//$lh = $this->lineheight; // Reset lineheight
							$ln++;
							$mxw = $maxwidth;
						}
						$text = mb_substr($word,0,$i,$this->mb_enc );
						$word = mb_substr($word,$i,mb_strlen($word, $this->mb_enc )-$i,$this->mb_enc );
						$wordwidth = $this->GetStringWidth($word);
						$width = $maxwidth; 
						break;
					}
				}
				// mPDF 4.0 to catch errors
				if (mb_strlen($word, $this->mb_enc )<2) {  
					$wordwidth = $maxwidth - 0.0001;
					if ($this->debug) { $this->Error("Table cell width calculated less than that needed for single character!"); }
				}
			}
		}
		// Word fits on line...
		if ($width + $wordwidth  < $maxwidth + 0.0001) {
			$mxw = max($mxw, ($width+$wordwidth));
			$width += $wordwidth + $space;
			$text .= $word.' ';
		}
		// Word does not fit on line...
		else {
			$alloworphans = false;
			// In case of orphan punctuation or SUB/SUP
			// Strip end punctuation
			$tmp = preg_replace('/[\.,;:!?"'.$curlyquote . $curlylowquote ."\xef\xbc\x8c\xe3\x80\x82".']*$/','',$word);
			if ($tmp != $word) {
				$tmpwidth = $this->GetStringWidth($tmp);
				if ($width + $tmpwidth  < $maxwidth + 0.0001) { $alloworphans = true; }
			}
			// If line = SUB/SUP to max of orphansallowed ( $this->SUP || $this->SUB ) // mPDF 3.0
			if(( (isset($chunk[5]) and $chunk[5]) || (isset($chunk[6]) and $chunk[6])) && strlen($word) <= $this->orphansAllowed) {
				$alloworphans = true;
			}


			// if [stripped] word fits
			if ($alloworphans) {
				$mxw = $maxwidth;
				$width += $wordwidth + $space;
				$text .= $word.' ';
			}
			else {
				$width = $wordwidth + $space;
				$text = $word.' ';
				$ch += $lh;
				$ln++;
				$mxw = $maxwidth;
				$lh = $fh; // Reset lineheight
			}
            }
	}

	// End of textbuffer chunk
	if (!$lsend) {
		$width -= $space;
		$text = $this->mb_rtrim($text , $this->mb_enc);
	}

	// RESET FONT SIZE/STYLE
	// RESETTING VALUES
	//Now we must deactivate what we have used
	if(isset($chunk[2]) and $chunk[2] != '') {
		$this->SetStyle('B',false);
		$this->SetStyle('I',false);
	}
	if(isset($chunk[4]) and $chunk[4] != '') {
		$this->SetFont($this->default_font,$this->FontStyle,0,false);
	}
	if(isset($chunk[11]) and $chunk[11] != '') { 
		$this->SetFontSize($this->default_font_size,false);
	}
   }

   if ($width) {
	if ($returnarray) { return array(($ch + $lh),$ln,$mxw); }
	else { return ($ch + $lh) ; }
   }
   else {
	if ($returnarray) { return array(($ch),$ln,$mxw); }
	else { return ($ch) ; }
   }

}


function TableCheckMinWidth(&$text, $maxwidth, $forcewrap = 0, $textbuffer = '')
{
    $biggestword=0;//EDITEI
    $toonarrow=false;//EDITEI
	if ((count($textbuffer) == 0) or ((count($textbuffer) == 1) && ($textbuffer[0][0] == ''))) { return 0; }

    foreach ($textbuffer as $chunk) {

		$line = $chunk[0];

		// IMAGES & FORM ELEMENTS
      	if (substr($line,0,3) == "\xbb\xa4\xac") { //inline object - FORM element or IMAGE!
			// mPDF 4.0
			$objattr = $this->_getObjAttr($line);
			if ($objattr['type']!='hr' && isset($objattr['width']) && ($objattr['width']/$this->shrin_k) > ($maxwidth + 0.0001) ) { 
				if (($objattr['width']/$this->shrin_k) > $biggestword) { $biggestword = ($objattr['width']/$this->shrin_k); }
				$toonarrow=true;//EDITEI
			}
			continue;
		}

		if ($line == "\n") {
			continue;
		}
    		$line = ltrim($line );
    		$line = $this->mb_rtrim($line , $this->mb_enc);
		// SET FONT SIZE/STYLE from $chunk[n]

		// FONTSIZE
	      if(isset($chunk[11]) and $chunk[11] != '') { 
		   if ($this->shrin_k) {
			$this->SetFontSize($chunk[11]/$this->shrin_k,false); 
		   }
		   else {
			$this->SetFontSize($chunk[11],false); 
		   }
		}
		// FONTFAMILY
	      if(isset($chunk[4]) and $chunk[4] != '') { $font = $this->SetFont($chunk[4],$this->FontStyle,0,false); }
		// B I U
	      if(isset($chunk[2]) and $chunk[2] != '') {
	          if (strpos($chunk[2],"B") !== false) $this->SetStyle('B',true);
	          if (strpos($chunk[2],"I") !== false) $this->SetStyle('I',true);
	      }

		$words = explode(' ', $line);
	foreach ($words as $word) {
		$word = $this->mb_rtrim($word, $this->mb_enc);
		$word = ltrim($word);
		$wordwidth = $this->GetStringWidth($word);

		//EDITEI
		//Warn user that maxwidth is insufficient
		if ($wordwidth > $maxwidth + 0.0001) {
			if ($wordwidth > $biggestword) { $biggestword = $wordwidth; }
			$toonarrow=true;//EDITEI
		}

	}

	// RESET FONT SIZE/STYLE
	// RESETTING VALUES
	//Now we must deactivate what we have used
	if(isset($chunk[2]) and $chunk[2] != '') {
	       $this->SetStyle('B',false);
	       $this->SetStyle('I',false);
	}
	if(isset($chunk[4]) and $chunk[4] != '') {
		$this->SetFont($this->default_font,$this->FontStyle,0,false);
	}
	if(isset($chunk[11]) and $chunk[11] != '') { 
		$this->SetFontSize($this->default_font_size,false);
	}
    }

    //Return -(wordsize) if word is bigger than maxwidth 
	// ADDED
      if (($toonarrow) && ($this->table_error_report)) {
		die("Word is too long to fit in table - ".$this->table_error_report_param); 
	}
    if ($toonarrow) return -$biggestword;
    else return 1;
}

function shrinkTable(&$table,$k) {
 		$table['border_spacing_H'] /= $k;
 		$table['border_spacing_V'] /= $k;

		$table['padding']['T'] /= $k;
		$table['padding']['R'] /= $k;
		$table['padding']['B'] /= $k;
		$table['padding']['L'] /= $k;

		$table['margin']['T'] /= $k;
		$table['margin']['R'] /= $k;
		$table['margin']['B'] /= $k;
		$table['margin']['L'] /= $k;

		$table['border_details']['T']['w'] /= $k;
		$table['border_details']['R']['w'] /= $k;
		$table['border_details']['B']['w'] /= $k;
		$table['border_details']['L']['w'] /= $k;

		if (isset($table['max_cell_border_width']['T'])) $table['max_cell_border_width']['T'] /= $k;
		if (isset($table['max_cell_border_width']['R'])) $table['max_cell_border_width']['R'] /= $k;
		if (isset($table['max_cell_border_width']['B'])) $table['max_cell_border_width']['B'] /= $k;
		if (isset($table['max_cell_border_width']['L'])) $table['max_cell_border_width']['L'] /= $k;

		$table['miw'] /= $k;
		$table['maw'] /= $k;

	//	unset($table['miw']);
	//	unset($table['maw']);
	//	$table['wc'] = array_pad(array(),$table['nc'],array('miw'=>0,'maw'=>0));

		for($j = 0 ; $j < $table['nc'] ; $j++ ) { //columns

		   $table['wc'][$j]['miw'] /= $k;
		   $table['wc'][$j]['maw'] /= $k;

		   if (isset($table['wc'][$j]['absmiw']) && $table['wc'][$j]['absmiw'] ) $table['wc'][$j]['absmiw'] /= $k;

		   for($i = 0 ; $i < $table['nr']; $i++ ) { //rows
			$c = &$table['cells'][$i][$j];
			if (isset($c) && $c)  {
				$c['border_details']['T']['w'] /= $k;
				$c['border_details']['R']['w'] /= $k;
				$c['border_details']['B']['w'] /= $k;
				$c['border_details']['L']['w'] /= $k;
				if (isset($c['border_details']['mbw']['T']['L'])) $c['border_details']['mbw']['T']['L'] /= $k;
				if (isset($c['border_details']['mbw']['T']['R'])) $c['border_details']['mbw']['T']['R'] /= $k;
				if (isset($c['border_details']['mbw']['B']['L'])) $c['border_details']['mbw']['B']['L'] /= $k;
				if (isset($c['border_details']['mbw']['B']['RL'])) $c['border_details']['mbw']['B']['R'] /= $k;
				if (isset($c['border_details']['mbw']['L']['T'])) $c['border_details']['mbw']['L']['T'] /= $k;
				if (isset($c['border_details']['mbw']['L']['B'])) $c['border_details']['mbw']['L']['B'] /= $k;
				if (isset($c['border_details']['mbw']['R']['T'])) $c['border_details']['mbw']['R']['T'] /= $k;
				if (isset($c['border_details']['mbw']['R']['B'])) $c['border_details']['mbw']['R']['B'] /= $k;
				$c['padding']['T'] /= $k;
				$c['padding']['R'] /= $k;
				$c['padding']['B'] /= $k;
				$c['padding']['L'] /= $k;
				$c['maxs'] /= $k;
				if (isset($c['w'])) { $c['w'] /= $k; }
				$c['s'] /= $k;
				$c['maw'] /= $k;
				$c['miw'] /= $k;
				if (isset($c['absmiw'])) $c['absmiw'] /= $k;
				if (isset($c['nestedmaw'])) $c['nestedmaw'] /= $k;
				if (isset($c['nestedmiw'])) $c['nestedmiw'] /= $k;
			}
		   }//rows
		}//columns
		unset($c);
}


////////////////////////TABLE CODE (from PDFTable)/////////////////////////////////////
////////////////////////TABLE CODE (from PDFTable)/////////////////////////////////////
////////////////////////TABLE CODE (from PDFTable)/////////////////////////////////////
//table		Array of (w, h, bc, nr, wc, hr, cells)
//w			Width of table
//h			Height of table
//nc			Number column
//nr			Number row
//hr			List of height of each row
//wc			List of width of each column
//cells		List of cells of each rows, cells[i][j] is a cell in the table
function _tableColumnWidth(&$table,$firstpass=false){
	$cs = &$table['cells'];

	$nc = $table['nc'];
	$nr = $table['nr'];
	$listspan = array();

	if ($table['borders_separate']) { 
		$tblbw = $table['border_details']['L']['w'] + $table['border_details']['R']['w'] + $table['margin']['L'] + $table['margin']['R'] +  $table['padding']['L'] + $table['padding']['R'] + $table['border_spacing_H'];
	}
	else { $tblbw = $table['max_cell_border_width']['L']/2 + $table['max_cell_border_width']['R']/2 + $table['margin']['L'] + $table['margin']['R']; }

	// ADDED table['l'][colno] 
	// = total length of text approx (using $c['s']) in that column - used to approximately distribute col widths in _tableWidth
	//
	for($j = 0 ; $j < $nc ; $j++ ) { //columns
		$wc = &$table['wc'][$j];
		for($i = 0 ; $i < $nr ; $i++ ) { //rows
			if (isset($cs[$i][$j]) && $cs[$i][$j])  {
				$c = &$cs[$i][$j];


				if ($table['borders_separate']) {	// NB twice border width
					$extrcw = $c['border_details']['L']['w'] + $c['border_details']['R']['w'] + $c['padding']['L'] + $c['padding']['R'] + $table['border_spacing_H'];
				}
				else {
					$extrcw = $c['border_details']['L']['w']/2 + $c['border_details']['R']['w']/2 + $c['padding']['L'] + $c['padding']['R'];
				}

				// mPDF 3.0
				//$mw = $this->GetStringWidth('W') + $extrcw ;
				$mw = 0;

				$c['absmiw'] = $mw;

				if (isset($c['R']) && $c['R']) {
					$c['maw'] = $c['miw'] = $this->FontSize + $extrcw ;
					if (isset($c['w'])) {	// If cell width is specified
						if ($c['miw'] <$c['w'])	{ $c['miw'] = $c['w']; }
					}
					if (!isset($c['colspan'])) {
						if ($wc['miw'] < $c['miw']) { $wc['miw']	= $c['miw']; }
						if ($wc['maw'] < $c['maw']) { $wc['maw']	= $c['maw']; }

						if ($firstpass) { 
						   if (isset($table['l'][$j]) ) { 
							$table['l'][$j] += $c['miw'] ;
						   }
						   else {
							$table['l'][$j] = $c['miw'] ;
						   }
						}
					}
					if ($c['miw'] > $wc['miw']) { $wc['miw'] = $c['miw']; } 
        				if ($wc['miw'] > $wc['maw']) { $wc['maw'] = $wc['miw']; }
					continue;
				}

				if ($firstpass) {
					if (isset($c['s'])) { $c['s'] += $extrcw; }
					if (isset($c['maxs'])) { $c['maxs'] += $extrcw; }
					if (isset($c['nestedmiw'])) { $c['nestedmiw'] += $extrcw; }
					if (isset($c['nestedmaw'])) { $c['nestedmaw'] += $extrcw; }
				}


				// If minimum width has already been set by a nested table or inline object (image/form), use it
				if (isset($c['nestedmiw'])) { $miw = $c['nestedmiw']; }
				else  { $miw = $mw; }

				if (isset($c['maxs']) && $c['maxs'] != '') { $c['s'] = $c['maxs']; }

				// If maximum width has already been set by a nested table, use it
				if (isset($c['nestedmaw'])) { $c['maw'] = $c['nestedmaw']; }
				else $c['maw'] = $c['s'];

				if (isset($c['nowrap']) && $c['nowrap']) { $miw = $c['maw']; }

				if (isset($c['wpercent']) && $firstpass) {
	 				if (isset($c['colspan'])) {	// Not perfect - but % set on colspan is shared equally on cols.
					   for($k=0;$k<$c['colspan'];$k++) {
						$table['wc'][($j+$k)]['wpercent'] = $c['wpercent'] / $c['colspan'];
					   }
					}
	 				else {
						if (isset($table['w']) && $table['w']) { $c['w'] = $c['wpercent']/100 * ($table['w'] - $tblbw ); }
						$wc['wpercent'] = $c['wpercent'];
					}
				}


				if (isset($c['w'])) {	// If cell width is specified
					if ($miw<$c['w'])	{ $c['miw'] = $c['w']; }	// Cell min width = that specified
					if ($miw>$c['w'])	{ $c['miw'] = $c['w'] = $miw; } // If width specified is less than minimum allowed (W) increase it
					if (!isset($wc['w'])) { $wc['w'] = 1; }		// If the Col width is not specified = set it to 1

				}
				else { $c['miw'] = $miw; }	// If cell width not specified -> set Cell min width it to minimum allowed (W)

				if ($c['maw']  < $c['miw']) { $c['maw'] = $c['miw']; }	// If Cell max width < Minwidth - increase it to =
				if (!isset($c['colspan'])) {
					if ($wc['miw'] < $c['miw']) { $wc['miw']	= $c['miw']; }	// Update Col Minimum and maximum widths
					if ($wc['maw'] < $c['maw']) { $wc['maw']	= $c['maw']; }
					if ((isset($wc['absmiw']) && $wc['absmiw'] < $c['absmiw']) || !isset($wc['absmiw'])) { $wc['absmiw'] = $c['absmiw']; }	// Update Col Minimum and maximum widths

					if (isset($table['l'][$j]) ) { 
						$table['l'][$j] += $c['s'];
	
					}
					else {
						$table['l'][$j] = $c['s'];
					}

				}
				else { 
					$listspan[] = array($i,$j);
				}

 			//Check if minimum width of the whole column is big enough for largest word to fit
        			$auxtext = implode("",$c['text']);
				if (isset($c['textbuffer'])) {
		       			$minwidth = $this->TableCheckMinWidth($auxtext,$wc['miw']- $extrcw ,0,$c['textbuffer']); 
				}
				else { $minwidth = 0; }
        			if ($minwidth < 0) { 
					//increase minimum width
					if (!isset($c['colspan'])) {
						$wc['miw'] = max($wc['miw'],((-$minwidth) + $extrcw) );  
					}
				}
 				if (!isset($c['colspan'])) {
	        			if ($wc['miw'] > $wc['maw']) { $wc['maw'] = $wc['miw']; } //update maximum width, if needed
				}
			}
			unset($c);
		}//rows
	}//columns


	// COLUMN SPANS
	$wc = &$table['wc'];
	foreach ($listspan as $span) {
		list($i,$j) = $span;
		$c = &$cs[$i][$j];
		$lc = $j + $c['colspan'];
		if ($lc > $nc) { $lc = $nc; }
		
		$wis = $wisa = 0;
		$was = $wasa = 0;
		$list = array();
		for($k=$j;$k<$lc;$k++) {
			if (isset($table['l'][$k]) ) { 
				if ($c['R']) { $table['l'][$k] += $c['miw']/$c['colspan'] ; }
				else { $table['l'][$k] += $c['s']/$c['colspan']; }
			}
			else {
				if ($c['R']) { $table['l'][$k] = $c['miw']/$c['colspan'] ; }
				else { $table['l'][$k] = $c['s']/$c['colspan']; }
			}
			$wis += $wc[$k]['miw'];
			$was += $wc[$k]['maw'];
			if (!isset($c['w'])) {
				$list[] = $k;
				$wisa += $wc[$k]['miw'];
				$wasa += $wc[$k]['maw'];
			}
		}
		if ($c['miw'] > $wis) {
			if (!$wis) {
				for($k=$j;$k<$lc;$k++) { $wc[$k]['miw'] = $c['miw']/$c['colspan']; }
			}
			else if (!count($list)) {
				$wi = $c['miw'] - $wis;
				for($k=$j;$k<$lc;$k++) { $wc[$k]['miw'] += ($wc[$k]['miw']/$wis)*$wi; }
			}
			else {
				$wi = $c['miw'] - $wis;
				foreach ($list as $k) { $wc[$k]['miw'] += ($wc[$k]['miw']/$wisa)*$wi; }
			}
		}
		if ($c['maw'] > $was) {
			if (!$wis) {
				for($k=$j;$k<$lc;$k++) { $wc[$k]['maw'] = $c['maw']/$c['colspan']; }
			}
			else if (!count($list)) {
				$wi = $c['maw'] - $was;
				for($k=$j;$k<$lc;$k++) { $wc[$k]['maw'] += ($wc[$k]['maw']/$was)*$wi; }
			}
			else {
				$wi = $c['maw'] - $was;
				foreach ($list as $k) { $wc[$k]['maw'] += ($wc[$k]['maw']/$wasa)*$wi; }
			}
		}
		unset($c);
	}


	$checkminwidth = 0;
	$checkmaxwidth = 0;
	$totallength = 0;

	for( $i = 0 ; $i < $nc ; $i++ ) {
		$checkminwidth += $table['wc'][$i]['miw'];
		$checkmaxwidth += $table['wc'][$i]['maw'];
		$totallength += $table['l'][$i];
	}

	if (!isset($table['w']) && $firstpass) {
	   $sumpc = 0;
	   // mPDF 3.2
	   $notset = 0;
	   for( $i = 0 ; $i < $nc ; $i++ ) {
		  if (isset($table['wc'][$i]['wpercent']) && $table['wc'][$i]['wpercent']) {
			$sumpc += $table['wc'][$i]['wpercent'];
		  }
		  // mPDF 3.2
		  else { $notset++; }
	   }

	   // mPDF 3.2   If sum of widths as %  >= 100% and not all columns are set
		// Set a nominal width of 1% for unset columns
	   if ($sumpc >= 100 && $notset) {
	   	for( $i = 0 ; $i < $nc ; $i++ ) {
		  if ((!isset($table['wc'][$i]['wpercent']) || !$table['wc'][$i]['wpercent']) &&
			(!isset($table['wc'][$i]['w']) || !$table['wc'][$i]['w'])) {
			$table['wc'][$i]['wpercent'] = 1;
		  }
	   	}
	   }


	   if ($sumpc) {	// if any percents are set
		// mPDF 4.0
		$sumnonpc = (100 - $sumpc);
		$sumpc = max($sumpc,100);
	      $miwleft = 0;
		$miwleftcount = 0;
		$miwsurplusnonpc = 0;
		$maxcalcmiw  = 0;
	      $mawleft = 0;
		$mawleftcount = 0;
		$mawsurplusnonpc = 0;
		$maxcalcmaw  = 0;
		for( $i = 0 ; $i < $nc ; $i++ ) {
		  if (isset($table['wc'][$i]['wpercent'])) {
			$maxcalcmiw = max($maxcalcmiw, ($table['wc'][$i]['miw'] * $sumpc /$table['wc'][$i]['wpercent']) );
			$maxcalcmaw = max($maxcalcmaw, ($table['wc'][$i]['maw'] * $sumpc /$table['wc'][$i]['wpercent']) );
		  }
		  else {
			$miwleft += $table['wc'][$i]['miw'];
			$mawleft += $table['wc'][$i]['maw'];
		  	if (!isset($table['wc'][$i]['w'])) { $miwleftcount++; $mawleftcount++; }
		  }
		}
		if ($miwleft && $sumnonpc > 0) { $miwnon = $miwleft * 100 / $sumnonpc; }
		if ($mawleft && $sumnonpc > 0) { $mawnon = $mawleft * 100 / $sumnonpc; }
		if (($miwnon > $checkminwidth || $maxcalcmiw > $checkminwidth) && $this->keep_table_proportions) {
			if ($miwnon > $maxcalcmiw) { 
				$miwsurplusnonpc = round((($miwnon * $sumnonpc / 100) - $miwleft),3); 
				$checkminwidth = $miwnon; 
			}
			else { $checkminwidth = $maxcalcmiw; }
			for( $i = 0 ; $i < $nc ; $i++ ) {
			  if (isset($table['wc'][$i]['wpercent'])) {
				$newmiw = $checkminwidth * $table['wc'][$i]['wpercent']/100;
				if ($table['wc'][$i]['miw'] < $newmiw) {
				  $table['wc'][$i]['miw'] = $newmiw;
				}
				$table['wc'][$i]['w'] = 1;
			  }
			  else if ($miwsurplusnonpc && !$table['wc'][$i]['w']) {
				$table['wc'][$i]['miw'] +=  $miwsurplusnonpc / $miwleftcount;
			  }
			}
		}
		if (($mawnon > $checkmaxwidth || $maxcalcmaw > $checkmaxwidth )) {
			if ($mawnon > $maxcalcmaw) { 
				$mawsurplusnonpc = round((($mawnon * $sumnonpc / 100) - $mawleft),3); 
				$checkmaxwidth = $mawnon; 
			}
			else { $checkmaxwidth = $maxcalcmaw; }
			for( $i = 0 ; $i < $nc ; $i++ ) {
			  if (isset($table['wc'][$i]['wpercent'])) {
				$newmaw = $checkmaxwidth * $table['wc'][$i]['wpercent']/100;
				if ($table['wc'][$i]['maw'] < $newmaw) {
				  $table['wc'][$i]['maw'] = $newmaw;
				}
				$table['wc'][$i]['w'] = 1;
			  }
			  else if ($mawsurplusnonpc && !$table['wc'][$i]['w']) {
				$table['wc'][$i]['maw'] +=  $mawsurplusnonpc / $mawleftcount;
			  }
			  if ($table['wc'][$i]['maw'] < $table['wc'][$i]['miw']) { $table['wc'][$i]['maw'] = $table['wc'][$i]['miw']; }
			}
		}
		if ($checkminwidth > $checkmaxwidth) { $checkmaxwidth = $checkminwidth; }
	   }
	}

	if (isset($table['wpercent']) && $table['wpercent']) {
		$checkminwidth *= (100 / $table['wpercent']);
		$checkmaxwidth *= (100 / $table['wpercent']);
	}


	$checkminwidth += $tblbw ;
	$checkmaxwidth += $tblbw ;

	// Table['miw'] set by percent in first pass may be larger than sum of column miw
	if ((isset($table['miw']) && $checkminwidth > $table['miw']) || !isset($table['miw'])) {  $table['miw'] = $checkminwidth; }
	if ((isset($table['maw']) && $checkmaxwidth > $table['maw']) || !isset($table['maw'])) { $table['maw'] = $checkmaxwidth; }
	$table['tl'] = $totallength ;
	if (!$this->isCJK) {
		if ($this->table_rotate) {
			$mxw = $this->tbrot_maxw;
		}
		else {
			$mxw = $this->blk[$this->blklvl]['inner_width'];
		}
		if (isset($table['w']) && $table['w'] ) {
			if ($table['w'] >= $checkminwidth && $table['w'] <= $mxw) { $table['maw'] = $mxw = $table['w']; }	// mPDF 4.0
			else if ($table['w'] >= $checkminwidth && $table['w'] > $mxw && $this->keep_table_proportions) { $checkminwidth = $table['w']; }
			else {  
				unset($table['w']); 
			}
		}
		$ratio = $checkminwidth/$mxw;
		if ($checkminwidth > $mxw) { return array(($ratio +0.001),$checkminwidth); }	// 0.001 to allow for rounded numbers when resizing
	}
	return array(0,0);
}



function _tableWidth(&$table){
	$widthcols = &$table['wc'];
	$numcols = $table['nc'];
	$tablewidth = 0;
	// Added mPDF1.4 - table border width if separate
	if ($table['borders_separate']) { 
		$tblbw = $table['border_details']['L']['w'] + $table['border_details']['R']['w'] + $table['margin']['L'] + $table['margin']['R'] +  $table['padding']['L'] + $table['padding']['R'] + $table['border_spacing_H'];
	}
	else { $tblbw = $table['max_cell_border_width']['L']/2 + $table['max_cell_border_width']['R']/2 + $table['margin']['L'] + $table['margin']['R']; }


	if ($table['level']>1 && isset($table['w'])) { 
		if (isset($table['wpercent']) && $table['wpercent']) { 
			$table['w'] = $temppgwidth = (($table['w']-$tblbw) * $table['wpercent'] / 100) + $tblbw ;  
		}
		else { 
			$temppgwidth = $table['w'] ;  
		}
	}
	else if ($this->table_rotate) {
		$temppgwidth = $this->tbrot_maxw;
		// If it is less than 1/20th of the remaining page height to finish the DIV (i.e. DIV padding + table bottom margin)
		// then allow for this
		$enddiv = $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'];
		if ($enddiv/$temppgwidth <0.05) { $temppgwidth -= $enddiv; }
	}
	else {
		if (isset($table['w']) && $table['w']< $this->blk[$this->blklvl]['inner_width']) { 
			$notfullwidth = 1;
			$temppgwidth = $table['w'] ;  
		}
		else { $temppgwidth = $this->blk[$this->blklvl]['inner_width']; }
	}


	$totaltextlength = 0;	// Added - to sum $table['l'][colno]
	$totalatextlength = 0;	// Added - to sum $table['l'][colno] for those columns where width not set
	$percentages_set = 0; 
	for ( $i = 0 ; $i < $numcols ; $i++ ) {
		if (isset($widthcols[$i]['wpercent']))  { $tablewidth += $widthcols[$i]['maw']; $percentages_set = 1; }
		else if (isset($widthcols[$i]['w']))  { $tablewidth += $widthcols[$i]['miw']; }
		else { $tablewidth += $widthcols[$i]['maw']; }
		$totaltextlength += $table['l'][$i];
	}
	if (!$totaltextlength) { $totaltextlength =1; }
	$tablewidth += $tblbw;	// Outer half of table borders

	if ($tablewidth > $temppgwidth) { 
		$table['w'] = $temppgwidth; 
	}
	// if any widths set as percentages and max width fits < page width
	else if ($tablewidth < $temppgwidth && !isset($table['w']) && $percentages_set) {
		$table['w'] = $table['maw'];
	}
	// if table width is set and is > allowed width
	if (isset($table['w']) && $table['w'] > $temppgwidth) { $table['w'] = $temppgwidth; }
	// IF the table width is now set - Need to distribute columns widths
	if (isset($table['w'])) {
		$wis = $wisa = 0;
		$list = array();
		$notsetlist = array();
		for( $i = 0 ; $i < $numcols ; $i++ ) {
			$wis += $widthcols[$i]['miw'];
			if (!isset($widthcols[$i]['w']) || ($widthcols[$i]['w'] && $table['w'] > $temppgwidth && !$this->keep_table_proportions && !$notfullwidth )){ 
				$list[] = $i;  
				$wisa += $widthcols[$i]['miw'];
				$totalatextlength += $table['l'][$i];
			}
		}
		if (!$totalatextlength) { $totalatextlength =1; }

		// Allocate spare (more than col's minimum width) across the cols according to their approx total text length
		// Do it by setting minimum width here
		if ($table['w'] > $wis + $tblbw) {
			// First set any cell widths set as percentages
			if ($table['w'] < $temppgwidth || $this->keep_table_proportions) {
				for($k=0;$k<$numcols;$k++) {
					if (isset($widthcols[$k]['wpercent'])) {
						$curr = $widthcols[$k]['miw'];
						$widthcols[$k]['miw'] = ($table['w']-$tblbw) * $widthcols[$k]['wpercent']/100;
						$wis += $widthcols[$k]['miw'] - $curr;
						$wisa += $widthcols[$k]['miw'] - $curr;
					}
				}
			}
			// Now allocate surplus up to maximum width of each column
			$surplus = 0;  $ttl = 0;	// number of surplus columns
			if (!count($list)) {
				$wi = ($table['w']-($wis + $tblbw));	//	i.e. extra space to distribute
				for($k=0;$k<$numcols;$k++) {
					$spareratio = ($table['l'][$k] / $totaltextlength); //  gives ratio to divide up free space
					// Don't allocate more than Maximum required width - save rest in surplus
					if ($widthcols[$k]['miw'] + ($wi * $spareratio) > $widthcols[$k]['maw']) {
						$surplus += ($wi * $spareratio) - ($widthcols[$k]['maw']-$widthcols[$k]['miw']);
						$widthcols[$k]['miw'] = $widthcols[$k]['maw'];
					}
					else { 
						$notsetlist[] = $k;  
						$ttl += $table['l'][$k];
						$widthcols[$k]['miw'] += ($wi * $spareratio); 
					}

				}
			}
			else {
				$wi = ($table['w'] - ($wis + $tblbw));	//	i.e. extra space to distribute
				foreach ($list as $k) {
					$spareratio = ($table['l'][$k] / $totalatextlength); //  gives ratio to divide up free space
					// Don't allocate more than Maximum required width - save rest in surplus
					if ($widthcols[$k]['miw'] + ($wi * $spareratio) > $widthcols[$k]['maw']) {
						$surplus += ($wi * $spareratio) - ($widthcols[$k]['maw']-$widthcols[$k]['miw']);
						$widthcols[$k]['miw'] = $widthcols[$k]['maw'];
					}
					else { 
						$notsetlist[] = $k;  
						$ttl += $table['l'][$k];
						$widthcols[$k]['miw'] += ($wi * $spareratio); 
					}
				}
			}
			// If surplus still left over apportion it across columns
			if ($surplus) { 
			   // if some are set only add to remaining - otherwise add to all of them
			   if (count($notsetlist) && count($notsetlist) < $numcols) {
				foreach ($notsetlist AS $i) {
					if ($ttl) $widthcols[$i]['miw'] += $surplus * $table['l'][$i] / $ttl ;
				}
			   }
			   // If some widths are defined, and others have been added up to their maxmum
			   else if (count($list) && count($list) < $numcols) {
				foreach ($list AS $i) {
					$widthcols[$i]['miw'] += $surplus / count($list) ;
				}
			   }
			   else if ($numcols) {	// If all columns
				$ttl = array_sum($table['l']);
				for ($i=0;$i<$numcols;$i++) {
					$widthcols[$i]['miw'] += $surplus * $table['l'][$i] / $ttl;
				}
			   }
			}

		}

		// This sets the columns all to minimum width (which has been increased above if appropriate)
		for ($i=0;$i<$numcols;$i++) {
			$widthcols[$i] = $widthcols[$i]['miw'];
		}

		// TABLE NOT WIDE ENOUGH EVEN FOR MINIMUM CONTENT WIDTH
		// If sum of column widths set are too wide for table
		$checktablewidth = 0;
		for ( $i = 0 ; $i < $numcols ; $i++ ) {
			$checktablewidth += $widthcols[$i];
		}
		if ($checktablewidth > ($temppgwidth + 0.001 - $tblbw)) { 
		   $usedup = 0; $numleft = 0;
		   for ($i=0;$i<$numcols;$i++) {
			if ((isset($widthcols[$i]) && $widthcols[$i] > (($temppgwidth - $tblbw) / $numcols)) && (!isset($widthcols[$i]['w']))) { 
				$numleft++; 
				unset($widthcols[$i]); 
			}
			else { $usedup += $widthcols[$i]; }
		   }
		   for ($i=0;$i<$numcols;$i++) {
			if (!isset($widthcols[$i]) || !$widthcols[$i]) { 
				$widthcols[$i] = ((($temppgwidth - $tblbw) - $usedup)/ ($numleft)); 
			}
		   }
		}

	}
	else { //table has no width defined
		$table['w'] = $tablewidth;  
		for ( $i = 0 ; $i < $numcols ; $i++) {
			if (isset($widthcols[$i]['wpercent']) && $this->keep_table_proportions)  { $colwidth = $widthcols[$i]['maw']; }
			else if (isset($widthcols[$i]['w']))  { $colwidth = $widthcols[$i]['miw']; }
			else { $colwidth = $widthcols[$i]['maw']; }
			unset($widthcols[$i]);
			$widthcols[$i] = $colwidth;
		}
	}
}
	
function _tableHeight(&$table){
	$level = $table['level'];
	$levelid = $table['levelid'];
	$cells = &$table['cells'];
	$numcols = $table['nc'];
	$numrows = $table['nr'];
	$listspan = array();
	$checkmaxheight = 0;
	$headerrowheight = 0;
	$checkmaxheightplus = 0;
	$headerrowheightplus = 0;
	// mPDF 4.0
	$footerrowheight = 0;
	$footerrowheightplus = 0;
	if ($this->table_rotate) {
		$temppgheight = $this->tbrot_maxh;
		$remainingpage = $this->tbrot_maxh;
	}
	else {
		$temppgheight = ($this->h - $this->bMargin - $this->tMargin) - $this->kwt_height;
		$remainingpage = ($this->h - $this->bMargin - $this->y) - $this->kwt_height;

		// If it is less than 1/20th of the remaining page height to finish the DIV (i.e. DIV padding + table bottom margin)
		// then allow for this
		$enddiv = $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $table['margin']['B'];
		// mPDF 4.0 bug fix: if remaining page == 0 get error
		if ($remainingpage > $enddiv && $enddiv/$remainingpage <0.05) { $remainingpage -= $enddiv; }
		else if ($remainingpage == 0) { $remainingpage = 0.001; }
		if ($temppgheight > $enddiv && $enddiv/$temppgheight <0.05) { $temppgheight -= $enddiv; }
		else if ($temppgheight == 0) { $temppgheight = 0.001; }
	}


	for( $i = 0 ; $i < $numrows ; $i++ ) { //rows
		$heightrow = &$table['hr'][$i];
		for( $j = 0 ; $j < $numcols ; $j++ ) { //columns
			if (isset($cells[$i][$j]) && $cells[$i][$j]) {
				$c = &$cells[$i][$j];

				if ($table['borders_separate']) {	// NB twice border width
					$extraWLR = ($c['border_details']['L']['w']+$c['border_details']['R']['w']) + ($c['padding']['L']+$c['padding']['R'])+$table['border_spacing_H'];
					$extrh = ($c['border_details']['T']['w']+$c['border_details']['B']['w']) + ($c['padding']['T']+$c['padding']['B'])+$table['border_spacing_V'];
				}
				else {
					$extraWLR = ($c['border_details']['L']['w']+$c['border_details']['R']['w'])/2 + ($c['padding']['L']+$c['padding']['R']);
					$extrh = ($c['border_details']['T']['w']+$c['border_details']['B']['w'])/2 + ($c['padding']['T']+$c['padding']['B']);
				}

				list($x,$cw) = $this->_tableGetWidth($table, $i,$j);
				//Check whether width is enough for this cells' text
				$auxtext = implode("",$c['text']);
				$auxtext2 = $auxtext; //in case we have text with styles

				$aux3 = $auxtext; //in case we have text with styles

				// Get CELL HEIGHT = NO OF LINES
				// ++ extra parameter forces wrap to break word
				if ($c['R']) {
					$aux4 = implode(" ",$c['text']);
					$s_fs = $this->FontSizePt;
					$s_f = $this->FontFamily;	// mPDF 3.0
					$s_st = $this->FontStyle;	// mPDF 3.0
					$this->SetFont($c['textbuffer'][0][4],$c['textbuffer'][0][2],$c['textbuffer'][0][11] / $this->shrin_k,true,true);
					$aux4 = ltrim($aux4);
					$aux4= $this->mb_rtrim($aux4,$this->mb_enc);
	       			$tempch = $this->GetStringWidth($aux4);
					if ($c['R'] >= 45 && $c['R'] < 90) {
						$tempch = ((sin(deg2rad($c['R']))) * $tempch ) + ((sin(deg2rad($c['R']))) * (($c['textbuffer'][0][11]/$this->k) / $this->shrin_k));
					} 
					$this->SetFont($s_f,$s_st,$s_fs,true,true);
					$ch = ($tempch ) + $extrh ;  
				}
				else {
				   if (isset($c['textbuffer'])) {
					$tempch = $this->TableWordWrap(($cw-$extraWLR),1,$c['textbuffer']);  
				   }
				   else { $tempch = 0; }

					// Added cellpadding top and bottom. (Lineheight already adjusted to table_lineheight)
					$ch = $tempch + $extrh ;
				}

				//If height is defined and it is bigger than calculated $ch then update values
				if (isset($c['h']) && $c['h'] > $ch) {
					$c['mih'] = $ch; //in order to keep valign working
					$ch = $c['h'];
				}
				else $c['mih'] = $ch;
				if (isset($c['rowspan']))	$listspan[] = array($i,$j);
				elseif ($heightrow < $ch) $heightrow = $ch;
	
				// this is the extra used in _tableWrite to determine whether to trigger a page change
				if ($table['borders_separate']) { 
				  // mPDF 3.0 - bug fix
				  //if ($i == ($numrows-1) || ($i+$cell['rowspan']) == ($numrows) ) {
				  if ($i == ($numrows-1) || (isset($c['rowspan']) && ($i+$c['rowspan']) == ($numrows)) ) {
					$extra = $table['margin']['B'] + $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; 
				  }
				  else {
					$extra = $table['border_spacing_V']/2; 
				  }
				}
				// mPDF 3.0 - bug fix
	  			else { $extra = $c['border_details']['B']['w'] /2; }

				// mPDF 3.0
				if ($i <$this->tableheadernrows && $this->usetableheader) {
					$headerrowheight = max($headerrowheight,$ch);
					$headerrowheightplus = max($headerrowheightplus,$ch+$extra);
				}
				// mPDF 4.0
				else if ($table['is_tfoot'][$i]) {
					$footerrowheight = max($footerrowheight,$ch);
					$footerrowheightplus = max($footerrowheightplus,$ch+$extra);
				}
				else {
					$checkmaxheight = max($checkmaxheight,$ch);
					$checkmaxheightplus = max($checkmaxheightplus,$ch+$extra);
				}


				unset($c);
			}
		}//end of columns
	}//end of rows
	$heightrow = &$table['hr'];
	foreach ($listspan as $span) {
		list($i,$j) = $span;
		$c = &$cells[$i][$j];
		$lr = $i + $c['rowspan'];
		if ($lr > $numrows) $lr = $numrows;
		$hs = $hsa = 0;
		$list = array();
		for($k=$i;$k<$lr;$k++) {
			$hs += $heightrow[$k];
			if (!isset($c['h'])) {
				$list[] = $k;
				$hsa += $heightrow[$k];
			}
		}
		// mPDF 3.0 
		if ($table['borders_separate']) { 
		  if ($i == ($numrows-1) || ($i+$c['rowspan']) == ($numrows) ) {
			$extra = $table['margin']['B'] + $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; 
		  }
		  else {
			$extra = $table['border_spacing_V']/2; 
		  }
		}
	  	else { $extra = $c['border_details']['B']['w'] /2; }
		if ($i <$this->tableheadernrows && $this->usetableheader) {
			$headerrowheight = max($headerrowheight,$hs);
			$headerrowheightplus = max($headerrowheightplus,$hs+$extra);
		}
		// mPDF 4.0
		else if ($table['is_tfoot'][$i]) {
			$footerrowheight = max($footerrowheight,$hs);
			$footerrowheightplus = max($footerrowheightplus,$hs+$extra);
		}
		else {
			$checkmaxheight = max($checkmaxheight,$hs);
			$checkmaxheightplus = max($checkmaxheightplus,$hs+$extra);
		}

		if ($c['mih'] > $hs) {
			if (!$hs) {
				for($k=$i;$k<$lr;$k++) $heightrow[$k] = $c['mih']/$c['rowspan'];
			}
			elseif (!count($list)) {
				$hi = $c['mih'] - $hs;
				for($k=$i;$k<$lr;$k++) $heightrow[$k] += ($heightrow[$k]/$hs)*$hi;
			}
			else {
				$hi = $c['mih'] - $hsa;
				foreach ($list as $k) $heightrow[$k] += ($heightrow[$k]/$hsa)*$hi;
			}
		}
		unset($c);
	}

	$table['h'] = array_sum($table['hr']);

	if ($table['borders_separate']) { 
		$table['h'] += $table['margin']['T'] + $table['margin']['B'] + $table['border_details']['T']['w'] + $table['border_details']['B']['w'] + $table['border_spacing_V'] + $table['padding']['T'] +  $table['padding']['B'];
	}
	else { 
		$table['h'] += $table['margin']['T'] + $table['margin']['B'] + $table['max_cell_border_width']['T']/2 + $table['max_cell_border_width']['B']/2;
	}

	// mPDF 4.0
	$maxrowheight = $checkmaxheightplus + $headerrowheightplus + $footerrowheightplus;
	return array($table['h'],$maxrowheight,$temppgheight,$remainingpage);
}

function _tableGetWidth(&$table, $i,$j){
	$cell = &$table['cells'][$i][$j];
	if ($cell) {
		if (isset($cell['x0'])) return array($cell['x0'], $cell['w0']);
		$x = 0;
		$widthcols = &$table['wc'];
		for( $k = 0 ; $k < $j ; $k++ ) $x += $widthcols[$k];
		$w = $widthcols[$j];
		if (isset($cell['colspan'])) {
			 for ( $k = $j+$cell['colspan']-1 ; $k > $j ; $k-- )	$w += $widthcols[$k];
		}
		$cell['x0'] = $x;
		$cell['w0'] = $w;
		return array($x, $w);
	}
	return array(0,0);
}

function _tableGetHeight(&$table, $i,$j){
	$cell = &$table['cells'][$i][$j];
	if ($cell){
		if (isset($cell['y0'])) return array($cell['y0'], $cell['h0']);
		$y = 0;
		$heightrow = &$table['hr'];
		for ($k=0;$k<$i;$k++) $y += $heightrow[$k];
		$h = $heightrow[$i];
		if (isset($cell['rowspan'])){
			for ($k=$i+$cell['rowspan']-1;$k>$i;$k--)
				$h += $heightrow[$k];
		}
		$cell['y0'] = $y;
		$cell['h0'] = $h;
		return array($y, $h);
	}
	return array(0,0);
}


// CHANGED TO ALLOW TABLE BORDER TO BE SPECIFIED CORRECTLY - added border_details
// mPDF 3.0 Table borders need additional parameters - either corner (TLBR) and/or border-spacing-H or -V ($bsh/$bsv)
function _tableRect($x, $y, $w, $h, $bord=-1, $details=array(), $buffer=false, $bSeparate=false, $cort='cell', $tablecorner='', $bsv=0, $bsh=0){
	// mPDF 3.0 Disabled again - buffer is printed at end of each table row - in fn. _tablewrite()
//	if ($this->ColActive) { $buffer = false; }

	// mPDF 3.0
	$cellBorderOverlay = array();

	if ($bord==-1) { $this->Rect($x, $y, $w, $h); }
	else if ($bord){
	   if (!$bSeparate && $buffer) {
		$priority = 'LRTB';
		for($p=0;$p<strlen($priority);$p++) {
			$side = substr($priority,$p,1);
			$details['p'] = $side ;

			$dom = 0;
			if (isset($details[$side]['w'])) { $dom += ($details[$side]['w'] * 100000); }
			if (isset($details[$side]['style'])) { $dom += (array_search($details[$side]['style'],$this->borderstyles)*100) ; }
			if (isset($details[$side]['dom'])) { $dom += ($details[$side]['dom']*10); }

			// mPDF 3.0 Precedence to darker colours at joins
			$coldom = 0;
			if (isset($details[$side]['c']) && is_array($details[$side]['c'])) { 
				$coldom = 10-((($details[$side]['c']['R']*1.00)+($details[$side]['c']['G']*1.00)+($details[$side]['c']['B']*1.00))/76.5); 
			} // 10 black - 0 white
			if ($coldom) { $dom += $coldom; }
			
			// mPDF 3.0 Lastly precedence to RIGHT and BOTTOM cells at joins
			if (isset($details['cellposdom'])) { $dom += $details['cellposdom']; } 

			$save = false;
			// mPDF 3.0
			if ($side == 'T' && $this->issetBorder($bord, _BORDER_TOP)) { $cbord = _BORDER_TOP; $save = true; }
			else if ($side == 'L' && $this->issetBorder($bord, _BORDER_LEFT)) { $cbord = _BORDER_LEFT; $save = true; }
			else if ($side == 'R' && $this->issetBorder($bord, _BORDER_RIGHT)) { $cbord = _BORDER_RIGHT; $save = true; }
			else if ($side == 'B' && $this->issetBorder($bord, _BORDER_BOTTOM)) { $cbord = _BORDER_BOTTOM; $save = true; }


			if ($save) {
			   $this->cellBorderBuffer[] = array(
				'side' => $side,
				'dom' => $dom,
				'x' => $x, 
				'y' => $y, 
				'w' => $w, 
				'h' => $h, 
				'bord' => $cbord, 
				'details' => $details,
				'borders_separate' => $bSeparate
			   );
			   if ($details[$side]['style'] == 'ridge' || $details[$side]['style'] == 'groove' || $details[$side]['style'] == 'inset' || $details[$side]['style'] == 'outset' || $details[$side]['style'] == 'double' ) {
			    $details[$side]['overlay'] = true;
			    $this->cellBorderBuffer[] = array(
				'side' => $side,
				'dom' => $dom+4,
				'x' => $x, 
				'y' => $y, 
				'w' => $w, 
				'h' => $h, 
				'bord' => $cbord, 
				'details' => $details,
				'borders_separate' => $bSeparate
			    );
			   }
			}
		}
		return;
	   }

	   if (isset($details['p']) && strlen($details['p'])>1) { $priority = $details['p']; }
	   else { $priority='LTRB'; }
	   $Tw = 0; 
	   $Rw = 0; 
	   $Bw = 0; 
	   $Lw = 0; 
		if (isset($details['T']['w'])) { $Tw = $details['T']['w']; }
		if (isset($details['R']['w'])) { $Rw = $details['R']['w']; }
		if (isset($details['B']['w'])) { $Bw = $details['B']['w']; }
		if (isset($details['L']['w'])) { $Lw = $details['L']['w']; }

	   $x2 = $x + $w; $y2 = $y + $h;
	   $oldlinewidth = $this->LineWidth;

	   for($p=0;$p<strlen($priority);$p++) {
		$side = substr($priority,$p,1);
		$xadj = 0;
		$xadj2 = 0;
		$yadj = 0;
		$yadj2 = 0;
		$print = false;
		if ($Tw && $side=='T' && $this->issetBorder($bord, _BORDER_TOP)) {	// TOP
			$ly1 = $y;
			$ly2 = $y;
			$lx1 = $x;
			$lx2 = $x2;
			$this->SetLineWidth($Tw);
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'L')!==false) {
				if ($Tw > $Lw) $xadj = ($Tw - $Lw)/2;
				if ($Tw < $Lw) $xadj = ($Tw + $Lw)/2;
			}
			else { $xadj = $Tw/2 - $bsh/2; }
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'R')!==false) {
				if ($Tw > $Rw) $xadj2 = ($Tw - $Rw)/2;
				if ($Tw < $Rw) $xadj2 = ($Tw + $Rw)/2;
			}
			else { $xadj2 = $Tw/2 - $bsh/2; }
			if (!$bSeparate && $details['mbw']['T']['L']) {
				$xadj = ($Tw - $details['mbw']['T']['L'])/2 ;
			}
			if (!$bSeparate && $details['mbw']['T']['R']) {
				$xadj2 = ($Tw - $details['mbw']['T']['R'])/2;
			}
			$print = true;
		}
		if ($Lw && $side=='L' && $this->issetBorder($bord, _BORDER_LEFT)) {	// LEFT
			$ly1 = $y;
			$ly2 = $y2;
			$lx1 = $x;
			$lx2 = $x;
			$this->SetLineWidth($Lw);
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'T')!==false) {
				if ($Lw > $Tw) $yadj = ($Lw - $Tw)/2;
				if ($Lw < $Tw) $yadj = ($Lw + $Tw)/2;
			}
			else { $yadj = $Lw/2 - $bsv/2; }
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'B')!==false) {
				if ($Lw > $Bw) $yadj2 = ($Lw - $Bw)/2;
				if ($Lw < $Bw) $yadj2 = ($Lw + $Bw)/2;
			}
			else { $yadj2 = $Lw/2 - $bsv/2; }
			if (!$bSeparate && $details['mbw']['L']['T']) {
				$yadj = ($Lw - $details['mbw']['L']['T'])/2;
			}
			if (!$bSeparate && $details['mbw']['L']['B']) {
				$yadj2 = ($Lw - $details['mbw']['L']['B'])/2;
			}
			$print = true;
		}
		if ($Rw && $side=='R' && $this->issetBorder($bord, _BORDER_RIGHT)) {	// RIGHT
			$ly1 = $y;
			$ly2 = $y2;
			$lx1 = $x2;
			$lx2 = $x2;
			$this->SetLineWidth($Rw);
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'T')!==false) {
				if ($Rw < $Tw) $yadj = ($Rw + $Tw)/2;
				if ($Rw > $Tw) $yadj = ($Rw - $Tw)/2;
			}
			else { $yadj = $Rw/2 - $bsv/2; }

			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'B')!==false) {
				if ($Rw > $Bw) $yadj2 = ($Rw - $Bw)/2;
				if ($Rw < $Bw) $yadj2 = ($Rw + $Bw)/2;
			}
			else { $yadj2 = $Rw/2 - $bsv/2; }

			if (!$bSeparate && $details['mbw']['R']['T']) {
				$yadj = ($Rw - $details['mbw']['R']['T'])/2;
			}
			if (!$bSeparate && $details['mbw']['R']['B']) {
				$yadj2 = ($Rw - $details['mbw']['R']['B'])/2;
			}
			$print = true;
		}
		if ($Bw && $side=='B' && $this->issetBorder($bord, _BORDER_BOTTOM)) {	// BOTTOM
			$ly1 = $y2;
			$ly2 = $y2;
			$lx1 = $x;
			$lx2 = $x2;
			$this->SetLineWidth($Bw);
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'L')!==false) {
				if ($Bw > $Lw) $xadj = ($Bw - $Lw)/2;
				if ($Bw < $Lw) $xadj = ($Bw + $Lw)/2;
			}
			else { $xadj = $Bw/2 - $bsh/2; }
			// mPDF 3.0
			if ($cort == 'cell' || strpos($tablecorner,'R')!==false) {
				if ($Bw > $Rw) $xadj2 = ($Bw - $Rw)/2;
				if ($Bw < $Rw) $xadj2 = ($Bw + $Rw)/2;
			}
			else { $xadj2 = $Bw/2 - $bsh/2; }
			if (!$bSeparate && $details['mbw']['B']['L']) {
				$xadj = ($Bw - $details['mbw']['B']['L'])/2;
			}
			if (!$bSeparate && $details['mbw']['B']['R']) {
				$xadj2 = ($Bw - $details['mbw']['B']['R'])/2;
			}
			$print = true;
		}

		// Now draw line
		if ($print) {
		   if ($details[$side]['style'] == 'dashed') {
			$dashsize = 2;	// final dash will be this + 1*linewidth
			$dashsizek = 1.5;	// ratio of Dash/Blank
			$this->SetDash($dashsize,($dashsize/$dashsizek)+($this->LineWidth*2));
		   }
		   else if ($details[$side]['style'] == 'dotted') {
  			$this->_out("\n".'1 J'."\n".'1 j'."\n");
			$this->SetDash(0.001,($this->LineWidth*2));
		   }
		   if ($details[$side]['c']) { 
			$this->SetDrawColor($details[$side]['c']['R'],$details[$side]['c']['G'],$details[$side]['c']['B']);
		   }
		   else { $this->SetDrawColor(0); }
		   $this->Line($lx1 + $xadj, $ly1 + $yadj, $lx2 - $xadj2, $ly2 - $yadj2);

	   	  // Reset Corners
	   	  $this->SetDash(); 
  		  //BUTT style line cap
  		  $this->_out('2 J');
		}
	   }

	   if ($bSeparate && count($cellBorderOverlay)) {
		foreach($cellBorderOverlay AS $cbo) {
			$this->SetLineWidth($cbo['lw']);
			$this->SetDrawColor($cbo['col'][0],$cbo['col'][1],$cbo['col'][2]); 
			$this->Line($cbo['x'], $cbo['y'], $cbo['x2'], $cbo['y2']);
		}
	   }

	   $this->SetLineWidth($oldlinewidth);
	   $this->SetDrawColor(0);
	}
}





function setBorder(&$var, $flag, $set = true) {
	$flag = intval($flag);
	if ($set) { $set = true; }
	$var = intval($var);
	$var = $set ? ($var | $flag) : ($var & ~$flag);
}
function issetBorder($var, $flag) {
	$flag = intval($flag);
	$var = intval($var);
	return (($var & $flag) == $flag);
}

// mPDF 4.0
function _table2cellBorder(&$tableb, &$cbdb, &$cellb, $bval) {
	if ($tableb && $tableb['w'] > $cbdb['w']) {
		$cbdb = $tableb;
		$this->setBorder($cellb, $bval); 
	}
	else if ($tableb && $tableb['w'] == $cbdb['w'] 
		&& array_search($tableb['style'],$this->borderstyles) > array_search($cbdb['style'],$this->borderstyles)) {
		$cbdb = $tableb;
		$this->setBorder($cellb, $bval); 
	}
}

// FIX BORDERS ********************************************
function _fixTableBorders(&$table){

	$cells = &$table['cells'];
	$numcols = $table['nc'];
	$numrows = $table['nr'];

	if (!$table['borders_separate'] && $table['border_details']['L']['w']) {
		$table['max_cell_border_width']['L'] = $table['border_details']['L']['w']; 
	}	
	if (!$table['borders_separate'] && $table['border_details']['R']['w']) {
		$table['max_cell_border_width']['R'] = $table['border_details']['R']['w']; 
	}	
	if (!$table['borders_separate'] && $table['border_details']['T']['w']) {
		$table['max_cell_border_width']['T'] = $table['border_details']['T']['w']; 
	}	
	if (!$table['borders_separate'] && $table['border_details']['B']['w']) {
		$table['max_cell_border_width']['B'] = $table['border_details']['B']['w']; 
	}	

	for( $i = 0 ; $i < $numrows ; $i++ ) { //Rows
	  for( $j = 0 ; $j < $numcols ; $j++ ) { //Columns
		if (isset($cells[$i][$j]) && $cells[$i][$j] && !$cells[$i][$j]['border']) {
  			if (isset($table['border']) && $table['border'] && $this->table_border_attr_set) {
				$cells[$i][$j]['border'] = $table['border'];
				$cells[$i][$j]['border_details'] = $table['border_details'];
			}
		}
	   }
	}

	for( $i = 0 ; $i < $numrows ; $i++ ) { //Rows
	  for( $j = 0 ; $j < $numcols ; $j++ ) { //Columns
		if (isset($cells[$i][$j]) && $cells[$i][$j]) {
			$cell = &$cells[$i][$j];
			// mPDF 4.0
			$cbd = &$cells[$i][$j]['border_details'];
			if (isset($cell['colspan']) && $cell['colspan']>1) { $ccolsp = $cell['colspan']; }
			else { $ccolsp = 1; }
			if (isset($cell['rowspan']) && $cell['rowspan']>1) { $crowsp = $cell['rowspan']; }
			else { $crowsp = 1; }

			// mPDF 3.0
			$cbd['cellposdom'] = ((($i+1)/$numrows) / 10000 ) + ((($j+1)/$numcols) / 10 );
			// Inherit Cell border from Table border
			if ($this->table_border_css_set && !$table['borders_separate']) {
				if ($i == 0) {
				  $this->_table2cellBorder($table['border_details']['T'], $cbd['T'], $cell['border'], _BORDER_TOP);	// mPDF 4.0
				}
				if ($i == ($numrows-1) || ($i+$crowsp) == ($numrows) ) {
				  $this->_table2cellBorder($table['border_details']['B'], $cbd['B'], $cell['border'], _BORDER_BOTTOM);	// mPDF 4.0
				}
				if ($j == 0) {
				  $this->_table2cellBorder($table['border_details']['L'], $cbd['L'], $cell['border'], _BORDER_LEFT);	// mPDF 4.0
				}
				if ($j == ($numcols-1) || ($j+$ccolsp) == ($numcols) ) {
				  $this->_table2cellBorder($table['border_details']['R'], $cbd['R'], $cell['border'], _BORDER_RIGHT);	// mPDF 4.0
				}
			}


			unset($cell );
			unset($cbd );
		}
	  }
	}
	unset($cell );


}
// END FIX BORDERS ************************************************************************************

function _tableWrite(&$table){
	$level = $table['level'];
	$levelid = $table['levelid'];

	$cells = &$table['cells'];
	$numcols = $table['nc'];
	$numrows = $table['nr'];


	// TABLE TOP MARGIN
	if ($table['margin']['T']) {
	   if (!$this->table_rotate && $level==1) {
		$this->DivLn($table['margin']['T'],$this->blklvl,true,1); 	// collapsible
	   }
	   else {
		$this->y += ($table['margin']['T']);
	   }
	}

	// Advance down page by half width of top border
	if ($table['borders_separate']) { 
		$adv = $table['padding']['T'] + $table['border_details']['T']['w'] + $table['border_spacing_V']/2; 
	}
	else { 
		$adv = $table['max_cell_border_width']['T']/2; 
	}
	if (!$this->table_rotate && $level==1) { $this->DivLn($adv); }
	else { $this->y += $adv; }


	if ($level==1) {
		$this->x = $this->lMargin  + $this->blk[$this->blklvl]['outer_left_margin'] + $this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'];
		$x0 = $this->x; 
		$y0 = $this->y;
		$right = $x0 + $this->blk[$this->blklvl]['inner_width'];
		$outerfilled = $this->y;	// Keep track of how far down the outer DIV bgcolor is painted (NB rowspans)
		$this->outerfilled = $this->y;
	}
	else {
		$x0 = $this->x; 
		$y0 = $this->y;
		$right = $x0 + $table['w'];		// ????
		// $outerfilled = $this->y;	// Keep track of how far down the outer DIV bgcolor is painted (NB rowspans)
	}

	if ($this->table_rotate) {
		$temppgwidth = $this->tbrot_maxw;
		$this->PageBreakTrigger = $pagetrigger = $y0 + ($this->blk[$this->blklvl]['inner_width']);
	   if ($level==1) {
		$this->tbrot_y0 = $this->y - $adv - $table['margin']['T'] ;
		$this->tbrot_x0 = $this->x;
		$this->tbrot_w = $table['w'];
		if ($table['borders_separate']) { $this->tbrot_h = $table['margin']['T'] + $table['padding']['T'] + $table['border_details']['T']['w'] + $table['border_spacing_V']/2; }
		else { $this->tbrot_h = $table['margin']['T'] + $table['padding']['T'] + $table['max_cell_border_width']['T']; }
	   }
	}
	else {
		$this->PageBreakTrigger = $pagetrigger = ($this->h - $this->bMargin);
	   	if ($level==1) {
			$temppgwidth = $this->blk[$this->blklvl]['inner_width'];
	   		if (isset($table['a']) and ($table['w'] < $this->blk[$this->blklvl]['inner_width'])) {
				if ($table['a']=='C') { $x0 += ((($right-$x0) - $table['w'])/2); }
				else if ($table['a']=='R') { $x0 = $right - $table['w']; }
			}
	   	}
		else {
			$temppgwidth = $table['w'];
		}
	}

	if ($table['borders_separate']) { $indent = $table['margin']['L'] + $table['border_details']['L']['w'] + $table['padding']['L'] + $table['border_spacing_H']/2; }
	else { $indent = $table['margin']['L'] + $table['max_cell_border_width']['L']/2; }
	$x0 += $indent;

	$returny = 0;
	$tableheader = array();
	$tablefooter = array();	// mPDF 4.0
	$tablefooterrowheight = 0;
	$footery = 0;

	// mPD 3.0 Set the Page & Column where table starts
	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$tablestartpage = 'EVEN'; 
	}
	else if (($this->mirrorMargins) && (($this->page)%2==1)) {	// ODD
		$tablestartpage = 'ODD'; 
	}
	else { $tablestartpage = ''; }
	if ($this->ColActive) { $tablestartcolumn = $this->CurrCol; }
	else { $tablestartcolumn = ''; }

	// mPDF 4.0 Initialise variable
	$y = $h = 0;

	// mPDF 4.0 Table Footer
	for( $i = 0 ; $i < $numrows ; $i++ ) { //Rows

	  if (isset($table['is_tfoot'][$i]) && $table['is_tfoot'][$i] && $level==1) { 
		$tablefooterrowheight += $table['hr'][$i]; 
	  	for( $j = 0 ; $j < $numcols ; $j++ ) { //Columns
			if (isset($cells[$i][$j]) && $cells[$i][$j]) {
				$cell = &$cells[$i][$j];
				list($x,$w) = $this->_tableGetWidth($table, $i, $j);
				list($y,$h) = $this->_tableGetHeight($table, $i, $j);  
				$x += $x0;
				$y += $y0;

				//Get info of tfoot ==>> table footer
				$tablefooter[$i][$j]['x'] = $x;
				$tablefooter[$i][$j]['y'] = $y;
				$tablefooter[$i][$j]['h'] = $h;
				$tablefooter[$i][$j]['w'] = $w;
				$tablefooter[$i][$j]['text'] = $cell['text'];
				if (isset($cell['textbuffer'])) { $tablefooter[$i][$j]['textbuffer'] = $cell['textbuffer']; }
				else { $tablefooter[$i][$j]['textbuffer'] = ''; }
				$tablefooter[$i][$j]['a'] = $cell['a'];
				$tablefooter[$i][$j]['R'] = $cell['R'];
				$tablefooter[$i][$j]['va'] = $cell['va'];
				$tablefooter[$i][$j]['mih'] = $cell['mih'];
				//CELL FILL BGCOLOR
				$fill = isset($cell['bgcolor']) ? $cell['bgcolor']
  					: (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
  					: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));
				$tablefooter[$i][$j]['bgcolor'] = $fill;
				$tablefooter[$i][$j]['border'] = $cell['border'];
				$tablefooter[$i][$j]['border_details'] = $cell['border_details'];
				$tablefooter[$i][$j]['padding'] = $cell['padding'];
				$tablefooter[$i][$j]['rowspan'] = $cell['rowspan'];
				$tablefooter[$i][$j]['colspan'] = $cell['colspan'];
			}
		}
	  }
	}

	//Draw Table Contents and Borders
	for( $i = 0 ; $i < $numrows ; $i++ ) { //Rows

	  // Get Maximum row/cell height in row - including rowspan>1 + 1 overlapping
	  $maxrowheight = 0;
	  for( $j = 0 ; $j < $numcols ; $j++ ) { //Columns
		list($y,$h) = $this->_tableGetHeight($table, $i, $j);
		$maxrowheight = max($maxrowheight,$h);
	  }

	  $skippage = false;
	  $newpagestarted = false;
	  for( $j = 0 ; $j < $numcols ; $j++ ) { //Columns
		if (isset($cells[$i][$j]) && $cells[$i][$j]) {
			$cell = &$cells[$i][$j];

			list($x,$w) = $this->_tableGetWidth($table, $i, $j);
			list($y,$h) = $this->_tableGetHeight($table, $i, $j);
			$x += $x0;
			$y += $y0;
			$y -= $returny;
			if ($table['borders_separate']) { 
			  // mPDF 3.0
			  if (!empty($tablefooter) || $i == ($numrows-1) || (isset($cell['rowspan']) && ($i+$cell['rowspan']) == $numrows)  || (!isset($cell['rowspan']) && ($i+1) == $numrows) ) {
				$extra = $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; 
				//$extra = $table['margin']['B'] + $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; 
			  }
			  else {
				$extra = $table['border_spacing_V']/2; 
			  }
			}
	  		else { $extra = $table['max_cell_border_width']['B']/2; }

			// mPDF 4.0
			if (((($y + $maxrowheight + $extra ) > $pagetrigger) || (!$this->ColActive && !empty($tablefooter) && ($y + $maxrowheight + $tablefooterrowheight + $extra) > $pagetrigger) && ($i < ($numrows - $this->tableheadernrows))) && ($y0 >0 || $x0 > 0) && !$this->InFooter) {
				if (!$skippage) {
					// mPDF 4.0
		      		if (!$this->ColActive && !empty($tablefooter) && $i > 0 ) { 
						$this->y = $y;
						$ya = $this->y;
						$this->TableHeaderFooter($tablefooter,$tablestartpage,$tablestartcolumn,'F');
						if ($this->table_rotate) {
							$this->tbrot_h += $this->y - $ya ;
						}
					}
					$y -= $y0;
					$returny += $y;

					$oldcolumn = $this->CurrCol;
					if ($this->AcceptPageBreak()) {
	  					$newpagestarted = true;
						$this->y = $y + $y0;

						// mPDF 3.0 - Move down to account for border-spacing or 
						// extra half border width in case page breaks in middle
						if($i>0 && !$this->table_rotate && $level==1 && !$this->ColActive) {
							if ($table['borders_separate']) { $adv = $table['border_spacing_V']/2; }
							else { 
								$maxbwbottom = 0;
								for( $ctj = 0 ; $ctj < $numcols ; $ctj++ ) {
									if (isset($cells[$i][$ctj]) && $cells[$i][$ctj]) {
										$maxbwbottom = max($maxbwbottom , $cells[$i][$ctj]['border_details']['T']['w']); 
									}
								}
								$adv = $maxbwbottom /2;
							}
							$this->y += $adv;
						}

						// mPDF 3.0 Rotated table split over pages - needs this->y for borders/backgrounds
						if($i>0 && $this->table_rotate && $level==1) {
							$this->y = $y0 + $this->tbrot_w;
						}

						$this->AddPage($this->CurOrientation);
						// Added to correct for OddEven Margins
						$x=$x +$this->MarginCorrection;
						$x0=$x0 +$this->MarginCorrection;


						// mPDF 3.0 - Move down to account for half of top border-spacing or 
						// extra half border width in case page was broken in middle
						if($i>0 && !$this->table_rotate && $level==1 && !$this->usetableheader) {
							if ($table['borders_separate']) { $adv = $table['border_spacing_V']/2; }
							else { 
								$maxbwtop = 0;
								for( $ctj = 0 ; $ctj < $numcols ; $ctj++ ) {
									if (isset($cells[$i][$ctj]) && $cells[$i][$ctj]) {
										$maxbwtop = max($maxbwtop, $cells[$i][$ctj]['border_details']['T']['w']); 
									}
								}
								$adv = $maxbwtop /2;
							}
							$this->y += $adv;
						}


						if ($this->table_rotate) {
							// mPDF 3.0 Rotated table
							//$this->tbrot_x0 = $x0;
							$this->tbrot_x0 = $this->lMargin  + $this->blk[$this->blklvl]['outer_left_margin'] + $this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'];
							if ($table['borders_separate']) { $this->tbrot_h = $table['margin']['T'] + $table['padding']['T'] + $table['border_details']['T']['w'] + $table['border_spacing_V']/2; }
							else { $this->tbrot_h = $table['margin']['T'] + $table['max_cell_border_width']['T'] ; }
							$this->tbrot_y0 = $this->y;
							$pagetrigger = $y0 + ($this->blk[$this->blklvl]['inner_width']);
						}
						else {
							$pagetrigger = $this->PageBreakTrigger;
						}

						if ($this->kwt_saved && $level==1) {
							$this->kwt_moved = true;
						}

						// Disable Table header repeat if Keep Block together
             				if ($this->usetableheader && !$this->keep_block_together && !empty($tableheader)) { 
							$ya = $this->y;
							$this->TableHeaderFooter($tableheader,$tablestartpage,$tablestartcolumn,'H');  // mPDF 4.0
							if ($this->table_rotate) {
								$this->tbrot_h = $this->y - $ya ;
							}
						}

						// mPDF 3.0
						else if ($i==0 && !$this->keep_block_together && !$this->table_rotate && $level==1 && !$this->ColActive) {
							// Advance down page
							if ($table['borders_separate']) { $adv = $table['border_spacing_V']/2 + $table['border_details']['T']['w'] + $table['padding']['T'];  }
							else { $adv = $table['max_cell_border_width']['T'] /2 ; }
							if ($adv) { 
							   if ($this->table_rotate) {
								$this->y += ($adv);
							   }
							   else {
								$this->DivLn($adv,$this->blklvl,true); 
							   }
							}
						}

						$outerfilled = 0;
						$y0 = $this->y; 
						$y = $y0;
					}
				}
				$skippage = true;
			}

			$this->x = $x; 
			$this->y = $y;

			if ($this->kwt_saved && $level==1) {
				$this->printkwtbuffer();
				$x0 = $x = $this->x; 
				$y0 = $y = $this->y;
				$this->kwt_moved = false;
				$this->kwt_saved = false;
			}


			// Set the Page & Column where table starts
			if ($i==0 && $j==0 && $level==1) {
				if (($this->mirrorMargins) && (($this->page)%2==0)) {				// EVEN
					$tablestartpage = 'EVEN'; 
				}
				else if (($this->mirrorMargins) && (($this->page)%2==1)) {				// ODD
					$tablestartpage = 'ODD'; 
				}
				else { $tablestartpage = ''; }
			}


			//ALIGN
			$align = $cell['a'];




			//TABLE BACKGROUND FILL BGCOLOR - for cellSpacing
			if ($table['borders_separate']) { 
			   $fill = isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
  					: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0);
			   if ($fill) {
  				$color = $this->ConvertColor($fill);
  				if ($color) $this->SetFillColor($color['R'],$color['G'],$color['B']);
				$xadj = ($table['border_spacing_H']/2);
				$yadj = ($table['border_spacing_V']/2);
				$wadj = $table['border_spacing_H'];
				$hadj = $table['border_spacing_V'];
 			   	if ($i == 0) {		// Top
					$yadj += $table['padding']['T'];
					$hadj += $table['padding']['T'];
			   	}
			   	if ($j == 0) {		// Left
					$xadj += $table['padding']['L'];
					$wadj += $table['padding']['L'];
			   	}
			   	if ($i == ($numrows-1) || (isset($cell['rowspan']) && ($i+$cell['rowspan']) == $numrows)  || (!isset($cell['rowspan']) && ($i+1) == $numrows)) {	// Bottom
					$hadj += $table['padding']['B'];
			   	}
			   	if ($j == ($numcols-1) || (isset($cell['colspan']) && ($j+$cell['colspan']) == $numcols)  || (!isset($cell['colspan']) && ($j+1) == $numcols)) {	// Right
					$wadj += $table['padding']['R'];
			   	}
				$this->Rect($x - $xadj, $y - $yadj, $w + $wadj, $h + $hadj, 'F');
			   }
			}

			// TABLE BORDER - if separate
 			if ($table['borders_separate'] && $table['border']) { 
			   $halfspaceL = $table['padding']['L'] + ($table['border_spacing_H']/2);
			   $halfspaceR = $table['padding']['R'] + ($table['border_spacing_H']/2);
			   $halfspaceT = $table['padding']['T'] + ($table['border_spacing_V']/2);
			   $halfspaceB = $table['padding']['B'] + ($table['border_spacing_V']/2);
			   $tbx = $x;
			   $tby = $y;
			   $tbw = $w;
			   $tbh = $h;
			   $tab_bord = 0;
			   // mPDF 3.0
			   $corner = '';
 			   if ($i == 0) {		// Top
				$tby -= $halfspaceT + ($table['border_details']['T']['w']/2);
				$tbh += $halfspaceT + ($table['border_details']['T']['w']/2);
				$this->setBorder($tab_bord , _BORDER_TOP); 
				$corner .= 'T';
			   }
			   if ($i == ($numrows-1) || (isset($cell['rowspan']) && ($i+$cell['rowspan']) == $numrows)  || (!isset($cell['rowspan']) && ($i+1) == $numrows) ) {	// Bottom
				$tbh += $halfspaceB + ($table['border_details']['B']['w']/2);
				$this->setBorder($tab_bord , _BORDER_BOTTOM); 
				$corner .= 'B';
			   }
			   if ($j == 0) {		// Left
				$tbx -= $halfspaceL + ($table['border_details']['L']['w']/2);
				$tbw += $halfspaceL + ($table['border_details']['L']['w']/2);
				$this->setBorder($tab_bord , _BORDER_LEFT); 
				$corner .= 'L';
			   }
			   if ($j == ($numcols-1) || (isset($cell['colspan']) && ($j+$cell['colspan']) == $numcols)  || (!isset($cell['colspan']) && ($j+1) == $numcols)) {	// Right
				$tbw += $halfspaceR + ($table['border_details']['R']['w']/2);
				$this->setBorder($tab_bord , _BORDER_RIGHT); 
				$corner .= 'R';
			   }
			   $this->_tableRect($tbx, $tby, $tbw, $tbh, $tab_bord , $table['border_details'], false, $table['borders_separate'], 'table', $corner, $table['border_spacing_V'], $table['border_spacing_H'] );
			}

			if ($table['empty_cells']!='hide' || !empty($cell['textbuffer']) || (isset($cell['nestedcontent']) && $cell['nestedcontent']) || !$table['borders_separate']  ) { $paintcell = true; }
			else { $paintcell = false; } 

			//Set Borders
			$bord = 0; 
			$bord_det = array();
  			if ($cell['border']) {
				$bord = $cell['border'];
				$bord_det = $cell['border_details'];
			}

			//CELL FILL BGCOLOR
			$fill = isset($cell['bgcolor']) ? $cell['bgcolor']
  					: (isset($table['bgcolor'][$i]) ? $table['bgcolor'][$i]
  					: (isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0));


			if ($fill && $paintcell) {
  				$color = $this->ConvertColor($fill);
  				if ($color) $this->SetFillColor($color['R'],$color['G'],$color['B']);
 				if ($table['borders_separate']) { 
 					$this->Rect($x+ ($table['border_spacing_H']/2), $y+ ($table['border_spacing_V']/2), $w- $table['border_spacing_H'], $h- $table['border_spacing_V'], 'F');
				}
 				else { 
	 				$this->Rect($x, $y, $w, $h, 'F');
				}
			}



			 if (isset($cell['colspan']) && $cell['colspan']>1) { $ccolsp = $cell['colspan']; }
			 else { $ccolsp = 1; }
			 if (isset($cell['rowspan']) && $cell['rowspan']>1) { $crowsp = $cell['rowspan']; }
			 else { $crowsp = 1; }


			// but still need to do this for repeated headers...
			if (!$table['borders_separate'] && $this->tabletheadjustfinished) { // $this->tabletheadjustfinished from tableheader
			  if (isset($table['topntail']) && $table['topntail']) {
					$bord_det['T'] = $this->border_details($table['topntail']);
					$bord_det['T']['w'] /= $this->shrin_k;
					$this->setBorder($bord, _BORDER_TOP); 
			  }
			  if (isset($table['thead-underline']) && $table['thead-underline']) {
					$bord_det['T'] = $this->border_details($table['thead-underline']);
					$bord_det['T']['w'] /= $this->shrin_k;
					$this->setBorder($bord, _BORDER_TOP); 
			  }
			}



			//Get info of first row ==>> table header
			//Use > 1 row if THEAD
			if ($this->usetableheader && ($i == 0  || (isset($table['is_thead'][$i]) && $table['is_thead'][$i])) && $level==1) {
				$tableheader[$i][$j]['x'] = $x;
				$tableheader[$i][$j]['y'] = $y;
				$tableheader[$i][$j]['h'] = $h;
				$tableheader[$i][$j]['w'] = $w;
				$tableheader[$i][$j]['text'] = $cell['text'];
				if (isset($cell['textbuffer'])) { $tableheader[$i][$j]['textbuffer'] = $cell['textbuffer']; }
				else { $tableheader[$i][$j]['textbuffer'] = ''; }
				$tableheader[$i][$j]['a'] = $cell['a'];
				$tableheader[$i][$j]['R'] = $cell['R'];

				$tableheader[$i][$j]['va'] = $cell['va'];
				$tableheader[$i][$j]['mih'] = $cell['mih'];
				$tableheader[$i][$j]['bgcolor'] = $fill;
				// mPDF 3.1
				// mPDF 4.0
				$tableheader[$i][$j]['rowspan'] = $cell['rowspan'];
				$tableheader[$i][$j]['colspan'] = $cell['colspan'];

				$tableheader[$i][$j]['border'] = $cell['border'];	// mPDF 3.0
				$tableheader[$i][$j]['border_details'] = $cell['border_details'];	// mPDF 3.0
				$tableheader[$i][$j]['padding'] = $cell['padding'];
				// mPDF 3.0 moved as needed earlier
				//$this->tableheadernrows = max($this->tableheadernrows, ($i+1));
			}

			// CELL BORDER
			if ($bord || $bord_det) { 
 				if ($table['borders_separate'] && $paintcell) {
 					$this->_tableRect($x + ($table['border_spacing_H']/2)+($bord_det['L']['w'] /2), $y+ ($table['border_spacing_V']/2)+($bord_det['T']['w'] /2), $w-$table['border_spacing_H']-($bord_det['L']['w'] /2)-($bord_det['R']['w'] /2), $h- $table['border_spacing_V']-($bord_det['T']['w'] /2)-($bord_det['B']['w']/2), $bord, $bord_det, false, $table['borders_separate']);
				}
 				else if (!$table['borders_separate']) { 
					$this->_tableRect($x, $y, $w, $h, $bord, $bord_det, true, $table['borders_separate']); 	// true causes buffer
				}

			}

			//VERTICAL ALIGN
			if ($cell['R'] && INTVAL($cell['R']) > 0 && INTVAL($cell['R']) < 90 && isset($cell['va']) && $cell['va']!='B') { $cell['va']='B';}
			if (!isset($cell['va']) || $cell['va']=='M') $this->y += ($h-$cell['mih'])/2;
			elseif (isset($cell['va']) && $cell['va']=='B') $this->y += $h-$cell['mih'];

			// NESTED CONTENT 

			// TEXT (and nested tables)
			$this->divalign=$align;

			$this->divwidth=$w;
			if (!empty($cell['textbuffer'])) {
				$opy = $this->y;
				if ($cell['R']) {
					$cellPtSize = $cell['textbuffer'][0][11] / $this->shrin_k;
					if (!$cellPtSize) { $cellPtSize = $this->default_font_size; }
					$cellFontHeight = ($cellPtSize/$this->k);
					$opx = $this->x;
					$angle = INTVAL($cell['R']);
					// Only allow 45 to 89 degrees (when bottom-aligned) or exactly 90 or -90
					if ($angle > 90) { $angle = 90; }
					else if ($angle > 0 && $angle <45) { $angle = 45; }
					else if ($angle < 0) { $angle = -90; }
					$offset = ((sin(deg2rad($angle))) * 0.37 * $cellFontHeight);
					if (isset($cell['a']) && $cell['a']=='R') { 
						$this->x += ($w) + ($offset) - ($cellFontHeight/3) - ($cell['padding']['R'] + ($table['border_spacing_H']/2)); 
					}
					else if (!isset($cell['a']) || $cell['a']=='C') { 
						$this->x += ($w/2) + ($offset); 
					}
					else { 
						$this->x += ($offset) + ($cellFontHeight/3)+($cell['padding']['L'] +($table['border_spacing_H']/2)); 
					}
					$str = ltrim(implode(' ',$cell['text']));
					$str = $this->mb_rtrim($str,$this->mb_enc);
					if (!isset($cell['va']) || $cell['va']=='M') { 
						$this->y -= ($h-$cell['mih'])/2; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += (($h-$cell['mih'])/2) + $cell['padding']['T'] + ($cell['mih']-($cell['padding']['T'] + $cell['padding']['B'])); }
						else if ($angle < 0) { $this->y += (($h-$cell['mih'])/2)+ ($cell['padding']['T'] + ($table['border_spacing_V']/2)); }
					}
					elseif (isset($cell['va']) && $cell['va']=='B') { 
						$this->y -= $h-$cell['mih']; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += $h-($cell['padding']['B'] + ($table['border_spacing_V']/2)); }
						else if ($angle < 0) { $this->y += $h-$cell['mih'] + ($cell['padding']['T'] + ($table['border_spacing_V']/2)); }
					}
					elseif (isset($cell['va']) && $cell['va']=='T') { 
						if ($angle > 0) { $this->y += $cell['mih']-($cell['padding']['B'] + ($table['border_spacing_V']/2)); }
						else if ($angle < 0) { $this->y += ($cell['padding']['T'] + ($table['border_spacing_V']/2)); }
					}
					$this->Rotate($angle,$this->x,$this->y);
					$s_fs = $this->FontSizePt;
					$s_f = $this->FontFamily;	// mPDF 3.0
					$s_st = $this->FontStyle;	// mPDF 3.0
					$this->SetFont($cell['textbuffer'][0][4],$cell['textbuffer'][0][2],$cellPtSize,true,true);
					$this->Text($this->x,$this->y,$str);
					$this->Rotate(0);
					$this->SetFont($s_f,$s_st,$s_fs,true,true);
					$this->x = $opx;
				}
				else {

					if ($table['borders_separate']) {	// NB twice border width
						$xadj = $cell['border_details']['L']['w'] + $cell['padding']['L'] +($table['border_spacing_H']/2);
						$wadj = $cell['border_details']['L']['w'] + $cell['border_details']['R']['w'] + $cell['padding']['L'] +$cell['padding']['R'] + $table['border_spacing_H'];
						$yadj = $cell['border_details']['T']['w'] + $cell['padding']['T'] + ($table['border_spacing_H']/2);
					}
					else {
						$xadj = $cell['border_details']['L']['w']/2 + $cell['padding']['L'];
						$wadj = ($cell['border_details']['L']['w'] + $cell['border_details']['R']['w'])/2 + $cell['padding']['L'] + $cell['padding']['R'];
						$yadj = $cell['border_details']['T']['w']/2 + $cell['padding']['T'];
					}

					$this->divwidth=$w-$wadj;
					if ($this->divwidth == 0) { $this->divwidth = 0.0001; }
					$this->x += $xadj;
					$this->y += $yadj;
					$this->printbuffer($cell['textbuffer'],'',true);
				}
				$this->y = $opy;
			}
			unset($cell );
			//Reset values
			$this->Reset();
		}//end of (if isset(cells)...)
	  }// end of columns

	  $newpagestarted = false;
	  $this->tabletheadjustfinished = false;




	  if ($i == $numrows-1) { $this->y = $y + $h; } //last row jump (update this->y position)
	  if ($this->table_rotate && $level==1) {
		$this->tbrot_h += $h;
	  }

	}// end of rows


	if (count($this->cellBorderBuffer)) { $this->printcellbuffer(); }


	// Advance down page by half width of bottom border
 	if ($table['borders_separate']) { $this->y += $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; }
	else { $this->y += $table['max_cell_border_width']['B']/2; }

	if ($table['borders_separate'] && $level==1) { $this->tbrot_h += $table['margin']['B'] + $table['padding']['B'] + $table['border_details']['B']['w'] + $table['border_spacing_V']/2; }
	else if ($level==1) { $this->tbrot_h += $table['margin']['B'] + $table['max_cell_border_width']['B']/2; }


	// TABLE BOTTOM MARGIN
	if ($table['margin']['B']) {
	  if (!$this->table_rotate && $level==1) {
		$this->DivLn($table['margin']['B'],$this->blklvl,true); 	// collapsible
	  }
	  else {
		$this->y += ($table['margin']['B']);
	  }
	}


}//END OF FUNCTION _tableWrite()


/////////////////////////END OF TABLE CODE//////////////////////////////////


function _putextgstates() {
	for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            foreach ($this->extgstates[$i]['parms'] as $k=>$v)
                $this->_out('/'.$k.' '.$v);
            $this->_out('>>');
            $this->_out('endobj');
	}
}










function _putresources() {
	$this->_putextgstates();
	$this->_putfonts();
	$this->_putimages();
	$this->_putformobjects();	// *IMAGES-WMF*	// *IMAGES-CORE*
	// mPDF 3.0 - Tiling Patterns
	//Resource dictionary
	$this->offsets[2]=strlen($this->buffer);
	$this->_out('2 0 obj');
	$this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	$this->_out('/Font <<');
	// mPDF 4.0
	foreach($this->fonts as $font) {
		// mPDF 4.0
		if (!$font['used'] && $font['type']=='TrueTypeUnicode') { continue; }
		if ($font['type']=='Type1subset') {
			foreach($font['n'] AS $k => $fid) {
				$this->_out('/F'.$font['subsetfontids'][$k].' '.$font['n'][$k].' 0 R');
			}
		}
		else { 
			$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
		}
	}
	$this->_out('>>');
	if (count($this->extgstates)) {
		$this->_out('/ExtGState <<');
		foreach($this->extgstates as $k=>$extgstate)
			$this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
		$this->_out('>>');
	}


	if(count($this->images) or count($this->formobjects))	{
		$this->_out('/XObject <<');
		foreach($this->images as $image)
			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
            foreach($this->formobjects as $formobject)
                $this->_out('/FO'.$formobject['i'].' '.$formobject['n'].' 0 R');
		$this->_out('>>');
	}


	$this->_out('>>');
	$this->_out('endobj');	// end resource dictionary


}


	function _puttrailer()
	{
		$this->_out('/Size '.($this->n+1));
		$this->_out('/Root '.$this->n.' 0 R');
		$this->_out('/Info '.($this->n-1).' 0 R');
	}


//=========================================



//======================================================


// DEPRACATED but included for backwards compatability
function startPageNums() {
}

//======================================================



//======================================================


function AcceptPageBreak() {
	if (count($this->cellBorderBuffer)) { $this->printcellbuffer(); }	// *TABLES*
	else if ($this->table_rotate) {
		if (count($this->tablebuffer)) { $this->printtablebuffer(); }
		return true;
	}
        	$this->ChangeColumn=0;
		return $this->autoPageBreak;	// mPDF 3.0
	return $this->autoPageBreak;	// mPDF 3.0
}


//----------- COLUMNS ---------------------


//==================================================================
function printcellbuffer() {
	if (count($this->cellBorderBuffer )) {
		usort( $this->cellBorderBuffer , array($this, "_cmpdom")); 
		foreach($this->cellBorderBuffer AS $cbb) {
			$this->_tableRect($cbb['x'],$cbb['y'],$cbb['w'],$cbb['h'],$cbb['bord'],$cbb['details'], false, $cbb['borders_separate']);
		}
		$this->cellBorderBuffer = array();
	}
}
//==================================================================
function printtablebuffer() {

	if (!$this->table_rotate) { 
		foreach($this->tablebuffer AS $s) { $this->pages[$this->page] .= $s['s']."\n"; }
		foreach($this->tbrot_Links AS $p => $l) {
		   foreach($l AS $v) {
			$this->PageLinks[$p][] = $v;
		   }
		}
		$this->tbrot_Links = array();




		return; 
	}

	// else if rotated
	$lm = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_left'];
	$pw = $this->blk[$this->blklvl]['inner_width'];
	//Start Transformation
	$this->pages[$this->page] .= $this->StartTransform(true)."\n";

	if ($this->table_rotate > 1) {	// clockwise
	   if ($this->tbrot_align == 'L') {
		$xadj = $this->tbrot_h ;	// align L (as is)
	   }
	   else if ($this->tbrot_align == 'R') {
		$xadj = $lm-$this->tbrot_x0+($pw) ;	// align R
	   }
	   else {
		$xadj = $lm-$this->tbrot_x0+(($pw + $this->tbrot_h)/2) ;	// align C
	   }
	   $yadj = 0;
	}
	else {	// anti-clockwise
	   if ($this->tbrot_align == 'L') {
		$xadj = 0 ;	// align L (as is)
	   }
	   else if ($this->tbrot_align == 'R') {
		$xadj = $lm-$this->tbrot_x0+($pw - $this->tbrot_h) ;	// align R
	   }
	   else {
		$xadj = $lm-$this->tbrot_x0+(($pw - $this->tbrot_h)/2) ;	// align C
	   }
	   $yadj = $this->tbrot_w;
	}


	$this->pages[$this->page] .= $this->transformTranslate($xadj, $yadj , true)."\n";
	$this->pages[$this->page] .= $this->transformRotate($this->table_rotate, $this->tbrot_x0 , $this->tbrot_y0 , true)."\n";

	// Now output the adjusted values
	foreach($this->tablebuffer AS $s) { $this->pages[$this->page] .= $s['s']."\n"; }


	foreach($this->tbrot_Links AS $p => $l) {
	    foreach($l AS $v) {
		$w = $v[2]/$this->k;
		$h = $v[3]/$this->k;
		$ax = ($v[0]/$this->k) - $this->tbrot_x0;
		$ay = (($this->hPt-$v[1])/$this->k) - $this->tbrot_y0;
		if ($this->table_rotate > 1) {	// clockwise
			$bx = $this->tbrot_x0+$xadj-$ay-$h;
			$by = $this->tbrot_y0+$yadj+$ax;
		}
		else {
			$bx = $this->tbrot_x0+$xadj+$ay;
			$by = $this->tbrot_y0+$yadj-$ax-$w;
		}
		$v[0] = $bx*$this->k;
		$v[1] = ($this->h-$by)*$this->k;
		$v[2] = $h*$this->k;	// swap width and height
		$v[3] = $w*$this->k;
		$this->PageLinks[$p][] = $v;
	    }
	}
	$this->tbrot_Links = array();






	// mPDF 3.0
	$this->tbrot_Reference = array();
	$this->tbrot_BMoutlines = array();
	$this->tbrot_toc = array();

	//Stop Transformation
	$this->pages[$this->page] .= $this->StopTransform(true)."\n";


	$this->y = $this->tbrot_y0 + $this->tbrot_w;
	$this->x = $this->lMargin;

	$this->tablebuffer = array();
}

//==================================================================
// Keep-with-table This buffers contents of h1-6 to keep on page with table
function printkwtbuffer() {
	if (!$this->kwt_moved) { 
		foreach($this->kwt_buffer AS $s) { $this->pages[$this->page] .= $s['s']."\n"; }
		foreach($this->kwt_Links AS $p => $l) {
		   foreach($l AS $v) {
			$this->PageLinks[$p][] = $v;
		   }
		}
		$this->kwt_Links = array();




		return; 
	}

	//Start Transformation
	$this->pages[$this->page] .= $this->StartTransform(true)."\n";
	$xadj = $this->lMargin - $this->kwt_x0 ;
	//$yadj = $this->y - $this->kwt_y0 ;
	$yadj = $this->tMargin - $this->kwt_y0 ;

	$this->pages[$this->page] .= $this->transformTranslate($xadj, $yadj , true)."\n";

	// Now output the adjusted values
	foreach($this->kwt_buffer AS $s) { $this->pages[$this->page] .= $s['s']."\n"; }

	// Adjust hyperLinks
	foreach($this->kwt_Links AS $p => $l) {
	    foreach($l AS $v) {
		$bx = $this->kwt_x0+$xadj;
		$by = $this->kwt_y0+$yadj;
		$v[0] = $bx*$this->k;
		$v[1] = ($this->h-$by)*$this->k;
		$this->PageLinks[$p][] = $v;
	    }
	}





	$this->kwt_Links = array();
	$this->kwt_Annots = array();
	// mPDF 3.0
	$this->kwt_Reference = array();
	$this->kwt_BMoutlines = array();
	$this->kwt_toc = array();
	//Stop Transformation
	$this->pages[$this->page] .= $this->StopTransform(true)."\n";

	$this->kwt_buffer = array();

	$this->y += $this->kwt_height;
}



//==================================================================
// mPDF 4.0
function printfloatbuffer() {
	if (count($this->floatbuffer)) {
		$this->objectbuffer = $this->floatbuffer;
		$this->printobjectbuffer(false);
		$this->objectbuffer = array();
		$this->floatbuffer = array();
		$this->floatmargins = array();
	}
}
//==================================================================

function printdivbuffer() {
	$k = $this->k;
	$p1 = $this->blk[$this->blklvl]['startpage'];
	$p2 = $this->page;
	$bottom[$p1] = $this->ktBlock[$p1]['bottom_margin'];
	$bottom[$p2] = $this->y;	// $this->ktBlock[$p2]['bottom_margin'];
	$top[$p1] = $this->blk[$this->blklvl]['y00'];
	$top2 = $this->h;
	foreach($this->divbuffer AS $key=>$s) { 
		if ($s['page'] == $p2) {
			$top2 = MIN($s['y'], $top2);
		}
	}
	$top[$p2] = $top2;
	$height[$p1] = ($bottom[$p1] - $top[$p1]);
	$height[$p2] = ($bottom[$p2] - $top[$p2]);
	$xadj[$p1] = $this->MarginCorrection;
	$yadj[$p1] = -($top[$p1] - $top[$p2]);
	$xadj[$p2] = 0;
	$yadj[$p2] = $height[$p1];

	if ($this->ColActive || !$this->keep_block_together || $this->blk[$this->blklvl]['startpage'] == $this->page || ($this->page - $this->blk[$this->blklvl]['startpage']) > 1 || ($height[$p1]+$height[$p2]) > $this->h) { 
		foreach($this->divbuffer AS $s) { $this->pages[$s['page']] .= $s['s']."\n"; }
		foreach($this->ktLinks AS $p => $l) {
		   foreach($l AS $v) {
			$this->PageLinks[$p][] = $v;
		   }
		}



		$this->divbuffer = array();
		$this->ktLinks = array();
		$this->ktAnnots = array();
		$this->ktBlock = array();
		$this->ktReference = array();
		$this->ktBMoutlines = array();
		$this->_kttoc = array();
		$this->keep_block_together = 0;
		return; 
	}
	else {
	   foreach($this->divbuffer AS $key=>$s) { 
		// callback function in htmltoolkit
		$t = $s['s'];
		$p = $s['page'];
		$t = preg_replace('/BT (\d+\.\d\d+) (\d+\.\d\d+) Td/e',"\$this->blockAdjust('Td',$k,$xadj[$p],$yadj[$p],'\\1','\\2')",$t);
		$t = preg_replace('/(\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) ([\-]{0,1}\d+\.\d\d+) re/e',"\$this->blockAdjust('re',$k,$xadj[$p],$yadj[$p],'\\1','\\2','\\3','\\4')",$t);
		$t = preg_replace('/(\d+\.\d\d+) (\d+\.\d\d+) l/e',"\$this->blockAdjust('l',$k,$xadj[$p],$yadj[$p],'\\1','\\2')",$t);
		$t = preg_replace('/q (\d+\.\d\d+) 0 0 (\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) cm \/I/e',"\$this->blockAdjust('img',$k,$xadj[$p],$yadj[$p],'\\1','\\2','\\3','\\4')",$t);
		$t = preg_replace('/(\d+\.\d\d+) (\d+\.\d\d+) m/e',"\$this->blockAdjust('draw',$k,$xadj[$p],$yadj[$p],'\\1','\\2')",$t);
		$t = preg_replace('/(\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) (\d+\.\d\d+) c/e',"\$this->blockAdjust('bezier',$k,$xadj[$p],$yadj[$p],'\\1','\\2','\\3','\\4','\\5','\\6')",$t);

		$this->pages[$this->page] .= $t."\n"; 
	   }
	   // Adjust hyperLinks
	   foreach($this->ktLinks AS $p => $l) {
	    foreach($l AS $v) {
		$v[0] += ($xadj[$p]*$k);
		$v[1] -= ($yadj[$p]*$k);
		$this->PageLinks[$p2][] = $v;
	    }
	   }




	   $this->y = $top[$p2] + $height[$p1] + $height[$p2];
	   $this->x = $this->lMargin;

	   $this->divbuffer = array();
	   $this->ktLinks = array();
	   $this->ktAnnots = array();
	   $this->ktBlock = array();
	   $this->ktReference = array();
	   $this->ktBMoutlines = array();
	   $this->_kttoc = array();
	   $this->keep_block_together = 0;
	}
}


//==================================================================
// Added ELLIPSES and CIRCLES
function Circle($x,$y,$r,$style='S') {
	$this->Ellipse($x,$y,$r,$r,$style);
}

function Ellipse($x,$y,$rx,$ry,$style='S') {
	if($style=='F') { $op='f'; }
	elseif($style=='FD' or $style=='DF') { $op='B'; }
	else { $op='S'; }
	$lx=4/3*(M_SQRT2-1)*$rx;
	$ly=4/3*(M_SQRT2-1)*$ry;
	$k=$this->k;
	$h=$this->h;
	$this->_out(sprintf('%.3f %.3f m %.3f %.3f %.3f %.3f %.3f %.3f c', ($x+$rx)*$k,($h-$y)*$k, ($x+$rx)*$k,($h-($y-$ly))*$k, ($x+$lx)*$k,($h-($y-$ry))*$k, $x*$k,($h-($y-$ry))*$k));
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f c', ($x-$lx)*$k,($h-($y-$ry))*$k, 	($x-$rx)*$k,($h-($y-$ly))*$k, 	($x-$rx)*$k,($h-$y)*$k)); 
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f c', ($x-$rx)*$k,($h-($y+$ly))*$k, ($x-$lx)*$k,($h-($y+$ry))*$k, $x*$k,($h-($y+$ry))*$k)); 
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f c %s', ($x+$lx)*$k,($h-($y+$ry))*$k, ($x+$rx)*$k,($h-($y+$ly))*$k, ($x+$rx)*$k,($h-$y)*$k, $op));
}






// ====================================================
// ====================================================

// 
// ****************************
// ****************************


// mPDF 4.0
function SetSubstitutions($dummy=array()) {	// parameter only for backward compatability
	$subsarray = array();
	@include(_MPDF_PATH.'includes/subs_'.strtolower($this->codepage).'.php');
	$this->substitute = array();
	foreach($subsarray AS $key => $val) {
		$this->substitute[code2utf($key)] = $val;
	}
}


function SubstituteChars($html) {
	// only substitute characters between tags
	if ($this->useSubstitutions && count($this->substitute)) {	
		$a=preg_split('/(<.*?>)/ms',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		$html = '';
		foreach($a as $i => $e) {
			if($i%2==0) {
			   $e = strtr($e, $this->substitute);
			}
			$html .= $e;
		}
	}
	return $html;
}


function setHiEntitySubstitutions() {
	$entarr = array (
  'nbsp' => '160',  'iexcl' => '161',  'cent' => '162',  'pound' => '163',  'curren' => '164',  'yen' => '165',  'brvbar' => '166',  'sect' => '167',
  'uml' => '168',  'copy' => '169',  'ordf' => '170',  'laquo' => '171',  'not' => '172',  'shy' => '173',  'reg' => '174',  'macr' => '175',
  'deg' => '176',  'plusmn' => '177',  'sup2' => '178',  'sup3' => '179',  'acute' => '180',  'micro' => '181',  'para' => '182',  'middot' => '183',
  'cedil' => '184',  'sup1' => '185',  'ordm' => '186',  'raquo' => '187',  'frac14' => '188',  'frac12' => '189',  'frac34' => '190',
  'iquest' => '191',  'Agrave' => '192',  'Aacute' => '193',  'Acirc' => '194',  'Atilde' => '195',  'Auml' => '196',  'Aring' => '197',
  'AElig' => '198',  'Ccedil' => '199',  'Egrave' => '200',  'Eacute' => '201',  'Ecirc' => '202',  'Euml' => '203',  'Igrave' => '204',
  'Iacute' => '205',  'Icirc' => '206',  'Iuml' => '207',  'ETH' => '208',  'Ntilde' => '209',  'Ograve' => '210',  'Oacute' => '211',
  'Ocirc' => '212',  'Otilde' => '213',  'Ouml' => '214',  'times' => '215',  'Oslash' => '216',  'Ugrave' => '217',  'Uacute' => '218',
  'Ucirc' => '219',  'Uuml' => '220',  'Yacute' => '221',  'THORN' => '222',  'szlig' => '223',  'agrave' => '224',  'aacute' => '225',
  'acirc' => '226',  'atilde' => '227',  'auml' => '228',  'aring' => '229',  'aelig' => '230',  'ccedil' => '231',  'egrave' => '232',
  'eacute' => '233',  'ecirc' => '234',  'euml' => '235',  'igrave' => '236',  'iacute' => '237',  'icirc' => '238',  'iuml' => '239',
  'eth' => '240',  'ntilde' => '241',  'ograve' => '242',  'oacute' => '243',  'ocirc' => '244',  'otilde' => '245',  'ouml' => '246',
  'divide' => '247',  'oslash' => '248',  'ugrave' => '249',  'uacute' => '250',  'ucirc' => '251',  'uuml' => '252',  'yacute' => '253',
  'thorn' => '254',  'yuml' => '255',  'OElig' => '338',  'oelig' => '339',  'Scaron' => '352',  'scaron' => '353',  'Yuml' => '376',
  'fnof' => '402',  'circ' => '710',  'tilde' => '732',  'Alpha' => '913',  'Beta' => '914',  'Gamma' => '915',  'Delta' => '916',
  'Epsilon' => '917',  'Zeta' => '918',  'Eta' => '919',  'Theta' => '920',  'Iota' => '921',  'Kappa' => '922',  'Lambda' => '923',
  'Mu' => '924',  'Nu' => '925',  'Xi' => '926',  'Omicron' => '927',  'Pi' => '928',  'Rho' => '929',  'Sigma' => '931',  'Tau' => '932',
  'Upsilon' => '933',  'Phi' => '934',  'Chi' => '935',  'Psi' => '936',  'Omega' => '937',  'alpha' => '945',  'beta' => '946',  'gamma' => '947',
  'delta' => '948',  'epsilon' => '949',  'zeta' => '950',  'eta' => '951',  'theta' => '952',  'iota' => '953',  'kappa' => '954',
  'lambda' => '955',  'mu' => '956',  'nu' => '957',  'xi' => '958',  'omicron' => '959',  'pi' => '960',  'rho' => '961',  'sigmaf' => '962',
  'sigma' => '963',  'tau' => '964',  'upsilon' => '965',  'phi' => '966',  'chi' => '967',  'psi' => '968',  'omega' => '969',
  'thetasym' => '977',  'upsih' => '978',  'piv' => '982',  'ensp' => '8194',  'emsp' => '8195',  'thinsp' => '8201',  'zwnj' => '8204',
  'zwj' => '8205',  'lrm' => '8206',  'rlm' => '8207',  'ndash' => '8211',  'mdash' => '8212',  'lsquo' => '8216',  'rsquo' => '8217',
  'sbquo' => '8218',  'ldquo' => '8220',  'rdquo' => '8221',  'bdquo' => '8222',  'dagger' => '8224',  'Dagger' => '8225',  'bull' => '8226',
  'hellip' => '8230',  'permil' => '8240',  'prime' => '8242',  'Prime' => '8243',  'lsaquo' => '8249',  'rsaquo' => '8250',  'oline' => '8254',
  'frasl' => '8260',  'euro' => '8364',  'image' => '8465',  'weierp' => '8472',  'real' => '8476',  'trade' => '8482',  'alefsym' => '8501',
  'larr' => '8592',  'uarr' => '8593',  'rarr' => '8594',  'darr' => '8595',  'harr' => '8596',  'crarr' => '8629',  'lArr' => '8656',
  'uArr' => '8657',  'rArr' => '8658',  'dArr' => '8659',  'hArr' => '8660',  'forall' => '8704',  'part' => '8706',  'exist' => '8707',
  'empty' => '8709',  'nabla' => '8711',  'isin' => '8712',  'notin' => '8713',  'ni' => '8715',  'prod' => '8719',  'sum' => '8721',
  'minus' => '8722',  'lowast' => '8727',  'radic' => '8730',  'prop' => '8733',  'infin' => '8734',  'ang' => '8736',  'and' => '8743',
  'or' => '8744',  'cap' => '8745',  'cup' => '8746',  'int' => '8747',  'there4' => '8756',  'sim' => '8764',  'cong' => '8773',
  'asymp' => '8776',  'ne' => '8800',  'equiv' => '8801',  'le' => '8804',  'ge' => '8805',  'sub' => '8834',  'sup' => '8835',  'nsub' => '8836',
  'sube' => '8838',  'supe' => '8839',  'oplus' => '8853',  'otimes' => '8855',  'perp' => '8869',  'sdot' => '8901',  'lceil' => '8968',
  'rceil' => '8969',  'lfloor' => '8970',  'rfloor' => '8971',  'lang' => '9001',  'rang' => '9002',  'loz' => '9674',  'spades' => '9824',
  'clubs' => '9827',  'hearts' => '9829',  'diams' => '9830',
 );
	foreach($entarr AS $key => $val) {
		$this->entsearch[] = '&'.$key.';';
		$this->entsubstitute[] = code2utf($val);
	}
}

function SubstituteHiEntities($html) {
	// converts html_entities > ASCII 127 to unicode (defined in includes/pdf/config.php
	// Leaves in particular &lt; to distinguish from tag marker
	if (count($this->entsearch)) {
		$html = str_replace($this->entsearch,$this->entsubstitute,$html);
	}
	return $html;
}


// Edited v1.2 Pass by reference; option to continue if invalid UTF-8 chars
function is_utf8(&$string) {
	if ($string === mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
		return true;
	} 
	else {
	  if ($this->ignore_invalid_utf8) {
		$string = mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32") ;
		return true;
	  }
	  else {
		return false;
	  }
	}
} 


function purify_utf8($html,$lo=true) {
	// For HTML
	// Checks string is valid UTF-8 encoded
	// converts html_entities > ASCII 127 to UTF-8
	// Only exception - leaves low ASCII entities e.g. &lt; &amp; etc.
	// Leaves in particular &lt; to distinguish from tag marker
	if (!$this->is_utf8($html)) { $this->Error("HTML contains invalid UTF-8 character(s)"); }
	$html = preg_replace("/\r/", "", $html );

	// converts html_entities > ASCII 127 to UTF-8 
	// Leaves in particular &lt; to distinguish from tag marker
	$html = $this->SubstituteHiEntities($html);

	// converts all &#nnn; or &#xHHH; to UTF-8 multibyte
	// If $lo==true then includes ASCII < 128
	$html = strcode2utf($html,$lo);	


	return ($html);
}

function purify_utf8_text($txt) {
	// For TEXT
	// Make sure UTF-8 string of characters
	if (!$this->is_utf8($txt)) { $this->Error("Text contains invalid UTF-8 character(s)"); }

	$txt = preg_replace("/\r/", "", $txt );


	return ($txt);
}
function all_entities_to_utf8($txt) {
	// converts txt_entities > ASCII 127 to UTF-8 
	// Leaves in particular &lt; to distinguish from tag marker
	$txt = $this->SubstituteHiEntities($txt);

	// converts all &#nnn; or &#xHHH; to UTF-8 multibyte
	$txt = strcode2utf($txt);	


	$txt = $this->lesser_entity_decode($txt);
	return ($txt);
}

		

// ====================================================

// ====================================================
// ====================================================

		// START TRANSFORMATIONS SECTION -----------------------
		// authors: Moritz Wagner, Andreas Wurmser, Nicola Asuni
		// From TCPDF
		// Starts a 2D tranformation saving current graphic state.
		function StartTransform($returnstring=false) {
		  if ($returnstring) { return('q'); }
		  else { $this->_out('q'); }
		}
		
		function StopTransform($returnstring=false) {
		  if ($returnstring) { return('Q'); }
		  else { $this->_out('Q'); }
		}
		
		// Vertical and horizontal non-proportional Scaling.
		function transformScale($s_x, $s_y, $x='', $y='', $returnstring=false) {
			if ($x === '') {
				$x=$this->x;
			}
			if ($y === '') {
				$y=$this->y;
			}
			if (($s_x == 0) OR ($s_y == 0)) {
				$this->Error('Please do not use values equal to zero for scaling');
			}
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$s_x /= 100;
			$s_y /= 100;
			$tm[0] = $s_x;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = $s_y;
			$tm[4] = $x * (1 - $s_x);
			$tm[5] = $y * (1 - $s_y);
			//scale the coordinate system
			if ($returnstring) { return($this->_transform($tm, true)); }
			else { $this->_transform($tm); }
		}
		
		
		// Translate graphic object horizontally and vertically.
		function transformTranslate($t_x, $t_y, $returnstring=false) {
			//calculate elements of transformation matrix
			$tm[0] = 1;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = 1;
			$tm[4] = $t_x * $this->k;
			$tm[5] = -$t_y * $this->k;
			//translate the coordinate system
			if ($returnstring) { return($this->_transform($tm, true)); }
			else { $this->_transform($tm); }
		}
		
		// Rotate object.
		function transformRotate($angle, $x='', $y='', $returnstring=false) {
			if ($x === '') {
				$x=$this->x;
			}
			if ($y === '') {
				$y=$this->y;
			}
			$angle = -$angle;
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$tm[0] = cos(deg2rad($angle));
			$tm[1] = sin(deg2rad($angle));
			$tm[2] = -$tm[1];
			$tm[3] = $tm[0];
			$tm[4] = $x + $tm[1] * $y - $tm[0] * $x;
			$tm[5] = $y - $tm[0] * $y - $tm[1] * $x;
			//rotate the coordinate system around ($x,$y)
			if ($returnstring) { return($this->_transform($tm, true)); }
			else { $this->_transform($tm); }
		}
		
		
		function _transform($tm, $returnstring=false) {
			if ($returnstring) { return(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5])); }
			else { $this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5])); }
		}
		
		// END TRANSFORMATIONS SECTION -------------------------




// AUTOFONT =========================
function AutoFont($html) {
	if ( !$this->is_MB ) { return $html; }
	$this->useLang = true;
	if ($this->autoFontGroupSize == 1) { $extra = $this->pregASCIIchars1; }
	else if ($this->autoFontGroupSize == 3) { $extra = $this->pregASCIIchars3; }
	else {  $extra = $this->pregASCIIchars2; }
	$n = '';
	$a=preg_split('/<(.*?)>/ms',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i => $e) {
	   if($i%2==0) {
		// mPDF 3.0	
		$e = strcode2utf($e);	
		$e = $this->lesser_entity_decode($e);

		// mPDF 4.0 Use U=FFF0 and U+FFF1 to mark start and end of span tags to prevent nesting occurring
		// "\xef\xbf\xb0" ##lthtmltag## "\xef\xbf\xb1" ##gthtmltag##





		// mPDF 4.0 Moved so Vietnamese detection better
		if ($this->autoFontGroups & AUTOFONT_THAIVIET) {
			// THAI
			$e = preg_replace("/([\x{0E00}-\x{0E7F}".$extra."]*[\x{0E00}-\x{0E7F}][\x{0E00}-\x{0E7F}".$extra."]*)/u", "\xef\xbf\xb0span lang=\"th\"\xef\xbf\xb1\\1\xef\xbf\xb0/span\xef\xbf\xb1", $e);
			// Vietnamese
			$e = preg_replace("/([".$this->pregVIETchars .$this->pregVIETPluschars ."]*[".$this->pregVIETchars ."][".$this->pregVIETchars .$this->pregVIETPluschars ."]*)/u", "\xef\xbf\xb0span lang=\"vi\"\xef\xbf\xb1\\1\xef\xbf\xb0/span\xef\xbf\xb1", $e);	
		}

		// mPDF 3.0	
		$e = preg_replace('/[&]/u','&amp;',$e);
		$e = preg_replace('/[<]/u','&lt;',$e);
		$e = preg_replace('/[>]/u','&gt;',$e);
		// mPDF 4.0
		$e = preg_replace("/(\xef\xbf\xb0span lang=\"([a-z\-A-Z]{2,5})\"\xef\xbf\xb1)\s+/",' \\1',$e);
		$e = preg_replace("/\s+(\xef\xbf\xb0\/span\xef\xbf\xb1)/",'\\1 ',$e);

		$e = preg_replace("/\xef\xbf\xb0span lang=\"([a-z\-A-Z]{2,5})\"\xef\xbf\xb1/","\xef\xbf\xb0span lang=\"\\1\" class=\"lang_\\1\"\xef\xbf\xb1",$e);

		$e = preg_replace("/\xef\xbf\xb0/",'<',$e);
		$e = preg_replace("/\xef\xbf\xb1/",'>',$e);

		$a[$i] = $e;
	   }
	   else {
		$a[$i] = '<'.$e.'>'; 
	   }
	}
	$n = implode('',$a);
	return $n;
}






//===========================
// Functions originally in htmltoolkit - moved mPDF 4.0

// Call-back function Used for usort in fn _tableWrite

function _cmpdom($a, $b) {
    return ($a["dom"] < $b["dom"]) ? -1 : 1;
}

function mb_rtrim($str, $enc = 'utf-8'){
	if ($str == ' ' || $str == "\n" || $str == "\t") { return ''; }
	$end = mb_strlen($str,$enc);
	for($i=$end;$i>0;$i--) {
		$last = mb_substr($str,$i-1,1,$enc);
		if (($last != ' ') && ($last != "\n") && ($last != "\r") && ($last != "\t")) { return mb_substr($str,0,$i,$enc); }
	}
	return $str;
}

function mb_strrev($str, $enc = 'utf-8'){
	$ch = array();
	for($i=0;$i<mb_strlen($str,$enc);$i++) {
		$ch[] = mb_substr($str,$i,1,$enc);
	}
	$revch = array_reverse($ch);
	return implode('',$revch);
}


// Callback function from function printdivbuffer in mpdf - keeping block together on one page
function blockAdjust($type,$k,$xadj,$yadj,$a,$b,$c=0,$d=0,$e=0,$f=0) {
   if ($type == 'Td') { 	// xpos,ypos
	$a += ($xadj * $k);
	$b -= ($yadj * $k);
	return 'BT '.sprintf('%.3f %.3f',$a,$b).' Td'; 
   }
   else if ($type == 're') { 	// xpos,ypos,width,height
	$a += ($xadj * $k);
	$b -= ($yadj * $k);
	return sprintf('%.3f %.3f %.3f %.3f',$a,$b,$c,$d).' re'; 
   }
   else if ($type == 'l') { 	// xpos,ypos,x2pos,y2pos
	$a += ($xadj * $k);
	$b -= ($yadj * $k);
	return sprintf('%.3f %.3f l',$a,$b); 
   }
   else if ($type == 'img') { 	// width,height,xpos,ypos
	$c += ($xadj * $k);
	$d -= ($yadj * $k);
	return sprintf('q %.3f 0 0 %.3f %.3f %.3f',$a,$b,$c,$d).' cm /I'; 
   }
   else if ($type == 'draw') { 	// xpos,ypos
	$a += ($xadj * $k);
	$b -= ($yadj * $k);
	return sprintf('%.3f %.3f m',$a,$b); 
   }
   else if ($type == 'bezier') { 	// xpos,ypos,x2pos,y2pos,x3pos,y3pos
	$a += ($xadj * $k);
	$b -= ($yadj * $k);
	$c += ($xadj * $k);
	$d -= ($yadj * $k);
	$e += ($xadj * $k);
	$f -= ($yadj * $k);
	return sprintf('%.3f %.3f %.3f %.3f %.3f %.3f',$a,$b,$c,$d,$e,$f).' c'; 
   }
}

function ConvertColor($color="#000000"){
  //returns an associative array (keys: R,G,B) from html code (e.g. #3FE5AA)
  //All color names array
  static $common_colors = array('antiquewhite'=>'#FAEBD7','aqua'=>'#00FFFF','aquamarine'=>'#7FFFD4','beige'=>'#F5F5DC','black'=>'#000000','blue'=>'#0000FF','brown'=>'#A52A2A','cadetblue'=>'#5F9EA0','chocolate'=>'#D2691E','cornflowerblue'=>'#6495ED','crimson'=>'#DC143C','darkblue'=>'#00008B','darkgoldenrod'=>'#B8860B','darkgreen'=>'#006400','darkmagenta'=>'#8B008B','darkorange'=>'#FF8C00','darkred'=>'#8B0000','darkseagreen'=>'#8FBC8F','darkslategray'=>'#2F4F4F','darkviolet'=>'#9400D3','deepskyblue'=>'#00BFFF','dodgerblue'=>'#1E90FF','firebrick'=>'#B22222','forestgreen'=>'#228B22','fuchsia'=>'#FF00FF','gainsboro'=>'#DCDCDC','gold'=>'#FFD700','gray'=>'#808080','green'=>'#008000','greenyellow'=>'#ADFF2F','hotpink'=>'#FF69B4','indigo'=>'#4B0082','khaki'=>'#F0E68C','lavenderblush'=>'#FFF0F5','lemonchiffon'=>'#FFFACD','lightcoral'=>'#F08080','lightgoldenrodyellow'=>'#FAFAD2','lightgreen'=>'#90EE90','lightsalmon'=>'#FFA07A','lightskyblue'=>'#87CEFA','lightslategray'=>'#778899','lightyellow'=>'#FFFFE0','lime'=>'#00FF00','limegreen'=>'#32CD32','magenta'=>'#FF00FF','maroon'=>'#800000','mediumaquamarine'=>'#66CDAA','mediumorchid'=>'#BA55D3','mediumseagreen'=>'#3CB371','mediumspringgreen'=>'#00FA9A','mediumvioletred'=>'#C71585','midnightblue'=>'#191970','mintcream'=>'#F5FFFA','moccasin'=>'#FFE4B5','navy'=>'#000080','olive'=>'#808000','orange'=>'#FFA500','orchid'=>'#DA70D6','palegreen'=>'#98FB98','palevioletred'=>'#D87093','peachpuff'=>'#FFDAB9','pink'=>'#FFC0CB','powderblue'=>'#B0E0E6','purple'=>'#800080','red'=>'#FF0000','royalblue'=>'#4169E1','salmon'=>'#FA8072','seagreen'=>'#2E8B57','sienna'=>'#A0522D','silver'=>'#C0C0C0','skyblue'=>'#87CEEB','slategray'=>'#708090','springgreen'=>'#00FF7F','steelblue'=>'#4682B4','tan'=>'#D2B48C','teal'=>'#008080','thistle'=>'#D8BFD8','turquoise'=>'#40E0D0','violetred'=>'#D02090','white'=>'#FFFFFF','yellow'=>'#FFFF00', 
'aliceblue'=>'#f0f8ff', 'azure'=>'#f0ffff', 'bisque'=>'#ffe4c4', 'blanchedalmond'=>'#ffebcd', 'blueviolet'=>'#8a2be2', 'burlywood'=>'#deb887', 'chartreuse'=>'#7fff00', 'coral'=>'#ff7f50', 'cornsilk'=>'#fff8dc', 'cyan'=>'#00ffff', 'darkcyan'=>'#008b8b', 'darkgray'=>'#a9a9a9', 'darkgrey'=>'#a9a9a9', 'darkkhaki'=>'#bdb76b', 'darkolivegreen'=>'#556b2f', 'darkorchid'=>'#9932cc', 'darksalmon'=>'#e9967a', 'darkslateblue'=>'#483d8b', 'darkslategrey'=>'#2f4f4f', 'darkturquoise'=>'#00ced1', 'deeppink'=>'#ff1493', 'dimgray'=>'#696969', 'dimgrey'=>'#696969', 'floralwhite'=>'#fffaf0', 'ghostwhite'=>'#f8f8ff', 'goldenrod'=>'#daa520', 'grey'=>'#808080', 'honeydew'=>'#f0fff0', 'indianred'=>'#cd5c5c', 'ivory'=>'#fffff0', 'lavender'=>'#e6e6fa', 'lawngreen'=>'#7cfc00', 'lightblue'=>'#add8e6', 'lightcyan'=>'#e0ffff', 'lightgray'=>'#d3d3d3', 'lightgrey'=>'#d3d3d3', 'lightpink'=>'#ffb6c1', 'lightseagreen'=>'#20b2aa', 'lightslategrey'=>'#778899', 'lightsteelblue'=>'#b0c4de', 'linen'=>'#faf0e6', 'mediumblue'=>'#0000cd', 'mediumpurple'=>'#9370db', 'mediumslateblue'=>'#7b68ee', 'mediumturquoise'=>'#48d1cc', 'mistyrose'=>'#ffe4e1', 'navajowhite'=>'#ffdead', 'oldlace'=>'#fdf5e6', 'olivedrab'=>'#6b8e23', 'orangered'=>'#ff4500', 'palegoldenrod'=>'#eee8aa', 'paleturquoise'=>'#afeeee', 'papayawhip'=>'#ffefd5', 'peru'=>'#cd853f', 'plum'=>'#dda0dd', 'rosybrown'=>'#bc8f8f', 'saddlebrown'=>'#8b4513', 'sandybrown'=>'#f4a460', 'seashell'=>'#fff5ee', 'slateblue'=>'#6a5acd', 'slategrey'=>'#708090', 'snow'=>'#fffafa', 'tomato'=>'#ff6347', 'violet'=>'#ee82ee', 'wheat'=>'#f5deb3', 'whitesmoke'=>'#f5f5f5', 'yellowgreen'=>'#9acd32');
  //http://www.w3schools.com/css/css_colornames.asp
  if (strtoupper($color)=='TRANSPARENT') { return false; }
  if (strtoupper($color)=='INHERIT') { return false; }

  if (isset($common_colors[strtolower($color)])) $color = $common_colors[strtolower($color)];

  if ($color{0} == '#') //case of #nnnnnn or #nnn
  {
  	$cor = strtoupper($color);
	$cor = preg_replace('/\s+.*/','',$cor);	// in case of Background: #CCC url() x-repeat etc.
  	if (strlen($cor) == 4) { // Turn #RGB into #RRGGBB
	 	  $cor = "#" . $cor{1} . $cor{1} . $cor{2} . $cor{2} . $cor{3} . $cor{3};
	}  
	$R = substr($cor, 1, 2);
	$vermelho = hexdec($R);
	$V = substr($cor, 3, 2);
	$verde = hexdec($V);
	$B = substr($cor, 5, 2);
	$azul = hexdec($B);
	$color = array();
	$color['R']=$vermelho;
	$color['G']=$verde;
	$color['B']=$azul;
  }
  else if (stristr($color,'cmyk(')) {	//case of CMYK(c,m,y,k)
	$color = str_replace("cmyk(",'',$color); //remove ´rgb(´
	$color = str_replace("CMYK(",'',$color); //remove ´RGB(´ 
	$color = str_replace(")",'',$color); //remove ´)´
	$cores = explode(",", $color);
	$color = array();
	$color['R']=$cores[0];
	$color['G']=$cores[1];
	$color['B']=$cores[2];
	$color['K']=$cores[3];
  }
  else if (stristr($color,'rgb(')) //case of RGB(r,g,b)
  {
	$color = str_replace("rgb(",'',$color); //remove ´rgb(´
	$color = str_replace("RGB(",'',$color); //remove ´RGB(´ -- PHP < 5 does not have str_ireplace
	$color = str_replace(")",'',$color); //remove ´)´
	$cores = explode(",", $color);
	$color = array();
	$color['R']=$cores[0];
	$color['G']=$cores[1];
	$color['B']=$cores[2];
  }
  else { return false; }
  if (empty($color)) return false;
  else return $color; // array['R']['G']['B']
}

function ConvertSize($size=5,$maxsize=0,$fontsize=false,$usefontsize=true){
// usefontsize - setfalse for e.g. margins - will ignore fontsize for % values
// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
// For text $maxsize = Fontsize
// Setting e.g. margin % will use maxsize (pagewidth) and em will use fontsize
  //Identify size (remember: we are using 'mm' units here)
  if ( strtolower($size) == 'thin' ) $size = 1*0.2645; //1 pixel width for table borders
  elseif ( strtolower($size) == 'medium' ) $size = 3*0.2645; //3 pixel width for table borders
  elseif ( strtolower($size) == 'thick' ) $size = 5*0.2645; //5 pixel width for table borders
  elseif ( stristr($size,'px') ) $size *= 0.2645; //pixels
  elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
  elseif ( stristr($size,'mm') ) $size += 0; //millimeters
  elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
  elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
  elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
  elseif ( stristr($size,'em') ) {
  	$size += 0; //make "0.83em" become simply "0.83" 
	if ($fontsize) { $size *= $fontsize; }
	else { $size *= $maxsize; }
  }
  elseif ( stristr($size,'%') ) {
  	$size += 0; //make "90%" become simply "90" 
	if ($fontsize && $usefontsize) { $size *= $fontsize/100; }
	else { $size *= $maxsize/100; }
  }
  // mPDF 2.3
  elseif (strtoupper($size) == 'XX-SMALL') {
	if ($fontsize) { $size *= $fontsize*0.7; }
	else { $size *= $maxsize*0.7; }
  }
  elseif (strtoupper($size) == 'X-SMALL') {
	if ($fontsize) { $size *= $fontsize*0.77; }
	else { $size *= $maxsize*0.77; }
  }
  elseif (strtoupper($size) == 'SMALL') {
	if ($fontsize) { $size *= $fontsize*0.86; }
	else { $size *= $maxsize*0.86; }
  }
  elseif (strtoupper($size) == 'MEDIUM') {
	if ($fontsize) { $size *= $fontsize; }
	else { $size *= $maxsize; }
  }
  elseif (strtoupper($size) == 'LARGE') {
	if ($fontsize) { $size *= $fontsize*1.2; }
	else { $size *= $maxsize*1.2; }
  }
  elseif (strtoupper($size) == 'X-LARGE') {
	if ($fontsize) { $size *= $fontsize*1.5; }
	else { $size *= $maxsize*1.5; }
  }
  elseif (strtoupper($size) == 'XX-LARGE') {
	if ($fontsize) { $size *= $fontsize*2; }
	else { $size *= $maxsize*2; }
  }
  else $size *= 0.2645; //nothing == px
  
  return $size;
}


function lesser_entity_decode($html) {
  //supports the most used entity codes (only does ascii safe characters)
 	$html = str_replace("&nbsp;"," ",$html);
 	$html = str_replace("&lt;","<",$html);
 	$html = str_replace("&gt;",">",$html);

 	$html = str_replace("&apos;","'",$html);
 	$html = str_replace("&quot;",'"',$html);
 	$html = str_replace("&amp;","&",$html);
	return $html;
}

function AdjustHTML($html,$directionality='ltr',$usepre=true, $tabSpaces=8) {
	//Try to make the html text more manageable (turning it into XHTML)

	//Remove javascript code from HTML (should not appear in the PDF file)
	$html = preg_replace('/<script.*?<\/script>/is','',$html); // mPDF 3.0 changed from ereg_

	//Remove special comments
	$html = preg_replace('/<!--mpdf/i','',$html); // mPDF 3.0 changed from ereg_
	$html = preg_replace('/mpdf-->/i','',$html); // mPDF 3.0 changed from ereg_

	//Remove comments from HTML (should not appear in the PDF file)
	$html = preg_replace('/<!--.*?-->/s','',$html); // mPDF 3.0 changed from ereg_

	$html = preg_replace('/\f/','',$html); //replace formfeed by nothing // mPDF 3.0 changed from ereg_
	$html = preg_replace('/\r/','',$html); //replace carriage return by nothing // mPDF 3.0 changed from ereg_

	// Well formed XHTML end tags
	$html = preg_replace('/<(br|hr)\/>/i',"<\\1 />",$html);	

	// Get rid of empty <thead></thead>
	$html = preg_replace('/<thead>\s*<\/thead>/i','',$html); // mPDF 3.0 changed from ereg_
	$html = preg_replace('/<tfoot>\s*<\/tfoot>/i','',$html); // mPDF 3.2
	$html = preg_replace('/<table[^>]*>\s*<\/table>/i','',$html); // mPDF 3.2

	// Remove spaces at end of table cells
	$html = preg_replace("/[ ]+<\/t(d|h)/",'</t\\1',$html);

	// Transposes Table Cells When RTL direction
	if ($directionality == 'rtl') { 
		preg_match_all('/<table(.*?)>(.*?)<\/table>/is',$html,$matches);
		for($i=0;$i<count($matches[0]);$i++) {
		  $pre = '<table' . $matches[1][$i] . '>';
		  $post = '</table>';
		  // mPDF 3.2 Don't change if nested tables
		  if (!preg_match('/<table/is',$matches[2][$i]) && !preg_match('/<\/table/is',$matches[2][$i]) ) {
		    $table = $matches[0][$i];
		    if (preg_match('/(<thead[^>]*>)/is',$table,$m)) { $thead = $m[0]; } else { $thead = ''; }
		    preg_match_all('/<tr(.*?)>(.*?)<\/tr>/is',$table,$tmatches);
		    $newrows = array();
		    for($j=0;$j<count($tmatches[0]);$j++) {
			$rpre = '<tr' . $tmatches[1][$j] . '>';
			$rpost = '</tr>';
			$row = $tmatches[0][$j];
			preg_match_all('/<t[hd].*?>.*?<\/t[hd]>/is',$row,$rmatches);
			$cells = array();
			for($k=0;$k<count($rmatches[0]);$k++) { $cells[] = $rmatches[0][$k]; }
			$cells = array_reverse($cells);
			if (($thead) && ($j == 0)) {	// First row
				$newrows[] = $thead . $rpre . implode('',$cells) . $rpost . '</thead><tbody>';
			}
			else if (($thead) && ($j == (count($tmatches[0]) - 1))) {	// last row adds </tbody>
				$newrows[] = $rpre . implode('',$cells) . $rpost . '</tbody>';
			}
			else {
				$newrows[] = $rpre . implode('',$cells) . $rpost;
			}
		    }
		    $newtable = $pre . implode('',$newrows) . $post;
		    $html = str_replace($table,$newtable,$html);
		  }
		}
	}

	// Concatenates any Substitute characters from symbols/dingbats
	$html = str_replace('</tts><tts>','|',$html);
	$html = str_replace('</ttz><ttz>','|',$html);
	$html = str_replace('</tta><tta>','|',$html);

	$html = mb_eregi_replace('/<br \/>\s*/is',"<br />",$html); // mPDF 3.0 changed from ereg_
	if ($usepre) //used to keep \n on content inside <pre> and inside <textarea>
 	{
		// Preserve '\n's in content between the tags <pre> and </pre>
		$thereispre = preg_match_all('#<pre(.*?)>(.*?)</pre>#si',$html,$temp);
		// Preserve '\n's in content between the tags <textarea> and </textarea>
		$thereistextarea = preg_match_all('#<textarea(.*?)>(.*?)</textarea>#si',$html,$temp2);
		$html = preg_replace('/[\n]/',' ',$html); //replace linefeed by spaces // mPDF 3.0 changed from ereg_
		$html = preg_replace('/[\t]/',' ',$html); //replace tabs by spaces // mPDF 3.0 changed from ereg_

		// mPDF 2.3 - moved
		// Converts < to &lt; when not a tag
		$html = preg_replace('/<([^!\/a-zA-Z])/i','&lt;\\1',$html);	

		// mPDF changed to prevent &nbsp; chars replaced
		$html = preg_replace("/[ ]+/",' ',$html);

		$html = preg_replace('/\/li>\s+<\/(u|o)l/i','/li></\\1l',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/(u|o)l>\s+<\/li/i','/\\1l></li',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/li>\s+<\/(u|o)l/i','/li></\\1l',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/li>\s+<li/i','/li><li',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/<(u|o)l([^>]*)>[ ]+/i','<\\1l\\2>',$html);
		$html = preg_replace('/[ ]+<(u|o)l/i','<\\1l',$html); // mPDF 3.0 changed from ereg_

		$iterator = 0;
		while($thereispre) //Recover <pre attributes>content</pre>
		{
			// mPDF 2.4 
			$temp[2][$iterator] = str_replace("<||@mpdf@||pre", "<pre", $temp[2][$iterator] );
			$temp[2][$iterator] = str_replace("<||@mpdf@||/pre", "</pre", $temp[2][$iterator] );
			// mPDF 2.4 (moved) / mPDF 2.3
			$temp[2][$iterator] = preg_replace("/^([^\n\t]*?)\t/me", "stripslashes('\\1') . str_repeat(' ',  ( $tabSpaces - (mb_strlen(stripslashes('\\1')) % $tabSpaces))  )",$temp[2][$iterator]);

			$temp[2][$iterator] = preg_replace('/&/',"&amp;",$temp[2][$iterator]); // mPDF 3.0 changed from ereg_
			$temp[2][$iterator] = preg_replace('/</',"&lt;",$temp[2][$iterator]); // mPDF 3.0 changed from ereg_

			$temp[2][$iterator] = preg_replace('/\t/',str_repeat(" ",$tabSpaces),$temp[2][$iterator]); // mPDF 3.0 changed from ereg_

			$temp[2][$iterator] = preg_replace('/\n/',"<br />",$temp[2][$iterator]); // mPDF 3.0 changed from ereg_
			// mPDF 1.3 Edited to fix bug with empty pre
			// mPDF 2.4 Removed /u as not needed to be Unicode and causing bugs with annotations
			$html = preg_replace('#<pre(.*?)>(.*?)</pre>#si','<erp'.$temp[1][$iterator].'>'.$temp[2][$iterator].'</erp>',$html,1);
			$thereispre--;
			$iterator++;
		}
		$iterator = 0;
		while($thereistextarea) //Recover <textarea attributes>content</textarea>
		{
			$temp2[2][$iterator] = preg_replace('/&/',"&amp;",$temp2[2][$iterator]); // mPDF 3.0 changed from ereg_
			$temp2[2][$iterator] = preg_replace('/</',"&lt;",$temp2[2][$iterator]); // mPDF 3.0 changed from ereg_

			$temp2[2][$iterator] = preg_replace('/\t/',str_repeat(" ",$tabSpaces),$temp2[2][$iterator]); // mPDF 3.0 changed from ereg_
			$temp2[2][$iterator] = preg_replace('/[ ]/',"&nbsp;",$temp2[2][$iterator]); // mPDF 3.0 changed from ereg_
			// mPDF 4.1 /u modifier removed - not required and causing problems if annotation special chars
			$html = preg_replace('#<textarea(.*?)>(.*?)</textarea>#si','<aeratxet'.$temp2[1][$iterator].'>'.trim($temp2[2][$iterator]) .'</aeratxet>',$html,1);
			$thereistextarea--;
			$iterator++;
		}
		//Restore original tag names
		$html = str_replace("<erp","<pre",$html);
		$html = str_replace("</erp>","</pre>",$html);
		$html = str_replace("<aeratxet","<textarea",$html);
		$html = str_replace("</aeratxet>","</textarea>",$html);
		// (the code above might slowdown overall performance?)
	} //end of if($usepre)
	else
	{
		$html = preg_replace('/\n/',' ',$html); //replace linefeed by spaces // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\t/',' ',$html); //replace tabs by spaces // mPDF 3.0 changed from ereg_

		// Converts < to &lt; when not a tag
		$html = preg_replace('/<([^!\/a-zA-Z])/i','&lt;\\1',$html);	

		// mPDF changed to prevent &nbsp; chars replaced
		$html = preg_replace("/[ ]+/u",' ',$html);

		$html = preg_replace('/\/li>\s+<\/(u|o)l/i','/li></\\1l',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/(u|o)l>\s+<\/li/i','/\\1l></li',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/li>\s+<\/(u|o)l/i','/li></\\1l',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/\/li>\s+<li/i','/li><li',$html); // mPDF 3.0 changed from ereg_
		$html = preg_replace('/<(u|o)l([^>]*)>[ ]+/i','<\\1l\\2>',$html);
		$html = preg_replace('/[ ]+<(u|o)l/i','<\\1l',$html); // mPDF 3.0 changed from ereg_

	}
	$html = preg_replace('/<textarea([^>]*)><\/textarea>/si','<textarea\\1> </textarea>',$html);
	$html = preg_replace('/<(h[1-6])([^>]*)(>(?:(?!h[1-6]).)*<\/\\1>\s*<table)/si','<\\1\\2 keep-with-table="1"\\3',$html);	// *TABLES*
	$html = preg_replace("/\xbb\xa4\xac/", "\n", $html);
	return $html;
}

function dec2alpha($valor,$toupper="true"){
// returns a string from A-Z to AA-ZZ to AAA-ZZZ
// OBS: A = 65 ASCII TABLE VALUE
  if (($valor < 1)  || ($valor > 18278)) return "?"; //supports 'only' up to 18278
  $c1 = $c2 = $c3 = '';
  if ($valor > 702) // 3 letters (up to 18278)
    {
      $c1 = 65 + floor(($valor-703)/676);
      $c2 = 65 + floor((($valor-703)%676)/26);
      $c3 = 65 + floor((($valor-703)%676)%26);
    }
  elseif ($valor > 26) // 2 letters (up to 702)
  {
      $c1 = (64 + (int)(($valor-1) / 26));
      $c2 = (64 + (int)($valor % 26));
      if ($c2 == 64) $c2 += 26;
  }
  else // 1 letter (up to 26)
  {
      $c1 = (64 + $valor);
  }
  $alpha = chr($c1);
  if ($c2 != '') $alpha .= chr($c2);
  if ($c3 != '') $alpha .= chr($c3);
  if (!$toupper) $alpha = strtolower($alpha);
  return $alpha;
}


function dec2roman($valor,$toupper=true){
 //returns a string as a roman numeral
  $r1=$r2=$r3=$r4='';
  if (($valor >= 5000) || ($valor < 1)) return "?"; //supports 'only' up to 4999
  $aux = (int)($valor/1000);
  if ($aux!==0)
  {
    $valor %= 1000;
    while($aux!==0)
    {
    	$r1 .= "M";
    	$aux--;
    }
  }
  $aux = (int)($valor/100);
  if ($aux!==0)
  {
    $valor %= 100;
    switch($aux){
    	case 3: $r2="C";
    	case 2: $r2.="C";
    	case 1: $r2.="C"; break;
  	  case 9: $r2="CM"; break;
  	  case 8: $r2="C";
  	  case 7: $r2.="C";
    	case 6: $r2.="C";
      case 5: $r2="D".$r2; break;
      case 4: $r2="CD"; break;
      default: break;
	  }
  }
  $aux = (int)($valor/10);
  if ($aux!==0)
  {
    $valor %= 10;
    switch($aux){
    	case 3: $r3="X";
    	case 2: $r3.="X";
    	case 1: $r3.="X"; break;
    	case 9: $r3="XC"; break;
    	case 8: $r3="X";
    	case 7: $r3.="X";
  	  case 6: $r3.="X";
      case 5: $r3="L".$r3; break;
      case 4: $r3="XL"; break;
      default: break;
    }
  }
  switch($valor){
  	case 3: $r4="I";
  	case 2: $r4.="I";
  	case 1: $r4.="I"; break;
  	case 9: $r4="IX"; break;
  	case 8: $r4="I";
    case 7: $r4.="I";
    case 6: $r4.="I";
    case 5: $r4="V".$r4; break;
    case 4: $r4="IV"; break;
    default: break;
  }
  $roman = $r1.$r2.$r3.$r4;
  if (!$toupper) $roman = strtolower($roman);
  return $roman;
}


//===========================

}//end of Class



?>