<?php

Class DropboxFolderShare2{
    public static $basename;//Indica el directorio hasta el archivo actual "dropbox-folder-share/class/DropboxFolderShare.php"
    public static $nombre;//Nombre de la carpeta "dropbox-folder-share"
    public static $url;
    
    
    public function __construct() {
        //echo self::$nombre;
        $this->asignar_variables_estaticas();
    }
    public function asignar_variables_estaticas(){
        self::$basename = plugin_basename( __FILE__ );
        self::$nombre = dirname(dirname( self::$basename ));
        self::$url = plugin_dir_url( __FILE__ );
    }
    public function verCarpeta(){
        echo self::$url;
        echo '<br />';
    }
}
?>
