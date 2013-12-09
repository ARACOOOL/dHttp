<?php

namespace dHttp;

/**
 * dHttp - http client based curl
 *
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */
class dResponse
{
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
	public function __construct($response, array $info)
	{
		$this->_raw = $response;
		$this->_info = $info;
		// Separate body a from a header
		if (preg_match("/\r\n\r\n/iu", $response)) {
			list($this->_headers, $this->_body) = explode("\r\n\r\n", $response, 2);
		}
		else {
			$this->_body = $response;
		}
	}

	/**
	 * Return raw response
	 *
	 * @return null|string
	 */
	public function getRaw()
	{
		return $this->_raw;
	}

	/**
	 * Return response headers
	 *
	 * @return null|string
	 */
	public function getHeader()
	{
		return $this->_header;
	}

	/**
	 * Return response body
	 *
	 * @return null|string
	 */
	public function getBody()
	{
		return $this->_body;
	}

	/**
	 * Set errors
	 *
	 * @param array $errors
	 */
	public function setError($errors)
	{
		$this->_errors = $errors;
	}

	/**
	 * Return request errors
	 *
	 * @return null|string
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Return request errors
	 *
	 * @return int
	 */
	public function getCode()
	{
		return $this->_info['http_code'];
	}

	/**
	 * Get access for properties
	 *
	 * @param string $name
	 * @param array @params
	 * @return mixed
	 */
	public function __call($name, $params)
	{
		$name = strtolower(str_replace('get', '', $name));
		if (isset($this->_info[$name])) {
			return $this->_info[$name];
		}

		return null;
	}
}
?>