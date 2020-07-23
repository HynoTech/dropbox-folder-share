<?php


namespace HynoTech\Cloud;


use HynoTech\Cloud;
use HynoTech\Drive\Adicionales\Carpeta;
use HynoTech\Drive\Adicionales\Archivo;
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
		$dataModule[0] = '/app", "props": {';
		$dataModule[1] = '}, "elem_id":';

		$patronModule = '|' . $dataModule[0] . '(.*?)' . $dataModule[1] . '|is';
		preg_match_all($patronModule, $this->dataCargado->response, $varTemp2);

		$soloDataModule = str_replace(array('/app", "props": {', '}, "elem_id":'), array('{', '}'), (isset($varTemp2[0][0])) ? $varTemp2[0][0] : '');

		$objImportante = json_decode($soloDataModule);

		$carpetaActual = new Carpeta();
		$carpetaActual->id = $objImportante->folderData->ns_id;
		$carpetaActual->nombre = $objImportante->folderSharedLinkInfo->displayName;
		$carpetaActual->href = $objImportante->folderSharedLinkInfo->url;
		$carpetaActual->linkKey = $objImportante->folderShareToken->linkKey;
		$carpetaActual->linkSecureHash = $objImportante->folderShareToken->secureHash;
		$carpetaActual->linkSubPath = $objImportante->folderShareToken->subPath;
		$carpetaActual->linkType = $objImportante->folderShareToken->linkType;
		$carpetaActual->propietario = $objImportante->folderSharedLinkInfo->ownerName;
		$carpetaActual->subCarpetas = [];
		$carpetaActual->archivos = [];
		$carpetaActual->dataOriginal = $objImportante;


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
							$objItem->dataOriginal = $item;
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
							$objItem->dataOriginal = $item;
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
