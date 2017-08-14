function loadContenDFS(data, idContent) {

    if (data.substring(0, 4) == "rev_") {
        data = data.substring(4, data.length);
        data = data.replace(/'/g, '"');
    }

    var datos = jQuery.parseJSON(data);

    jQuery.ajax({
        data:  {
            action: 'getFolderContent',
            dfs_nonce: objDFS.dfs_nonce,
            link: datos.link,
            showIcons: datos.showIcons,
            showSize: datos.showSize,
            showChange: datos.showChange,
            idContent: idContent,
            titleBar: jQuery('#Hyno_Breadcrumbs_' + idContent).html()
            //titleBar: jQuery('#Hyno_Header_'+idContent).html()
            //titleBar: ''
        },
        url:   objDFS.ajax_url,
        type:  'post',
        beforeSend: function () {
            jQuery('#' + idContent).html("<div class='Hyno_ContenFolder'><div class='sl-page-body sl-list-container'><div class='sl-body'><div style='text-align: center'><img src='" + objDFS.url_imgLoader + "'></div></div></div></div>");
        },
        success:  function (response) {
            jQuery('#'+idContent).html(response);
        }
    });
    
}


/////// Shortcodes Javascript ///////
jQuery(document).ready(function($) {


    $(document).on('click', '.notice-dismiss', function () {
        tipo = $(this).parent().data('tipo');


        jQuery.ajax({
            url: ajaxurl,
            data: {
                tipo: tipo,
                action: 'cerrarNota'
            }
        })

    });


    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });


});