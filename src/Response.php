<?php

namespace dHttp;

/**
 * dHttp - http client based curl
 *
 * @author Askar Fuzaylov <tkdforever@gmail.com>
 */
class Response
{
	/**
	 * @var string
	 */
	private $_raw;
	/**
	 * @var string
	 */
	private $_headers = [];
	/**
	 * @var string
	 */
	private $_body;
	/**
	 * @var array
	 */
	private $_errors = [];
	/**
	 * @var array
	 */
	private $_info = [];

	/**
	 * Constructor
	 *
	 * @param array $response
	 */
	public function __construct(array $response)
	{
		$this->_raw = $response['response'];
		$this->_info = $response['info'];

		// Separate body a from a header
		if (isset($response['options'][CURLOPT_HEADER]) && $response['options'][CURLOPT_HEADER]) {
			list($headers, $this->_body) = explode("\r\n\r\n", $response['response'], 2);
			// Parse headers
			$this->parseHeaders($headers);
		} else {
			$this->_body = $response['response'];
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
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * Return response headers
	 *
	 * @param string $name
	 * @param string $default
	 * @return null|string
	 */
	public function getHeader($name, $default = null)
	{
		return array_key_exists($name, $this->_headers) ? $this->_headers[$name] : $default;
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
		if (array_key_exists($name, $this->_info)) {
			return $this->_info[$name];
		}

		return null;
	}

    /**
     * Parse headers
     * @param $headers
     */
	private function parseHeaders($headers)
	{
		$exploded = explode("\r\n", $headers);
		foreach($exploded as $headerString) {
			if(strpos($headerString, ':') !== false) {
				list($key, $val) = explode(':', $headerString, 2);
				$this->_headers[trim($key)] = trim($val);
			}
		}
	}
}
