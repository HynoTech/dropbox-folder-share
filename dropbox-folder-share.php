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
            if (get_option(self::_OPT_SEETINGS_) == null){
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
                        //echo "<B>".$partes[$idx]."</B>";
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
            /*switch ($opcion['tipoConexion']) {
                case "curl":
                    echo '<h1>HHHHHHHHH'.$opcion['tipoConexion'].'HHHHHHHHHH</h1>';
                    if (function_exists("curl_init")) {*/
                        if (!class_exists("Curl")) {
                            include "class/Curl.class.php";
                            
                    $txtLocale = str_replace("_", "-", get_locale());
                            $curl = new Curl();
                            $curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
                            $curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);
                            $curl->setHeader('Accept-Language', $txtLocale);
                            $curl->get($url);
                        }
                        return $curl->response;
                    /*} else {
                        return "NADA";
                    }
                    break;
                case "fopen": // falls through
                default:
                    return ($fp = fopen($url, 'r')) ? stream_get_contents($fp) : false;
                    break;
                
            }*/

            return false;
        }

        function get_folder($link, $ver_como = '') {
            $url = $link;
            $content = $this->fetch_url($url);
            echo "<textarea cols=80 rows=10>" . $content . "</textarea>";
            //echo get_locale();
            if ($content != "") {
                $htmlCode = str_get_html($content);
                $e = $htmlCode->find('body', 0);
//div[id=list-view-container]
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
                    $data_lineas = explode('.escapeHTML();', $cadena);
                    if (count($data_lineas) > 1) {

                        $file_data = array();
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
                                $inicio_id_archivo = 'on(';
                                $fin_id_archivo = ').';

                                //echo strpos($data_link[0], $inicio_id_archivo).'<br>';
                                //echo strpos($data_link[0], $fin_id_archivo).'<br>';

                                $total = strpos($data_link[0], $inicio_id_archivo) + 4;
                                $total2 = strpos($data_link[0], $fin_id_archivo);
                                $total3 = ($num_car - $total2 + 2);
                                $id_archivo = substr($data_link[0], $total + 1, -$total3);
                                $id_archivo = eregi_replace('\"', '', $this->formatFileNames($id_archivo));
                                //echo $id_archivo."<br />";
                                // - Nombre de Archivo - //
                                $num_car = strlen($data_link[1]);
                                $inicio_nombre_archivo = "on(";
                                $fin_nombre_archivo = ").";
                                $total = strpos($data_link[1], $inicio_nombre_archivo) + 6;
                                $total2 = strpos($data_link[1], $fin_nombre_archivo);
                                //echo $total2."<br />";
                                $total3 = ($num_car - $total2 + 3 );
                                $nombre_archivo = substr($data_link[1], $total, -$total3);
                                //echo $nombre_archivo."<br />";
                                $tam_nombre = strlen($this->formatFileNames($nombre_archivo));
                                if ($tam_nombre > 16) {
                                    $nnnn = substr($this->formatFileNames($nombre_archivo), 0, 16) . '...';
                                } else {
                                    $nnnn = $this->formatFileNames($nombre_archivo);
                                }
                                if ($es_carpeta > 0) {
                                    $nnnn = $this->formatFileNames($nombre_archivo);
                                    $es_carpeta = 0;
                                }


                                $file_data['id'][] = $this->formatFileNames($id_archivo);
                                $file_data['nombre'][] = $nnnn;
                            }
                        }

                        foreach ($div_contenedor->find('script') as $scripts) {
                            $scripts->outertext = '';
                        }

                        $data_all_files = array();
                        foreach ($div_contenedor->find('li[class=browse-file]') as $archivos) {

                            foreach ($archivos->find('div[class=filename] span') as $file_names) {
                                foreach ($file_data['id'] as $key => $value) {
                                    //echo $key.'->>>'.$value.'<br>';
                                    //echo $file_names->id.'<br>';
                                    if ($file_names->id == $value) {
                                        $file_names->innertext = $file_data['nombre'][$key];
                                    }
                                }
                            }//echo "<textarea cols=80 rows=10>".$file_names."</textarea>";
                            foreach ($archivos->find('div[class=filename] a') as $datos) {
                                $data_all_files['link'][] = $datos->href;
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
                        //echo var_dump($data_all_files);
                        $print_first = '<div id="Hyno_ContenFolder"><div class="nav-header">
                        <div id="icon_folder"></div>
                        <span id="folder-title" class="shmodel-filename header_1">Dropbox://<span id="' . $file_data['id'][0] . '">' . eregi_replace('\"', '', $file_data['nombre'][0]) . '</span></span>
                        </div>
<div style="" id="list-view-container" class="gallery-view-section">
';
                        $print_list = '
  <div id="list-view-header" class="list-view-cols clearfix">
    <div class="filename-col">Nombre</div>
    <div class="filesize-col">Tamaño</div>
    <div class="modified-col">Modificado</div>
  </div>
  <ol class="browse-files gallery-list-view">';
                        foreach ($data_all_files['link'] as $key => $value) {
                            $print_list .= '
    <li class="browse-file list-view-cols">
      <div class="filename-col"><a href="' . $value . '" target="_blank" class="thumb-link" onclick="" rel="nofollow">
          <img src="' . plugins_url("dropbox-folder-share") . '/img/icon_spacer.gif" style="" class="' . $data_all_files['icon_class'][$key] . '" alt=""></a>
        <div class="filename"><a href="' . $value . '" target="_blank" class="filename-link" onclick="" rel="nofollow">
            <span id="' . $data_all_files['id'][$key] . '">' . $data_all_files['nombre'][$key] . '</span></a></div>
      </div>
      <div class="filesize-col"><span class="size">' . $data_all_files['peso'][$key] . '</span></div>
      <div class="modified-col"><span><span class="modified-time">' . $data_all_files['modificado'][$key] . '</span></span></div>
      <br class="clear">
    </li>';
                        }
                        $print_list .= '</ol>';
                        $print_last = '

  
</div></div>';

                        $print_icons = '';
                        foreach ($data_all_files['link'] as $key => $value) {
                            $print_icons .= '
<div class="filename-col iconos"><a href="' . $value . '" target="_blank" class="thumb-link" onclick="" rel="nofollow">
    <img src="' . plugins_url("dropbox-folder-share") . '/img/icon_spacer.gif" style="" class="' . $data_all_files['icon_class'][$key] . '" alt=""></a>
    <div class="filename"><a href="' . $value . '" target="_blank" class="filename-link" onclick="" rel="nofollow">
        <span id="' . $data_all_files['id'][$key] . '">' . $data_all_files['nombre'][$key] . '</span></a>
            </div>
</div>
                    ';
                        }
                        //get_option('db_fs_hyno_show')
                        if ($ver_como == 'lista') {
                            $retorno = $print_first . $print_list . $print_last;
                        } else {
                            $retorno = $print_first . $print_icons . $print_last;
                        }
                    } else {
                        $retorno = '<div id="Hyno_ContenFolder"><div class="nav-header">
                        <div id="icon_folder"></div>
                        <span id="folder-title" class="shmodel-filename header_1"><span style="color: red;font-weight: black;">Error</span>://<span id="ERROR"><span style="color: red;font-style: italic; font-weight: lighter;">' . _e('No se puede leer carpeta compartida', 'dropbox-folder-share') . '</span></span></span>
                        </div>
						</div>';
                    }
                    return $retorno;
                } else {
                    $verse = __("No podemos Revisar ", 'dropbox-folder-share') . urldecode($lookup) . " ($url).";
                }
            } else {
                $verse = __("No encontrado", 'dropbox-folder-share');
            }
        }
    }

    
    
    $objDropboxFolderSharePrincipal = new DropboxFolderSharePrincipal;
    
