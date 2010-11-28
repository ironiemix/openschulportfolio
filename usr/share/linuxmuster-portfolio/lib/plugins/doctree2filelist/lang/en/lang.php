<?php
/**
 * English language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

// custom language strings for the ospdocimport plugin
$lang['plugname'] = 'Import Wizard for existing document collections';
$lang['headline'] = 'Import Wizard for existing document collections';
$lang['wizard'] = 'Wizard';
$lang['settings'] = 'Settings';
$lang['importdir'] = 'Import directory';
$lang['targetns'] = 'Target namespace';
$lang['description'] = 'The Import Wizard facilitates the import of existing document collections (word, powerpoint, PDF etc.) as link list to the wiki.';
$lang['detaildesc'] = '<p>This process requires four steps:</p>
         <ol><li>The complete document collection (including directories) must be copied to the server that hosts openSchulportfolio.
         The files need to be transferred to a target directory which is created by the Import Wizard.</li>
         <li>The files are (automatically) copied and integrated into the document tree of the wiki (if necessary, the files are renamed, thus eliminating   		umlauts, blanks and the like - these characters are unsuitable for use in online systems). This step creates the site structure which allows 		navigation to the documents (the site structure may be customized after the import).</li>
         <li>Make sure the import has been completed and delete the target directory on the server.</li>
         <li>Customize the wiki/document structure according to your needs and liking!</li>
         </ol>';
$lang['warning_osp'] = 'All documents and wiki pages in the target namespace will be <strong>irreversibly</strong> replaced by the imported document tree!';
$lang['filelist_plugin_required'] = 'This plugin depends on the <tt>filelist</tt>-plugin for DokuWiki.</div>';

$lang['sourcedir_exists'] = 'The import directory has been created.';
$lang['sourcedir_does_not_exist'] = 'The import directory has <strong>not</strong> been created.';
$lang['docuploadnow'] = "Copy the file directory tree of your document collection to the import directory on the web server. Confirm the upload after completion."; 
$lang['importnow'] = "Start import now."; 
$lang['lastimport'] ="Last import:";
$lang['fromuser'] ="by user";
$lang['docsuploaded'] ="Document collection has been uploaded to server.";
$lang['btn_import'] = 'Import files and create document structure';
$lang['btn_reimport'] = 'Re-import files';
$lang['btn_create_upload_dir'] = 'Create import directory';
$lang['btn_delete_upload_dir'] = 'Delete import directory';
$lang['btn_confirm_upload'] = 'File upload complete';
$lang['btn_start_over'] = 'Start over import';
$lang['subnamespaces'] = 'Subnamespaces';
$lang['edit_files'] = 'Edit files';
$lang['ns_up'] = 'Go one namespace up';
$lang['documents_for'] = 'Documents for directory: ';
$lang['docslisted'] = 'All documents for the directory:';
