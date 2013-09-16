<?php
require_once('../../../../wp-blog-header.php');
global $wpdb;
?>
<?php
include("../bible-versions.php");


$import_content = get_option("bp_hyno_importar");
$import_content_sel = $import_content == "1" ? 'checked' : '';
$link_content_sel = $import_content == "0" ? 'checked' : '';

$hide_content = get_option("bp_hyno_estado");
$visible_content_sel = $hide_content == "visible" ? 'checked' : '';
$oculto_content_sel = $hide_content == "oculto" ? 'checked' : '';

$referencias = get_option("bp_hyno_footnotes");
$referencias_sel = $referencias == "1" ? 'checked' : '';

$id_page = get_option("bp_hyno_id_page");

$contenedor_sel = "";
$desactivados = "";
$contenedor_content = get_option("bp_hyno_estilo");
if($contenedor_content == "1"){
    $contenedor_sel = "checked";
}
else{
    $desactivados = "disabled";
}
//$contenedor_sel = $contenedor_content == "1" ? 'checked' : '';
//$contenedor_no_sel = $contenedor_content == "oculto" ? 'checked' : '';

?>
<html>
    <head>
    <!-- <link rel='stylesheet' id='global-css'  href='<?php bloginfo('wpurl'); ?>/wp-admin/css/global.css?ver=20110121' type='text/css' media='all' /> 
    <link rel='stylesheet' id='wp-admin-css'  href='http://localhost/wordpress/wp-admin/css/wp-admin.css?ver=20110214' type='text/css' media='all' />  -->
        <style type="text/css"> 
            body{
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
        <title>{#biblepost.titulo}</title>
        <script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js'></script>
        <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
        <script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script> 
        <script type="text/javascript" src="biblepost_tinymce.js"></script>
    </head>
    <body>
        <form id="superbutton_form" action="#">
            <ul id="tabs"> 
                <li><a id="tab1" href="javascript:return false;" class="current">{#biblepost.titulo}</a></li> 
                <li><span style="color: #999; font-style: italic;">Creado por HynoTech Peru.</span></li> 
            </ul> 

            <div id="flipper" class="wrap"> 
                <div id="content1"> 
                    <h2>{#biblepost.descripcion}</h2> 
                    <table border="0" cellpadding="4" cellspacing="0">
                        <tr>
                            <td class="nowrap"><label for="txt_version">{#biblepost.txt_version}*</label></td>
                            <td>
                                <select id="txt_version" name="txt_version">
                                    <?php
                                    foreach ($versions as $num => $name) {
                                        $sel = get_option("bp_hyno_version") == $num ? 'selected' : '';
                                        echo '<option value="' . $num . '" ' . $sel . '>' . $name . '(' . $num . ')' . '</option>' . "\r\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="nowrap"><label for="txt_versiculo">{#biblepost.txt_versiculo}*</label></td>
                            <td><input id="txt_versiculo" name="txt_versiculo" type="text" class="mceFocus" value="Josue 1:8-9" style="width: 200px" onfocus="try{this.select();}catch(e){}" /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" border="0" cellpadding="4" cellspacing="0">
                                    <tr>
                                        <td class="nowrap"><label for="txt_clase">{#biblepost.txt_clase}</label></td>
                                        <td><input id="txt_clase" name="txt_clase" type="text" class="mceFocus" value="" style="width: 80px" onfocus="try{this.select();}catch(e){}" /></td>
                                        <td class="nowrap"><label for="txt_mostrarversion">{#biblepost.txt_mostrarversion}</label></td>
                                        <td>
                                            <select id="txt_mostrarversion" name="txt_mostrarversion">
                                                <option value="">{#biblepost.txt_n_a}</option>
                                                <option value="1">{#biblepost.txt_afirmacion}</option>
                                                <option value="0">{#biblepost.txt_negacion}</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="nowrap" colspan="2">
                                            <input id="chk_contenedor" name="chk_contenedor" type="checkbox" class="mceFocus" value="" <?php echo $contenedor_sel; ?> onclick="hide_contenedor();"/> 
                                            <label for="chk_contenedor">{#biblepost.txt_contenedor}</label></td>
                                        <td class="nowrap" colspan="2">
                                            <input id="chk_referencias" name="txt_referencias" type="checkbox" class="mceFocus" value="" <?php echo $referencias_sel; ?>/>
                                            <label for="chk_referencias">{#biblepost.txt_referencias}</label></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="nowrap"><label for="rbt_importar">{#biblepost.txt_importar}</label></td>
                            <td><input id="rbt_importar" name="rbt_importar" type="radio" value="local" <?php echo $import_content_sel; ?> /> {#biblepost.txt_importar_local}<br />
                                <input id="rbt_importar" name="rbt_importar" type="radio" value="remoto" <?php echo $link_content_sel; ?> /> {#biblepost.txt_importar_remoto}<br /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div id="loading_remote" style="width: 100%; text-align: center;visibility:none;">
                                    <span style="color: #f00; font-style: italic;">{#biblepost.txt_importar_advertencia_import}</span><br />
                                    <span style="color: #060; font-style: italic;">{#biblepost.txt_importar_advertencia_linked}</span>
                                </div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <input <?php echo $desactivados; ?> id="rbt_hide" name="rbt_hide" type="radio" value="visible" <?php echo $visible_content_sel; ?> /> {#biblepost.txt_hide_s}&nbsp;
                                <input <?php echo $desactivados; ?> id="rbt_hide" name="rbt_hide" type="radio" value="oculto" <?php echo $oculto_content_sel; ?> /> {#biblepost.txt_hide_h}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{#biblepost.txt_necesario}
                                <input type="hidden" id="hdn_id_page" value="<?php echo $id_page; ?>" /></td>
                        </tr>
                    </table>
                </div> 
            </div>
            <div class="mceActionPanel"> 
                <div style="float: left"> 
                    <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" /> 
                </div> 

                <div style="float: right"> 
                    <input type="button" id="insert" name="insert" value="{#insert}" onclick="insertarBiblePost();" /> 
                </div> 
            </div>

        </form>
    </body>
</html>