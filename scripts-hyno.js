
function loadContenDFS(link, ver_como, idContent){


    jQuery.ajax({
        data:  {
            action: 'getFolderContent',
            dfs_nonce: objDFS.dfs_nonce,
            link: link,
            ver_como: ver_como,
            idContent: idContent,
            titleBar: jQuery('#Hyno_Header_'+idContent).html()
            //titleBar: ''
        },
        url:   objDFS.ajax_url,
        type:  'post',
        beforeSend: function () {
            jQuery('#'+idContent).html("<div style='text-align: center'><img src='"+ objDFS.url_imgLoader +"'></div>");
        },
        success:  function (response) {
            jQuery('#'+idContent).html(response);
        }
    });
    
}


/////// Shortcodes Javascript ///////
jQuery(document).ready(function($) {


});