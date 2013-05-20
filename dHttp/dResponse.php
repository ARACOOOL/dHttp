<?php
/**
 * dHttp - http client based curl
 *
 * @version 0.2
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */

namespace dHttp;

class dResponse {
	/**
	 * @var string
	 */
	private $_raw = null;
	/**
	 * @var string
	 */
	private $_headers = null;
	/**
	 * @var string
	 */
	private $_body = null;
	/**
	 * @var array
	 */
	private $_errors = array();
	/**
	 * @var array
	 */
	private $_info = array();

	/**
	 * @param $response
	 * @param $info
	 */
	public function __construct($response, array $info) {
		$this->_raw = $response;
		$this->_info = $info;
		// Separate body a from a header
		list($this->_headers, $this->_body) = explode("\r\n\r\n", $response, 2);
	}

	/**
	 * @param $errors
	 */
	public function set_error($errors) {
		$this->_errors = $errors;
	}

	/**
	 * @return null|string
	 */
	public function get_raw() {
		return $this->_raw;
	}

	/**
	 * @return null|string
	 */
	public function get_headers() {
		return $this->_headers;
	}

	/**
	 * @return null|string
	 */
	public function get_body() {
		return $this->_body;
	}

	/**
	 * @return null|string
	 */
	public function get_errors() {
		return $this->_errors;
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function __get($name) {
		if(isset($this->_info[$name])) {
			return $this->_info[$name];
		}

		if(method_exists($this, 'get_' . $name)) {
			return $this->{'get_' . $name}();
		}

		return null;
	}
}