tinyMCEPopup.requireLangPack();
function insertarShortCode(txt_link,ver_como){
    data = '[DFS link="' + txt_link + '" ver_como="' + ver_como + '"]';
    tinyMCEPopup.execCommand('mceInsertContent', false, data);
    // Refocus in window
    if (tinyMCEPopup.isWindow)
        window.focus();
    tinyMCEPopup.editor.focus();
    tinyMCEPopup.close();
}

function insertarContenido() {
    var winder = window.top;
    var txt_link = jQuery('#txt_link').val();
    var rbt_ver_como = jQuery("input:radio[name*='rbt_ver_como']:checked").val();

	ver_como = (rbt_ver_como == 'lista')?'lista':'iconos';
    
    insertarShortCode(txt_link,ver_como);
}
