<?php
/**
 * dHttp is library to work with Curl
 * Example to use library
 */
include_once('dHttp/dHttp.php');
$http = new dHttp('http://website.com', array(
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1'
));

/*
 * Simple get request
 */
$http->get();

/*
 * Simple post request
 */
$http->add_options(array(
		CURLOPT_RETURNTRANSFER => false,
	))->post(array(
		'field1' => 'value1',
		'field2' => 'value2',
	));

/**
 * Another way of setting
 */
$http = new dHttp();

$http->set_user_agent('Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1')
	->set_cookie('/tmp/cookies.txt')
	->set_url('http://website.com')
	->get();