<?php
/**
 * @author: Askar
 * @date: 20.05.13
 */
include_once(__DIR__ . '/../vendor/autoload.php');

class dHttpTest extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 */
	public function testGetRequest()
	{
		$http = new dHttp\Client('http://php.net');
		$resp = $http->get(array(
				CURLOPT_HEADER => true
			));

		$this->assertInstanceOf('dHttp\Response', $resp);
		$this->assertEquals($resp->getCode(), 200);
		$this->assertInternalType('array', $resp->getHeaders());
		$this->assertInternalType('string', $resp->getHeader('Content-Type'));
		$this->assertEquals('text/html; charset=utf-8', $resp->getHeader('Content-Type'));
	}

	/**
	 *
	 */
	public function testPostRequest()
	{
		$http = new dHttp\Client('http://php.net');
		$resp = $http->post(array(), array(CURLOPT_HEADER => true));

		$this->assertInstanceOf('dHttp\Response', $resp);
		$this->assertEquals($resp->getCode(), 200);
	}

	/**
	 *
	 */
	public function testMultiRequest()
	{
		$multi = new dHttp\Client();
		$response_array = $multi->multi(array(
			new dHttp\Client('http://php.net', array(
				CURLOPT_FOLLOWLOCATION => true	
			)),

			new dHttp\Client('http://www.python.org/', array(
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
				CURLOPT_TIMEOUT => 10,
				CURLOPT_FOLLOWLOCATION => true
			))
		));

		foreach($response_array as $item) {
			$this->assertInstanceOf('dHttp\Response', $item);
			$this->assertEquals($item->getCode(), 200);
		}
	}
}
