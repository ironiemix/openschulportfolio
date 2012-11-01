<?php
if(!defined('DOKU_INC')) die();

class action_plugin_selectsearch extends DokuWiki_Action_Plugin {

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER',  $this, '_fixquery');
    }

    /**
     * Put namespace into search
     */
    function _fixquery(&$event, $param) {
        global $QUERY;
        global $ACT;

        if($ACT != 'search'){
            $QUERY = '';
            return;
        }

        if(trim($_REQUEST['namespace'])){
            $QUERY .= ' @'.trim($_REQUEST['namespace']);
        }
    }

    function tpl_searchform() {

        global $QUERY;

        $searchnamespaces = explode(",",$this->getConf('searchnamespaces'));
        foreach ($searchnamespaces as $ns) {
            list($namespace,$displayname) = explode(">",$ns);
            trim($namespace);
            trim($displayname);
            $namespaces[$namespace] = $displayname;
        }

        $cur_val = isset($_REQUEST['namespace']) ? $_REQUEST['namespace'] : '';

        echo '<form id="dw__search" class="search" method="post" accept-charset="utf-8" action="">';
        echo '<div class="no">';
        echo '<select class="selectsearch_namespace" name="namespace">';
        foreach ($namespaces as $ns => $displayname){
            echo '<option value="'.hsc($ns).'"'.($cur_val === $ns ? ' selected="selected"' : '').'>'.hsc($displayname).'</option>';
        }
        echo '</select>';
        echo '<input type="hidden" name="do" value="search" />';
        echo '<input type="hidden" id="qsearch__in"/>';
        echo '<input class="edit" id="selectsearch__input" type="text" name="id" autocomplete="off" title="[F]" value="'.hsc(preg_replace('/ ?@\S+/','',$QUERY)).'" accesskey="f" />';
        echo '<input class="button" type="submit" title="Search" value="Search">';
        echo '<div id="qsearch__out" class="ajax_qsearch JSpopup" style="display: none;"></div>';
        echo '</div>';
        echo '</form>';
    }
}
