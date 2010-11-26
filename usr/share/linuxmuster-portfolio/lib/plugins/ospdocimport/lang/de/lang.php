<?php
/**
 * German language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Frank Schiebel <frank@linuxmuster.net>
 */

// custom language strings for the ospdocimport plugin
$lang['headline'] = 'Importassistent für bestehende Dokumentensammlungen';
$lang['description'] = 'Dieser Assistent soll Ihnen ermöglichen, eine vorhanden Sammlung von Office-Dokumenten (Word, Powerpoint, PDF u.ä.) als Dateiverweise in das Wiki zu importieren.';
$lang['detaildesc'] = '<p>Der Vorgang besteht aus vier Schritten:</p>
         <ol><li>Zunächst wird der gesamte vorhandene Dokumentenbestand mit allen Unterverzeichnissen auf den Server kopiert, auf dem openSchulportftolio installiert ist.
         Dazu wird vom Assistenten vorübergehend ein Verzeichnis angelegt, in welches die Dateien transferiert werden müssen.</li>
         <li>In einem weiteren Schritt werden die Dateien in den eigentlichen Dokumentenbaum des Wikis kopiert. Dabei werden die Dateien wenn nötig umbenannt
         (Umlaute, Leerzeichen und ähnliches sind für die Verwendung in Online-Systemen nicht geeignet). Außerdem wird bei diesem Vorgang im Wiki eine
         Seitenstruktur erzeugt, über die alle kopierten Dokumente anschließend erreichbar sind. Diese Wikiseiten können nach erfolgreichem Import beliebig
         angepasst werden.</li>
         <li>Wenn alles geklappt hat, können die zuvor auf den Server geladenen Dokumente gelöscht werden.</li>
         </ol>';
$lang['warning_osp'] = ' <div class="notewarning">Bei der Ausführung des Assistenten werden alle Dokumente im Namensraum <tt>portfolio:dokumente</tt> <strong>unwiderruflich durch den importierten Dokumentenstamm ersetzt</strong>!<br /> Außerdem wird die Startseite im Namensraum <tt>portfolio</tt> durch eine Vorlage ersetzt, die einen Verweis auf die importierte Seitenstruktur enthält. Ältere Versionen dieser Wiki-Seite können wie gewohnt wiederhergestellt werden.</div>';

$lang['sourcedir_exists'] = 'Das Quellverzeichnis existiert:';
$lang['sourcedir_does_not_exist'] = 'Das Quellverzeichnis existiert <strong>nicht</strong>: ';
$lang['btn_import'] = 'Dateien importieren und Dokumentstruktur anlegen';
$lang['btn_create_upload_dir'] = 'Importverzeichnis anlegen';
