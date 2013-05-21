<?php
/**
 * @namespace
 */
namespace dHttp;

/**
 * dHttp - http client based curl
 *
 * @version 0.2.0
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */
class dResponse {
	/**
	 * @var string
	 */
	private $_raw = null;
	/**
	 * @var string
	 */
	private $_header = null;
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
	 * Constructor
	 *
	 * @param string $response
	 * @param array $info
	 */
	public function __construct($response, array $info) {
		$this->_raw = $response;
		$this->_info = $info;
		// Separate body a from a header
		if(preg_match("/\r\n\r\n/iu", $response)) {
			list($this->_headers, $this->_body) = explode("\r\n\r\n", $response, 2);
		}
		else {
			$this->_body = $response;
		}
	}

	/**
	 * Set errors
	 *
	 * @param array $errors
	 */
	public function set_error($errors) {
		$this->_errors = $errors;
	}

	/**
	 * Return raw response
	 *
	 * @return null|string
	 */
	public function get_raw() {
		return $this->_raw;
	}

	/**
	 * Return response headers
	 *
	 * @return null|string
	 */
	public function get_header() {
		return $this->_header;
	}

	/**
	 * Return response body
	 *
	 * @return null|string
	 */
	public function get_body() {
		return $this->_body;
	}

	/**
	 * Return request errors
	 *
	 * @return null|string
	 */
	public function get_errors() {
		return $this->_errors;
	}

	/**
	 * Get access for properties
	 *
	 * @param string $name
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