<?php
/**
 * @author: Askar
 * @date: 20.05.13
 */
include_once(__DIR__ . '/../dHttp/Client.php');
include_once(__DIR__ . '/../dHttp/dResponse.php');

class dHttpTest extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 */
	public function testGetRequest()
	{
		$http = new dHttp\Client('http://php.net');
		$resp = $http->get();

		$this->assertInstanceOf('dHttp\dResponse', $resp);
		$this->assertEquals($resp->getCode(), 200);
	}

	/**
	 *
	 */
	public function testPostRequest()
	{
		$http = new dHttp\Client('http://php.net');
		$resp = $http->post();

		$this->assertInstanceOf('dHttp\dResponse', $resp);
		$this->assertEquals($resp->getCode(), 200);
	}

	/**
	 *
	 */
	public function testMultiRequest()
	{
		$multi = new dHttp\Client();
		$response_array = $multi->multi(array(
			new dHttp\Client('http://php.net'),

			new dHttp\Client('http://www.python.org/', array(
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
				CURLOPT_TIMEOUT => 10,
			))
		));

		foreach($response_array as $item) {
			$this->assertInstanceOf('dHttp\dResponse', $item);
			$this->assertEquals($item->getCode(), 200);
		}
	}
}
