<?php
if (!file_exists('../../../../wp-blog-header.php')){
    require_once ('/Users/Antony/Hosts/wordpress/wp-blog-header.php');
}
else{
    require_once('../../../../wp-blog-header.php');
}
global $wpdb;
?>
<?php
$opcion = get_option("dropbox-folder-share-options");
?>
<html>
<head>
    <style type="text/css">
        body {
            height: auto;
        }
        #wphead {
            font-size: 80%;
            border-top: 0;
            color: #555;
            background-color: #f1f1f1;
        }
        #wphead h1 {
            font-size: 24px;
            color: #555;
            margin: 0;
            padding: 10px;
        }
        #tabs {
            padding: 15px 15px 3px;
            background-color: #f1f1f1;
            border-bottom: 1px solid #dfdfdf;
        }
        #tabs li {
            display: inline;
        }
        #tabs a.current {
            background-color: #fff;
            border-color: #dfdfdf;
            border-bottom-color: #fff;
            color: #d54e21;
        }
        #tabs a {
            color: #2583AD;
            padding: 6px;
            border-width: 1px 1px 0;
            border-style: solid solid none;
            border-color: #f1f1f1;
            text-decoration: none;
        }
        #tabs a:hover {
            color: #d54e21;
        }
        .wrap h2 {
            border-bottom-color: #dfdfdf;
            color: #555;
            margin: 5px 0;
            padding: 0;
            font-size: 18px;
        }
        h3 {
            font-size: 1.1em;
            margin-top: 10px;
            margin-bottom: 0px;
        }
        #flipper {
            margin: 0;
            padding: 5px 20px 10px;
            background-color: #fff;
            border-left: 1px solid #dfdfdf;
            border-bottom: 1px solid #dfdfdf;
        }
        * html {
            overflow-x: hidden;
            overflow-y: scroll;
        }
        #flipper div p {
            margin-top: 0.4em;
            margin-bottom: 0.8em;
            text-align: justify;
        }
        td b {
            font-family: "Times New Roman" Times serif;
        }
    </style>
    <title>{#DropBoxFolderShare.titulo}</title>
    <script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js'></script>
    <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script type="text/javascript" src="DropBoxFolderShare_tinymce.js"></script>
</head>
<body>
<form id="superbutton_form" action="#">
    <ul id="tabs">
        <li><a id="tab1" href="javascript:return false;" class="current">{#DropBoxFolderShare.titulo}</a></li>
        <li><span style="color: #999; font-style: italic;">Creado por HynoTech Peru.</span></li>
    </ul>
    <div id="flipper" class="wrap">
        <div id="content1">
            <h2>{#DropBoxFolderShare.descripcion}</h2>
            <table border="0" cellpadding="4" cellspacing="0">
                <tr>
                    <td class="nowrap"><label for="txt_versiculo">{#DropBoxFolderShare.txt_url}*</label></td>
                    <td><input id="txt_link" name="txt_link" type="text" class="mceFocus" value="http://" style="width: 250px" onFocus="try {
                                        this.select();
                                    } catch (e) {
                                    }" /></td>
                </tr>
                <tr>
                    <td colspan="2">*{#DropBoxFolderShare.txt_necesario}
                        <input type="hidden" id="hdn_id_page" value="<?php echo $id_page; ?>" /></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="mceActionPanel">
        <div style="float: left">
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onClick="tinyMCEPopup.close();" />
        </div>
        <div style="float: right">
            <input type="button" id="insert" name="insert" value="{#insert}" onClick="insertarContenido();" />
        </div>
    </div>
</form>
</body>
</html>