tinyMCEPopup.requireLangPack();

function importarTexto(txt_version,txt_versiculo,txt_clase,txt_mostrarversion,rbt_hide,chk_contenedor,chk_referencias,id_page){
    //http://localhost/WP/wp-content/plugins/bible-post/script/test.html
    //var resultado = "hola";
    jQuery.ajax({
        url: "../importContent.php",
        type: "POST",
        data:"txt_version_p="+txt_version+"&txt_versiculo_p="+txt_versiculo+"&txt_clase_p="+txt_clase+"&txt_mostrarversion_p="+txt_mostrarversion+"&rbt_hide_p="+rbt_hide+"&chk_contenedor="+chk_contenedor+"&chk_referencias="+chk_referencias+"&id_page="+id_page,
        dataType: "html",
        //timeout: 3000,
        beforeSend: function(objeto){
            jQuery("#loading_remote").html('<img src="../img/loading.gif" /><br /><span style="color: #00f; font-style: italic;">Cargando Contenido.. Espere por favor</span>');
        },
        complete: function(objeto, exito){
            if(exito=="success"){
                jQuery("#loading_remote").html('<span style="color: #060; font-style: italic;">Contenido Cargado</span>');
            }
        },
        error: function(objeto, quepaso, otroobj){
            jQuery("#loading_remote").html("Error: "+quepaso);
        },
        success: function(datos){
            //alert (datos);
			
            tinyMCEPopup.execCommand('mceInsertContent', false, datos);
            // Refocus in window
            if (tinyMCEPopup.isWindow)
                window.focus();
            tinyMCEPopup.editor.focus();
            tinyMCEPopup.close();
			
        }
    });
}
function insertarShortCode(txt_version,txt_versiculo,txt_clase,rbt_hide,chk_contenedor,chk_referencias){
    txt_clase_total = "";
    if(!txt_clase ==""){
        txt_clase_total = ' class="' + txt_clase + '"';
    }
    var txt_mostrarversion = jQuery('#txt_mostrarversion').val();
    txt_mostrarversion_total = "";
    if(!txt_mostrarversion ==""){
        txt_mostrarversion_total = ' showversion="' + txt_mostrarversion + '"';
    }
    contenedor_hide = ' contenedor="'+chk_contenedor+'"';
    
    if(chk_contenedor == 1){
        contenedor_hide += ' estado="' + rbt_hide + '"';
    }
    data = '[bible-post-hyno version="' + txt_version + '"' + txt_mostrarversion_total + txt_clase_total + ' versiculo="' + txt_versiculo +'"'+ ' ref_cruzadas="' + chk_referencias +'"'+ contenedor_hide + ']';
    tinyMCEPopup.execCommand('mceInsertContent', false, data);
    // Refocus in window
    if (tinyMCEPopup.isWindow)
        window.focus();
    tinyMCEPopup.editor.focus();
    tinyMCEPopup.close();
}

function hide_contenedor(){
    if(!jQuery('#chk_contenedor').prop('checked')){
        jQuery("input:radio[name*='rbt_hide']").attr("disabled", true); 
    }
    else{
        jQuery("input:radio[name*='rbt_hide']").removeAttr('disabled');
    }
}

function insertarBiblePost() {
    var winder = window.top;
    var txt_version = jQuery('#txt_version').val();
    var txt_versiculo = jQuery('#txt_versiculo').val();
    var txt_clase = jQuery('#txt_clase').val();
    var txt_mostrarversion = jQuery('#txt_mostrarversion').val();
    var rbt_importar = jQuery("input:radio[name*='rbt_importar']:checked").val();//jQuery("input:radio:checked").val();
    var rbt_hide = jQuery("input:radio[name*='rbt_hide']:checked").val();
    
    var chk_contenedor = jQuery('#chk_contenedor');
    chk_contenedor = chk_contenedor.prop('checked')?"1":"0";
    
    var chk_referencias = jQuery('#chk_referencias');
    chk_referencias = chk_referencias.prop('checked')?"1":"0";
    
    var id_page = jQuery('#hdn_id_page').val();
    
    if( rbt_importar == "local" ){
        importarTexto(txt_version,txt_versiculo,txt_clase,txt_mostrarversion,rbt_hide,chk_contenedor,chk_referencias,id_page);
    }
    if( rbt_importar == "remoto" ){
        insertarShortCode(txt_version,txt_versiculo,txt_clase,rbt_hide,chk_contenedor,chk_referencias);
    }
}
