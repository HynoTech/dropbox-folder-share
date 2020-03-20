/**
 * Created by anton on 13/03/2017.
 */
popupwindow: function (editor, values) {

    console.dir(values);
    values = values || [];
    editor.windowManager.open({
        title: 'Dropbox Folder Share - HynoTech',
        onsubmit: function (e) {

            e.preventDefault();
            if ((e.data.link !== "") && (e.data.link.substr(0, 4) === "http")) {
                e.data.innercontent = '';
                var dataDB = {};
                jQuery.ajax({
                    data: {
                        action: 'getFolderHeaders',
                        dfs_nonce: objDFS.dfs_nonce,
                        link: e.data.link
                    },
                    beforeSend: function () {
                        //$("#loading-image").show();
                        //$("#" + e.control._id).append('<div style="width:100%; text-align: center;"><img src="images/loading.gif"></div>');
                        $();
                        $("#" + e.control._id + " :input").attr("disabled", true);
                        $("#" + e.control._id).prepend('<div id="tmpLoading" style="width: 100%;height: 20px;background: url(../wp-includes/js/thickbox/loadingAnimation.gif) no-repeat 0 0;background-size: 80%;background-position: center;"></div>');
                    },
                    url: objDFS.ajax_url,
                    type: 'post',
                    dataType: "json",
                    async: false,
                    success: function (response) {
                        dataDB = response;
                        console.log(response);
                    }
                });

                if ((typeof dataDB['og:title'] != 'undefined') && (dataDB['og:title'].indexOf('not found') == -1) && (dataDB['og:title'].indexOf('- Error') == -1)) {

                    //if(e.data.show_icon != '' )
                    var args = {
                        tag: shortcode_string,
                        type: e.data.innercontent.length ? 'closed' : 'single',
                        content: e.data.innercontent,
                        attrs: {
                            link: e.data.link,
                            //show_icon: e.data.show_icon,
                            show_icon: e.data.show_icon == 'checked' ? '1' : '0',
                            //show_size: e.data.show_size,
                            show_size: e.data.show_size == 'checked' ? '1' : '0',
                            //show_change: e.data.show_change,
                            show_change: e.data.show_change == 'checked' ? '1' : '0',
                            titulo: dataDB['og:title']
                            //link     : e.data.link,
                            //linkhref : e.data.linkhref
                        }
                    };
                    editor.insertContent(wp.shortcode.string(args));

                    this.close();


                } else {

                    alert((typeof dataDB['og:title'] != 'undefined') ? dataDB['og:title'] : "URL ERROR");
                    //alert("URL Error");
                    //:first-child
                    $("#" + e.control._id).live("elimElem", function (event, myName, myValue) {
                        $("#tmpLoading").remove();
                    });
                    $("#" + e.control._id + ":first").trigger("elimElem");
                    $("#" + e.control._id + " :input").attr("disabled", false);
                }
            } else {

                alert("URL Error");

            }


            //console.dir(e);
            //console.log(e);
            //return onsubmit_callback;
        },
        body: [
            <?php
$lastElement = end($dataShortcode["Boton"]["controles"]);
                foreach($dataShortcode["Boton"]["controles"] as $clave => $valor){
                    echo "{type: '".$valor['type']."', name: '".$valor['name']."', label: '".$valor['label']."', value: values.".$valor['name'].", tooltip: '".$valor['tooltip']."', classes:'".$valor['classes']."'";
    if(isset($valor['multiline'])){
    echo ", multiline: ".$valor['multiline'];
}
    if((in_array($valor['type'], array('checkbox','radio'))) && ($valor['checked'] != '')) {
    echo ", checked: 'checked'";
}
    echo "}";
                    echo ( $lastElement['name'] != $valor['name'] )?',':'';
                }
            ?>
        ]/*,
        onsubmit: onsubmit_callback*/
    } );
}