<?php
if (!class_exists("ReadDrives")) {

    class ReadDrives extends DropboxFolderSharePrincipal{

        var $url_lnk;
        var $htmlCode;
        var $nombreCarpeta;

        function __construct($url_lnk, $htmlCode, $nombreCarpeta) {
            $this->url = $url_lnk;
            $this->htmlCode = $htmlCode;
            $this->nombreCarpeta = $nombreCarpeta;
            include_once (parent::$url_path . 'js/scripts-hyno.php');
            //include_once('../js/scripts-hyno.php');
        }

        function Dropbox() {
            $htmlCode = $this->htmlCode;
            $e = $htmlCode->find('body', 0);
            if ($e) {
                $div_contenedor = str_get_html($e->innertext);
                $script_nombres = $div_contenedor->find('script', -1);
                $inicio = strpos($script_nombres, '$(');
                $fin = strpos($script_nombres, 'window.c2d_tabs', $inicio);
                $cadena = substr($script_nombres, $inicio, $fin - $inicio);
                $data_lineas = explode('.escapeHTML();', $cadena);
                if (count($data_lineas) > 1) {
                    $file_data = array();
                    foreach ($data_lineas as $links) {
                        $links = trim($links);
                        $data_link = explode('=', $links);
                        // - Id de Archivo - //
                        $es_carpeta = 1;
                        if ($data_link[0] != "") {
                            $num_car = strlen($data_link[0]);
                            $inicio_id_archivo = 'on(';
                            $fin_id_archivo = ').';

                            $total = strpos($data_link[0], $inicio_id_archivo) + 4;
                            $total2 = strpos($data_link[0], $fin_id_archivo);
                            $total3 = ($num_car - $total2 + 2);
                            $id_archivo = substr($data_link[0], $total + 1, -$total3);
                            $id_archivo = eregi_replace('\"', '', $this->formatFileNames($id_archivo));
                            // - Nombre de Archivo - //
                            $num_car = strlen($data_link[1]);
                            $inicio_nombre_archivo = "on(";
                            $fin_nombre_archivo = ").";
                            $total = strpos($data_link[1], $inicio_nombre_archivo) + 6;
                            $total2 = strpos($data_link[1], $fin_nombre_archivo);
                            $total3 = ($num_car - $total2 + 3 );
                            $nombre_archivo = substr($data_link[1], $total, -$total3);
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
                    //echo "<textarea cols=80 rows=10>";
                    $resArchivos = "";
                    foreach ($div_contenedor->find('li[class=browse-file]') as $archivos) {

                        foreach ($archivos->find('div[class=filename] span') as $file_names) {
                            foreach ($file_data['id'] as $key => $value) {
                                if ($file_names->id == $value) {
                                    $file_names->innertext = $file_data['nombre'][$key];
                                }
                            }
                        }
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
                        }
                        foreach ($archivos->find('div[class=filesize-col] span') as $datos) {
                            $data_all_files['peso'][] = $datos->innertext;
                        }
                        foreach ($archivos->find('div[class=modified-col] span[!class]') as $datos) {
                            $data_all_files['modificado'][] = $datos->innertext;
                        }
                        $resArchivos .= $archivos;
                        //echo $archivos;
                    }
                    
                
                //echo "</textarea>";
                }


                $contenDropbox = $div_contenedor->find('#outer-frame', 0)->outertext;
                $contenDropbox = str_get_html($contenDropbox);
                $contenDropbox->find('.buttons', 0)->outertext = "";
                $contenDropbox->find('#db-modal-locale-selector-modal', 0)->outertext = "";
                $contenDropbox->find('noscript', 0)->outertext = "";
                $contenDropbox->find('.big', 0)->outertext = "";
                //$contenDropbox->find('h3#folder-title', 0)->outertext = "";
                
                foreach ($contenDropbox->find('
                    #modal-progress-content, 
                    #twitter-login, 
                    #facebook-auth, 
                    #twitter-posting, 
                    #facebook-posting, 
                    #disable-token-modal,
                    #album-disable-token-modal') as $txt_version) {
                    $txt_version->outertext = '';
                }
                echo "<textarea cols=80 rows=10>" . $contenDropbox . "</textarea>";
                $header = str_get_html($contenDropbox->find('.nav-header', 0)->innertext);
                $header->find('a', 0)->href = $this->url;
                $header->find('a', 0)->target = "new";
                $head[0] = $header->find('a', 0)->innertext;
                
                if($this->nombreCarpeta){
                    $n_Carpeta = $this->nombreCarpeta;
                }else{
                    $n_Carpeta = $file_data['nombre'][0];
                }
                
                $head[1] = "<div id='folder-title'>://". $n_Carpeta ."/</div><div class='cloud-link'></div>";
                $header->find('a', 0)->innertext = $head[0] . $head[1];
                
                
                
                $contenDropbox->find('.nav-header', 0)->innertext = $header->find('a', 0)->outertext;
                $contenDropbox->find('.content-flag', 0)->outertext = "";
                
                $contenDropbox->find('ol', 0)->innertext = $resArchivos;
                $arch = "<div class='CFS_Content'>";
                $arch .= $contenDropbox->find('ol', 0)->outertext;
                $arch .= "<div>";
                $contenDropbox->find('ol', 0)->outertext = $arch;
                $esto = $contenDropbox->find('.nav-header', 0)->outertext;
                $esto .= $contenDropbox->find('#shmodel-content-area', 0)->outertext;
                $contenDropbox->find('#page-content', 0)->innertext = $esto;

                //echo "<textarea cols=80 rows=10>";
                //echo $contenDropbox;
                //print_r($data_lineas);
                //echo "</textarea>";
            }
            return $contenDropbox;
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

    }

}

