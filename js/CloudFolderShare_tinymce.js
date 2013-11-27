//tinyMCEPopup.requireLangPack();
function insertarShortCode(txt_name,txt_link,cbo_nube){
    //[CFS cloud='dropbox' name='Nombre Carpeta']https://www.dropbox.com/sh/jt8rwz8jfqcr9vh/yv0oj0MMvc[/CFS]
    data  = "[CFS cloud='" + cbo_nube + "' ";
    if(txt_name != ''){
        data += "name='" + txt_name + "'"; 
    }
    data += "]";
    data += txt_link;
    data += "[/CFS]";
    //data = '[CFS cloud="'+ cbo_nube + ' name="' + txt_name + '"]"' + txt_link + '[/CFS]';
    tinyMCEPopup.execCommand('mceInsertContent', false, data);
    // Refocus in window
    if (tinyMCEPopup.isWindow)
        window.focus();
    tinyMCEPopup.editor.focus();
    tinyMCEPopup.close();
}

function insertarContenido() {
    //var winder = window.top;
    var txt_link = jQuery('#txt_link').val();
    var txt_name = jQuery('#txt_name').val();
    var cbo_nube = jQuery('#cbo_nube').val();
    //var txt_height = jQuery('#txt_height').val();    
    insertarShortCode(txt_name,txt_link,cbo_nube);
}
