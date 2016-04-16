<?php

/**
 * Plugin Name: DropBox Folder Share
 * Plugin URI: http://www.hynotech.com/wp-plugins/dropbox-folder-share/
 * Description: Plugin que permitira incluir carpetas de DropBox en nuestras entradas de blog.
 * Version: 1.6.1
 * Author: Antonio Salas (Hyno)
 * Author URI: http://www.hynotech.com/
 * Twitter: _AntonySalas_
 * GitHub URI: https://github.com/HynoTech/dropbox-folder-share
 * Text Domain: dropbox-folder-share
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
if (!\class_exists("DropboxFolderSharePrincipal")) {

    Class DropboxFolderSharePrincipal
    {

        const _VERSION_GENERAL_ = "1.6.1";
        const _VERSION_JS_ = "1.6";
        const _VERSION_CSS_ = "1.6";
        const _VERSION_ADMIN_ = "2.0.1";
        const _VERSION_CSS_DROPBOX_ = "2.0";

        const _PARENT_PAGE_ = "options-general.php";
        const _OPT_SEETINGS_ = "dropbox-folder-share-options";
        const _PERMISOS_REQUERIDOS_ = 'manage_options';

        public static $basename; //Indica el directorio hasta el archivo actual "dropbox-folder-share/DropboxFolderShare.php"
        public static $nombre; //Nombre de la carpeta "dropbox-folder-share"
        public static $url; //URL completa dela carpeta actual "http://localhost:8080/wp/wp-content/plugins/dropbox-folder-share/"
        public static $url_path; //URL completa dela carpeta actual "d:\Projects\Hosts\wordpress\wp-content\plugins\dropbox-folder-share/"
        var $formSections = array();
        var $settings = array(); //Almacena los opciones actuales del Plugin
        var $opcDefault = array(
            "UseAjax" => '1',
            "SeeAs" => "lista",
            "showIcons" => '1',
            "showSize" => '1',
            "showChange" => '1',
            "allowDownload" => '1',
            "allowDownloadFolder" => '1',
            "imagesPopup" => '1',
            "link2Folder" => '1',
            "tipoConexion" => 'fopen'
        );

        public function __construct()
        {
            include_once 'class/admin.class.php';
            $this->asignar_variables_estaticas();
            load_plugin_textdomain("dropbox-folder-share", false, "dropbox-folder-share" . '/languages/');

            $objDFS_Admin = new DFS_Admin;
            add_action('admin_menu', array(&$objDFS_Admin, 'pagAdmin'));
            add_action('admin_init', array(&$objDFS_Admin, 'plugin_admin_init'));

            add_filter('plugin_action_links_' . self::$basename, array(&$this, 'add_settings_link'), 10, 2);

            $this->actualizarOpcAntiguas();
        }

        public function asignar_variables_estaticas()
        {
            self::$basename = plugin_basename(__FILE__);
            self::$nombre = dirname(self::$basename);
            self::$url = plugin_dir_url(__FILE__);
            self::$url_path = plugin_dir_path(__FILE__);
        }

        function actualizarOpcAntiguas()
        {
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

        function ajaxReplaceShortcode($atts){

            if (!isset($_POST['dfs_nonce']) || !wp_verify_nonce($_POST['dfs_nonce'],'dfs_nonce'))
                die(__("Error de seguridad", "dropbox-folder-share"));

            if ( !isset($_POST['link']) || !isset($_POST['ver_como']))
                die( __("Error de parametros", "dropbox-folder-share") );

            // set defaults
            $opciones = get_option(self::_OPT_SEETINGS_);
            extract(shortcode_atts(array(
                'link' => $_POST['link'],
                'ver_como' => $_POST['ver_como']
            ), $atts));

            $idContent = $_POST['idContent'];
            $titleBar = $_POST["titleBar"];

            echo $this->get_folder($link, $ver_como, $idContent, $titleBar);
            die();
        }

        function scriptAjax($link, $ver_como,$idContent){
            //$idContent = "DFS".rand(1,99999);
            $url_imgLoader = self::$url."/img/gears.svg";

            $regresarScript = "<div id='$idContent'>";
            //$regresarScript .= "<div class=\"loader\">Loading...</div>";
            $regresarScript .= "<div style='text-align: center'><img src=\"{$url_imgLoader}\"></div>";
            $regresarScript .= "</div>";
            $regresarScript .= "<script>";
            $regresarScript .= "loadContenDFS('$link', '$ver_como', '$idContent')";
            $regresarScript .= "</script>";
            return $regresarScript;
        }

        function replace_shortcode($atts)
        {
            $idContent = "DFS".rand(1,99999);
            // set defaults
            $opciones = get_option(self::_OPT_SEETINGS_);
            extract(shortcode_atts(array(
                'link' => 'https://www.dropbox.com/sh/8ifs95x8qgcaf71/1TCmt_bBy1',
                'ver_como' => $opciones['SeeAs']
            ), $atts));


            if ($opciones['UseAjax'] === '1'){
                return $this->scriptAjax($link, $ver_como, $idContent);
            }
            else{
                return $this->get_folder($link, $ver_como, $idContent);
            }

            //

        }

        function fetch_url($url)
        {
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

        function get_folder($link, $ver_como = '', $id_content = null, $titleBar = null)
        {
            $opcion = get_option(self::_OPT_SEETINGS_);

            $url_data = $link;
            $content = $this->fetch_url($url_data);

            $ver_como = ($ver_como == '') ? $opcion['SeeAs'] : $ver_como;

            if ($content != "") {

                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                //$dom->loadHTMLFile($content);
                //$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
                libxml_use_internal_errors(false);
                $dom->preserveWhiteSpace = false;


                $body = $dom->getElementsByTagName('body');
                //echo $body->length;
                //echo "<textarea>".$body->item(0)->ownerDocument->saveHTML()."</textarea>";


                if ($body->length > 0) {

                    foreach( $dom->getElementsByTagName('meta') as $meta ) {
                        if($meta->getAttribute('property') != "")
                        $metaData[$meta->getAttribute('property')] = $meta->getAttribute('content');

                    }

                    //var_dump($metaData);


                    $body_txt = $body->item(0)->ownerDocument->saveHTML();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML(mb_convert_encoding($body_txt, 'HTML-ENTITIES', 'UTF-8'));
                    libxml_use_internal_errors(false);

                    $dom->preserveWhiteSpace = false;
                    $titulosDentro = $dom->getElementById('list-view-header');

                    if($titulosDentro){
                        //var_dump($titulosDentro);
                        $lista_archivos = $dom->getElementById('list-view-container');
                        $lista_archivos->setAttribute('style', '');

                        $detalleURL = parse_url($link);

                        $arrayPath = explode("/",$detalleURL['path']);
                        $codeRel = end($arrayPath);

                        $txtTitulosIdioma = $dom->saveHTML($lista_archivos->childNodes->item(0));

                        //echo "<textarea>". ($dom->saveHTML($lista_archivos->childNodes->item(0))); echo "</textarea>";
//die();

                        $txtCarpeta ="";
                        if ($opcion['link2Folder'] === '1') {

                            $txtZip = "";
                            if ($opcion['allowDownloadFolder'] === '1') {

                                $query_params['dl'] = 1;
                                $detalleURL['query'] = http_build_query($query_params);

                                $newUrl = http_build_url($link,
                                    $detalleURL,
                                    HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
                                );


                                $lnkDescarga = $newUrl;

                                $txtZip = '<a href="' . $lnkDescarga . '" target="_blank">';
                                $txtZip .= '<img style="float: right;" src="'. self::$url .'/img/zip.png">';
                                $txtZip .= '</a>';
                            }

                            if($titleBar != NULL){

                                $titleBar = str_replace("\\","",$titleBar);


                                $doc = new \DOMDocument();
                                $doc->loadHTML(mb_convert_encoding($titleBar, 'HTML-ENTITIES', 'UTF-8'));


                                $aElement = $doc->getElementsByTagName('span');
                                $aData = $doc->getElementsByTagName('div');


                                $elementsToDelete = array();
                                $elimActivado = false;
                                for ($i = 0; $i < $aElement->length; $i++){

                                    if($elimActivado){
                                        $elementsToDelete[] = $aElement->item($i);
                                    }
                                    $nomVinculo = $aElement->item($i)->childNodes->item(0);
                                    $icoVinculo = $aElement->item($i)->childNodes->item(1);

                                    if( ( $nomVinculo->nodeValue != "" ) && ( $nomVinculo->nodeValue == $metaData["og:title"] ) && ( $nomVinculo->getAttribute('href') == $link ) ) {
                                        $elimActivado = true;
                                    }
                                }

                                foreach ( $elementsToDelete as $elementToDelete ) {
                                    $elementToDelete->parentNode->removeChild($elementToDelete);
                                }

                                if(!$elimActivado){
                                    $fragInsertHTML = "<span>";
                                    $fragInsertHTML .= "<a href='{$link}' data-titulo='1' onclick=\"loadContenDFS('{$link}', '{$ver_como}', '{$id_content}'); varTitulo = 1; return false;\">";
                                    $fragInsertHTML .= $metaData["og:title"];
                                    $fragInsertHTML .= '</a>';
                                    $fragInsertHTML .= "<a href='{$link}' target='_blank'>";
                                    $fragInsertHTML .= '<img src="'. self::$url .'/img/ico-external-link.png" />';
                                    $fragInsertHTML .= '</a>/';
                                    $fragInsertHTML .= "</span>";

                                    $frag = $doc->createDocumentFragment();
                                    $frag->appendXML($fragInsertHTML);

                                    $aData->item(0)->appendChild($frag);
                                }



                                $txtCarpeta = $doc->saveHTML($aData->item(0));

                            }
                            else{
                                $txtCarpeta .= '<div class="DropboxIcon">://';
                                $txtCarpeta .= "<span>";
                                $txtCarpeta .= "<a href='{$link}' data-titulo='1' onclick=\"loadContenDFS('{$link}', '{$ver_como}', '{$id_content}'); varTitulo = 1; return false;\">";
                                $txtCarpeta .= $metaData["og:title"];
                                $txtCarpeta .= '</a>';
                                $txtCarpeta .= "<a href='{$link}' target='_blank'>";
                                $txtCarpeta .= '<img src="'. self::$url .'/img/ico-external-link.png">';
                                $txtCarpeta .= '</a>/';
                                $txtCarpeta .= "</span>";
                                $txtCarpeta .= '</div>';
                            }


                            $txtCarpeta .= $txtZip;
                        } else {
                            $txtCarpeta .= '<div class="DropboxIcon">://'.$metaData["og:title"].'</div>';
                        }

                        //echo "<textarea>". ($txtCarpeta = $doc->saveHTML($aData->item(0));); echo "</textarea>";


                        $lista_archivos->removeChild($titulosDentro);


                        $txtContenedor[0] = "";
                        $txtContenedor[0] = "<div id='$id_content'>";
                        $txtContenedor[0] .= '<div class="Hyno_ContenFolder">';
                        $txtContenedor[0] .= "  <div id='Hyno_Header_{$id_content}'>";
                        $txtContenedor[0] .= '      '.$txtCarpeta;
                        $txtContenedor[0] .= '  </div>';
                        if ($ver_como == 'lista'){
                            $txtContenedor[0] .= $txtTitulosIdioma;
                        }

                        $txtContenedor[0] .= '';
                        $txtContenedor[1] = '</div>';
                        $txtContenedor[1] .= '</div>';

                        //$lista_archivos->childNodes->item(0)->C14N();
                        $txtIconosDt = '';

                        $olFiles = ($ver_como != 'lista')?'':'<ol class="browse-files gallery-list-view">';
                        foreach($lista_archivos->childNodes->item(0)->childNodes as $childNode){
                            $filename_col = $childNode->childNodes->item(0);

                            $lnkIcono = $filename_col->childNodes->item(0);
                            $lnkFilename = $filename_col->childNodes->item(1)->childNodes->item(0);

                            if ($opcion['allowDownload'] != '1') {
                                $this->DOMRemove($lnkIcono);
                                $this->DOMRemove($lnkFilename);

                            }
                            else{
                                $downloadParam = array("query" => "dl=1");
                                $downloadFlags = HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT;

                                $urlArchivo = $lnkIcono->getAttribute("href");
                                $urlFilename = $lnkFilename->getAttribute("href");

                                //echo "----<pre>"; print_r(pathinfo($urlArchivo)); echo "</pre>---------";
                                $dataArchivo = pathinfo($urlArchivo);
                                if ($opcion['allowBrowseFolder'] == '1') {
                                    if(!isset($dataArchivo['extension'])){
                                        $lnkIcono->setAttribute("onclick","loadContenDFS('{$urlArchivo}', '{$ver_como}', '{$id_content}'); return false;");
                                        $urlArchivo = http_build_url('', [], $downloadFlags);

                                        $lnkFilename->setAttribute("onclick","loadContenDFS('{$urlFilename}', '{$ver_como}', '{$id_content}'); return false;");
                                        $urlFilename = http_build_url('', [], $downloadFlags);
                                    }
                                }


                                $urlArchivo = http_build_url($urlArchivo, $downloadParam, $downloadFlags);
                                $lnkIcono->setAttribute("href",$urlArchivo);

                                //$urlFilename = $lnkFilename->getAttribute("href");
                                $urlFilename = http_build_url($urlFilename, $downloadParam, $downloadFlags);
                                $lnkFilename->setAttribute("href",$urlFilename);

                                if($opcion['imagesPopup'] === '1') {
                                    $partUrl = explode("?",$urlFilename);
                                    $fileInfo = explode("/", wp_check_filetype($partUrl[0])['type']);

                                    if($fileInfo[0] == 'image'){
                                        $lnkIcono->setAttribute("class",$lnkIcono->getAttribute("class")." thickbox");
                                        $lnkIcono->setAttribute("rel", $codeRel);

                                        $lnkFilename->setAttribute("class",$lnkFilename->getAttribute("class")." thickbox");
                                        $lnkFilename->setAttribute("rel", $codeRel."_txt");
                                    }
                                }

                            }

                            if ($opcion['showIcons'] != '1') {
                                $childNode->childNodes->item(0)->removeChild($filename_col->childNodes->item(0));
                            }
                            if ($opcion['showSize'] != '1') {
                                $childNode->childNodes->item(1)->lastChild->nodeValue = " -- ";
                            }
                            if ($opcion['showChange'] != '1') {
                                $childNode->childNodes->item(2)->lastChild->nodeValue = " -- ";
                            }


                            if ($ver_como != 'lista') {
                                //$lnkIcono
                                //$childNode->childNodes->item(0)->childNodes->item(0)->appendChild($lnkIcono);
                                $childNode->removeChild($childNode->childNodes->item(1));
                                $childNode->removeChild($childNode->childNodes->item(1));
                                $childNode->setAttribute("class",$childNode->getAttribute("class"). " iconos");

                                $filename_col->setAttribute('style','display: table; width: 100%;');
                                $lnkIcono->setAttribute('style','display: table-row; width: 100%;');
                                if ($opcion['allowDownload'] == '1') {
                                    $lnkFilename->parentNode->setAttribute('style','display: table-row; width: 100%;');
                                }

                            }

                            //echo "<h1>".$childNode->parentNode->nodeName."</h1>";
                            //echo "<h1>".$childNode->lastChild->nodeName."</h1>";
                            //ELIMINAR BR CLEAR
                            //$childNode->removeChild($childNode->lastChild);

                            //$childNode->parentNode->removeChild($childNode->lastChild);
                            //$childNode->childNodes->item(0)->removeChild($filename_col->childNodes->item(0));
                            //$filename_col['icon'] = $filename_col->childNodes->item(0);
                            //$filename_col['filename'] = $filename_col->childNodes->item(1);


                            //echo '<textarea>'.$childNode->ownerDocument->saveHTML($childNode)."</textarea>";
                            //echo '<textarea>'.$childNode->childNodes->item(0)->ownerDocument->saveHTML()."</textarea>";
                            $olFiles .= $childNode->ownerDocument->saveHTML($childNode);


                        }

                        $olFiles .= ($ver_como != 'lista')?'':'</ol>';






                        //$imprimirCaja = $txtContenedor[0].'<div id="list-view-container" class="gallery-view-section">' . $olFiles . "</div>".$txtContenedor[1];
                        //$imprimirCaja = $txtContenedor[0].'<div id="list-view-container" class="gallery-view-section">' . $txtIconosDt . "</div>".$txtContenedor[1];


                        //echo $imprimirCaja;

                        //echo '<textarea>'.$imprimirCaja."</textarea>";
                        if ($ver_como === 'lista') {
                            $retorno = $txtContenedor[0].'<div id="list-view-container" class="gallery-view-section">' . $olFiles . "</div>".$txtContenedor[1];
                        } else {
                            $retorno = $txtContenedor[0].'<div id="list-view-container" class="gallery-view-section">' . $txtIconosDt . "</div>".$txtContenedor[1];
                        }
                        $retorno = $txtContenedor[0].'<div id="list-view-container" class="gallery-view-section">' . $olFiles . "</div>".$txtContenedor[1];
                        //echo '<textarea>'.$retorno."</textarea>";
                    }
                    else {
                        $retorno = '<div class="Hyno_ContenFolder"><div class="nav-header">
                        <div id="icon_folder"></div>
                        <span id="folder-title" class="shmodel-filename header_1"><span style="color: red;font-weight: black;">Error</span>://<span id="ERROR"><span style="color: red;font-style: italic; font-weight: lighter;">' . _e('No se puede leer carpeta compartida', "dropbox-folder-share") . '</span></span></span>
                        </div>
						</div>';
                    }

                }
                else {
                    $retorno = '<div class="Hyno_ContenFolder"><div class="nav-header">
                        <div id="icon_folder"></div>
                        <span id="folder-title" class="shmodel-filename header_1"><span style="color: red;font-weight: black;">Error</span>://<span id="ERROR"><span style="color: red;font-style: italic; font-weight: lighter;">' . _e('No se puede leer carpeta compartida', "dropbox-folder-share") . '</span></span></span>
                        </div>
						</div>';
                }

                return $retorno;
            }
            else {
                return  __("No encontrado", "dropbox-folder-share");
            }
        }

        function add_settings_link($links, $file)
        {
            if (self::$basename === $file && current_user_can(self::_PERMISOS_REQUERIDOS_)) {
                $links[] = '<a href="' . esc_url($this->plugin_options_url()) . '" alt="' . esc_attr__('Dropbox Folder Share - Configuracion', "dropbox-folder-share") . '">' . esc_html__('Configurar', "dropbox-folder-share") . '</a>';
            }
            return $links;
        }

        function plugin_options_url()
        {
            return add_query_arg('page', self::$nombre, admin_url(self::_PARENT_PAGE_));
        }

        function DOMRemove(DOMNode $from) {
            $sibling = $from->firstChild;
            do {
                $next = $sibling->nextSibling;
                $from->parentNode->insertBefore($sibling, $from);
            } while ($sibling = $next);
            $from->parentNode->removeChild($from);
        }

    }

    $objDropboxFolderSharePrincipal = new DropboxFolderSharePrincipal;
    include_once 'class/http_build_url.php';
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

    //AJAX
    add_action( 'wp_ajax_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode') );
    add_action( 'wp_ajax_nopriv_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode') );

}
