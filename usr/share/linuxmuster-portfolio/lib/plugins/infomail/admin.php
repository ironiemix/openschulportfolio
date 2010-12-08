<?php
require_once DOKU_PLUGIN . 'admin.php';
require_once dirname(__FILE__) . '/log.php';

class admin_plugin_infomail extends DokuWiki_Admin_Plugin {
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getMenuText() {
        return 'Log of recommendations';
    }

    function handle() {
        if (isset($_REQUEST['rec_month']) &&
            preg_match('/^\d{4}-\d{2}$/', $_REQUEST['rec_month'])) {
            $this->month = $_REQUEST['rec_month'];
        } else {
            $this->month = date('Y-m');
        }
        $log = new Plugin_infomail_Log($this->month);
        $this->entries = $log->getEntries();
        $this->logs = Plugin_infomail_Log::getLogs();
    }

    function getTOC() {
        return array_map('infomail_make_toc', $this->logs);
    }

    function html() {
        if (!$this->logs) {
            echo 'No recommendations.';
            return;
        }
        if (!$this->entries) {
            echo 'No recommendations were made in ' . $this->month . '.';
            return;
        }
        echo '<p>In ' . $this->month . ', your users made the following ' . count($this->entries) . ' recommendations:</p>';
        echo '<ul>';
        foreach(array_reverse($this->entries) as $entry) {
            echo "<li>$entry</li>";
        }
        echo '</ul>';
    }
}

function infomail_make_toc($month) {
    global $ID;
    return html_mktocitem('?do=admin&page=infomail&id=' . $ID . '&rec_month=' . $month, $month, 1, '');

}
