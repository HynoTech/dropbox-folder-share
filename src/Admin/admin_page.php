<style type="text/css">
    #version_defecto .form-table{
        margin-left: 2em;
    }



    h2.wpmm-title {
        background: url('../images/icon-48.png') no-repeat left center;
        line-height: 42px;
        margin-bottom: 30px;
        height: 48px;
        padding-left: 55px;
    }

    /* WRAP*/
    .wpmm-wrapper {
        display: table;
        width: 100%;
    }

    /*
    .wpmm-wrapper #content {
        min-width: 800px;
    }
    */

    .wpmm-wrapper #sidebar {
        padding: 0 0 0 20px;
        width: 280px;
    }

    .wpmm-wrapper #sidebar .sidebar_box {
        background: none repeat scroll 0 0 #fff;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        min-width: 255px;
        line-height: 1;
        margin-bottom: 20px;
        padding: 0;
    }

    .wpmm-wrapper #sidebar .sidebar_box h3 {
        margin: 0;
        padding: 8px 12px;
        border-bottom: 1px solid #ececec;
    }

    .wpmm-wrapper #sidebar .sidebar_box .inside {
        margin: 6px 0 0;
        font-size: 13px;
        line-height: 1.4em;
        padding: 0 12px 12px;
    }

    .wpmm-wrapper .wrapper-cell {
        display: table-cell;
    }

    .wpmm-wrapper .hidden {
        display: none;
    }

    /* TABS */
    .nav-tab-wrapper {
        border-bottom: 1px solid #ccc;
        padding-bottom: 0;
        padding-left: 10px;
    }

    .nav-tab-wrapper a {
        font-size: 15px;
        font-weight: 700;
        line-height: 24px;
        padding: 6px 10px;
    }

    /* TABS CONTENT */
    .tabs-content {
        /*margin-top: 20px;*/
        padding-left: 10px;
    }

    .tabs-content .wp-color-result {
        margin-bottom: -2px;
    }

    .tabs-content ul.bg_list {
        float: left;
    }

    .tabs-content ul.bg_list li {
        float: left;
        margin-right: 7px;
        opacity: 0.4;
    }

    .tabs-content ul.bg_list li.active {
        opacity: 1;
    }

    .tabs-content ul.bg_list li input {
        display: none;
    }

    .tabs-content .countdown_details input {
        width: 70px;
    }

    .tabs-content .countdown_details .margin_left {
        margin-left: 30px;
    }

    /* SIDEBARS */
    #sidebar .info_box ul {
        margin-top: 10px;
        margin-bottom: -5px;
    }

    #sidebar .resources_box ul, #sidebar .themes_box ul {
        margin-top: 10px;
        margin-bottom: -15px;
    }

    #sidebar .resources_box li, #sidebar .themes_box li {
        margin-bottom: 10px;
    }
</style>


<div class="wrap">
    <img src="<?php echo DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/logo.png'; ?>" />

    <?php if (!empty($_POST)) { ?>
        <div class="updated settings-error" id="setting-error-settings_updated">
            <p><strong><?php _e('Configuraciones guardadas.', "dropbox-folder-share"); ?></strong></p>
        </div>
    <?php } ?>

    <div class="wpmm-wrapper">
        <div id="content" class="wrapper-cell">
            <div class="nav-tab-wrapper">
                <a class="nav-tab" href="#configuraciones">Configuraciones</a>
                <a class="nav-tab" href="#parametros">Parametros</a>
                <a class="nav-tab" href="#donaciones" style="background-color: darkred; color: white;">Donaciones</a>
            </div>

            <div class="tabs-content stuffbox">
                <div id="tab-configuraciones" class="stuff hidden">
                    <form action="options.php#configuraciones" method="post">
                        <?php
                        settings_fields(parent::_OPT_SEETINGS_ . '-group');
                        do_settings_sections(DROPBOX_FOLDER_SHARE_PLUGIN_NOMBRE);
                        //wp_nonce_field('tab-configuraciones');
                        ?>
                        <div class="inside">
                            <?php submit_button(); ?>
                        </div>
                    </form>
                </div>
                <div id="tab-parametros" class="stuff hidden">
                    <h3>
                        <?php _e('Parametros de Shortcode', "dropbox-folder-share"); ?>
                    </h3>
                    <div class="inside">
                        <p class="popular-tags"><em>[DFS link="LNK_FOLDER" show_icon="true" show_size="true"
                                show_change="true" titulo="_TITULO_"]</em></p>
                        <table cellpadding="0" class="links-table">
                            <tbody>
                            <tr>
                                <th scope="row">link</th>
                                <td><?php _e('URL de la carpeta compartida de DropBox.', "dropbox-folder-share"); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">show_icon <code>(true/false)</code></th>
                                <td><?php _e( 'Mostrar iconos en la visualizacion.', "dropbox-folder-share" ); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">show_size <code>(true/false)</code></th>
                                <td><?php _e( 'Mostrar tamaño de archivos.', "dropbox-folder-share" ); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">show_change <code>(true/false)</code></th>
                                <td><?php _e( 'Mostrar fecha de modificacion.', "dropbox-folder-share" ); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">titulo</th>
                                <td><?php _e( 'Titulo de la seccion (solo para ver en editor).', "dropbox-folder-share" ); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tab-donaciones" class="">
		            <?php echo parent::nota_donacion(); ?>
                </div>


            </div>
            <div id="SugerenciasComments" class="stuffbox">
                <div class="inside">
                    <em><?php _e('Gracias por Utilizar este plugin. Me gustaria leer sugerencias u opiniones para que juntos mejoremos esta herramienta, cualquier sugerencia para mejorar el plugin o reportar algun error nos ayuda muchisimo, no duden en hacernoslo saber. ',"dropbox-folder-share"); ?></em>
                    <br />
                    <em><?php _e('Pueden hacernos llegar sus sugerencias, opiniones y/o criticas a travez del formulario de contactos de', "dropbox-folder-share") ?> <a href="http://www.hynotech.com"> HynoTech.com</a></em>
                </div>
            </div>
        </div>

        <?php include_once('sidebar.php'); ?>
    </div>
</div>






