<?php


namespace HynoTech;


use HynoTech\UsosGenerales\FetchCurl;

class Cloud {
	public $dataCargado;
	public $url;

	public function __construct($url) {
		$this->url = $url;
		$objFetchCurl = new FetchCurl();
		try {
			$data = $objFetchCurl->getContent($url);

			if (isset($data) && $data->response != ''){
				$this->dataCargado = $data;
			}
		}
		catch (\ErrorException $e) {
			echo "Error: ".$e->getMessage();
		}
	}

	public function getHeaders() {
		$objFetchCurl = new FetchCurl();
		try {
			$data = $objFetchCurl->getContent($this->url, true);

			if (isset($data)){
				d($data->responseHeaders);
			}
		}
		catch (\ErrorException $e) {
			echo "Error: ".$e->getMessage();
		}
	}
}
