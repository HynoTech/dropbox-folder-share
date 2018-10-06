# Troubleshooting

### Ensure you have the latest version installed

```bash
$ cd php-curl-class/
$ composer update
$ composer info
```
Compare your version with latest release listed on the [releases page](https://github.com/php-curl-class/php-curl-class/releases).

### Turn on error reporting

```php
error_reporting(E_ALL);
```

### Turn on verbose mode

```php
error_reporting(E_ALL);
$curl = new Curl();
$curl->verbose();
$curl->get('https://www.example.com/');
var_dump($curl);
```

### Compare request with and without the library

```php
error_reporting(E_ALL);
$curl = new Curl();
$curl->get('https://www.example.com/');
var_dump($curl);
```

```php
error_reporting(E_ALL);
$ch = curl_init();
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'https://www.example.com/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPGET, true);
$raw_response = curl_exec($ch);
$curl_error_code = curl_errno($ch);
$curl_error_message = curl_error($ch);
$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$request_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
```
