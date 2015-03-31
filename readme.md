dHttp is a lightweight library to work with Curl.
Easy-to-use library!

[![Latest Stable Version](https://poser.pugx.org/aracoool/dhttp/v/stable.svg)](https://packagist.org/packages/aracoool/dhttp) [![Build Status](https://travis-ci.org/ARACOOOL/dHttp.png?branch=master)](https://travis-ci.org/ARACOOOL/dHttp) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ARACOOOL/dHttp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ARACOOOL/dHttp/?branch=master) [![License](https://poser.pugx.org/aracoool/dhttp/license.svg)](https://packagist.org/packages/aracoool/dhttp)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/576d6279-1a3b-48db-945c-41e2723fe15a/big.png)](https://insight.sensiolabs.com/projects/576d6279-1a3b-48db-945c-41e2723fe15a)

## Install

* Using packagist/composer:
The recommended way to install library is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "aracoool/dhttp" : "dev-master"
    }
}
```

* Cloning the git repository

## Examples

### GET request:

```php
include_once('dHttp/Client.php');
include_once('dHttp/Response.php');

$http = new dHttp\Client('http://website.com');

$resp = $http->get();
// Get response code
var_dump($resp->getCode());
// Get response body
var_dump($resp->getBody());
// Get request errors
var_dump($resp->getErrors());
// Return response headers
var_dump($resp->getHeaders());
// Return a specific (text/html; charset=utf-8)
var_dump($resp->getHeader('Content-Type'));
```

### POST request:

```php
include_once('dHttp/Client.php');
include_once('dHttp/Response.php');

$http = new dHttp\Client('http://website.com');

$http->addOptions(array(CURLOPT_RETURNTRANSFER => false))
	->setCookie('/tmp/cookie.txt')
	->setUserAgent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31')
	->post(array(
		'field1' => 'value1',
		'field2' => 'value2',
));
```

### Multithreaded query:

```php
include_once('dHttp/Client.php');
include_once('dHttp/Response.php');

$multi = new dHttp\Client();
$response_array = $multi->multi(array(
	new dHttp\Client('http://website1.com'),

	new dHttp\Client('http://website2.com', array(
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
		CURLOPT_TIMEOUT => 5,
	))
));

foreach($response_array as $item) {
	var_dump($item->getCode());
}
```

### Get cURL version:

```php
\dHttp\Client::v();
```
