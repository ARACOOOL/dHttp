<?php
/**
 * dHttp - http client based curl
 *
 * @version 0.2
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */

namespace dHttp;

class dHttp {
	/**
	 * @var array
	 */
	private $_default = array(
		CURLOPT_ENCODING => 'utf-8',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_USERAGENT => 'dHttp',
		CURLOPT_TIMEOUT => 2,
		CURLOPT_MAXREDIRS => 5
	);
	/**
	 * @var array
	 */
	private $_options = array();

	/**
	 * Construct
	 * @param string $url
	 * @param array $options
	 */
	public function __construct($url = null, array $options = array()) {
		if(!extension_loaded('curl')) {
			die('Error: Curl is not supported');
		}

		// Merge with default options
		$this->add_options($options);

		// Set URL
		$this->set_url($url);
	}

	/**
	 * Set URL
	 *
	 * @param string $url
	 * @return dHttp
	 */
	public function set_url($url) {
		if(!is_null($url)) {
			$this->_options[CURLOPT_URL] = $url;
		}

		return $this;
	}

	/**
	 * Set user agent
	 *
	 * @param string $agent
	 * @return dHttp
	 */
	public function set_user_agent($agent) {
		$this->_options[CURLOPT_USERAGENT] = $agent;
		return $this;
	}

	/**
	 * Set cookies
	 *
	 * @param string $cookie
	 * @return dHttp
	 */
	public function set_cookie($cookie) {
		$this->_options[CURLOPT_COOKIEFILE] = $cookie;
		$this->_options[CURLOPT_COOKIEJAR] = $cookie;
		return $this;
	}

	/**
	 * Add options
	 *
	 * @param array $params
	 * @return dHttp
	 */
	public function add_options(array $params) {
		if(!count($this->_options)) {
			$this->_options = $this->_default;
		}

		foreach($params as $key => $val) {
			$this->_options[$key] = $val;
		}

		return $this;
	}

	/**
	 * Send post request
	 *
	 * @param array $fields
	 * @param array $options
	 * @return dResponse
	 * @throws Exception
	 */
	public function post(array $fields = array(), array $options = array()) {
		$this->add_options($options);
		$this->add_options(array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => $this->build_fields($fields)));
		return $this->_exec();
	}

	/**
	 * Send get request
	 *
	 * @param array $options
	 * @return dResponse
	 */
	public function get(array $options = array()) {
		$this->add_options($options);
		return $this->_exec();
	}

	/**
	 * Execute the query
	 *
	 * @return dResponse
	 * @throws Exception
	 */
	private function _exec() {
		$ch = $this->_init();

		$result = curl_exec($ch);
		// Collect response data
		$response = new dResponse($result, curl_getinfo($ch));

		if($result === false) {
			$response->set_error(array(curl_errno($ch) => curl_error($ch)));
		}
		curl_close($ch);

		return $response;
	}

	/**
	 * Initialize curl
	 *
	 * @return resource
	 */
	private function _init() {
		$ch = curl_init();
		// The initial parameters
		$this->_set_curl_options($ch, $this->_options);

		return $ch;
	}

	/**
	 * Set curl options
	 *
	 * @param resource $ch
	 * @param array $options
	 * @return void
	 */
	private function _set_curl_options(&$ch, array $options) {
		curl_setopt_array($ch, $options);
	}

	/**
	 * Build request fields
	 *
	 * @param array $params
	 * @return string
	 */
	private function build_fields(array $params) {
		$result = array();
		foreach($params as $key => $val) {
			$result[] = $key . '=' . $val;
		}

		return implode('&', $result);
	}

	/**
	 * Reset options
	 *
	 * @return dHttp
	 */
	public function reset() {
		$this->_options = array();
		return $this;
	}
}