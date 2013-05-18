<?php
/**
 * @version 0.1
 * @author Askar Fuzaylov <afuzaylov@dealerfire.com>
 */

class dHttp {
	/**
	 * @var array
	 */
	private $_params = array(
		CURLOPT_ENCODING => 'utf-8',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => false,
		CURLOPT_TIMEOUT_MS => 2000,
		CURLOPT_TIMEOUT => 2,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_MAXREDIRS => 3
	);
	/**
	 * @var null
	 */
	private $_response = null;

	/**
	 * Construct
	 */
	public function __construct($url, array $options = array()) {
		if(!extension_loaded('curl')) {
			die('Error: Curl is not supported');
		}

		// Set URL
		$options[CURLOPT_URL] = $url;
		// Merge with default options
		$this->_merge_params($options);
	}

	/**
	 * @param array $fields
	 * @param array $options
	 * @return dHttp
	 * @throws Exception
	 */
	public function post(array $fields = array(), array $options = array()) {
		$options = array_merge($options, array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => $this->build_params($fields)));
		$this->_exec($options);
	}

	/**
	 * @param array $options
	 */
	public function get(array $options = array()) {
		$this->_exec($options);
	}

	/**
	 * @param array $options
	 * @return mixed
	 * @throws Exception
	 */
	private function _exec(array $options = array()) {
		if(count($options)) {
			$this->_merge_params($options);
		}

		$ch = $this->_init();

		$this->_response = curl_exec($ch);

		if($this->_response === false) {
			throw new Exception(curl_error($ch), curl_errno($ch));
		}

		curl_close($ch);

		return $this->_response;
	}

	/**
	 * @return resource
	 */
	private function _init() {
		$ch = curl_init();
		// The initial parameters
		$this->_set_curl_options($ch, $this->_params);

		return $ch;
	}

	/**
	 * @param $ch
	 * @param array $options
	 */
	private function _set_curl_options(&$ch, array $options) {
		curl_setopt_array($ch, $options);
	}

	/**
	 * @param array $params
	 */
	private function _merge_params(array $params) {
		foreach($params as $key => $val) {
			$this->_params[$key] = $val;
		}
	}

	/**
	 * @param array $params
	 * @return string
	 */
	private function build_params(array $params) {
		$result = array();
		foreach($params as $key => $val) {
			$result[] = $key . '=' . $val;
		}

		return implode('&', $result);
	}
}