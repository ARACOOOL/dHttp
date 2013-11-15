<?php
/**
 * dHttp is library to work with Curl
 * Example to use library
 */

include_once('dHttp/dHttp.php');
include_once('dHttp/dResponse.php');

$http = new dHttp\dHttp('http://website.com', array(
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
	CURLOPT_TIMEOUT => 5,
	CURLOPT_HEADER => true
));

/*
 * Simple get request
 */
$resp = $http->get();
// Get response code
var_dump($resp->http_code);
// Get response body
var_dump($resp->getBody());
// Get request errors
var_dump($resp->errors);

/*
 * Simple post request
 */
$resp = $http->post(array(
		'field1' => 'value1',
		'field2' => 'value2',
	));

// Get response headers
var_dump($resp->header);

/**
 * Another way of setting.
 * Output response
 */
$http = new dHttp\dHttp();

$http->add_options(array(CURLOPT_RETURNTRANSFER => false))
	->setUserAgent('Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1')
	->setCookie('/tmp/cookies.txt')
	->setUrl('http://website.com')
	->get();

/**
 * Use multi curl
 */

$multi = new dHttp\dHttp();
$response_array = $multi->multi(array(
	new dHttp\dHttp('http://website1.com'),

	new dHttp\dHttp('http://website2.com', array(
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
		CURLOPT_TIMEOUT => 5,
	))
));

foreach($response_array as $item) {
	var_dump($item->http_code);
}

/**
 * Get cURL version
 */
\dHttp\dHttp::v();