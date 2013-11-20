<?php
/**
 * Plugin Name: DropBox Folder Share Nuevo
 * Plugin URI: http://www.hyno.ok.pe/wp-plugins/dropbox-folder-share/
 * Description: Plugin que permitira incluir carpetas de DropBox en nuestras entradas de blog.
 * Version: 1.2
 * Author: Antonio Salas (Hyno)
 * Author URI: http://www.hyno.ok.pe
 * License:     GNU General Public License
 */
if (!class_exists("DropboxFolderSharePrincipal")) {
    Class DropboxFolderSharePrincipal {

        const _VERSION_GENERAL_ = "1.2";
        const _VERSION_JS_ = "1.2";
        const _VERSION_CSS_ = "1.2";
        const _VERSION_ADMIN_ = "1.2";
        const _PARENT_PAGE_ = "options-general.php";
        const _OPT_SEETINGS_ = "dropbox-folder-share-options";
        const _PERMISOS_REQUERIDOS_ = 'manage_options';

        public static $basename; //Indica el directorio hasta el archivo actual "dropbox-folder-share/DropboxFolderShare.php"
        public static $nombre; //Nombre de la carpeta "dropbox-folder-share"
        public static $url; //URL completa dela carpeta actual "http://localhost:8080/wp/wp-content/plugins/dropbox-folder-share/"
        public static $url_path; //URL completa dela carpeta actual "http://localhost:8080/wp/wp-content/plugins/dropbox-folder-share/"
        var $formSections = array();
        var $settings = array(); //Almacena los opciones actuales del Plugin
        var $opcDefault = array(
            "estado" => TRUE,
            "showIcons" => FALSE,
            "showSize" => TRUE,
            "showChange" => TRUE,
            "allowDownload" => FALSE,
            "link2Folder" => TRUE,
            "tipoConexion" => 'fopen'
        );

        public function __construct() {
            include_once 'class/admin.class.php';
            $this->asignar_variables_estaticas();
            load_plugin_textdomain(self::$nombre, false, self::$nombre . '/languages/');

            
            $objDFS_Admin = new DFS_Admin;
            add_action('admin_menu', array(&$objDFS_Admin,'pagAdmin'));
        }

        public function asignar_variables_estaticas() {
            self::$basename = plugin_basename(__FILE__);
            self::$nombre = dirname(self::$basename);
            self::$url = plugin_dir_url(__FILE__);
            self::$url_path = plugin_dir_path(__FILE__);
        }

    }

    $objDropboxFolderSharePrincipal = new DropboxFolderSharePrincipal;
    
    
}

add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() {
    add_options_page('Custom Plugin Page', 'Custom Plugin Menu', 'manage_options', 'plugin', 'plugin_options_page');
}

function plugin_options_page() {
    ?>
    <div>
        <h2>My custom plugin</h2>
        Options relating to the Custom Plugin.
        <form action="options.php" method="post">
    <?php settings_fields('plugin_options'); ?>
            <?php do_settings_sections('plugin'); ?>

            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form></div>

    <?php
}

add_action('admin_init', 'plugin_admin_init');

function plugin_admin_init() {
    register_setting('plugin_options', 'plugin_options', 'plugin_options_validate');
    add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
    add_settings_section('plugin_conexion', 'Tipo de Conexion', 'HTML_seccionTipoConexion', 'plugin');
    add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
    add_settings_field('id_tipo_conexion', 'Tipo de Conexion a usar :D', 'tipoConexionHTML', 'plugin', 'plugin_conexion');
}

function plugin_section_text() {
    echo '<p>Main description of this section here.</p>';
}

function HTML_seccionTipoConexion() {
    echo '<i>Descripcoon de seccion</i>';
}

function plugin_setting_string() {
    $options = get_option('plugin_options');
    echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

function tipoConexionHTML() {
    $options = get_option('plugin_options');
    echo '<pre>';
    print_r($options);
    echo '</pre>';
    echo "<input id='id_tipo_conexion' name='plugin_options[conexionID]' size='40' type='text' value='{$options['conexionID']}' />";
}

function plugin_options_validate($input) {
    $options = get_option('plugin_options');
    $options['text_string'] = trim($input['text_string']);
    $options['conexionID'] = trim($input['conexionID']);
    /* if (!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
      $options['text_string'] = '';
      } */
    return $options;
}
