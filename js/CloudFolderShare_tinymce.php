<?php
//require_once('../../../../wp-blog-header.php');
//Load bootstrap file
require_once( dirname( dirname(__FILE__) ) .'/tinymceBootstrap.php'); 

global $wpdb;
include("../cloud_drives.php");
?>
<style>
    * {
        border: 0px;
        margin: 0px;
    }
    #OptContent {
        background: #F1F1F1;
        font-size: 12px;
        font-style: italic;
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
    #flipper {
        margin: 0;
        padding: 5px 20px 10px;
        background-color: #fff;
        border-left: 1px solid #dfdfdf;
        border-bottom: 1px solid #dfdfdf;
    }
    .wrap h2 {
        border-bottom-color: #dfdfdf;
        color: #555;
        margin: 5px 0;
        padding: 0;
        font-size: 18px;
    }
    #flipper div p {
        margin-top: 0.4em;
        margin-bottom: 0.8em;
        text-align: justify;
    }
    td  {
        font-family: "Times New Roman" Times serif;
        font-size:12px;
    }
</style>
<title>Cloud Folder Share</title>
<script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js'></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script> 
<script type="text/javascript" src="jquery.dd.min.js"></script>
<script type="text/javascript" src="scripts-hyno.js"></script>
<LINK href="../css/msdropdown/dd.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="CloudFolderShare_tinymce.js"></script>
<form id="superbutton_form" action="#">
    <div id="OptContent">
        <ul id="tabs">
            <li><a id="tab1" href="javascript:return false;" class="current">
                    <?php _e('Cloud Folder Share WP', 'CFS-Hyno'); ?>
                </a></li>
            <li><span style="color: #999; font-style: italic;">
                    <?php _e('Creado por HynoTech Peru.', 'CFS-Hyno'); ?>
                </span></li>
        </ul>
        <div id="flipper" class="wrap">
            <div id="content1">
                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="nowrap"><label for="txt_name">
                                <?php _e('Nombre Carpeta: ', 'CFS-Hyno'); ?>
                                </label></td>
                        <td><input id="txt_name" name="txt_name" type="text" class="mceFocus" value="" style="width: 250px" onFocus="try {
                        this.select();
                    } catch (e) {
                    }" /></td>
                    </tr>
                    <tr>
                        <td class="nowrap"><label for="txt_link">
                                <?php _e('URL de carpeta', 'CFS-Hyno'); ?>
                                *</label></td>
                        <td><input id="txt_link" name="txt_link" type="text" class="mceFocus" value="http://" style="width: 250px" onFocus="try {
                        this.select();
                    } catch (e) {
                    }" /></td>
                    </tr>
                    <tr>
                        <td class="nowrap"><label for="cbo_nube">
                                <?php _e('Nube de la URL', 'CFS-Hyno'); ?>
                                *</label></td>
                        <td><select name ="cbo_nube" id ="cbo_nube" style="width: 150px;">
                                <?php
                                $icons_dir = '../img/icons/';
                                foreach ($drive as $drives) {
                                    $status = ($drives['state']) ? '' : 'disabled';
                                    $selected = (get_option('CFS_cloud') == $drives['id']) ? 'selected' : ' ';
                                    echo '<option ' . $selected . $status . ' value="' . $drives['id'] . '" data-image="' . $icons_dir . $drives['icon'] . '">' . $drives['name'] . '</option>';
                                }
                                ?>
                                <!-- <option disabled="disabled" value="box" data-image="http://www.google.com/s2/favicons?domain=www.box.com">Google Drive</option> -->
                            </select></td>
                    </tr>
                    <tr>
                        <td colspan="2">*
                            <?php _e('Campos Obligatorios', 'CFS-Hyno'); ?>
                            <input type="hidden" id="hdn_id_page" value="<?php echo $id_page; ?>" /></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mceActionPanel">
            <div style="float: left">
                <input type="button" id="cancel" name="cancel" value="<?php _e('Cancelar', 'CFS-Hyno'); ?>" onClick="tinyMCEPopup.close();" />
            </div>
            <div style="float: right">
                <input type="button" id="insert" name="insert" value="<?php _e('Insertar', 'CFS-Hyno'); ?>" onClick="insertarContenido();
                    alert('hola');" />
            </div>
        </div>
    </div>
</form>
