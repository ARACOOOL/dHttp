<?php

namespace dHttp;

/**
 * dHttp - http client based curl
 *
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */
class Client
{
	/**
	 * @var array
	 */
	private $_default = array(
		CURLOPT_ENCODING => 'utf-8',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_USERAGENT => 'dHttp'
	);
	/**
	 * @var array
	 */
	private $_options = array();

	/**
	 * Construct
	 *
	 * @param null $url
	 * @param array $options
	 * @throws \Exception
	 */
	public function __construct($url = null, array $options = array())
	{
		if (!extension_loaded('curl')) {
			throw new \Exception('The PHP cURL extension must be installed to use dHttp');
		}
		
		// Force IPv4, since this class isn't yet comptible with IPv6
	        if (self::v('features') & CURLOPT_IPRESOLVE) {
	            $this->addOptions(array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4));
	        }

		// Merge with default options
		$this->addOptions($options);
		// Set URL
		$this->setUrl($url);
	}

	/**
	 * Set URL
	 *
	 * @param string $url
	 * @return Client
	 */
	public function setUrl($url)
	{
		if (!is_null($url)) {
			$this->_options[CURLOPT_URL] = $url;
		}

		return $this;
	}

	/**
	 * Set user agent
	 *
	 * @param string $agent
	 * @return Client
	 */
	public function setUserAgent($agent)
	{
		$this->_options[CURLOPT_USERAGENT] = $agent;
		return $this;
	}

	/**
	 * Set cookies
	 *
	 * @param string $cookie
	 * @return Client
	 */
	public function setCookie($cookie)
	{
		$this->_options[CURLOPT_COOKIEFILE] = $cookie;
		$this->_options[CURLOPT_COOKIEJAR] = $cookie;
		return $this;
	}

	/**
	 * Add options
	 *
	 * @param array $params
	 * @return Client
	 */
	public function addOptions(array $params)
	{
		if (!count($this->_options)) {
			$this->_options = $this->_default;
		}

		foreach ($params as $key => $val) {
			$this->_options[$key] = $val;
		}

		return $this;
	}

	/**
	 * Send post request
	 *
	 * @param array $fields
	 * @param array $options
	 * @return Response
	 */
	public function post($fields = array(), array $options = array())
	{
		$fields = is_array($fields) ? http_build_query($fields) : $fields;
		
		$this->addOptions($options);
		$this->addOptions(array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => $fields));
		return $this->_exec();
	}
	
	/**
	 * Send put request
	 *
	 * @param array $fields
	 * @param array $options
	 * @return Response
	 */
	public function put($fields = array(), array $options = array())
	{
		$fields = is_array($fields) ? http_build_query($fields) : $fields;
		
		$this->addOptions($options);
		$this->addOptions(array(CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_POSTFIELDS => $fields));
		return $this->_exec();
	}

	/**
	 * Send get request
	 *
	 * @param array $options
	 * @return Response
	 */
	public function get(array $options = array())
	{
		$this->addOptions($options);
		return $this->_exec();
	}
	
	/**
	 * Send delete request
	 *
	 * @param array $options
	 * @return Response
	 */
	public function delete(array $options = array())
	{
		$this->addOptions($options);
		$this->addOptions(array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
		return $this->_exec();
	}

	/**
	 * Send multithreaded queries
	 *
	 * @param array $handlers
	 * @return array
	 * @throws \Exception
	 */
	public function multi(array $handlers)
	{
		//create the multiple cURL handle
		$mc = curl_multi_init();
		$resources = array();

		foreach ($handlers as $item) {
			if (!$item instanceof Client) {
				throw new \Exception('Handler should be object instance of dHttp');
			}
			$res = $item->_init();

			curl_multi_add_handle($mc, $res);
			$resources[] = $res;
		}

		$running = null;
		do {
			usleep(100);
			curl_multi_exec($mc, $running);
		} while ($running > 0);

		$result = array();
		foreach ($resources as $item) {
			$resp = new Response(curl_multi_getcontent($item), curl_getinfo($item));
			$resp->setError(array(curl_errno($item) => curl_error($item)));
			$result[] = $resp;
			curl_multi_remove_handle($mc, $item);
		}

		curl_multi_close($mc);
		return $result;
	}

	/**
	 * Execute the query
	 *
	 * @return Response
	 */
	private function _exec()
	{
		$ch = $this->_init();

		$result = curl_exec($ch);
		// Collect response data
		$response = new Response($result, curl_getinfo($ch));

		if ($result === false) {
			$response->setError(array(curl_errno($ch) => curl_error($ch)));
		}
		curl_close($ch);

		return $response;
	}

	/**
	 * Initialize curl
	 *
	 * @return resource
	 */
	public function _init()
	{
		$ch = curl_init();
		// The initial parameters
		$this->_setCurlOptions($ch, $this->_options);
		return $ch;
	}

	/**
	 * Set curl options
	 *
	 * @param resource $ch
	 * @param array $options
	 * @return void
	 */
	private function _setCurlOptions(&$ch, array $options)
	{
		curl_setopt_array($ch, $options);
	}

	/**
	 * Reset options
	 *
	 * @return Client
	 */
	public function reset()
	{
		$this->_options = array();
		return $this;
	}

	/**
	 * Return curl information
	 *
	 * @param string $type
	 * @return mixed
	 */
	public static function v($type = 'version')
	{
		$info = curl_version();
		return isset($info[$type]) ? $info[$type] : null;
	}
}