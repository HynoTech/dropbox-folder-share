<?php
/**
 * Created by PhpStorm.
 * User: antony
 * Date: 06/08/18
 * Time: 11:14 PM
 */

namespace HynoTech\DropboxFolderShare;


Class Principal
{

    const _VERSION_GENERAL_ = "1.8.5";
    const _VERSION_JS_ = "1.8.5";
    const _VERSION_CSS_ = "1.8.5";
    const _VERSION_ADMIN_ = "3.0";
    const _VERSION_CSS_DROPBOX_ = "3.0";

    const _PARENT_PAGE_ = "options-general.php";
    const _OPT_SEETINGS_ = "dropbox-folder-share-options";
    const _PERMISOS_REQUERIDOS_ = 'manage_options';

    var $formSections = array();
    var $settings = array(); //Almacena los opciones actuales del Plugin
    var $opcDefault = array(
        "showIcons" => '1',
        "showSize" => '1',
        "showChange" => '1',
        "showThumbnail" => '1',
        "allowDownload" => '1',
        "allowBrowseFolder" => '1',
        "allowDownloadFolder" => '1',
        "showInEditor" => '1',
        "dbNativeViewer" => '1',
        "imagesPopup" => '1',
        "link2Folder" => '1',
        "datetimeFormat" => 'd/m/Y H:i',
        "thickboxTypes" => 'txt,html,htm',
        "defaultHeight" => '300px'
    );

    public function __construct() {

        $zone = explode("_", get_locale());

        setlocale(LC_TIME, get_locale());

        \Carbon\Carbon::setLocale($zone[0]);

        \Carbon\Carbon::setToStringFormat(get_option('date_format') . " " . get_option('time_format'));

        load_plugin_textdomain("dropbox-folder-share", false, "dropbox-folder-share" . '/languages/');

        $objDFS_Admin = new Admin();
        add_action('admin_menu', array(&$objDFS_Admin, 'pagAdmin'));
        add_action('admin_init', array(&$objDFS_Admin, 'plugin_admin_init'));

        add_filter('plugin_action_links_' . DROPBOX_FOLDER_SHARE_PLUGIN_BASENAME, array(&$this, 'add_settings_link'), 10, 2);

        $this->inicializarVariables();
    }

    public function inicializarVariables() {
        if (get_option(self::_OPT_SEETINGS_) == null) {
            update_option(self::_OPT_SEETINGS_, $this->opcDefault);
        }
    }

    public function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }


    function getMetaTags($str) {
        $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

        if (preg_match_all($pattern, $str, $out)) {
            return array_combine($out[1], $out[2]);
        }

        return array();
    }

    function ajaxGetHeaders() {
        $content = $this->fetch_url($_POST['link']);

        $content = $this->getMetaTags($content->response);

        header('Content-type: application/json; charset=utf-8');
        echo json_encode($content);
        exit();
    }

    function ajaxGetImgBase64() {

        $tipo = $_POST["tipo"];
        $imgURLContent = $this->fetch_url($_POST['img_url']);

        $b64image = base64_encode($imgURLContent->response);

        $_urlImg64 = "data:" . $tipo . ";base64," . $b64image;
        echo $_urlImg64;
        exit();
    }

    function ajaxReplaceShortcode($atts) {

        if (!isset($_POST['dfs_nonce']) || !wp_verify_nonce($_POST['dfs_nonce'], 'dfs_nonce'))
            die(__("Error de seguridad", "dropbox-folder-share"));


        // set defaults
        $opciones = get_option(self::_OPT_SEETINGS_);
        extract(shortcode_atts(array(
            'link' => $_POST['link'],
            'show_icon' => $_POST['showIcons'],
            'show_size' => $_POST['showSize'],
            'show_change' => $_POST['showChange']
        ), $atts));

        $opciones_shortcode = array(
            'link' => $link,
            'showIcons' => $show_icon,
            'showSize' => $show_size,
            'showChange' => $show_change
        );

        $idContent = $_POST['idContent'];
        $titleBar = @$_POST["titleBar"];

        echo $this->get_folder($opciones_shortcode, $idContent, $titleBar);
        die();
    }

    function scriptAjax($opciones_shortcode, $idContent) {
        //$idContent = "DFS".rand(1,99999);
        $url_imgLoader = DROPBOX_FOLDER_SHARE_PLUGIN_URL . "/src/img/gears.svg";

        $data = json_encode($opciones_shortcode);

        $regresarScript = "<div id='$idContent'>";
        //$regresarScript .= "<div class=\"loader\">Loading...</div>";
        $regresarScript .= "<div style='text-align: center'><img src=\"{$url_imgLoader}\"></div>";
        $regresarScript .= "</div>";
        $regresarScript .= "<script>";
        $regresarScript .= "loadContenDFS('$data', '$idContent')";
        $regresarScript .= "</script>";
        return $regresarScript;
    }

    function replace_shortcode($atts) {
        $idContent = "DFS" . mt_rand(1, 99999);
        // set defaults
        $opciones = get_option(self::_OPT_SEETINGS_);
        extract(shortcode_atts(array(
            'link' => 'https://www.dropbox.com/sh/8ifs95x8qgcaf71/1TCmt_bBy1',
            'show_icon' => $opciones['showIcons'],
            'show_size' => $opciones['showSize'],
            'show_change' => $opciones['showChange']
        ), $atts));

        $opciones_shortcode = array(
            'link' => $link,
            'showIcons' => (($show_icon === 'true') || ($show_icon === '1')) ? '1' : '',
            'showSize' => (($show_size === 'true') || ($show_size === '1')) ? '1' : '',
            'showChange' => (($show_change === 'true') || ($show_change === '1')) ? '1' : ''
        );
        return $this->scriptAjax($opciones_shortcode, $idContent);

        //

    }

    function fetch_url($url, $headers = false, $tipo = 'get', $data = [], $cookies = []) {

        if (function_exists("curl_init")) {

            $txtLocale = str_replace("_", "-", get_locale());

            $curl = new \Curl\Curl();
            $curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
            $curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);

            /*
            if(wp_is_mobile()){
                $curl->setUserAgent('Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
            }
            */
            if ($headers) {
                $curl->setopt(CURLOPT_HEADER, true);
                $curl->setopt(CURLOPT_NOBODY, true);

            }
            $curl->setopt(CURLOPT_FOLLOWLOCATION, true);
            $curl->setHeader('Accept-Language', $txtLocale);

            //$curl->setCookie('key', 'value');
            $curl->setCookies($cookies);

            switch ($tipo) {
                case 'post':
                    $curl->post($url, $data);
                    break;
                case 'options':
                    $curl->options($url, $data);
                    break;
                default:
                    $curl->get($url);
                    break;
            }

            return ($headers) ? $curl->responseHeaders : $curl;
        } else {
            echo "<h3>ERROR: curl disabled</h3>";
            return false;
        }

        return false;
    }

    function get_folder($opciones_shortcode, $id_content = null, $titleBar = null) {
        $opcion = get_option(self::_OPT_SEETINGS_);

        $link = $opciones_shortcode['link'];

        $opcion = array_merge($opcion, $opciones_shortcode);

        $url_data = $link;

        //d($url_data);

        $response = $this->fetch_url($url_data);

        //d($response);
        //d($response->response);

        //archivo prueba en carpeta _apoyo_

        $data = json_encode($opciones_shortcode);
        $data = str_replace("\"", "\\'", $data);
        $data = 'rev_' . $data;


        if ($response->response != "") {

            $cookies = $response->responseCookies;

            $dataModule[0] = '"props": {';
            $dataModule[1] = '}, "elem_id":';

            $patronModule = '|' . $dataModule[0] . '(.*?)' . $dataModule[1] . '|is';
            preg_match_all($patronModule, $response->response, $varTemp2);

            $soloDataModule = str_replace('"props": {', '{', (isset($varTemp2[0][0])) ? $varTemp2[0][0] : '');
            $soloDataModule = str_replace('}, "elem_id":', '}', $soloDataModule);

            $objImportante = json_decode($soloDataModule);

            if ($objImportante == null) {
                $retorno = '
                        <div class="err sl-list-container" style="width: 100%; text-align: center;">
                            <img src="' . DROPBOX_FOLDER_SHARE_PLUGIN_URL . '/src/img/error_404.png" alt="" width="30%">
                            <h4 class="sl-empty-folder-message">' . __("No encontramos lo que buscas.", "dropbox-folder-share") . '</h4>
                        </div>
                        ';
                return json_encode(['html' => $retorno, 'imgs' => []]);
            }

            $postValues = [
                'is_xhr' => 'true',
                'link_key' => $objImportante->folderShareToken->linkKey,
                'secure_hash' => $objImportante->folderShareToken->secureHash,
                'link_type' => $objImportante->folderShareToken->linkType,
                'sub_path' => $objImportante->folderShareToken->subPath,
                't' => $cookies['t']
            ];
            //$responseData = $this->fetch_url('https://www.dropbox.com/list_shared_link_folder_entries',false,'post',$postValues,$cookies);
            //archivo prueba en carpeta _apoyo_


            $carpetasCarpeta = array();
            $archivosCarpeta = array();
            $masArchivos = false;
            do {
                $responseData = $this->fetch_url('https://www.dropbox.com/list_shared_link_folder_entries', false, 'post', $postValues, $cookies);
                foreach ($responseData->response->entries as $item) {

                    if ($item->is_dir) {
                        //CARPETAS
                        $carpetasCarpeta[] = $item;
                    } else {
                        //ARCHIVOS
                        $archivosCarpeta[] = $item;
                    }
                }

                if ($responseData->response->has_more_entries) {
                    $postValues["voucher"] = $responseData->response->next_request_voucher;
                }

            } while ($responseData->response->has_more_entries);


            //Incluir Extensiones
            //include_once (__DIR__.'/extensiones.php');
            //d(json_encode($varExt));


            $jsonExtensiones = file_get_contents(__DIR__ . '/json/extensiones.json');

            $varExt = json_decode($jsonExtensiones);


            //$archivosCarpeta = $objImportante->contents->files;
            //$carpetasCarpeta = $objImportante->contents->folders;

            $domRetorno = new \DOMDocument('1.0', 'UTF-8');

            //d($objImportante);

            $datosCarpetaLocal = [
                "nombre" => $objImportante->folderSharedLinkInfo->displayName,
                "path" => $objImportante->folderShareToken->subPath,
                "linkKey" => $objImportante->folderShareToken->linkKey,
                "secureHash" => $objImportante->folderShareToken->secureHash,
                "link" => $objImportante->folderSharedLinkInfo->url,
                "propietario" => $objImportante->folderSharedLinkInfo->ownerName,
                "archivos" => $archivosCarpeta,
                "carpetas" => $carpetasCarpeta,
            ];

            $detalleURL = parse_url($datosCarpetaLocal["link"]);

            $html_ol['breadcrumb'] = $domRetorno->createElement('ol');
            $html_ol['breadcrumb']->setAttribute('class', 'breadcrumb');
            $html_ol['breadcrumb']->setAttribute('style', 'font-size: 16px; margin:0px;');


            $addLIZip = $domRetorno->createElement('li');
            if ($opcion['allowDownloadFolder'] === '1') {

                $query_params['dl'] = 1;
                $detalleURL['query'] = http_build_query($query_params);

                $lnkDescarga = http_build_url($link,
                    $detalleURL,
                    HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
                );


                $addIMGZip = $domRetorno->createElement('img');
                $addIMGZip->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/zip.png');
                $addIMGZip->setAttribute('title', __('Descargar carpeta actual (zip)', 'dropbox-folder-share'));

                $addAZip = $domRetorno->createElement('a');
                $addAZip->setAttribute('href', $lnkDescarga);
                $addAZip->setAttribute('target', "_blank");
                $addAZip->appendChild($addIMGZip);

                $addLIZip->setAttribute('class', 'pull-right'); //creado fuera del IF
                $addLIZip->appendChild($addAZip);

            }


            //solo entra si existe una barra de titulo, es decir si es sub carpeta
            if ($titleBar != null) {
                $titleBar = str_replace('\\"', '"', $titleBar);
                $titleBar = str_replace("\\'", "'", $titleBar);
                $titleBar = str_replace("\\\\", "\\", $titleBar);

                $titleBar = $this->limpiahtml($titleBar);

                $doc = new \DOMDocument();

                $doc->loadHTML(mb_convert_encoding($titleBar, 'HTML-ENTITIES', 'UTF-8'));

                $elemLiBreadcrumb = $doc->getElementsByTagName('li');

                $elemOlBreadcrumb = $doc->getElementsByTagName('ol');

                $lnkSup = false;
                foreach ($elemLiBreadcrumb as $li) {

                    if ($li->getAttribute('class') == 'pull-right') {
                        $li->parentNode->removeChild($li);
                    } else {
                        $html_ol['breadcrumb']->appendChild($domRetorno->importNode($li, true));
                        if ($li->getAttribute('data-id') == $datosCarpetaLocal["secureHash"]) {
                            $lnkSup = true;
                            break;
                        }
                    }
                }

                $elemA_1 = $domRetorno->createElement('a', $datosCarpetaLocal["nombre"]);
                $elemA_1->setAttribute('href', $link);
                $elemA_1->setAttribute('onclick', "loadContenDFS('{$data}', '{$id_content}'); varTitulo = 1; return false;");
                $elemA_1->setAttribute('data-titulo', '1');

                $elemIMG_2 = $domRetorno->createElement('img');
                $elemIMG_2->setAttribute('width', '12px');
                $elemIMG_2->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . '/src/img/ico-external-link.png');

                $elemA_2 = $domRetorno->createElement('a');
                $elemA_2->setAttribute('href', $link);
                $elemA_2->setAttribute('target', '_blank');
                $elemA_2->appendChild($elemIMG_2);

                $elemLi = $domRetorno->createElement('li');
                $elemLi->setAttribute('data-id', $datosCarpetaLocal["secureHash"]);
                $elemLi->appendChild($elemA_1);
                $elemLi->appendChild($elemA_2);

                if (!$lnkSup) {
                    $html_ol['breadcrumb']->appendChild($elemLi);
                }


                //$elemOlBreadcrumb->item(0)->appendChild($elemLi);

                if ($opcion['allowDownloadFolder'] === '1') {
                    $html_ol['breadcrumb']->appendChild($addLIZip);
                    //$elemOlBreadcrumb->item(0)->appendChild($doc->importNode( $addLIZip, true ));
                }

                //d($doc->saveHTML($elemOlBreadcrumb->item(0)));


            } else {
                $html_i['fa-dropbox'] = $domRetorno->createElement('i');
                $html_i['fa-dropbox']->setAttribute('class', 'fa fa-dropbox');
                $html_i['fa-dropbox']->setAttribute('style', 'color: #0082E6;');

                $html_li[0] = $domRetorno->createElement('li');
                $html_li[0]->appendChild($html_i['fa-dropbox']);

                $html_a['ajax'] = $domRetorno->createElement('a', $datosCarpetaLocal["nombre"]);
                $html_a['ajax']->setAttribute('href', $link);
                $html_a['ajax']->setAttribute('data-titulo', '1');
                $html_a['ajax']->setAttribute('onclick', "loadContenDFS('{$data}', '{$id_content}'); varTitulo = 1; return false;");

                $html_img['ext_icon'] = $domRetorno->createElement('img');
                $html_img['ext_icon']->setAttribute('width', '12px');
                $html_img['ext_icon']->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . "src/img/ico-external-link.png");

                $html_a['blank'] = $domRetorno->createElement('a');
                $html_a['blank']->setAttribute('href', $link);
                $html_a['blank']->setAttribute('target', '_blank');
                $html_a['blank']->appendChild($html_img['ext_icon']);

                $html_li[1] = $domRetorno->createElement('li');
                $html_li[1]->setAttribute('data-id', $datosCarpetaLocal["secureHash"]);
                $html_li[1]->appendChild($html_a['ajax']);
                $html_li[1]->appendChild($html_a['blank']);

                //$html_ol['breadcrumb'] creado al inicio del if de este else y superior
                $html_ol['breadcrumb']->appendChild($html_li[0]);
                $html_ol['breadcrumb']->appendChild($html_li[1]);
                if ($opcion['allowDownloadFolder'] === '1') {
                    $html_ol['breadcrumb']->appendChild($addLIZip);
                }

            }


            $cantData = count($datosCarpetaLocal["carpetas"]) + count($datosCarpetaLocal["archivos"]);

            if ($cantData > 0) {


                if ($opcion['link2Folder'] != '1') {

                    $domRetorno->appendChild($html_ol['breadcrumb']);

                    $this->EliminarTAG($domRetorno, '//a');
                    $this->EliminarTAG($domRetorno, '//img[@width="12px"]');

                }


                $displaySize = "auto";
                $displayChange = "auto";
                $seccionesLista = [58.6, 19.5, 21.9];
                if (($opcion['allowDownload'] === '1') || ($opcion['allowDownloadFolder'] === '1')) {
                    $seccionesLista = [58.6, 16.5, 18.9];
                }
                if(wp_is_mobile()){
                    $seccionesLista = [100, 0, 0];
                    if (($opcion['allowDownload'] === '1') || ($opcion['allowDownloadFolder'] === '1')) {
                        $seccionesLista = [94, 0, 0];
                    }
                    $displaySize = "none";
                    $displayChange = "none";
                }
                else{
                    if ($opcion['showSize'] != '1') {
                        $displaySize = "none";
                        $seccionesLista[0] += $seccionesLista[1];
                        $seccionesLista[1] = 0;
                    }


                    if ($opcion['showChange'] != '1') {
                        $displayChange = "none";
                        $seccionesLista[0] += $seccionesLista[2];
                        $seccionesLista[2] = 0;
                    }
                }


                $html_ol['sl-list-body'] = $domRetorno->createElement('ol');//revisar separacion
                $html_ol['sl-list-body']->setAttribute('class', 'sl-list-body o-list-ui o-list-ui--dividers');
                $html_ol['sl-list-body']->setAttribute('style', "max-height:" . (($opcion['defaultHeight'] != '0') ? $opcion['defaultHeight'] : 'auto') . "; overflow:auto; margin: 0px !important");
                foreach ($datosCarpetaLocal["carpetas"] as $carpeta) {
                    $folderName = $carpeta->filename;
                    $folderHref = $carpeta->href;
                    $opciones_shortcode['link'] = $folderHref;

                    $data = json_encode($opciones_shortcode);
                    $data = str_replace("\"", "\\'", $data);
                    $data = 'rev_' . $data;

                    $displayIcon = "auto";
                    if ($opcion['showIcons'] != '1') {
                        $displayIcon = "none";
                    }

                    $iconSpacer = DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/icon_spacer.gif';

                    $html_li_div_a_div_div_img_lista = $domRetorno->createElement('img');
                    $html_li_div_a_div_div_img_lista->setAttribute('class', 'sprite sprite_web s_web_folder_32 icon');
                    $html_li_div_a_div_div_img_lista->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/icon_spacer.gif');

                    $html_li_div_a_div_div_lista = $domRetorno->createElement('div');
                    $html_li_div_a_div_div_lista->setAttribute('class', 'o-flag__fix');
                    $html_li_div_a_div_div_lista->setAttribute('style', "display: {$displayIcon} ");
                    $html_li_div_a_div_div_lista->appendChild($html_li_div_a_div_div_img_lista);

                    $html_li_div_a_div_div2_lista = $domRetorno->createElement('div', htmlentities($folderName));
                    $html_li_div_a_div_div2_lista->setAttribute('class', 'o-flag__flex');
                    $html_li_div_a_div_div2_lista->setAttribute('style', 'flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;');
                    //$html_li_div_a_div_div2_lista->setAttribute('style',"display: {$displayIcon} ");

                    $html_li_div_a_div_lista = $domRetorno->createElement('div');
                    $html_li_div_a_div_lista->setAttribute('class', 'o-flag');
                    $html_li_div_a_div_lista->appendChild($html_li_div_a_div_div_lista);
                    $html_li_div_a_div_lista->appendChild($html_li_div_a_div_div2_lista);


                    $html_li_div_a_lista = $domRetorno->createElement('a');
                    $html_li_div_a_lista->setAttribute('href', $folderHref);
                    $html_li_div_a_lista->setAttribute('data-titulo', '1');
                    if ($opcion['allowBrowseFolder'] == '1') {
                        $html_li_div_a_lista->setAttribute('onclick', "loadContenDFS('{$data}', '{$id_content}'); varTitulo = 1; return false;");
                    } else {
                        $html_li_div_a_lista->setAttribute('onclick', "return false;");
                    }
                    $html_li_div_a_lista->setAttribute('class', 'sl-file-link');
                    $html_li_div_a_lista->appendChild($html_li_div_a_div_lista);

                    $html_li_div_lista = $domRetorno->createElement('div');
                    $html_li_div_lista->setAttribute('class', 'sl-list-column sl-list-column--filename');
                    $html_li_div_lista->setAttribute('style', "width: {$seccionesLista[0]}% !important;");
                    $html_li_div_lista->appendChild($html_li_div_a_lista);

                    $html_li_div2_lista = $domRetorno->createElement('div', '--');
                    $html_li_div2_lista->setAttribute('class', 'sl-list-column sl-list-column--filesize');
                    $html_li_div2_lista->setAttribute('style', "width: {$seccionesLista[1]}% !important;; display: {$displaySize} ");

                    $html_li_div3_lista = $domRetorno->createElement('div', '--');
                    $html_li_div3_lista->setAttribute('class', 'sl-list-column sl-list-column--modified');
                    $html_li_div3_lista->setAttribute('style', "width: {$seccionesLista[2]}% !important;; display: {$displayChange} ");

                    $html_li_div4_lista = $domRetorno->createElement('span');
                    if ($opcion['allowDownloadFolder'] === '1') {
                        $fileLinkMostrar = $this->downloadLinkGenerator($folderHref);
                        $lnkOrigDescarga = $fileLinkMostrar;

                        $aDescarga = $domRetorno->createElement('a');
                        $aDescarga->setAttribute('href', $lnkOrigDescarga);
                        //$aDescarga->setAttribute('target', '_blank');

                        $html_li_div4_lista = $domRetorno->createElement('i');
                        $html_li_div4_lista->setAttribute('class', 'fa fa-file-archive-o');
                        $html_li_div4_lista->setAttribute('style', 'color: #0082E6;');
                        $aDescarga->appendChild($html_li_div4_lista);
                        $html_li_div4_lista = $aDescarga;
                    }

                    //<i class="fas fa-download"></i>


                    $html_li_lista = $domRetorno->createElement('li');
                    $html_li_lista->setAttribute('class', 'sl-list-row clearfix');
                    $html_li_lista->appendChild($html_li_div_lista);
                    $html_li_lista->appendChild($html_li_div2_lista);
                    $html_li_lista->appendChild($html_li_div3_lista);
                    $html_li_lista->appendChild($html_li_div4_lista);

                    $html_ol['sl-list-body']->appendChild($html_li_lista);

                }


                $htmlArchivos = '';
                $arrayIMG = array();

                foreach ($datosCarpetaLocal["archivos"] as $archivo) {

                    $fileCode = (isset($archivo->htmlified_link)) ? $archivo->htmlified_link : (isset($archivo->direct_blockserver_link)) ? $archivo->direct_blockserver_link : '';

                    $previsualizacion = $archivo->preview_url;
                    //$lnkThumbnail = $archivo->thumbnail_url_tmpl;
                    $lnkThumbnail = $this->downloadLinkGenerator($archivo->thumbnail_url_tmpl, array(
                        'size' => '32x32',
                        'size_mode' => '1'
                    ));
//https://photos-1.dropbox.com/t/2/AABpcRRa9-szXO86wYQd_9D4swB2a6t27uVGAqfu8vFahw/12/34022148/png/32x32/3/1499155200/0/2/Moquegua%20copia.svg/ELPV5xkY7OsPIAIoAg/uynCf0GA6KbIf2laTx_8uTLgxpINAA9AhRgHeI03FeQ?size=178x178&size_mode=3
//https://photos-1.dropbox.com/t/2/AADIsyR3bGVG9hjPPuVemTqb6nVqbxcM8yQjW3ZYNaTvvg/12/34022148/jpeg/32x32/3/1499155200/0/2/Moquegua.ai/ELPV5xkY7OsPIAIoAg/H9gDaPbdXrlkEOBcJd5vV1Lwt14f5FktYzxclzFEC8E?size=178x178&size_mode=3


                    $file_link = $archivo->href;

                    $is_dir = $archivo->is_dir;
                    $file_name = $archivo->filename;

                    $bytes = $archivo->bytes;
                    $creado = $archivo->ts;
                    $prevType = $archivo->preview_type;

                    $dataArchivo = explode("?", (isset(pathinfo($file_link)["extension"])) ? pathinfo($file_link)["extension"] : 'none');
                    $dataArchivo[0] = strtolower($dataArchivo[0]);
                    $typeIcon = "";
                    $displayIcon = "auto";
                    if ($opcion['showIcons'] === '1') {
                        foreach ($varExt as $fileType => $ext) {
                            if (in_array($dataArchivo[0], $ext)) {
                                $typeIcon = "_" . $fileType;
                                break;
                            }
                        }
                    } else {
                        $displayIcon = "none";
                    }

                    $fileLinkMostrar = $file_link;

                    $lnkOrigDescarga = "";
                    if ($opcion['allowDownload'] === '1') {
                        $fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                        $lnkOrigDescarga = $fileLinkMostrar;
                    }

                    $arrayExtThickbox = explode(",", $opcion['thickboxTypes']);
                    $classThickBox = "";
                    $relThickBox = "";

//d([$arrayExtThickbox,$dataArchivo[0]]);
                    $infoFile = wp_check_filetype($file_name);
                    if (in_array($dataArchivo[0], $arrayExtThickbox)) {

                        $classThickBox = "lightbox";
                        $fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                        $fileLinkMostrar = "https://docs.google.com/viewer?url=" . $fileLinkMostrar . "&embedded=true&KeepThis=true&TB_iframe=true&height=400&width=800";

                        $dataWidth = "1000";

                        //https://docs.google.com/gview?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true
                    }

                    //elseif (($prevType === 'text') && ($infoFile['ext'] !== 'txt')){
                    if ((!is_null($fileCode)) && ($fileCode != "") && ($opcion['dbNativeViewer'] === '1')) {
                        $classThickBox = "lightbox";
                        //$fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                        $fileLinkMostrar = $fileCode;//"https://docs.google.com/viewer?url=".$fileLinkMostrar."&embedded=true&KeepThis=true&TB_iframe=true&height=400&width=800";

                        $dataWidth = "1000";
                    }


                    if ($typeIcon != "") {

                        $esImg = str_replace("_", "", $typeIcon);
                        if (($opcion['imagesPopup'] === '1') || (in_array($dataArchivo[0], $arrayExtThickbox))) {


                            if ($esImg == 'picture') {
                                $classThickBox = "lightbox";
                                $relThickBox = "gal_" . $id_content;


                                $fileLinkMostrar = $this->downloadLinkGenerator($file_link);

                                //$fileLinkMostrar = $this->downloadLinkGenerator($archivo->thumbnail_url_tmpl,array('size_mode'=>'5'));;
                            }
                        }
                    }

                    //if (($esImg === 'picture') && ($opcion['showThumbnail'] === '1') && ($_imgContent = @file_get_contents($lnkThumbnail))) {
                    if (($esImg === 'picture') && ($opcion['showThumbnail'] === '1')) {

                        //data:image/png;base64,
                        //Kint::dump([$file_link,wp_check_filetype($file_name),wp_check_filetype($this->downloadLinkGenerator($file_link)),$b64image]);

                        //$b64image = base64_encode($_imgContent);


                        //$_urlImg64 = "data:" . $infoFile['type'] . ";base64," . $b64image;

                        $arrayIMG[] = ['img_id' => $archivo->sjid, 'type' => $infoFile['type'], 'img_url' => $lnkThumbnail];
                        /*
                        $html_li_div_a_div_div_img_lista = $domRetorno->createElement('img');
                        $html_li_div_a_div_div_img_lista->setAttribute('class', "icon thumbnail-image--loaded");
                        $html_li_div_a_div_div_img_lista->setAttribute('src', $_urlImg64);*/
                    } /*else {*/
                    $html_li_div_a_div_div_img_lista = $domRetorno->createElement('img');
                    $html_li_div_a_div_div_img_lista->setAttribute('class', "sprite sprite_web s_web_page_white" . $typeIcon . "_32 icon");
                    $html_li_div_a_div_div_img_lista->setAttribute('id', $archivo->sjid);
                    $html_li_div_a_div_div_img_lista->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/icon_spacer.gif');
                    /*}*/


                    $html_li_div_a_div_div_lista = $domRetorno->createElement('div');
                    $html_li_div_a_div_div_lista->setAttribute('class', 'o-flag__fix');
                    $html_li_div_a_div_div_lista->setAttribute('style', "display: {$displayIcon} ");
                    $html_li_div_a_div_div_lista->appendChild($html_li_div_a_div_div_img_lista);

                    $html_li_div_a_div_div2_lista = $domRetorno->createElement('div', htmlentities($file_name));
                    $html_li_div_a_div_div2_lista->setAttribute('class', 'o-flag__flex');
                    $html_li_div_a_div_div2_lista->setAttribute('style', 'flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;');

                    $html_li_div_a_div_lista = $domRetorno->createElement('div');
                    $html_li_div_a_div_lista->setAttribute('class', 'o-flag');
                    $html_li_div_a_div_lista->appendChild($html_li_div_a_div_div_lista);
                    $html_li_div_a_div_lista->appendChild($html_li_div_a_div_div2_lista);

                    $html_li_div_a_lista = $domRetorno->createElement('a');
                    $html_li_div_a_lista->setAttribute('href', $fileLinkMostrar);
                    //$html_li_div_a_lista->setAttribute('class','sl-file-link '.$classThickBox);
                    $html_li_div_a_lista->setAttribute('class', 'sl-file-link ');
                    $html_li_div_a_lista->setAttribute('title', $file_name);
                    if ($relThickBox != "") {
                        $html_li_div_a_lista->setAttribute('data-gallery', $relThickBox);
                    }
                    $html_li_div_a_lista->setAttribute('data-title', $file_name);
                    $html_li_div_a_lista->setAttribute('data-toggle', $classThickBox);
                    if (isset($dataWidth)) {
                        $html_li_div_a_lista->setAttribute('data-width', $dataWidth);
                    }
                    //$html_li_div_a_lista->setAttribute('data-remote', $fileLinkMostrar);
                    //$html_li_div_a_lista->setAttribute('rel',$relThickBox);
                    $html_li_div_a_lista->appendChild($html_li_div_a_div_lista);

                    $html_li_div_lista = $domRetorno->createElement('div');
                    $html_li_div_lista->setAttribute('class', 'sl-list-column sl-list-column--filename');
                    $html_li_div_lista->setAttribute('style', "width: {$seccionesLista[0]}% !important;");
                    $html_li_div_lista->appendChild($html_li_div_a_lista);

                    $html_li_div2_lista = $domRetorno->createElement('div', (($opcion['showSize'] != '1') ? "" : self::formatSizeUnits($bytes)));
                    $html_li_div2_lista->setAttribute('class', 'sl-list-column sl-list-column--filesize');
                    $html_li_div2_lista->setAttribute('style', "width: {$seccionesLista[1]}% !important;; display: {$displaySize} ");

                    $strFecha = \Carbon\Carbon::createFromTimestamp($creado)->format(isset($opcion['datetimeFormat']) ? $opcion['datetimeFormat'] : get_option('date_format') . " " . get_option('time_format'));
                    //$strFecha = \Carbon\Carbon::createFromTimestamp($creado)->format(get_option('date_format')." ". get_option('time_format'));
                    if ((\Carbon\Carbon::createFromTimestamp($creado)->diffInDays()) <= 30) {
                        $strFecha = \Carbon\Carbon::createFromTimestamp($creado)->diffForHumans();
                    }

                    $html_li_div3_lista = $domRetorno->createElement('div', $strFecha);
                    $html_li_div3_lista->setAttribute('class', 'sl-list-column sl-list-column--modified');
                    $html_li_div3_lista->setAttribute('style', "width: {$seccionesLista[2]}% !important;; display: {$displayChange} ");

                    $html_li_div4_lista = $domRetorno->createElement('span');
                    if ($opcion['allowDownload'] === '1') {
                        $fileLinkMostrar = $this->downloadLinkGenerator($file_link);
                        $lnkOrigDescarga = $fileLinkMostrar;

                        $aDescarga = $domRetorno->createElement('a');
                        $aDescarga->setAttribute('href', $lnkOrigDescarga);
                        //$aDescarga->setAttribute('target', '_blank');

                        $html_li_div4_lista = $domRetorno->createElement('i');
                        $html_li_div4_lista->setAttribute('class', 'fa fa-download');
                        $html_li_div4_lista->setAttribute('style', 'color: #0082E6;');

                        $aDescarga->appendChild($html_li_div4_lista);
                        $html_li_div4_lista = $aDescarga;
                    }

                    $html_li_lista = $domRetorno->createElement('li');
                    $html_li_lista->setAttribute('class', 'sl-list-row clearfix');
                    $html_li_lista->appendChild($html_li_div_lista);
                    $html_li_lista->appendChild($html_li_div2_lista);
                    $html_li_lista->appendChild($html_li_div3_lista);
                    $html_li_lista->appendChild($html_li_div4_lista);

                    $domRetorno->appendChild($html_li_lista);

                    //d($domRetorno->saveHTML($html_li_lista));

                    $html_ol['sl-list-body']->appendChild($html_li_lista);

                }
                //d($domRetorno->saveHTML($html_ol_lista));
                //d($datosCarpetaLocal);


                /**
                 * DATOS NECESARIOS
                 */


                $html_div['Hyno_Breadcrumbs'] = $domRetorno->createElement('div');//content $txtNavegacion
                $html_div['Hyno_Breadcrumbs']->setAttribute('class', 'row');
                $html_div['Hyno_Breadcrumbs']->setAttribute('id', "Hyno_Breadcrumbs_{$id_content}");
                $html_div['Hyno_Breadcrumbs']->appendChild($domRetorno->importNode($html_ol['breadcrumb']));

                $html_div['Hyno_Header'] = $domRetorno->createElement('div');//contenido $txtCarpeta
                $html_div['Hyno_Header']->setAttribute('class', 'sl-header clearfix');
                $html_div['Hyno_Header']->setAttribute('id', "Hyno_Header_{$id_content}");
                //$html_div['Hyno_Header']->appendChild($domRetorno->importNode($elemOlBreadcrumbPrincipal, true));

                //-----bloque grande ---
                $html_div['filename'] = $domRetorno->createElement('div', __('Nombre', 'dropbox-folder-share'));
                $html_div['filename']->setAttribute('class', 'sl-list-column sl-list-column--filename');
                $html_div['filename']->setAttribute('style', "width: {$seccionesLista[0]}% !important;");

                $html_div['filesize'] = $domRetorno->createElement('div', __('Tamaño', 'dropbox-folder-share'));
                $html_div['filesize']->setAttribute('class', 'sl-list-column sl-list-column--filesize');
                $html_div['filesize']->setAttribute('style', "width: {$seccionesLista[1]}% !important; display: {$displaySize} ");

                $html_div['modified'] = $domRetorno->createElement('div', __('Modificado', 'dropbox-folder-share'));
                $html_div['modified']->setAttribute('class', 'sl-list-column sl-list-column--modified');
                $html_div['modified']->setAttribute('style', "width: {$seccionesLista[2]}% !important; display: {$displayChange} ");


                $html_div['accion'] = $domRetorno->createElement('span');
                if ($opcion['allowDownload'] === '1') {
                    $html_div['accion'] = $domRetorno->createElement('div', '');
                    $html_div['accion']->setAttribute('class', 'sl-list-column');
                    $html_div['accion']->setAttribute('style', "width: 4% !important; display: {$displayChange} ");
                }


                $html_div['sl-list-row'] = $domRetorno->createElement('div');
                $html_div['sl-list-row']->setAttribute('class', 'sl-list-row clearfix');
                $html_div['sl-list-row']->appendChild($html_div['filename']);
                $html_div['sl-list-row']->appendChild($html_div['filesize']);
                $html_div['sl-list-row']->appendChild($html_div['modified']);
                $html_div['sl-list-row']->appendChild($html_div['accion']);

                $html_div['sl-list-header'] = $domRetorno->createElement('div');
                $html_div['sl-list-header']->setAttribute('class', 'sl-list-header');
                $html_div['sl-list-header']->setAttribute('style', "border-bottom: solid 1px; ");
                $html_div['sl-list-header']->appendChild($html_div['sl-list-row']);

                $html_div['sl-list-container'] = $domRetorno->createElement('div');
                $html_div['sl-list-container']->setAttribute('class', 'sl-list-container');
                $html_div['sl-list-container']->appendChild($html_div['sl-list-header']);
                $html_div['sl-list-container']->appendChild($html_ol['sl-list-body']);

                $html_div['sl-body'] = $domRetorno->createElement('div');
                $html_div['sl-body']->setAttribute('class', 'sl-body');
                $html_div['sl-body']->appendChild($html_div['sl-list-container']);
                //---------------------------------------------------------

                $html_div['sl-page-body'] = $domRetorno->createElement('div');//content $txtNavegacion
                $html_div['sl-page-body']->setAttribute('class', 'sl-page-body');
                $html_div['sl-page-body']->appendChild($html_div['Hyno_Breadcrumbs']);
                $html_div['sl-page-body']->appendChild($html_div['Hyno_Header']);
                $html_div['sl-page-body']->appendChild($html_div['sl-body']);

                $html_div['sl-body']->setAttribute('class', 'sl-body');


                $html_div['Hyno_ContenFolder'] = $domRetorno->createElement('div');
                $html_div['Hyno_ContenFolder']->setAttribute('class', 'Hyno_ContenFolder');
                $html_div['Hyno_ContenFolder']->appendChild($html_div['sl-page-body']);


                $retorno = json_encode(['html' => $domRetorno->saveHTML($html_div['Hyno_ContenFolder']), 'imgs' => $arrayIMG]);
            } else {

                $html_div['Hyno_Breadcrumbs'] = $domRetorno->createElement('div');//content $txtNavegacion
                $html_div['Hyno_Breadcrumbs']->setAttribute('class', 'row');
                $html_div['Hyno_Breadcrumbs']->setAttribute('id', "Hyno_Breadcrumbs_{$id_content}");
                $html_div['Hyno_Breadcrumbs']->appendChild($domRetorno->importNode($html_ol['breadcrumb']));

                $html_div['Hyno_Header'] = $domRetorno->createElement('div');//contenido $txtCarpeta
                $html_div['Hyno_Header']->setAttribute('class', 'sl-header clearfix');
                $html_div['Hyno_Header']->setAttribute('id', "Hyno_Header_{$id_content}");

                $html_img['sl-empty'] = $domRetorno->createElement('img');
                $html_img['sl-empty']->setAttribute('class', 'sl-empty-folder-icon');
                $html_img['sl-empty']->setAttribute('src', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/carpeta.png');

                $html_h4['sl-empty'] = $domRetorno->createElement('h4', __("Esta carpeta está vacía", "dropbox-folder-share"));
                $html_h4['sl-empty']->setAttribute('class', 'sl-empty-folder-message');

                $html_div['sl-body'] = $domRetorno->createElement('div');
                $html_div['sl-body']->setAttribute('class', 'sl-body sl-body--empty-folder');
                $html_div['sl-body']->appendChild($html_img['sl-empty']);
                $html_div['sl-body']->appendChild($html_h4['sl-empty']);

                $html_div['sl-page-body'] = $domRetorno->createElement('div');//content $txtNavegacion
                $html_div['sl-page-body']->setAttribute('class', 'sl-page-body sl-list-container');
                $html_div['sl-page-body']->appendChild($html_div['Hyno_Breadcrumbs']);
                $html_div['sl-page-body']->appendChild($html_div['Hyno_Header']);
                $html_div['sl-page-body']->appendChild($html_div['sl-body']);

                $html_div['Hyno_ContenFolder'] = $domRetorno->createElement('div');
                $html_div['Hyno_ContenFolder']->setAttribute('class', 'Hyno_ContenFolder');
                $html_div['Hyno_ContenFolder']->appendChild($html_div['sl-page-body']);

                $retorno = json_encode(['html' => $domRetorno->saveHTML($html_div['Hyno_ContenFolder']), 'imgs' => []]);


            }


            return $retorno;
        } else {
            return __("No encontrado", "dropbox-folder-share");
        }
    }

    function downloadLinkGenerator($link, $query_params = array()) {
        if (count($query_params) === 0)
            $query_params['dl'] = 1;
        $detalleURL['query'] = http_build_query($query_params);

        $newUrl = http_build_url($link,
            $detalleURL,
            HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
        );
        return $newUrl;
    }

    function add_settings_link($links, $file) {
        if (DROPBOX_FOLDER_SHARE_PLUGIN_BASENAME === $file && current_user_can(self::_PERMISOS_REQUERIDOS_)) {
            $links[] = '<a href="' . esc_url($this->plugin_options_url()) . '" alt="' . esc_attr__('Dropbox Folder Share - Configuracion', "dropbox-folder-share") . '">' . esc_html__('Configurar', "dropbox-folder-share") . '</a>';
        }
        return $links;
    }

    function plugin_options_url() {
        return add_query_arg('page', DROPBOX_FOLDER_SHARE_PLUGIN_NOMBRE, admin_url(self::_PARENT_PAGE_));
    }

    function EliminarTAG(DOMDocument $dom, $query_xpath) {
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query($query_xpath) as $link) {
            // Move all link tag content to its parent node just before it.
            while ($link->hasChildNodes()) {
                $child = $link->removeChild($link->firstChild);
                $link->parentNode->insertBefore($child, $link);
            }
            // Remove the link tag.
            $link->parentNode->removeChild($link);
        }
    }

    function dropbox_foldershare_styles_and_scripts($posts) {
        if (empty($posts)) {
            return $posts;
        }
        $shortcode_found = false; // usamos shortcode_found para saber si nuestro plugin esta siendo utilizado
        foreach ($posts as $post) {

            if (stripos($post->post_content, 'dropbox-foldershare-hyno')) { //shortcode a buscar
                $shortcode_found = true; // bingo!
                break;
            }
            if (stripos($post->post_content, 'DFS')) { //shortcode a buscar
                $shortcode_found = true; // bingo!
                break;
            }

            if (stripos($post->post_content, 'hyno_learn_more')) { //cambiamos testiy por cualquier shortcode
                $shortcode_found = true; // bingo!
                break;
            }
        }
        if ($shortcode_found) {

            self::incluir_JS_CSS();

            //wp_enqueue_script('bible-post-script', plugins_url('scripts-hyno.js', __FILE__)); //en caso de necesitar la ruta de nuestro script js
        }

        return $posts;
    }

    function incluir_JS_CSS() {
        // enqueue
        wp_enqueue_script('jquery');
        //wp_enqueue_script( 'thickbox' );
        wp_enqueue_script('jquery-ui-tooltip');

        wp_enqueue_script('bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), true); // all the bootstrap javascript goodness
        wp_enqueue_script('ekko-lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.js', array('jquery'), true); // all the bootstrap javascript goodness
        wp_enqueue_script('DFS-Script', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/js/scripts-hyno.js', array('jquery'));

        $url_imgLoader = DROPBOX_FOLDER_SHARE_PLUGIN_URL . "/src/img/gears.svg";

        wp_localize_script('DFS-Script', 'objDFS',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'dfs_nonce' => wp_create_nonce('dfs_nonce'),
                'url_imgLoader' => $url_imgLoader
            )
        );

        //wp_enqueue_style( 'thickbox' );
        wp_enqueue_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_enqueue_style('ekko-lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css');
        wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('DFS-Style', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/styles-hyno.css'); //la ruta de nuestro css
    }

    function limpiahtml($codigo) {
        $buscar = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
        $reemplazar = array('>', '<', '\\1');
        $codigo = preg_replace($buscar, $reemplazar, $codigo);
        $codigo = str_replace("> <", "><", $codigo);

        return $codigo;
    }


    function nota_actualizacion() {
        //if(!current_user_can( 'manage_options')) return;
        //$class = 'notice updated is-dismissible';
        $message = '<strong>' . esc_html__('Dropbox Folder Share ha mejorado!', 'dropbox-folder-share') . '</strong><br/>';
        $message .= '<em>' . esc_html__('Seccion en editor.', 'dropbox-folder-share') . ' — ' . esc_html__('Añadido la posibilidad de Widget', 'dropbox-folder-share') . '</em><br/>';
        $message .= sprintf(__('Por favor <a href="%s">revisa las configuraciones</a> para asegurarte de que todo esta bien.', 'dropbox-folder-share'), admin_url('options-general.php?page=dropbox-folder-share')) . '<br/>';
        $message .= '<em>' . esc_html__('Gracias por Utilizar este plugin. Me gustaria leer sugerencias u opiniones para que juntos mejoremos esta herramienta, cualquier sugerencia para mejorar el plugin o reportar algun error nos ayuda muchisimo, no duden en hacernoslo saber. ', "dropbox-folder-share") . '</em>';
        $message .= '<em>' . esc_html__('Pueden hacernos llegar sus sugerencias, opiniones y/o criticas a travez del formulario de contactos de', "dropbox-folder-share") . '<a href="http://www.hynotech.com"> HynoTech.com</a></em>';
        $message .= '<br>';
        $message .= '<em>' . esc_html__('Considera hacer una donacion para ayudarnos con el proyecto. Lo tendremos en gran valor.', "dropbox-folder-share") . '</em>';
        $message .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
</form>
';

        return $message;
        //echo '<div data-tipo="actualizacion" class="'.$class.'"><p>'.$message.'</p></div>';
    }

//https://premium.wpmudev.org/blog/adding-admin-notices/?utm_expid=3606929-101._J2UGKNuQ6e7Of8gblmOTA.0&utm_referrer=https%3A%2F%2Fwww.google.com.pe%2F
    function nota_donacion() {

        $message = "<div class='wrap'>
                    <div class='wpmm-wrapper'>
                        <div class='wrapper-cell'>
                            <div id='SugerenciasComments' class='stuffbox' style='border: 0px;'>
                                <div class='inside'>
                                    <h3>" . __('Dropbox Folder Share NECESITA TU AYUDA!', 'dropbox-folder-share') . "</h3>
                                    <p class='popular-tags'>" . __('Es muy dificil escribir este mensaje, pero es necesario hacerlo.', 'dropbox-folder-share') . "</p>
                                    <p class='popular-tags'>" . __('Dropbox Folder Share lleva siendo actualizada de manera casi continua e inmediata ya mas de 4 años. Lamentablemente el HOST y DOMINIO estan por caducar y hace falta de sus donaciones para poder pagarlos. Lamentablemente no puedo seguir pagando estos servicios por mi cuenta por lo que recurro a ustedes a que puedan ayudarme con esto.', 'dropbox-folder-share') . " </p>
                                    <p class='popular-tags'>" . __('Cualquier donacion que puedan hacer sera muy bien recibido y estare muy agradecido por ello.', 'dropbox-folder-share') . " </p>
                                    <p class='popular-tags'>" . __('No quiero dejar pasar esta oportunidad de dar gracias a los 2 amigos de FRANCIA que tuvieron a bien donar el año 2014. MUCHAS GRACIAS', 'dropbox-folder-share') . " </p>
                                    <img align='right' src='" . DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/AntonySalas_signature.png' . "'>
                                    <br>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                        <div id='sidebar' class='wrapper-cell'>

                            <div class='sidebar_box info_box'>
                                <h3>" . __('Donaciones', 'dropbox-folder-share') . "</h3>
                                <div class='inside'>
                                    <div class='misc-pub-section center'>
                                        <form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_blank'>
                                            <input type='hidden' name='cmd' value='_s-xclick'>
                                            <input type='hidden' name='encrypted' value='-----BEGIN PKCS7-----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-----END PKCS7-----
                                                           '>
                                            <input type='image' src='" . DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/paypal_200x96.png' . "' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
                                            <img alt='' border='0' src='https://www.paypalobjects.com/es_XC/i/scr/pixel.gif' width='1' height='1'>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class='sidebar_box info_box'>
                                <div class='inside'>
                                    <div class='misc-pub-section center'>

                                        <img width='150px' src='" . DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/DropBox-Folder-Share-300x300.png' . "'>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";


        return $message;

    }

    function nota_assets() {
        //ddd("HOLAAAAA");
        //wp_enqueue_script( 'my-notice-update', plugins_url( '/js/notice-update.js', __FILE__ ), array( 'jquery' ), '1.0', true  );
        wp_enqueue_script('DFS-Script-Admin', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/js/scripts-hyno.js', array('jquery'));
        wp_enqueue_style('DFS-Style-Admin', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/styles-hyno-admin.css'); //la ruta de nuestro css
    }


}
