/**
 * Register a quicklink to mediamanager for: 
 * Dokuwiki Action Plugin Media-File-Names-Rename
 * Uses addInitEvent;
 * 
 * @author Christian Eder 
 * 
 */



function mediarename_plugin(){
    var $opts = jQuery('#media__opts');
    var opts = $opts[0];
    if(!opts) return;
    if(!window.opener) return;

    //show a checkbox
    var gbox = document.createElement('input');
    gbox.type= 'checkbox';
    gbox.checked = gbox.defaultChecked = false;
    gbox.name='mediarename_plugin_recursive';
    gbox.id='mediarename_plugin_recursive';
    var gboxlbl  = document.createElement('label');
    gboxlbl.htmlFor   = 'mediarename_plugin_recursive';
    var capt1,capt2;
    capt1=LANG['mediarename_plugin_recursive'];
    capt2=LANG['mediarename_plugin'];
    gboxlbl.innerHTML = capt1;

    //show a quicklink
    var glbl = document.createElement('label');
    var glnk = document.createElement('a');
    var gbrk = document.createElement('br');
    glnk.name         = 'mediarename_plugin';
    glnk.innerHTML    = capt2;
    glnk.style.cursor = 'pointer';


    glnk.onclick = function(){
        var $h1 = jQuery('#media__ns');
        if(!$h1[0]) return;
        var ns = $h1.html();
	var rename='flat';
        if (gbox.checked) rename='recv';
        window.location.href=window.location.href+'&ns='+ns+'&rename='+rename;
    };


    opts.appendChild(gbrk);
    opts.appendChild(gbox);
    opts.appendChild(gboxlbl);
    opts.appendChild(glbl);
    glbl.appendChild(glnk);
    opts.appendChild(gbrk);


    
 
}


// === main ===
jQuery(function() {


    mediarename_plugin();
    

});
