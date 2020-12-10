<?php

namespace dHttp;

use RuntimeException;

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
    private $_default = [
        CURLOPT_ENCODING       => 'utf-8',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'PHP dHttp/Client 1.3'
    ];
    /**
     * @var array
     */
    private $_options = [];

    /**
     * Construct
     *
     * @param string $url
     * @param array $options
     * @throws RuntimeException
     */
    public function __construct($url = null, array $options = [])
    {
        if (!extension_loaded('curl')) {
            throw new RuntimeException('The PHP cURL extension must be installed to use dHttp');
        }

        $this->addOptions([CURLOPT_RETURNTRANSFER => true]);

        // Force IPv4, since this class isn't yet compatible with IPv6
        if (self::v('features') & CURLOPT_IPRESOLVE) {
            $this->addOptions([CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]);
        }

        // Merge with default options
        $this->addOptions($options);
        // Set URL
        $this->setUrl($url);
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

    /**
     * Add options
     *
     * @param array $params
     * @return Client
     */
    public function addOptions(array $params): Client
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
     * Set URL
     *
     * @param string|null $url
     * @return Client
     */
    public function setUrl(?string $url): Client
    {
        if ($url !== null) {
            $this->_options[CURLOPT_URL] = $this->prepareUrl($url);
        }

        return $this;
    }

    /**
     * Generate url
     *
     * @param string |array $url
     * @return string
     */
    public function prepareUrl($url)
    {
        if (is_array($url) && count($url)) {
            $newUrl = $url[0];

            if (isset($url[1]) && is_array($url[1])) {
                $newUrl = '?' . http_build_query($url[1]);
            }
        } else {
            $newUrl = $url;
        }

        return $newUrl;
    }

    /**
     * Set user agent
     *
     * @param string $agent
     * @return Client
     */
    public function setUserAgent(string $agent): Client
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
    public function setCookie(string $cookie): Client
    {
        $this->_options[CURLOPT_COOKIEFILE] = $cookie;
        $this->_options[CURLOPT_COOKIEJAR]  = $cookie;
        return $this;
    }

    /**
     * Set referer
     *
     * @param string $referer
     * @return Client
     */
    public function setReferer(string $referer): Client
    {
        $this->_options[CURLOPT_REFERER] = $referer;
        return $this;
    }

    /**
     * The maximum amount of HTTP redirects to follow
     *
     * @param int $redirects
     * @return Client
     */
    public function setMaxRedirects(int $redirects): Client
    {
        $this->_options[CURLOPT_MAXREDIRS] = $redirects;
        return $this;
    }

    /**
     * The maximum number of seconds to allow cURL functions to execute.
     *
     * @param int $timeout
     * @return Client
     */
    public function setTimeout(int $timeout): Client
    {
        $this->_options[CURLOPT_TIMEOUT] = $timeout;
        return $this;
    }

    /**
     * The number of seconds to wait while trying to connect.
     *
     * @param int $timeout
     * @return Client
     */
    public function setConnectionTimeout(int $timeout): Client
    {
        $this->_options[CURLOPT_CONNECTTIMEOUT] = $timeout;
        return $this;
    }

    /**
     * Include the header in the output
     *
     * @param bool $show
     * @return Client
     */
    public function showHeaders(bool $show): Client
    {
        $this->_options[CURLOPT_HEADER] = $show;
        return $this;
    }

    /**
     * Send post request
     *
     * @param array $fields
     * @param array $options
     * @return Response
     */
    public function post($fields = [], array $options = []): Response
    {
        $this->addOptions($options + [
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $fields
            ]);
        return $this->exec();
    }

    /**
     * Execute the query
     *
     * @return Response
     */
    private function exec(): Response
    {
        $ch = $this->init();
        // Collect response data
        $response = new Response([
            'response' => curl_exec($ch),
            'options'  => $this->_options,
            'info'     => curl_getinfo($ch)
        ]);

        $errno = curl_errno($ch);
        if ($errno) {
            $response->setError([$errno => curl_error($ch)]);
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
    private function setCurlOptions($ch, array $options)
    {
        curl_setopt_array($ch, $options);
    }

    /**
     * Send put request
     *
     * @param array $fields
     * @param array $options
     * @return Response
     */
    public function put(array $fields = [], array $options = []): Response
    {
        $this->addOptions($options + [
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS    => is_array($fields) ? http_build_query($fields) : $fields
            ]);
        return $this->exec();
    }

    /**
     * Send delete request
     *
     * @param array $options
     * @return Response
     */
    public function delete(array $options = []): Response
    {
        return $this->get($options + [CURLOPT_CUSTOMREQUEST => 'DELETE']);
    }

    /**
     * Send get request
     *
     * @param array $options
     * @return Response
     */
    public function get(array $options = []): Response
    {
        $this->addOptions($options);
        return $this->exec();
    }

    /**
     * Send multithread queries
     *
     * @param Client[] $handlers
     * @return array
     * @throws RuntimeException
     */
    public function multi(array $handlers): array
    {
        //create the multiple cURL handle
        $mc        = curl_multi_init();
        $resources = [];

        foreach ($handlers as $item) {
            if (!$item instanceof Client) {
                throw new RuntimeException('Handler should be object instance of dHttp\Client');
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

        $result = [];
        foreach ($resources as $item) {
            $resp = new Response([
                'response' => curl_multi_getcontent($item),
                'options'  => $this->_options,
                'info'     => curl_getinfo($item)
            ]);

            $errno = curl_errno($item);
            if ($errno) {
                $resp->setError([curl_errno($item) => curl_error($item)]);
            }

            $result[] = $resp;
            curl_multi_remove_handle($mc, $item);
        }

        curl_multi_close($mc);
        return $result;
    }

    /**
     * Reset options
     *
     * @return Client
     */
    public function reset(): Client
    {
        $this->_options = [];
        return $this;
    }
}
