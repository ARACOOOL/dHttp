<?php
/**
 * dHttp is is library to work with Curl	
 * Example to use library
 */
include_once('dHttp/dHttp.php');
$http = new dHttp('http://website.com', array(
	CURLOPT_RETURNTRANSFER => false
));

/*
 * Simple request
 */
$http->get();

/*
 * Simple request
 */
$http->post(array(
	'field1' => 'value1',
	'field2' => 'value2',
));