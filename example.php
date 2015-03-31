<?php
/**
 * dHttp is library to work with Curl
 * Example to use library
 */

include_once('dHttp/Client.php');
include_once('dHttp/Response.php');

$http = new dHttp\Client('http://website.com', [
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
	CURLOPT_TIMEOUT => 5,
	CURLOPT_HEADER => true
]);

/*
 * Simple get request
 */
$resp = $http->get();
// Get response code
$resp->getCode();
// Get response body
$resp->getBody();
// Get request errors
$resp->getErrors();

/*
 * Simple post request
 */
$resp = $http->post([
	'field1' => 'value1',
	'field2' => 'value2',
]);

$resp->getRaw();
// Return response headers
$resp->getHeaders();
// Return a specific (text/html; charset=utf-8)
$resp->getHeader('Content-Type');

/**
 * Another way of setting.
 * Output response
 */
$http = new dHttp\Client();

$http->addOptions([CURLOPT_RETURNTRANSFER => false])
	->setUserAgent('Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1')
	->setCookie('/tmp/cookies.txt')
	->setUrl('http://website.com')
	->get();

/**
 * Use multi curl
 */

$multi = new dHttp\Client();
$response_array = $multi->multi([
	new dHttp\Client('http://website1.com'),

	new dHttp\Client('http://website2.com', [
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
		CURLOPT_TIMEOUT => 5,
    ])
]);

foreach ($response_array as $item) {
	$resp->getCode();
}

/**
 * Get cURL version
 */
\dHttp\Client::v();
