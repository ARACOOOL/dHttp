<?php

include_once('./dHttp.php');
$data = array('test'=>'Simple');
 
$http = new dHttp();
$http->setParams($data);
$http->showHeaders(true);
$http->setUrl('http://test1.ru/test2.php');
echo $http->run();
?>
