<?php
/**
 * @author: User
 * @date: 20.05.13
 */
include_once(__DIR__ . '/../dHttp/dHttp.php');
include_once(__DIR__ . '/../dHttp/dResponse.php');

class dHttpTest extends PHPUnit_Framework_TestCase {
	/**
	 *
	 */
	public function testGetRequest() {
		$http = new dHttp\dHttp('http://jangoz.net');
		$resp = $http->get();

		$this->assertInstanceOf('dHttp\dResponse', $resp);
		$this->assertEquals($resp->http_code, 200);
	}

	/**
	 *
	 */
	public function testPostRequest() {
		$http = new dHttp\dHttp('http://jangoz.net');
		$resp = $http->post();

		$this->assertInstanceOf('dHttp\dResponse', $resp);
		$this->assertEquals($resp->http_code, 200);
	}
}