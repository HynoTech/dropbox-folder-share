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
        require_once (parent::$url_path.'admin/admin_page.php');
    }
    function plugin_admin_init() {
    register_setting(parent::_OPT_SEETINGS_, 'main_options', 'DFS_options_validate');
    add_settings_section('instrucciones', 'Instrucciones de Uso', 'plugin_section_text', 'plugin');
    
    //register_setting('plugin_options', 'plugin_options', 'plugin_options_validate');
    add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
    add_settings_section('plugin_conexion', 'Tipo de Conexion', 'HTML_seccionTipoConexion', 'plugin');
    add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
    add_settings_field('id_tipo_conexion', 'Tipo de Conexion a usar :D', 'tipoConexionHTML', 'plugin', 'plugin_conexion');
}

}
