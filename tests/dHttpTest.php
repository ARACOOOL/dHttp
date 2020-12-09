<?php
/**
 * @author: Askar
 * @date: 20.05.13
 */

use PHPUnit\Framework\TestCase;

include_once(__DIR__ . '/../vendor/autoload.php');

/**
 * Class dHttpTest
 */
class dHttpTest extends TestCase
{
    /**
     *
     */
    public function testGetRequest()
    {
        $http = new dHttp\Client('https://www.php.net/');
        $resp = $http->get([
            CURLOPT_HEADER => true
        ]);

        $this->assertInstanceOf('dHttp\Response', $resp);
        $this->assertEquals(200, $resp->getCode());
        $this->assertIsArray($resp->getHeaders());
        $this->assertIsString($resp->getHeader('Content-Type'));
        $this->assertEquals('text/html; charset=utf-8', $resp->getHeader('Content-Type'));
    }

    /**
     *
     */
    public function testPostRequest()
    {
        $http = new dHttp\Client('https://www.php.net/');
        $resp = $http->post([], [CURLOPT_HEADER => true]);

        $this->assertInstanceOf('dHttp\Response', $resp);
        $this->assertEquals(200, $resp->getCode());
    }

    /**
     *
     */
    public function testMultiRequest()
    {
        $multi          = new dHttp\Client();
        $response_array = $multi->multi([
            new dHttp\Client('https://www.php.net/', [
                CURLOPT_FOLLOWLOCATION => true
            ]),
            new dHttp\Client('http://www.python.org/', [
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 5.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_FOLLOWLOCATION => true
            ])
        ]);

        /* @var $item \dHttp\Response */
        foreach ($response_array as $item) {
            $this->assertInstanceOf('dHttp\Response', $item);
            $this->assertEquals(200, $item->getCode());
        }
    }
}
