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
    private $raw;
    /**
     * @var string
     */
    private $headers = [];
    /**
     * @var string
     */
    private $_body;
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var array
     */
    private $info;

    /**
     * Constructor
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->raw  = $response['response'];
        $this->info = $response['info'];

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
     * Parse headers
     * @param $headers
     */
    private function parseHeaders($headers)
    {
        $exploded = explode("\r\n", $headers);
        foreach ($exploded as $headerString) {
            if (strpos($headerString, ':') !== false) {
                list($key, $val) = explode(':', $headerString, 2);
                $this->headers[trim($key)] = trim($val);
            }
        }
    }

    /**
     * Return raw response
     *
     * @return null|string
     */
    public function getRaw(): ?string
    {
        return $this->raw;
    }

    /**
     * Return response headers
     *
     * @return null|string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return response headers
     *
     * @param string $name
     * @param null $default
     * @return null|string
     */
    public function getHeader(string $name, $default = null): ?string
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : $default;
    }

    /**
     * Return response body
     *
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->_body;
    }

    /**
     * Set errors
     *
     * @param array $errors
     */
    public function setError(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Return request errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return request errors
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->info['http_code'];
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->info['http_code'] == 200;
    }

    /**
     * Get access for properties
     *
     * @param string $name
     * @param array @params
     * @return mixed
     */
    public function __call(string $name, $params)
    {
        $name = strtolower(str_replace('get', '', $name));
        if (array_key_exists($name, $this->info)) {
            return $this->info[$name];
        }

        return null;
    }
}
