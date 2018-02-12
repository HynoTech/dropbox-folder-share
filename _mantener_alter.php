<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 11/02/2018
 * Time: 22:42
 */




//d($content);

$dataTotal = new Query($response->response);

$files_Folders = $dataTotal->execute('#file-listing li');

//$tablaHTML = $results[0]->ownerDocument->saveHTML($results[0]);

foreach ($files_Folders as $file) {



    $archivoData = $file->firstChild->ownerDocument->saveHTML($file->firstChild);

    $tipo = $file->firstChild->lastChild->getAttribute('class');

    $carpetasCarpeta = array();
    $archivosCarpeta = array();

    switch ($tipo){
        case 'folder-text':
            $carpeta = [
                'filename' => $file->childNodes->item(0)->textContent,
                'href' => $file->childNodes->item(0)->getAttribute('href')
            ];
            $carpetasCarpeta[] = (object) $carpeta;
            break;
        case 'file-text':
            $archivo = [
                'filename' => $file->childNodes->item(0)->textContent,
                'href' => $file->childNodes->item(0)->getAttribute('href')
            ];
            $archivosCarpeta[] = (object) $archivo;

            break;
        default:
            break;
    }

    d($tipo);

    //$dataTotal = new Query($archivoData);


    $resLinea = $file->childNodes->item(0)->tagName;
    d($resLinea);
    $resLinea = $file->childNodes->item(0)->textContent;
    d($resLinea);
    $resLinea = $file->childNodes->item(0)->getAttribute('href');
    d($resLinea);


    //$tablaHTML = $result->ownerDocument->saveHTML($result);
    //d($tablaHTML);
}

