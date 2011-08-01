<?php
/**
 * dHttp is is library to work with Curl	
 * Example to use library
 */
include_once('dHttp/dHttp.php');
$http = new dHttp();

/*
 * Simple request
 */
$data = array('test'=>'Simple');
$http->setParams($data);
$http->showHeaders(true);
$http->setUrl('http://test1.com/test');
echo $http->run();
// Close CURL
unset($http);
?>
