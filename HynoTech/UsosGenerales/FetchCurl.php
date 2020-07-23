<?php


namespace HynoTech\UsosGenerales;

use Curl\Curl;

class FetchCurl {

	function __construct() {
		if (!function_exists("curl_init")) {
			echo "ERROR: CURL no Habilitado";
			die;
		}
	}

	/**
	 * @param        $url
	 * @param bool   $headers
	 * @param string $tipo
	 * @param array  $data
	 * @param array  $cookies
	 * @param string $locale
	 *
	 * @return Curl|null
	 * @throws \ErrorException
	 */
	public function getContent($url, $headers = false, $tipo = 'get', $data = array(), $cookies =  array(), $otherHeaders = array(), $locale = 'es-ES') {
		$curl = new Curl();
		$curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
		$curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);

		if ($headers) {
			$curl->setopt(CURLOPT_HEADER, true);
			$curl->setopt(CURLOPT_NOBODY, true);
		}
		$curl->setopt(CURLOPT_FOLLOWLOCATION, true);
		$curl->setHeader('User-Agent', 'Mozilla/5.0 (Linux; U; Android 4.3; en-us; SM-N900T Build/JSS15J) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
		$curl->setHeader('Accept-Language', $locale . ',en-US;q=0.7,en;q=0.3');

		foreach ($otherHeaders as $head => $value) {
			$curl->setHeader($head, $value);
		}

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

		return $curl;

		return ($headers) ? $curl->responseHeaders : $curl;
	}
}
