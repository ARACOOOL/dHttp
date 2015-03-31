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
	 * @param string $url
	 * @param array $options
	 * @throws \Exception
	 */
	public function __construct($url = null, array $options = array())
	{
		if (!extension_loaded('curl')) {
			throw new \Exception('The PHP cURL extension must be installed to use dHttp');
		}
		
		// Force IPv4, since this class isn't yet compatible with IPv6
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
		if ($url !== null) {
			$this->_options[CURLOPT_URL] = Url::validateUrl($url);
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
	 * @param string|array $fields
	 * @param array $options
	 * @return Response
	 */
	public function post($fields = array(), array $options = array())
	{
		return $this->get($options + array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => is_array($fields) ? http_build_query($fields) : $fields));
	}
	
	/**
	 * Send put request
	 *
	 * @param string|array $fields
	 * @param array $options
	 * @return Response
	 */
	public function put($fields = array(), array $options = array())
	{
        return $this->get($options + array(CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_POSTFIELDS => is_array($fields) ? http_build_query($fields) : $fields));
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
		return $this->exec();
	}
	
	/**
	 * Send delete request
	 *
	 * @param array $options
	 * @return Response
	 */
	public function delete(array $options = array())
	{
        return $this->get($options + array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
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
				throw new \Exception('Handler should be object instance of dHttp\Client');
			}
			$res = $item->init();

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
			$resp = new Response(array(
				'response' => curl_multi_getcontent($item),
				'options' => $this->_options,
				'info' => curl_getinfo($item)
			));

			$errno = curl_errno($item);
			if($errno) {
				$resp->setError(array(curl_errno($item) => curl_error($item)));
			}
			
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
	private function exec()
	{
		$ch = $this->init();
		// Collect response data
		$response = new Response(array(
			'response' => curl_exec($ch),
			'options' => $this->_options,
			'info' => curl_getinfo($ch)
		));

		$errno = curl_errno($ch);
		if ($errno) {
			$response->setError(array($errno => curl_error($ch)));
		}
		curl_close($ch);

		return $response;
	}

	/**
	 * Initialize curl
	 *
	 * @return resource
	 */
	public function init()
	{
		$ch = curl_init();
		// The initial parameters
		$this->setCurlOptions($ch, $this->_options);
		return $ch;
	}

	/**
	 * Set curl options
	 *
	 * @param resource $ch
	 * @param array $options
	 * @return void
	 */
	private function setCurlOptions(&$ch, array $options)
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
		return array_key_exists($type, $info) ? $info[$type] : null;
	}
}
