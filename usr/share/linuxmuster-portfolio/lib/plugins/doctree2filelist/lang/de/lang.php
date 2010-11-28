<?php
/**
 * German language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

// custom language strings for the ospdocimport plugin
$lang['plugname'] = 'Importassistent für bestehende Dokumentensammlungen';
$lang['headline'] = 'Importassistent für bestehende Dokumentensammlungen';
$lang['wizard'] = 'Assistent';
$lang['settings'] = 'Einstellungen';
$lang['importdir'] = 'Importverzeichnis';
$lang['targetns'] = 'Ziel-Namensraum';
$lang['description'] = 'Dieser Assistent soll Ihnen ermöglichen, eine vorhanden Sammlung von Office-Dokumenten (Word, Powerpoint, PDF u.ä.) als Dateiverweise in das Wiki zu importieren.';
$lang['detaildesc'] = '<p>Der Vorgang besteht aus vier Schritten:</p>
         <ol><li>Zunächst wird der gesamte vorhandene Dokumentenbestand mit allen Unterverzeichnissen auf den Server kopiert, auf dem openSchulportftolio installiert ist.
         Dazu wird vom Assistenten vorübergehend ein Verzeichnis angelegt, in welches die Dateien transferiert werden müssen.</li>
         <li>In einem weiteren Schritt werden die Dateien in den eigentlichen Dokumentenbaum des Wikis kopiert. Dabei werden die Dateien wenn nötig umbenannt
         (Umlaute, Leerzeichen und ähnliches sind für die Verwendung in Online-Systemen nicht geeignet). Außerdem wird bei diesem Vorgang im Wiki eine
         Seitenstruktur erzeugt, über die alle kopierten Dokumente anschließend erreichbar sind. Diese Wikiseiten können nach erfolgreichem Import beliebig
         angepasst werden.</li>
         <li>Wenn alles geklappt hat, können die zuvor auf den Server geladenen Dokumente gelöscht werden.</li>
         <li>Nun können Sie die erzeugten Wikiseiten an Ihre Bedürfnisse anpassen.</li>
         </ol>';
$lang['warning_osp'] = 'Beim Importvorgang werden alle Dokumente und Wiki-Seiten im Ziel-Namensraum <strong>unwiderruflich durch den importierten Dokumentenstamm ersetzt</strong>!';
$lang['filelist_plugin_required'] = 'Dieses Plugin ben&ouml;tigt das <tt>filelist</tt>-Plugin f&uuml;r DokuWiki.</div>';

$lang['sourcedir_exists'] = 'Das Importverzeichnis existiert.';
$lang['sourcedir_does_not_exist'] = 'Das Importverzeichnis existiert <strong>nicht</strong>. ';
$lang['docuploadnow'] = "Laden Sie nun den gesamten Verzeichnisbaum ihrer Dokumentensammlung in das Importverzeichnis auf den Webserver. Wenn der Vorgang beendet ist, bestätigen Sie den Upload."; 
$lang['importnow'] = "Starten Sie nun den Importvorgang."; 
$lang['lastimport'] ="Letzer Import:";
$lang['fromuser'] ="von Benutzer";
$lang['docsuploaded'] ="Die Dokumentensammlung wurde auf den Server hochgeladen.";
$lang['btn_import'] = 'Dateien importieren und Dokumentstruktur anlegen';
$lang['btn_reimport'] = 'Dateien erneut importieren';
$lang['btn_create_upload_dir'] = 'Importverzeichnis anlegen';
$lang['btn_delete_upload_dir'] = 'Importverzeichnis löschen';
$lang['btn_confirm_upload'] = 'Die Dateien wurden hochgeladen';
$lang['btn_start_over'] = 'Import neu starten';
$lang['subnamespaces'] = 'Untergeordnete Namensräume';
$lang['edit_files'] = 'Dateien bearbeiten';
$lang['ns_up'] = 'Zum übergeordneten Namensraum wechseln';
$lang['documents_for'] = 'Dokumente für das Verzeichnis';
$lang['docslisted'] = 'Alle Dateien des Verzeichnisses:';
