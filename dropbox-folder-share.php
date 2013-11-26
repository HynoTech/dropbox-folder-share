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
if (!\class_exists("DropboxFolderSharePrincipal")) {

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
            add_action('admin_menu', array(&$objDFS_Admin, 'pagAdmin'));
            add_action('admin_init', array(&$objDFS_Admin, 'plugin_admin_init'));
            
            $this->actualizarOpcAntiguas();
        }

        public function asignar_variables_estaticas() {
            self::$basename = plugin_basename(__FILE__);
            self::$nombre = dirname(self::$basename);
            self::$url = plugin_dir_url(__FILE__);
            self::$url_path = plugin_dir_path(__FILE__);
        }

        function actualizarOpcAntiguas() {
            if (get_option('db_fs_hyno_show')) {
                $estado = (get_option('db_fs_hyno_show') != 'lista') ? TRUE : FALSE;
                $showIcons = (get_option('db_fs_hyno_icons') == '1') ? TRUE : FALSE;
                $showSize = (get_option('db_fs_hyno_size') == '1') ? TRUE : FALSE;
                $showChange = (get_option('db_fs_hyno_changed') == '1') ? TRUE : FALSE;
                $tipoConexion = get_option('db_fs_hyno_conexion');

                $this->opcDefault = array(
                    "estado" => $estado,
                    "showIcons" => $showIcons,
                    "showSize" => $showSize,
                    "showChange" => $showChange,
                    "allowDownload" => FALSE,
                    "link2Folder" => TRUE,
                    "tipoConexion" => $tipoConexion
                );

                delete_option("db_fs_hyno_show");
                delete_option("db_fs_hyno_icons");
                delete_option("db_fs_hyno_size");
                delete_option("db_fs_hyno_changed");
                delete_option("db_fs_hyno_conexion");
                delete_option("db_fs_hyno_link");
            }
        }

    }

    $objDropboxFolderSharePrincipal = new DropboxFolderSharePrincipal;
}
