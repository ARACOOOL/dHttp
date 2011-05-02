<?php
/*
 * Example to use library
 */
include_once('dHttp/dHttp.php');
$data = array('test'=>'Simple');
 
$http = new dHttp();
$http->setParams($data);
$http->showHeaders(true);
$http->setUrl('http://test1.com/test');
echo $http->run();
?>
