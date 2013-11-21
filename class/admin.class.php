<?php

class DFS_Admin extends DropboxFolderSharePrincipal {

    public function __construct() {
        //parent::__construct();
    }

    function pagAdmin() {
        add_options_page(
                '[HT]DropBox Folder Share', /* Titulo Pagina */ 
                '[HT]DropBox Folder Share', /* Titulo Menu */ 
                parent::_PERMISOS_REQUERIDOS_, 
                parent::$nombre, 
                array(&$this, 'pagina_de_opciones')
        );
    }

    function pagina_de_opciones() {
        require_once (parent::$url_path . 'admin/admin_page.php');
    }

    function plugin_admin_init() {
        register_setting(parent::_OPT_SEETINGS_, 'main_options', 'DFS_options_validate');
        add_settings_section('idSeccionInstrucciones', 'Instrucciones de Uso', 'instruccionesSection',parent::$nombre);
        add_settings_field('idCampoInstrucciones', 'Instrucciones', 'plugin_setting_string', parent::$nombre, 'idSeccionInstrucciones');
        //add_settings_section('instrucciones', 'Instrucciones de Uso', 'instruccionesSection', 'configPage_unique');

        //register_setting('plugin_options', 'plugin_options', 'plugin_options_validate');
        //add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
        //add_settings_section('plugin_conexion', 'Tipo de Conexion', 'HTML_seccionTipoConexion', 'plugin');
        //add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
        //add_settings_field('id_tipo_conexion', 'Tipo de Conexion a usar :D', 'tipoConexionHTML', 'plugin', 'plugin_conexion');
    }
    
    function instruccionesSection(){
        ?>
                    <div class="stuffbox" id="instrucciones_hyno">
                        <h3>YA ESTAMOS</h3>
                        <div class="inside">
                            <ol>
                                <!--<li><?php _e('Usar el widget para compartir una carpeta', 'dropbox-folder-share'); ?></li>-->
                                <li><?php _e('Usa la etiqueta [dropbox-foldershare-hyno] para compartir una carpeta en tus post:', 'dropbox-folder-share'); ?><br />
                                    [dropbox-foldershare-hyno link="LNK_FOLDER" ver_como='lista'] (Parametros abajo)</li>
                            </ol>
                        </div>
                    </div>
            <?php
    }
function plugin_setting_string() {
    $options = get_option('plugin_options');
    echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}
}
