<?php
/**
 * DokuWiki 'portfolio' Template
 * Based on 'ACH' Template for DokuWiki
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:templates
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Anika Henke <a.c.henke@arcor.de>
 * @author Frank Schiebel <schiebel@aeg-reutlingen.de>
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

/* include template translations */
include_once(dirname(__FILE__).'/lang/en/lang.php');
@include_once(dirname(__FILE__).'/lang/'.$conf['lang'].'/lang.php');
if (file_exists(DOKU_PLUGIN.'displaywikipage/code.php')) include_once(DOKU_PLUGIN.'displaywikipage/code.php'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php tpl_pagetitle()?> [<?php echo strip_tags($conf['title'])?>]</title>
  <?php tpl_metaheaders()?>
  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />
</head>

<body>
<div id="ach__template" class="dokuwiki">
  <?php html_msgarea()?><!-- error messages, etc. -->
  <div id="ach__header">
   <div id="pf_header">
     <div id="pf_nameblock"> 
      <div id="pf_logo">
      <h1><?php tpl_link(wl(),$conf['title'],'name="dokuwiki__top" id="dokuwiki__top" accesskey="h" title="[ALT+H]"')?></h1>
      <p><?php tpl_link(wl(),$conf['schoolname'],'name="Schulname"')?></p>
      </div>
      </div>
        <h2>[[<?php echo $ID?>]]</h2>
      <div id="pf_topmenu">
        <?php if (function_exists('dwp_display_wiki_page')): ?>
        <?php dwp_display_wiki_page("allusers:topmenu"); ?>
        <?php else: ?>
        <?php include(dirname(__FILE__) .  '/sidebar.php'); ?>
        <?php endif; ?>
      </div>
   </div>  
    <?php if($conf['breadcrumbs']){?>
      <p class="trace"><?php tpl_breadcrumbs()?></p>
    <?php }?>
    <?php if($conf['youarehere']){?>
      <p class="trace"><?php tpl_youarehere()?></p>
    <?php }?>
  </div><!-- /ach__header -->
  <hr class="invisible" />

  <div id="ach__mainbox">
    <div id="ach__pageactions">
      <?php tpl_button('edit')?>
      <?php
        $discussNS='discussion:';
        if(substr($ID,0,strlen($discussNS))==$discussNS) {
          $backID=substr(strstr($ID,':'),1);
          print html_btn('back',$backID,'',array());
          /*link instead of button: tpl_pagelink(':'.$backID,$lang['btn_back']);*/
        } else {
          #print html_btn('discussion',$discussNS.$ID,'',array());
          /*link instead of button: tpl_pagelink($discussNS.$ID,$lang['btn_discussion']);*/
        }
      ?>
      <?php tpl_button('history')?>
      <?php tpl_button('backlink')?>
      <?php 
        if ( $INFO['isadmin'] == 1) {
          print html_btn('move',$ID,'M',array('do' => 'admin', 'page' => 'pagemove'));
        }
      ?>

      <?php
        if ( $INFO['writable'] == 1 ) {
          print html_btn('newpage','shared:do_newpage','',array());
        }
      ?>
      <div id="explinks">
       <a href="<?php echo exportlink($ID, 'xhtml')?>"><img src="<?php echo DOKU_BASE?>lib/images/fileicons/html.png" alt="HTML" /></a>
       <a href="<?php echo exportlink($ID, 'pdf')?>"><img src="<?php echo DOKU_BASE?>lib/images/fileicons/pdf.png" alt="PDF Export" /></a>
       <a href="<?php echo exportlink($ID, 'odt')?>"><img src="<?php echo DOKU_BASE?>lib/images/fileicons/odt.png" alt="ODT Export" /></a>
      </div>
    </div><!-- /ach__pageactions -->
    

    <div id="ach__siteactions">
      <div class="box" id="pf_searchbox">
        <?php tpl_searchform()?>
      </div>
       
      <div class="box" id="pf_sidebar">
        <ul>
          <li><?php tpl_actionlink('recent')?></li>
        </ul>
       <?php if (function_exists('dwp_display_wiki_page')): ?>
       <?php dwp_display_wiki_page("allusers:sidebar"); ?>
       <?php else: ?>
       <?php include(dirname(__FILE__) .  '/sidebar.php'); ?>
       <?php endif; ?>
      
      </div>
      <?php if($conf['useacl']){?>
      <div class="box" id="pf_userinfo">
      <?php if($_SERVER['REMOTE_USER']){
         ob_start();
         tpl_actionlink('profile');
         $_profile = ob_get_contents();
         ob_end_clean();

         ob_start();
         tpl_actionlink('subscription');
         $_subscription = ob_get_contents();
         ob_end_clean();

         ob_start();
         tpl_actionlink('admin');
         $_admin = ob_get_contents();
         ob_end_clean();
      ?>
          <ul>
            <li><?php tpl_actionlink('login')?></li>
            <?php if($_profile){?>
               <li><?php print $_profile;?></li>
            <?php }?>
            <?php if($_subscription){?>
               <li><?php print $_subscription;?></li>
            <?php }?>
            <?php if($_admin){?>
               <li><?php print $_admin;?></li>
            <?php }?>
          </ul>
          <p><em><?php tpl_userinfo()?></em></p>
        <?php }?>
      </div>
      <div class="box" id="pf_clouds">
       <?php if (function_exists('dwp_display_wiki_page')): ?>
       <?php dwp_display_wiki_page("allusers:clouds"); ?>
       <?php else: ?>
       <?php include(dirname(__FILE__) .  '/clouds.php'); ?>
       <?php endif; ?>
      </div>
      <?php }?>
    </div><!-- /ach__siteactions -->
    <hr class="invisible" />

    <?php flush()?>

    <div id="ach__content">
      <!-- wikipage start -->
      <?php tpl_content()?>
      <!-- wikipage stop -->
      <div class="clearer">&nbsp;</div>
    </div><!-- /ach__content -->

    <?php flush()?>

  </div><!-- /ach__mainbox -->
  <hr class="invisible" />


  <div id="ach__footer">
        <p class="pageinfo"><?php tpl_pageinfo()?></p>
        <p><?php tpl_actionlink('top')?></p>
      <div class="clearer">&nbsp;</div>
  </div>

</div><!-- /ach__template -->

<div class="no"><?php tpl_indexerWebBug()?></div>
</body>
</html>
