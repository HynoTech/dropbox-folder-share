<?php

/**
 * Plugin Name: DropBox Folder Share
 * Plugin URI: http://www.hynotech.com/wp-plugins/dropbox-folder-share/
 * Description: Plugin que permitira incluir carpetas de DropBox en nuestras entradas de blog.
 * Version: 1.3.2
 * Author: Antonio Salas (Hyno)
 * Author URI: http://www.hynotech.com/
 * License:     GNU General Public License
 */
if (!\class_exists("DropboxFolderSharePrincipal")) {

    Class DropboxFolderSharePrincipal {

        const _VERSION_GENERAL_ = "1.3.2";
        const _VERSION_JS_ = "1.3";
        const _VERSION_CSS_ = "1.3";
        const _VERSION_ADMIN_ = "1.3";
        const _VERSION_CSS_DROPBOX_ = "1.0";
        
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
            "SeeAs" => "lista",
            "showIcons" => '1',
            "showSize" => '1',
            "showChange" => '1',
            "allowDownload" => '1',
            "link2Folder" => '1',
            "tipoConexion" => 'fopen'
        );

        public function __construct() {
            include_once 'class/admin.class.php';
            $this->asignar_variables_estaticas();
            load_plugin_textdomain(self::$nombre, false, self::$nombre . '/languages/');

            $objDFS_Admin = new DFS_Admin;
            add_action('admin_menu', array(&$objDFS_Admin, 'pagAdmin'));
            add_action('admin_init', array(&$objDFS_Admin, 'plugin_admin_init'));

            add_filter('plugin_action_links_' . self::$basename, array(&$this, 'add_settings_link'), 10, 2);

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
                $estado = (get_option('db_fs_hyno_show') != 'lista') ? 'lista' : 'iconos';
                $showIcons = (get_option('db_fs_hyno_icons') == '1') ? '1' : '';
                $showSize = (get_option('db_fs_hyno_size') == '1') ? '1' : '';
                $showChange = (get_option('db_fs_hyno_changed') == '1') ? '1' : '';
                $tipoConexion = get_option('db_fs_hyno_conexion');

                $this->opcDefault = array(
                    "SeeAs" => $estado,
                    "showIcons" => $showIcons,
                    "showSize" => $showSize,
                    "showChange" => $showChange,
                    "allowDownload" => '',
                    "link2Folder" => '1',
                    "tipoConexion" => $tipoConexion
                );

                delete_option("db_fs_hyno_show");
                delete_option("db_fs_hyno_icons");
                delete_option("db_fs_hyno_size");
                delete_option("db_fs_hyno_changed");
                delete_option("db_fs_hyno_conexion");
                delete_option("db_fs_hyno_link");
            }
            if (get_option(self::_OPT_SEETINGS_) == null) {
                update_option(self::_OPT_SEETINGS_, $this->opcDefault);
            }
        }

        function replace_shortcode($atts) {
            // set defaults 
            $opciones = get_option(self::_OPT_SEETINGS_);
            extract(shortcode_atts(array(
                'link' => 'https://www.dropbox.com/sh/8ifs95x8qgcaf71/1TCmt_bBy1',
                'ver_como' => $opciones['SeeAs']
                            ), $atts));
            return $this->get_folder($link, $ver_como);
        }

        function formatFileNames($name) {
            //"Lista de Precio - Convenio Marco Diciembre.pdf"
            $delimitador = '\\';
            $n_partes = substr_count($name, $delimitador);

            if ($n_partes > 0) {
                $partes = explode($delimitador, $name);
                foreach ($partes as $idx => $p) {
                    $p_caracter = substr($p, 0, 1);
                    if ($p_caracter == 'x') {

                        $hex_code = substr($p, 0, 3);
                        $n_hex_code = $delimitador . $hex_code;
                        $char = chr(hexdec($n_hex_code) * 1);
                        $partes[$idx] = str_replace($hex_code, $char, $p);
                    }
                }
                $retorno = implode('', $partes);
            } else {
                $retorno = $name;
            }

            return $retorno;
        }

        function fetch_url($url) {
            $opcion = get_option(self::_OPT_SEETINGS_);
            switch ($opcion['tipoConexion']) {
                case "curl":
                    if (function_exists("curl_init")) {
                        if (!class_exists("Curl")) {
                            include "class/Curl.class.php";
                        }
                        $txtLocale = str_replace("_", "-", get_locale());
                        $curl = new Curl();
                        $curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
                        $curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);
                        $curl->setHeader('Accept-Language', $txtLocale);
                        $curl->get($url);

                        return $curl->response;
                    } else {
                        return "NADA";
                    }
                    break;
                case "fopen": // falls through
                default:
                    return ($fp = fopen($url, 'r')) ? stream_get_contents($fp) : false;
                    break;
            }

            return false;
        }

        function get_folder($link, $ver_como = '') {
            $opcion = get_option(self::_OPT_SEETINGS_);

            $url = $link;
            $content = $this->fetch_url($url);
            
            $ver_como = ($ver_como == '') ? $opcion['SeeAs'] : $ver_como;

            if ($content != "") {
                $htmlCode = str_get_html($content);
                $e = $htmlCode->find('body', 0);
                if ($e) {
                    $div_contenedor = str_get_html($e->innertext);
                    foreach ($div_contenedor->find('textarea') as $tag_div_footnotes) {
                        $tag_div_footnotes->outertext = '';
                    }
                    // - Obtener Datos de cada archivo - //
                    $script_nombres = $div_contenedor->find('script', -1);
                    $inicio = strpos($script_nombres, '$(');
                    $fin = strpos($script_nombres, 'window.c2d_tabs', $inicio);
                    $cadena = substr($script_nombres, $inicio, $fin - $inicio);
                    //echo '<textarea>'.$cadena."</textarea>";
                    $data_lineas = explode('.escapeHTML();', $cadena);
                    if (count($data_lineas) > 1) {

                        //echo "<textarea cols=80 rows=10>";
                        foreach ($data_lineas as $links) {
                            //'$('emsnippet-321be91d24995cbc').innerHTML = 'E01'.em_snippet(50, 0.750)'
                            $links = trim($links);
                            //echo $links."<br />";
                            $data_link = explode('=', $links);
                            //echo var_dump($data_link);
                            // - Id de Archivo - //
                            $es_carpeta = 1;
                            if ($data_link[0] != "") {
                                $num_car = strlen($data_link[0]);

                                /* id data */
                                //$(JSON.parse("\"emsnippet-429621644f00fe1d\"")).innerHTML = JSON.parse("\"Menus\"").em_snippet(50, 0.750000)
                                //$("emsnippet-9a640cc1d4125c83").innerHTML = "readme.html".em_snippet(40, 0.750000)
                                $patron_id = '|\"(.*?)\"|is';
                                $patron_nombre = '|\"(.*?)\"|is';
                                preg_match($patron_id, $data_link[0], $idArchivo);
                                //print_r($idArchivo[0]);//."\n";
                                preg_match($patron_nombre, $data_link[1], $nombreArchivo);
                                //print_r($nombreArchivo[0]);//."\n";
                                
                                $idArchivo = str_replace('"', '', $idArchivo[0]);
                                $nombreArchivo =  str_replace('"', '', $nombreArchivo[0]);
                                $file_data_A[$idArchivo] = $nombreArchivo;
                            }
                        }
                        $file_data_A = array_unique($file_data_A);
                        /*
                        foreach ($file_data_A as $idxNombre => $valNombre) {
                            //echo $idxNombre . '=>'.$valNombre.'<br />';
                                    $tam_nombre = strlen($this->formatFileNames($valNombre));
                                    if ($tam_nombre > 16) {
                                        $nnnn = substr($this->formatFileNames($valNombre), 0, 16) . '...';
                                    } else {
                                        $nnnn = $this->formatFileNames($valNombre);
                                    }
                                    $file_data_A[$idxNombre] = $nnnn;
                        }
                        foreach ($file_data_A as $idxNombre => $valNombre) {
                            echo $idxNombre . '=>'.$valNombre.'<br />';
                        }
                        */

                        foreach ($div_contenedor->find('
                            script,
                            div.buttons,
                            div#top-bar,
                            noscript,
                            a.content-flag,
                            ol#gallery-view-media,
                            ol#gallery-view-folders,
                            div#c2d-modal,
                            div#file-preview-modal,
                            div#db-modal-locale-selector-modal,
                            div[style^=display:none],
                            div[id^=sharing-],
                    #modal-progress-content, 
                    #twitter-login, 
                    #facebook-auth, 
                    #twitter-posting, 
                    #facebook-posting, 
                    #disable-token-modal,
                    #album-disable-token-modal,
                    #gallery-view-container
                    ') as $txt_version) {
                            $txt_version->outertext = '';
                        }
                        foreach ($div_contenedor->find('div#outer-frame') as $txt_version) {
                            $div_contenedor = str_get_html($txt_version->outertext);
                        }

                        $data_all_files = array();
                        //echo "<textarea cols=80 rows=10>".$div_contenedor."</textarea>";
                        foreach ($div_contenedor->find('li[class=browse-file]') as $archivos) {

                            foreach ($archivos->find('div[class=filename] span') as $file_names) {
                                foreach ($file_data_A as $key => $value) {
                                    //echo $key.'->>>'.$value.'<br>';
                                    //echo $file_names->id.'<br>';
                                    if ($key == $file_names->id) {
                                        $tam_nombre = strlen($this->formatFileNames($value));
                                        if ($tam_nombre > 20) {
                                            $nnnn = ($ver_como != 'lista')?substr($this->formatFileNames($value), 0, 20) . '...' :$this->formatFileNames($value);
                                        } else {
                                            $nnnn = $this->formatFileNames($value);
                                        }
                                        $file_names->innertext = $nnnn;
                                    }
                                }
                            }//echo "<textarea cols=80 rows=10>".$archivos."</textarea>";
                            foreach ($archivos->find('div[class=filename] a') as $datos) {
                                if ($opcion['allowDownload'] == "1") {
                                    $data_all_files['link'][] = str_replace("https://www", "https://dl", $datos->href);
                                } else {
                                    $data_all_files['link'][] = $datos->href;
                                }
                            }
                            foreach ($archivos->find('a img') as $datos) {
                                $data_all_files['icon_class'][] = $datos->class;
                            }
                            foreach ($archivos->find('div[class=filename] span') as $datos) {
                                $id_nombre = $datos->id;
                                $data_all_files['id'][] = $datos->id;
                            }
                            foreach ($archivos->find('span[id=' . $id_nombre . ']') as $datos) {
                                $data_all_files['nombre'][] = eregi_replace('\"', '', $datos->innertext);
                                //$id_archivo = eregi_replace('\"','',$this->formatFileNames($id_archivo));
                                //echo $datos->innertext;
                            }
                            foreach ($archivos->find('div[class=filesize-col] span') as $datos) {
                                $data_all_files['peso'][] = $datos->innertext;
                            }
                            foreach ($archivos->find('div[class=modified-col] span[!class]') as $datos) {
                                $data_all_files['modificado'][] = $datos->innertext;
                            }
                        }

                        $headersCarpeta = $div_contenedor->find('div#list-view-header', 0)->outertext;

                        $txtCarpeta = '<span id="folder-title" class="shmodel-filename header_1">';
                        reset($file_data_A);
                        $dataCarpetaPrincipal = each($file_data_A);
                        if ($opcion['link2Folder'] === '1') {
                            $txtCarpeta .= '<a href="' . $link . '" target="_blank">';
                            $txtCarpeta .= 'Dropbox://<span id="' . $dataCarpetaPrincipal[key] . '">' .$dataCarpetaPrincipal[value] . '</span>';
                            $txtCarpeta .= '</a>';
                        } else {
                            $txtCarpeta .= 'Dropbox://<span id="' . $dataCarpetaPrincipal[key] . '">' . $dataCarpetaPrincipal[value] . '</span>';
                        }
                        $txtCarpeta .= '</span>';
                        //echo '<pre>';
                        //print_r ($data_all_files);
                        //echo '</pre>';
                        //echo "<textarea cols=80 rows=10>" . $headersCarpeta . "</textarea>";
                        //echo var_dump($data_all_files);
                        $txtContenedor[0] = '<div id="Hyno_ContenFolder">';
                        $txtContenedor[0] .= '<div class="nav-header"><div id="icon_folder"></div>';
                        $txtContenedor[0] .= $txtCarpeta;
                        $txtContenedor[0] .= '</div>';
                        $txtContenedor[0] .= '<div style="" id="list-view-container" class="gallery-view-section">';

                        $txtContenedor[1] = '</div>';
                        $txtContenedor[1] .= '</div>';

                        $txtLista[0] = $headersCarpeta . '<ol class="browse-files gallery-list-view">';
                        $txtLista[1] = '</ol>';
                        $txtIconos[0] = '';
                        $txtIconos[1] = '';
                        foreach ($data_all_files['link'] as $key => $value) {
                            if (strrpos($data_all_files['icon_class'][$key], "s_web_folder_") !== FALSE) {
                                $value = str_replace("https://dl", "https://www", $value);
                            }
                            $txtLista[0] .= '<li class="browse-file list-view-cols" ' . (($opcion['showIcons'] === '1') ? '' : 'style="line-height: 19px !important;" ') . '>';
                            if ($opcion['showIcons'] === '1') {
                                $txtLista[0] .= '<div class="filename-col">';
                                $txtLista[0] .= '<a href="' . $value . '" target="_blank" class="thumb-link" onclick="" rel="nofollow">';
                                $txtLista[0] .= '<img src="' . self::$url . '/img/icon_spacer.gif" style="" class="' . $data_all_files['icon_class'][$key] . '" alt="">';
                                $txtLista[0] .= '</a>';
                            } else {
                                $txtLista[0] .= '<div class="filename-col">';
                            }
                            $txtLista[0] .= '<div class="filename"><a href="' . $value . '" target="_blank" class="filename-link" onclick="" rel="nofollow">';
                            $txtLista[0] .= '<span id="' . $data_all_files['id'][$key] . '">' . $data_all_files['nombre'][$key] . '</span></a></div>';
                            $txtLista[0] .= '</div>';
                            if ($opcion['showSize'] === '1') {
                                $txtLista[0] .= '<div class="filesize-col"><span class="size">' . $data_all_files['peso'][$key] . '</span></div>';
                                $SizeIcon = $data_all_files['peso'][$key];
                            } else {
                                $txtLista[0] .= '<div class="filesize-col"><span class="size"> -- </span></div>';
                                $SizeIcon = '';
                            }
                            if ($opcion['showChange'] === '1') {
                                $txtLista[0] .= '<div class="modified-col"><span><span class="modified-time">' . $data_all_files['modificado'][$key] . '</span></span></div>';
                            } else {
                                $txtLista[0] .= '<div class="modified-col"><span><span class="modified-time"> -- </span></span></div>';
                            }

                            $txtLista[0] .= '<br class="clear">';
                            $txtLista[0] .= '</li>';
                            $txtIconos[0] .= '<div class="filename-col iconos">'
                                    . '<a href="' . $value . '" target="_blank" class="thumb-link" title="' . $SizeIcon . '" rel="nofollow">';
                            $txtIconos[0] .= '<img src="' . self::$url . '/img/icon_spacer.gif" style="" class="' . $data_all_files['icon_class'][$key] . '" alt=""></a>';
                            $txtIconos[0] .= '<div class="filename"><a href="' . $value . '" target="_blank" class="filename-link" onclick="" rel="nofollow">';
                            $txtIconos[0] .= '<span id="' . $data_all_files['id'][$key] . '">' . $data_all_files['nombre'][$key] . '</span></a>';
                            $txtIconos[0] .= '</div>';
                            $txtIconos[0] .= '</div>';
                        }
                        
                        if ($ver_como === 'lista') {
                            //$retorno = $txtContenedor[0].$txtLista[0].$txtLista[1].$txtContenedor[1];
                            $retorno = $txtContenedor[0] . $txtLista[0] . $txtLista[1] . $txtContenedor[1];
                        } else {
                            $retorno = $txtContenedor[0] . $txtIconos[0] . $txtIconos[1] . $txtContenedor[1];
                        }
                    } else {
                        $retorno = '<div id="Hyno_ContenFolder"><div class="nav-header">
                        <div id="icon_folder"></div>
                        <span id="folder-title" class="shmodel-filename header_1"><span style="color: red;font-weight: black;">Error</span>://<span id="ERROR"><span style="color: red;font-style: italic; font-weight: lighter;">' . _e('No se puede leer carpeta compartida', self::$nombre) . '</span></span></span>
                        </div>
						</div>';
                    }
                    return $retorno;
                } else {
                    $verse = __("No podemos Revisar ", self::$nombre) . urldecode($lookup) . " ($url).";
                }
            } else {
                $verse = __("No encontrado", self::$nombre);
            }
        }

        function add_settings_link($links, $file) {
            if (self::$basename === $file && current_user_can(self::_PERMISOS_REQUERIDOS_)) {
                $links[] = '<a href="' . esc_url($this->plugin_options_url()) . '" alt="' . esc_attr__('Dropbox Folder Share - Configuracion', self::$nombre) . '">' . esc_html__('Configurar', self::$nombre) . '</a>';
            }
            return $links;
        }

        function plugin_options_url() {
            return add_query_arg('page', self::$nombre, admin_url(self::_PARENT_PAGE_));
        }

    }

    $objDropboxFolderSharePrincipal = new DropboxFolderSharePrincipal;
    if (!function_exists("file_get_html")) {
        include_once('class/simple_html_dom.php');
    }
    if (!class_exists("DFS_TinyMCE")) {
        include_once 'class/tinymce.class.php';
        $objDFS_TinyMCE = new DFS_TinyMCE();
        add_filter("mce_css", array(&$objDFS_TinyMCE, "dropboxfoldershare_plugin_mce_css"));
        add_filter("mce_external_plugins", array(&$objDFS_TinyMCE, "dropboxfoldershare_register_button"));
        add_filter("mce_buttons", array(&$objDFS_TinyMCE, "dropboxfoldershare_add_button"), 0);
        add_filter("the_posts", array(&$objDFS_TinyMCE, "dropbox_foldershare_styles_and_scripts"));
    }
        
    add_shortcode('dropbox-foldershare-hyno', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));
    add_shortcode('DFS', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));
}
