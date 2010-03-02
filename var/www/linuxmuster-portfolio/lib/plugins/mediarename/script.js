/**
 * Register a quicklink to mediamanager for: 
 * Dokuwiki Action Plugin Media-File-Names-Rename
 * Uses addInitEvent;
 * 
 * @author Christian Eder 
 * 
 */



function mediarename_plugin(){
    var opts = $('media__opts');
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
    if (LANG['mediarename_plugin_recursive']) capt1=LANG['mediarename_plugin_recursive']; 
    else capt1='Rename recursive ?';
    if (LANG['mediarename_plugin']) capt2=LANG['mediarename_plugin']; 
    else capt2='Fix invalid mediafilenames';
    gboxlbl.innerHTML = capt1;

    //show a quicklink
    var glbl = document.createElement('label');
    var glnk = document.createElement('a');
    var gbrk = document.createElement('br');
    glnk.name         = 'mediarename_plugin';
    glnk.innerHTML    = capt2;
    glnk.style.cursor = 'pointer';
 
    
    glnk.onclick = function(){
        var h1 = $('media__ns');
        if(!h1) return;
        var ns = h1.innerHTML;
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
addInitEvent(function() {


    mediarename_plugin();
    

});
