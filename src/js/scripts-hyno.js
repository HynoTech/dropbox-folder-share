function loadContenDFS(data, idContent) {

    if (data.substring(0, 4) == "rev_") {
        data = data.substring(4, data.length);
        data = data.replace(/'/g, '"');
    }

    var datos = jQuery.parseJSON(data);

    var dataImgs = null;

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
        dataType: "json",
        async: true,
        beforeSend: function () {
            jQuery('#' + idContent).html("<div class='Hyno_ContenFolder'><div class='sl-page-body sl-list-container'><div class='sl-body'><div style='text-align: center'><img src='" + objDFS.url_imgLoader + "'></div></div></div></div>");
        },
        success:  function (response) {
            //alert(response.html);
            console.log(response.imgs);
            //jQuery('#'+idContent).html(response.html);
            contenedor = jQuery('#'+idContent);
            contenedor.html(response.html);


            jQuery.each(response.imgs,function (i, item) {

                jQuery.ajax({
                    data:  {
                        action: 'getImgBase64',
                        dfs_nonce: objDFS.dfs_nonce,
                        tipo: item.type,
                        img_url: item.img_url,
                    },
                    async: true,
                    url:   objDFS.ajax_url,
                    type:  'post',
                    success:  function (data) {
                        contenedor.find("#"+item.img_id).attr('class','icon thumbnail-image--loaded');
                        contenedor.find("#"+item.img_id).attr('src',data);
                    }
                });
            });

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

/*
    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
    */

// delegate calls to data-toggle="lightbox"
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        dataOriginalSRC = $(this).attr('data-orighref')
        console.log($(this).attr('href'));
        return $(this).ekkoLightbox({
            onShow: function(elem) {
                if(dataOriginalSRC != ""){
                    var html = '<a href="' + dataOriginalSRC + '" target="_blank"><span class="glyphicon glyphicon-save" aria-hidden="true"></span></a>';
                    $(elem.currentTarget).find('.modal-header').prepend(html);
                    //$(elem.currentTarget).find('.modal-header h4').append(html);
                    $(elem.currentTarget).find('.modal-header:last-child').append(html);
                }
            },
            onNavigate: function(direction, itemIndex) {
                if (window.console) {
                    return console.log('Navigating '+direction+'. Current item: '+itemIndex);
                }
            }
        });
    });




});



