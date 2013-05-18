<?php
/**
 * dHttp is is library to work with Curl	
 * Example to use library
 */
include_once('dHttp/dHttp.php');
$http = new dHttp('http://www.mail.ru');

/*
 * Simple request
 */
//$data = array('test'=>'Simple');
$http->get();