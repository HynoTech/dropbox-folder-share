<?php
/**
 * Plugin Name: DropBox Folder Share
 * Plugin URI: http://www.hynotech.com/wp-plugins/dropbox-folder-share/
 * Description: Plugin que permitira incluir carpetas de DropBox en nuestras entradas de blog.
 * Version: 1.7.2
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



    //use Carbon\Carbon;


    Class DropboxFolderSharePrincipal
    {

        const _VERSION_GENERAL_ = "1.7.2";
        const _VERSION_JS_ = "1.7";
        const _VERSION_CSS_ = "1.7";
        const _VERSION_ADMIN_ = "2.0.2";
        const _VERSION_CSS_DROPBOX_ = "3.0";

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
            "showIcons" => '1',
            "showSize" => '1',
            "showChange" => '1',
            "allowDownload" => '1',
            "allowDownloadFolder" => '1',
            "imagesPopup" => '1',
            "link2Folder" => '1',
            "tipoConexion" => 'fopen',
            "thickboxTypes" => 'txt,html,htm',
            "defaultHeight" => '300px'
        );

        public function __construct()
        {
            require __DIR__.'/vendor/autoload.php';
            include_once __DIR__.'/class/admin.class.php';


            $zone = explode("_",get_locale());

            setlocale(LC_TIME, get_locale());

            \Carbon\Carbon::setLocale($zone[0]);

            //echo "<h1>" . get_locale() . "</h1>";



            $this->asignar_variables_estaticas();
            load_plugin_textdomain("dropbox-folder-share", false, "dropbox-folder-share" . '/languages/');

            $objDFS_Admin = new DFS_Admin;
            add_action('admin_menu', array(&$objDFS_Admin, 'pagAdmin'));
            add_action('admin_init', array(&$objDFS_Admin, 'plugin_admin_init'));

            add_filter('plugin_action_links_' . self::$basename, array(&$this, 'add_settings_link'), 10, 2);

            $this->actualizarOpcAntiguas();
        }

        public function formatSizeUnits($bytes)
        {
            if ($bytes >= 1073741824)
            {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            }
            elseif ($bytes >= 1048576)
            {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            }
            elseif ($bytes >= 1024)
            {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            }
            elseif ($bytes > 1)
            {
                $bytes = $bytes . ' bytes';
            }
            elseif ($bytes == 1)
            {
                $bytes = $bytes . ' byte';
            }
            else
            {
                $bytes = '0 bytes';
            }

            return $bytes;
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

            // set defaults
            $opciones = get_option(self::_OPT_SEETINGS_);
            extract(shortcode_atts(array(
                'link' => $_POST['link'],
            ), $atts));

            $idContent = $_POST['idContent'];
            $titleBar = $_POST["titleBar"];

            echo $this->get_folder($link, $idContent, $titleBar);
            die();
        }

        function scriptAjax($link, $idContent){
            //$idContent = "DFS".rand(1,99999);
            $url_imgLoader = self::$url."/img/gears.svg";

            $regresarScript = "<div id='$idContent'>";
            //$regresarScript .= "<div class=\"loader\">Loading...</div>";
            $regresarScript .= "<div style='text-align: center'><img src=\"{$url_imgLoader}\"></div>";
            $regresarScript .= "</div>";
            $regresarScript .= "<script>";
            $regresarScript .= "loadContenDFS('$link', '$idContent')";
            $regresarScript .= "</script>";
            return $regresarScript;
        }

        function replace_shortcode($atts)
        {
            $idContent = "DFS".mt_rand(1,99999);
            // set defaults
            $opciones = get_option(self::_OPT_SEETINGS_);
            extract(shortcode_atts(array(
                'link' => 'https://www.dropbox.com/sh/8ifs95x8qgcaf71/1TCmt_bBy1',
            ), $atts));


            if ($opciones['UseAjax'] === '1'){
                return $this->scriptAjax($link, $idContent);
            }
            else{
                return $this->get_folder($link, $idContent);
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

        function get_folder($link, $id_content = null, $titleBar = null)
        {
            $opcion = get_option(self::_OPT_SEETINGS_);

            $url_data = $link;
            $content = $this->fetch_url($url_data);


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


                    /**
                     * V 1.6.2
                     * NewForma
                     */



                    $varExt = array(
                        'acrobat' => array(
                            "pdf",
                            "eps",
                        ),
                        'audition' => array("ai"),
                        'code' => array(
                            "c",
                            "cpp",
                            "cs",
                            "css",
                            "h",
                            "htm",
                            "html",
                            "java",
                            "js",
                            "php",
                            "pl",
                            "py",
                            "rb",
                            "xml",
                        ),
                        'compressed' => array(
                            "bz2",
                            "gz",
                            "rar",
                            "zip",
                            "tar",
                        ),
                        'dvd' => array(
                            "dmg",
                            "iso",
                        ),
                        'excel' => array(
                            "csv",
                            "ods",
                            "xls",
                            "xlsb",
                            "xlsm",
                            "xlsx",
                        ),
                        'film' => array(
                            "3gp",
                            "3gpp",
                            "asf",
                            "avi",
                            "flv",
                            "m4v",
                            "mkv",
                            "mov",
                            "mp4",
                            "mpg",
                            "ogv",
                            "vob",
                            "wmv",
                        ),
                        'flash' => array(
                            "fla",
                            "swf",
                        ),
                        'gear' => array(
                            "exe",
                            "app",
                            "dll",
                        ),
                        'gray' => array(),
                        'illustrator' => array(
                            "ai",
                        ),
                        'keynote' => array(
                            "key",
                        ),
                        'linkfile' => array(
                            "webloc",
                            "url",
                        ),
                        'mp3' => array(

                        ),
                        'paint' => array(
                            "psd"
                        ),
                        'paper' => array(),
                        'picture' => array(
                            "dcr",
                            "r3d",
                            "bmp",
                            "dcs",
                            "gif",
                            "jpeg",
                            "jpg",
                            "png",
                            "svg",
                            "tif",
                            "tiff",
                            "ptx",
                            "rwz",
                            "kdc",
                        ),
                        'playlist' => array(),
                        'powerpoint' => array(
                            "pps",
                            "ppsm",
                            "ppsx",
                            "ppt",
                            "pptm",
                            "pptx",
                        ),
                        'premiere' => array(),
                        'sketch' => array(),
                        'sound' => array(
                            "3ga",
                            "aac",
                            "aif",
                            "aiff",
                            "amr",
                            "au",
                            "iff",
                            "m3u",
                            "m4a",
                            "mid",
                            "mp3",
                            "mpa",
                            "oga",
                            "ogg",
                            "ra",
                            "wav",
                            "wma",
                        ),
                        'stack' => array(),
                        'text' => array(
                            "txt",
                            "wps",
                        ),
                        'vector' => array(
                            "ai",
                        ),
                        'webcode' => array(),
                        'word' => array(
                            "doc",
                            "docm",
                            "rtf",
                            "odt",
                            "pages",
                            "wpd",
                        )
                    );


	                $dataModule[0] = '"props": {';
	                $dataModule[1] = '}, "elem_id":';

	                $patronModule = '|'.$dataModule[0].'(.*?)'.$dataModule[1].'|is';
	                preg_match_all($patronModule, $body->item(0)->ownerDocument->saveHTML(), $varTemp2);



	                /*
                    $soloDataModule = str_replace('window.MODULE_CONFIG = ', '', $varTemp2[0][0]);
                    $soloDataModule = str_replace('}}}};', '}}}}', $soloDataModule);
	                */

	                $soloDataModule = str_replace('"props": {', '{', $varTemp2[0][0]);
	                $soloDataModule = str_replace('}, "elem_id":', '}', $soloDataModule);



	                $objImportante2 = json_decode( $soloDataModule );

	                if ($objImportante2 == NULL){
		                $retorno = '
                        <div class="err sl-list-container" style="width: 100%; text-align: center;">
                            <img src="'. self::$url .'/img/error_404.png" alt="" width="30%">
                            <h4 class="sl-empty-folder-message">'. __("No encontramos lo que buscas.", "dropbox-folder-share").'</h4>
                        </div>
                        ';

		                return $retorno;
	                }




	                //$dataCarpeta = $objImportante2->modules->clean->init_react->components;
	                /*
										$patronScript = '|'.$dataScript[0].'(.*?)'.$dataScript[1].'|is';

										preg_match_all($patronScript, $body->item(0)->ownerDocument->saveHTML(), $varTemp);

										$objImportante = json_decode("{". $varTemp[0][0]. "}");

										echo "<pre>";
										echo "{". $varTemp[0][0]. "}";
										echo "</pre>";


										$dataContents = $objImportante->contents;


										//$archivosCarpeta = $dataContents->files;
										//$carpetasCarpeta = $dataContents->folders;

										*/


	                /*
	                $archivosCarpeta = $dataCarpeta[0]->props->contents->files;
	                $carpetasCarpeta = $dataCarpeta[0]->props->contents->folders;
	                */

	                $archivosCarpeta = $objImportante2->contents->files;
	                $carpetasCarpeta = $objImportante2->contents->folders;

	                $datosCarpetaLocal = array(
		                "nombre" => $objImportante2->folderShareToken->displayName,
		                "link" => $objImportante2->folderSharedLinkInfo->url,
		                "archivos" => $archivosCarpeta,
		                "carpetas" => $carpetasCarpeta,
	                );







	                $cantData = count($datosCarpetaLocal["carpetas"]) + count($datosCarpetaLocal["archivos"]);


                    if($cantData > 0){


                        $detalleURL = parse_url($datosCarpetaLocal["link"]);

                        $arrayPath = explode("/",$detalleURL['path']);
                        $codeRel = end($arrayPath);


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
                                $txtZip .= '<img style="float: right;" src="'. self::$url .'/img/zip.png" title="' . __('Descargar carpeta actual (zip)', 'dropbox-folder-share') . '" >';
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

                                    if( ( $nomVinculo->nodeValue != "" ) && ( $nomVinculo->nodeValue == $datosCarpetaLocal["nombre"] ) && ( $nomVinculo->getAttribute('href') == $link ) ) {
                                        $elimActivado = true;
                                    }
                                }


                                foreach ( $elementsToDelete as $elementToDelete ) {
                                    $elementToDelete->parentNode->removeChild($elementToDelete);
                                }

                                if(!$elimActivado){
                                    $fragInsertHTML = "<span>";
                                    $fragInsertHTML .= "<a href='{$link}' data-titulo='1' onclick=\"loadContenDFS('{$link}', '{$id_content}'); varTitulo = 1; return false;\">";
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

                                $txtCarpeta .= "<div style='display: block;'>";
                                $txtCarpeta .= '<img class="sprite sprite_web s_web_dropbox21x20 icon" src="'. self::$url .'img/icon_spacer.gif" alt=""><i style="float: left;">://</i>';
                                $txtCarpeta .= "<span>";
                                $txtCarpeta .= "<a href='{$link}' data-titulo='1' onclick=\"loadContenDFS('{$link}', '{$id_content}'); varTitulo = 1; return false;\">";
                                $txtCarpeta .= $datosCarpetaLocal["nombre"];
                                $txtCarpeta .= '</a>';
                                $txtCarpeta .= "<a href='{$link}' target='_blank'>";
                                $txtCarpeta .= '<img src="'. self::$url .'/img/ico-external-link.png">';
                                $txtCarpeta .= '</a>/';
                                $txtCarpeta .= '';
                                $txtCarpeta .= "</span>";
                                $txtCarpeta .= "</div>";
                            }


                            $txtCarpeta .= $txtZip;
                        } else {
                            $txtCarpeta .= '<img class="sprite sprite_web s_web_dropbox21x20 icon" src="'. self::$url .'img/icon_spacer.gif" alt="">//'.$datosCarpetaLocal["nombre"].'</div>';
                        }


                        $seccionesLista = array(58.6,19.5,21.9);

                        $displaySize = "auto";
                        if ($opcion['showSize'] != '1') {
                            $displaySize = "none";
                            $seccionesLista[0] += $seccionesLista[1];
                            $seccionesLista[1] = 0;
                        }

                        $displayChange = "auto";
                        if ($opcion['showChange'] != '1') {
                            $displayChange = "none";
                            $seccionesLista[0] += $seccionesLista[2];
                            $seccionesLista[2] = 0;
                        }




                        $htmlCarpetas = "";
                        foreach ($datosCarpetaLocal["carpetas"] as $carpeta) {
                            $folderName = $carpeta->filename;
                            $folderHref = $carpeta->href;
                            //s_web_folder_32

                            $displayIcon = "auto";
                            if ($opcion['showIcons'] != '1') {
                                $displayIcon = "none";
                            }


                            $strOnclick = "onclick='return false;'";
                            if ($opcion['allowBrowseFolder'] == '1'){
                                //onclick=\"loadContenDFS('{$folderHref}', '{$ver_como}', '{$id_content}'); varTitulo = 1; return false;\"
                                $strOnclick = "onclick=\"loadContenDFS('{$folderHref}', '{$id_content}'); varTitulo = 1; return false;\"";
                            }



                                $htmlCarpetas .= "
<li class='sl-list-row clearfix'>
          <div class='sl-list-column sl-list-column--filename' style='width: {$seccionesLista[0]}% !important;'>
            <a href='{$folderHref}' data-titulo='1' {$strOnclick} class='sl-file-link'>
              <div class='o-flag'>
                <div class='o-flag__fix' style='display: {$displayIcon} '>
                  <img class='sprite sprite_web s_web_folder_32 icon' src='" . self::$url . "img/icon_spacer.gif' alt=''>
                </div>
                <div class='o-flag__flex'>{$folderName} </div>
              </div>
            </a>

          </div>
          <div class='sl-list-column sl-list-column--filesize' style='width: {$seccionesLista[1]}% !important; display: {$displaySize} '>--
          </div>
          <div class='sl-list-column sl-list-column--modified' style='width: {$seccionesLista[2]}% !important; display: {$displayChange} '>--
          </div>
        </li>
                        ";

                        }


                        $htmlArchivos = '';
                        foreach ($datosCarpetaLocal["archivos"] as $archivo) {
                            $previsualizacion = $archivo->preview_url;
                            $file_link = $archivo->href;
                            $is_dir = $archivo->is_dir;
                            $file_name = $archivo->filename;
                            $bytes = $archivo->bytes;
                            $creado = $archivo->ts;
                            $thumb = $archivo->thumbnail_url_tmpl;
                            $link_descarga = $archivo->direct_blockserver_link;

                            $pathinfoTempArr = pathinfo($file_link);
                            $dataArchivo = explode("?", $pathinfoTempArr["extension"]);
                            $typeIcon = "";
                            $displayIcon = "auto";
                            if ($opcion['showIcons'] === '1') {
                                foreach ($varExt as $fileType => $ext) {
                                    if (in_array($dataArchivo[0], $ext)) {
                                        $typeIcon = "_".$fileType;
                                        break;
                                    }
                                }
                            }
                            else{
                                $displayIcon = "none";
                            }


                            $fileLinkMostrar = $file_link;
                            if($opcion['allowDownload'] === '1'){
                                $fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                            }



                            $classThickBox = "";
                            if(($opcion['imagesPopup'] === '1') || (in_array($dataArchivo[0], $arrayExtThickbox))) {

                                $esImg = str_replace("_","",$typeIcon);

                                if($esImg == 'picture'){
                                    $classThickBox = "thickbox";

                                    $fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                                }
                            }

                            $arrayExtThickbox = explode(",",$opcion['thickboxTypes']);

                            if(in_array($dataArchivo[0], $arrayExtThickbox)) {

                                $classThickBox = "thickbox";

                                $fileLinkMostrar = $this->downloadLinkGenerator($file_link);

                                $fileLinkMostrar = "https://docs.google.com/viewer?url=".$fileLinkMostrar."&embedded=true&KeepThis=true&TB_iframe=true&height=400&width=600";

                                //https://docs.google.com/gview?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true

                            }




                            $htmlArchivos .= '
<li class="sl-list-row clearfix">
          <div class="sl-list-column sl-list-column--filename"  style="width: '.$seccionesLista[0].'% !important;">
            <a href="' . $fileLinkMostrar . '" class="sl-file-link '.$classThickBox.'">
              <div class="o-flag">
                <div class="o-flag__fix" style="display:'. $displayIcon .'">
                  <img class="sprite sprite_web s_web_page_white' . $typeIcon . '_32 icon" src="' . self::$url . 'img/icon_spacer.gif" alt="">
                </div>
                <div class="o-flag__flex">' . $file_name . '</div>
              </div>
            </a>
          </div>
          <div class="sl-list-column sl-list-column--filesize" style="width: '.$seccionesLista[1].'% !important; display:'. $displaySize .'">' . (($opcion['showSize'] != '1')?"": self::formatSizeUnits($bytes) ) . '</div>
          <div class="sl-list-column sl-list-column--modified" style="width: '.$seccionesLista[2].'% !important; display:'. $displayChange .'">' . \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::createFromTimestamp($creado)) . '</div>
        </li>
                        ';


                        }


                        /**
                         * DATOS NECESARIOS
                         */

                        $nombreCarpeta = $datosCarpetaLocal['nombre'];
                        $linkCarpeta = $datosCarpetaLocal['link'];
                        $codTitulo = "
  <div class='sl-header clearfix'>
  <div id='Hyno_Header_{$id_content}'>
  {$txtCarpeta}
  </div>
  </div>
                    ";

                        $codInicial = "
