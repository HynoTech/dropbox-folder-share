<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 20/03/2017
 * Time: 23:33
 */

namespace HynoTech;


class WpPlugin{

    public static $basename; //Indica el directorio hasta el archivo actual "dropbox-folder-share/DropboxFolderShare.php"
    public static $nombre; //Nombre de la carpeta "dropbox-folder-share"
    public static $url; //URL completa dela carpeta actual "http://localhost:8080/wp/wp-content/plugins/dropbox-folder-share/"
    public static $url_path; //URL completa dela carpeta actual "d:\Projects\Hosts\wordpress\wp-content\plugins\dropbox-folder-share/"
    var $formSections = array();
    var $settings = array(); //Almacena los opciones actuales del Plugin
    var $opcDefault = array();



}