if (!function_exists("file_get_html")) {
    include_once('class/simple_html_dom.php');
}

function dropboxfoldershare_add_button($buttons) {
    array_push($buttons, "|", "DropBoxFolderShare");
    return $buttons;
}

function dropboxfoldershare_register_button($plugin_array) {
    //$url_biblepost = WP_PLUGIN_URL . "/dropbox-folder-share/script/DropBoxFolderShare.js";
    $url_biblepost = plugins_url('/script/DropBoxFolderShare.js', __FILE__);
    $plugin_array['DropBoxFolderShare'] = $url_biblepost;
    return $plugin_array;
}

function dropboxfoldershare_plugin_mce_css($mce_css) {
    if (!empty($mce_css))
        $mce_css .= ',';

    $mce_css .= plugins_url('styles-hyno.css', __FILE__);

    return $mce_css;
}
if (!function_exists('dropbox_foldershare_styles_and_scripts')) {

    function dropbox_foldershare_styles_and_scripts($posts) {
        if (empty($posts))
            return $posts;
        $shortcode_found = false; // usamos shortcode_found para saber si nuestro plugin esta siendo utilizado
        foreach ($posts as $post) {

            if (stripos($post->post_content, 'dropbox-foldershare-hyno')) { //shortcode a buscar
                $shortcode_found = true; // bingo!
                break;
            }

            if (stripos($post->post_content, 'hyno_learn_more')) { //cambiamos testiy por cualquier shortcode
                $shortcode_found = true; // bingo!
                break;
            }
        }
        if ($shortcode_found) {
            // enqueue
            wp_enqueue_script('jquery');
            wp_enqueue_style('bible-post-style', plugins_url("dropbox-folder-share/") . 'styles-hyno.css'); //la ruta de nuestro css
            wp_enqueue_script('bible-post-script', plugins_url('scripts-hyno.js', __FILE__)); //en caso de necesitar la ruta de nuestro script js
        }

        return $posts;
    }

}
add_filter('mce_css', 'dropboxfoldershare_plugin_mce_css');



    add_filter('mce_external_plugins', "dropboxfoldershare_register_button");
    add_filter('mce_buttons', 'dropboxfoldershare_add_button', 0);
    add_filter('the_posts', 'dropbox_foldershare_styles_and_scripts'); // the_posts es lanzando antes que el header
    // administrative options
    //add_action('admin_menu', array(&$objDropboxFolderSharePrincipal, 'add_admin_page'));
    // shortcodes
    add_shortcode('dropbox-foldershare-hyno', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));
    // activate/deactivate
    //register_activation_hook(__file__, array(&$objDropboxFolderSharePrincipal, 'activate'));
    //register_deactivation_hook(__file__, array(&$objDropboxFolderSharePrincipal, 'deactivate'));
}