<div class='sl-page-body'>
{$codTitulo}
  <div class='sl-body'>
    <div class='sl-list-container'>
      <div class='sl-list-header'>
        <div class='sl-list-row clearfix'>
          <div class='sl-list-column sl-list-column--filename' style='width: {$seccionesLista[0]}% !important;'>" . __('Nombre', 'dropbox-folder-share') . "
          </div>
          <div class='sl-list-column sl-list-column--filesize' style='width: {$seccionesLista[1]}% !important; display: {$displaySize} '>" . __('Tamaño', 'dropbox-folder-share') . "
          </div>
          <div class='sl-list-column sl-list-column--modified' style='width: {$seccionesLista[2]}% !important; display: {$displayChange}'>" . __('Modificado', 'dropbox-folder-share') . "
          </div>
        </div>
      </div>
      <ol class='sl-list-body o-list-ui o-list-ui--dividers' style='max-height:". (($opcion['defaultHeight'] != '0')?$opcion['defaultHeight']:'auto') ."; overflow:auto;'>
                    ";


                        $codFinal = "
      </ol>
    </div>
  </div>
</div>
                    ";




                        $txtContenedor[0] = "";
                        $txtContenedor[0] = "<div id='$id_content'>";
                        $txtContenedor[0] .= '<div class="Hyno_ContenFolder">';

                        $txtContenedor[0] .= '';

                        $txtContenedor[0] .= $codInicial.$htmlCarpetas.$htmlArchivos.$codFinal;

                        $txtContenedor[1] = '</div>';
                        $txtContenedor[1] .= '</div>';


                        $retorno = $txtContenedor[0].$txtContenedor[1];
                    }
                    else {
                        $retorno = '
                        <div class="sl-page-body sl-list-container">
                            <div class="sl-header clearfix">
                                <h3 class="sl-title">'.$datosCarpetaLocal['nombre'].'</h3>
                            </div>
                            <div class="sl-body sl-body--empty-folder">
                                <img class="sl-empty-folder-icon" src="'. self::$url .'/img/carpeta.png" alt="">
                                <h4 class="sl-empty-folder-message">' . __("Esta carpeta está vacía", "dropbox-folder-share") .'</h4>
                            </div>
                         </div>
                        ';
                        return $retorno;
                    }










                }
                else {
                    $retorno = '
                        <div class="err sl-list-container" style="width: 100%; text-align: center;">
                            <img src="'. self::$url .'/img/error_404.png" alt="" width="30%">
                            <h4 class="sl-empty-folder-message">'. __("No encontramos lo que buscas.", "dropbox-folder-share").'</h4>
                        </div>
                        ';
                }

                return $retorno;
            }
            else {
                return  __("No encontrado", "dropbox-folder-share");
            }
        }

        function downloadLinkGenerator($link){
            $query_params['dl'] = 1;
            $detalleURL['query'] = http_build_query($query_params);

            $newUrl = http_build_url($link,
                $detalleURL,
                HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
            );
            return $newUrl;
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
	    add_filter( 'mce_external_languages', array( &$objDFS_TinyMCE, 'dropboxfoldershare_add_tinymce_translations' ) );

	    add_filter("the_posts", array(&$objDFS_TinyMCE, "dropbox_foldershare_styles_and_scripts"));
    }

    add_shortcode('dropbox-foldershare-hyno', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));
    add_shortcode('DFS', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));

    //AJAX
    add_action( 'wp_ajax_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode') );
    add_action( 'wp_ajax_nopriv_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode') );


}
