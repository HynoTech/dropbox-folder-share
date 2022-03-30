<?php


namespace HynoTech\Drives\Cloud;


use HynoTech\Drives\Adicionales\Archivo;
use HynoTech\Drives\Adicionales\Carpeta;
use HynoTech\UsosGenerales\FetchCurl;

class Dropbox extends Cloud{

	/*
	private $dataCargado;

	public function __construct($url) {
		$objFetchCurl = new FetchCurl();
		try {
			$data = $objFetchCurl->getContent($url);

			if (isset($data) && $data->response != ''){
				$this->dataCargado = $data;
				// $this->dataContenido();
			}
		}
		catch (\ErrorException $e) {
			echo "Error: ".$e->getMessage();
		}
	}
	*/
	function dataContenido($retorno = 'json'){
        $match[0] = 'responseReceived("{';
        $match[1] = '}")});';
        $dataModule[0] = str_replace('(', '\(', $match[0]);
        $dataModule[1] = str_replace(')', '\)', $match[1]);

		$patronModule = '|' . $dataModule[0] . '(.*?)' . $dataModule[1] . '|is';
		preg_match_all($patronModule, $this->dataCargado->response, $varTemp2);
		$soloDataModule = str_replace($match, array('{', '}'), (isset($varTemp2[0][0])) ? $varTemp2[0][0] : '');

        $soloDataModule = str_replace('\"', '"', $soloDataModule);
        $re = '/\\\\+"/';
        $subst = '\'';
        $soloDataModule = preg_replace($re, $subst, $soloDataModule);

		$objImportante = json_decode($soloDataModule);

		$carpetaActual = new Carpeta();
		$carpetaActual->id = $objImportante->folder->ns_id;
		$carpetaActual->nombre = $objImportante->folder_shared_link_info->displayName;
		$carpetaActual->href = $objImportante->folder_shared_link_info->url;
		$carpetaActual->linkKey = $objImportante->folder_share_token->linkKey;
		$carpetaActual->linkSecureHash = $objImportante->folder_share_token->secureHash;
		$carpetaActual->linkSubPath = $objImportante->folder_share_token->subPath;
		$carpetaActual->linkType = $objImportante->folder_share_token->linkType;
		$carpetaActual->propietario = $objImportante->folder_shared_link_info->ownerName;
		$carpetaActual->subCarpetas = [];
		$carpetaActual->archivos = [];
//		$carpetaActual->dataOriginal = $objImportante;

		$cookies = $this->dataCargado->responseCookies;
		$postValues = [
			'is_xhr' => 'true',
			'link_key' => $carpetaActual->linkKey,
			'secure_hash' => $carpetaActual->linkSecureHash,
			'link_type' => $carpetaActual->linkType,
			'sub_path' => $carpetaActual->linkSubPath,
			't' => $cookies['t']
		];

		$objFetchCurl = new FetchCurl();

		try {
			do {
				$responseData = $objFetchCurl->getContent('https://www.dropbox.com/list_shared_link_folder_entries', false, 'post', $postValues, $cookies);
				if ($responseData !== null) {
					foreach ($responseData->response->entries as $item) {
						$item->filename = htmlentities($item->filename);
						if ($item->is_dir) {
							//CARPETAS
							$objItem = new Carpeta();
							$objItem->id = $item->ns_id;
							$objItem->nombre = $item->filename;
							$objItem->href = $item->href;
							//$objItem->dataOriginal = $item;
							$carpetaActual->subCarpetas[] = $objItem;
						} else {
							//ARCHIVOS
							$objItem = new Archivo();
							$objItem->id = $item->sjid;
							$objItem->nombre = $item->filename;
							$objItem->href = $item->href;
							$objItem->peso = $item->bytes;
							$objItem->fechaCreacion = $item->ts;
							$objItem->miniatura = $item->thumbnail_url_tmpl;
							$objItem->tipo = $item->preview_type;
							$objItem->icono = $item->icon;
							$objItem->previsualizacion = $item->preview->preview_url;
							//$objItem->dataOriginal = $item;
							$objItem->extension = pathinfo($item->filename)['extension'];
							$carpetaActual->archivos[] = $objItem;
						}
					}

					if ($responseData->response->has_more_entries) {
						$postValues["voucher"] = $responseData->response->next_request_voucher;
					}
				}

			} while ($responseData->response->has_more_entries);

			// !d($carpetasCarpeta, $archivosCarpeta);

		}
		catch (\ErrorException $e) {
			echo "Error: ".$e->getMessage();
		}

		if ($retorno != 'json'){
			return $carpetaActual;
		}

		return json_encode($carpetaActual);
	}
}
