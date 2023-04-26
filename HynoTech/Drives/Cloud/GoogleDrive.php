<?php


namespace HynoTech\Drives\Cloud;


use HynoTech\Drives\Adicionales\Archivo;
use HynoTech\Drives\Adicionales\Carpeta;
use HynoTech\UsosGenerales\FetchCurl;

class GoogleDrive extends Cloud {

	private function getId(){
		$d = explode('/', $this->url);
		return end($d);
	}

	function dataContenido($retorno = 'json'){

		// OBTENER INFO DE CARPETA O ARCHIVO SEGUN ID
		//!d($this->getId());
		//$this->getFileInfo($this->getId());
		//die;

		$patronTitulo = "|<title>(.*)</title>|m";
		preg_match($patronTitulo, $this->dataCargado->response, $dataTitulo);

		// !d($dataTitulo);
		$carpetaActual = new Carpeta();
		$carpetaActual->id = $this->getId();
		$carpetaActual->nombre = explode(' - ', $dataTitulo[1])[0];
		$carpetaActual->href = $this->url;
		$carpetaActual->subCarpetas = [];
		$carpetaActual->archivos = [];

		$patronModule = "|window\[\\'_DRIVE_ivd\\'\]\s*=\s*\\'([^\\']+)\\'|m";
		preg_match_all($patronModule, $this->dataCargado->response, $varTemp2);
		// preg_match_all("/window\[\\'_DRIVE_ivd\\'\]\s*=\s*\\'([^\\']+)\\'/m", $this->dataCargado->response, $varTemp2);

		// AL PARECER EL ID ES UNICO POR ENDE, NO ES NECESARIO ESTA PARTE A MENOS QUE LUEGO SEA DIF
/*
		preg_match_all("/<script>__initData\s*=\s*([^\\']+);<\/script>/m", $this->dataCargado->response, $output_array);
		$jsonDatKey = json_decode($output_array[1][0]);
		$tiposPreview = explode(',', $jsonDatKey[0][32][0]);*/

//		!d($tiposPreview);
//		!d($jsonDatKey);
		//!d($jsonDatKey[0][9][32][35]);



		$patron = [
			'/\\\\x22/',
			'/\\\\x5b/',
			'/\\\\x5d/',
			'/\\\\x7b/',
			'/\\\\x7d/',
			'/\\\\n/',
			//"/\\\\\\/",
		];
		$reemplazos = [
			'"',
			'[',
			']',
			'{',
			'}',
			'',
			//'\\',
		];

		$input_lines = $varTemp2[1][0];
		//$a = preg_replace(['/\\\\x5b/', '/\\\\x22/'], ['[', '"'], $input_lines);
		$dataString = preg_replace($patron, $reemplazos, $input_lines);

		// !d($dataString);

		$jsonR = json_decode($dataString);

		$jsonR = $jsonR[0];


		if ($jsonR != null) {
			 // d($jsonR);

			foreach ($jsonR as $file) {
				if (isset($file) && is_array($file)){
					$type = $file[3];

					// !d($file);
					if ($this->endsWith($type, 'folder')) {
						$objItem = new Carpeta();
						$objItem->id = $file[0];
						$objItem->nombre = $file[2];
						$objItem->href = 'https://drive.google.com/drive/folders/' . $file[0];
						$objItem->fechaCreacion = $file[9];
						$objItem->propietario = $file[14][0][2];
						$objItem->dataOriginal = $file;
						$carpetaActual->subCarpetas[] = $objItem;
					} else {
						//ARCHIVOS
						$objItem = new Archivo();
						$objItem->id = $file[0];
						$objItem->nombre = $file[2];
						$objItem->href = 'https://drive.google.com/uc?id='.$file[0].'&export=download';
						$objItem->peso = $file[13];
						$objItem->fechaCreacion = $file[9];
						$objItem->fechaEdicion = $file[10];
						$objItem->miniatura = 'https://lh3.googleusercontent.com/u/0/d/'. $file[0] .'=w32-h32-p-k-nu-iv1'; //(in_array($type, $tiposPreview) ? 'https://lh3.googleusercontent.com/u/0/d/'. $file[0] .'=w32-h32-p-k-nu-iv1' : null);
						$objItem->tipo = $type;
						$objItem->icono = 'https://drive-thirdparty.googleusercontent.com/16/type/' . $type;
						//$objItem->previsualizacion = $item->preview->preview_url;
						$objItem->dataOriginal = $file;
						$carpetaActual->archivos[] = $objItem;
					}

				}

			}

        }

		if ($retorno != 'json'){
			return $carpetaActual;
		}

		return json_encode($carpetaActual);
	}

	public function getFileInfo($id) {
		$objFetchCurl = new FetchCurl();
		try {
			$data = $objFetchCurl->getContent(
				//'https://clients6.google.com/drive/v2beta/files/0Bzk1CxT5hrDdb2J2eGcwdjRrMXM?key=AIzaSyC1qbk75NzWBvSaDh6KnsjjA9pIrP4lYIE',
				'https://clients6.google.com/drive/v2beta/files/'.$id.'?key=AIzaSyC1qbk75NzWBvSaDh6KnsjjA9pIrP4lYIE',
				false,
				'get',
				[
					//'key' => 'AIzaSyC1qbk75NzWBvSaDh6KnsjjA9pIrP4lYIE',
					//'supportsTeamDrives' => 'true',
					//'fields' => 'alternateLink%2CcopyRequiresWriterPermission%2CcreatedDate%2Cdescription%2CfileSize%2CiconLink%2Cid%2Clabels(starred%2C%20trashed)%2ClastViewedByMeDate%2CmodifiedDate%2Cshared%2CteamDriveId%2CimageMediaMetadata(height%2C%20width)%2CthumbnailLink%2CuserPermission(id%2Cname%2CemailAddress%2Cdomain%2Crole%2CadditionalRoles%2CphotoLink%2Ctype%2CwithLink)%2Cpermissions(id%2Cname%2CemailAddress%2Cdomain%2Crole%2CadditionalRoles%2CphotoLink%2Ctype%2CwithLink)%2Cparents(id)%2Ccapabilities(canMoveTeamDriveItem%2CcanAddChildren%2CcanEdit%2CcanDownload%2CcanComment%2CcanRename%2CcanRemoveChildren%2CcanMoveItemIntoTeamDrive)%2Ckind',
				],
				[],
				[
					'Referer' => 'https://clients6.google.com/static/proxy.html?usegapi=1&jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.gapi.en.7kWSr24wXFc.O%2Fd%3D1%2Fct%3Dzgms%2Frs%3DAHpOoo-i9r7IbCTUQfJ0v-FPhRKRS8aihQ%2Fm%3D__features__',
					//'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:71.0) Gecko/20100101 Firefox/71.0',
				]
			);

			if (isset($data)){
				d($data->requestHeaders);
				d($data->response);
			}
		}
		catch (\ErrorException $e) {
			echo "Error: ".$e->getMessage();
		}
	}

	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}

}

/*
 *                        .replace(/\\x22/g, '"')
                        .replace(/\\x5b/g, '[').replace(/\\x5d/g, ']')
                        .replace(/\\x7b/g, '{').replace(/\\x7d/g, '}')
                        .replace(/\\n/g, '').replace(/\\\\/g, '\\')
 */
