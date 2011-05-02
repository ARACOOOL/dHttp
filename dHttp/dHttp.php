<?php
/**
* @version 0.1
* @author Askar Fuzaylov <afuzaylov@dealerfire.com>
*/

class dHttp {

	// CURL resource handler
	private $ch = null;

	// Full Url string
	private $URL = null;

	// On or off debuge
	private $debug = null;

	// Error message string
	private $error_msg = null;

	// Flag that defines if cURL should automatically follow the "Location" header or not
	private $followlocation = true;

	// Flag that defines if cURL should return the body of the response
	private $return_transfer = true;

	// Show headers or not
	private $header = false;

	// Page encoding (default utf-8)
    private $encoding = 'utf-8';

	// Response string
	private $response = null;

	/**
	 * @param <string> $url
	 * @param <string> $method (default POST)
	 */
	public function __construct() {
		if (!defined('CURLE_OK')){
            throw new dException(10);
        }
		
		$this->ch = curl_init();
	}
	
	/**
	 * Main method for request
	 */
	public function run() {
		// Set url to post to
		curl_setopt($this->ch, CURLOPT_URL, $this->URL);

		// If the request use SSL
		$scheme = parse_url($this->URL, PHP_URL_SCHEME);
		$scheme = strtolower($scheme);
		
		if ($scheme == 'https') {
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		
		// Basic params
		curl_setopt ($this->ch, CURLOPT_HEADER, $this->header);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
		curl_setopt ($this->ch, CURLOPT_ENCODING, $this->encoding);
        curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, $this->return_transfer);
		
		$this->response = curl_exec($this->ch);
		curl_close($this->ch);
		return $this->response;
	}
	
	public function setUrl($url) {
		if(!is_null($this->params)) {
			$this->URL .= '?'.$this->params;
		}
		else {
			$this->URL = $url;
		}
	}

	/**
	 * Set post params
	 */
	public function setParams($data, $method='post') {
		
		if(strtolower($method) == 'post') {
			curl_setopt($this->ch, CURLOPT_POST, true);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		}
		else {
			if(!is_array($data)) {
				throw new dException(51);
			}
			
			$params = array();
			foreach($data as $key=>$val) {
				$params[] = $key.'='.$val;
			}
			$params = implode('&', $params);
			$this->params = $params;
		}
	}

	/**
	 * Set headers
	 */
	public function setHeaders($headers) {
		if(!is_array($headers)) {
			throw new dException(24);
		}
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
	}

	/**
	 * Show headers or not
	 */
	public function showHeaders($value) {
		$this->header = (bool)$value;
	}

	/**
	 *  Set cookies
	 */
	 private function setCookies($cookie_file) {
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_file);
	 }
	 
	/**
	 *  Define whether to return the transfer or not
	 */
	public function setTransfer($value) {
        $this->return_transfer = (bool)$value;
    }

	/**
     * Sets the time limit of time the CURL can execute
     */
    public function setTimeout($seconds){
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $seconds);
        if ($this->catchCurlError()){
            throw new dException(22);
        }
    }

	/**
     * Check for an error
     */
    private function catchCurlError(){
        if(!is_resource($this->ch) || !($curl_errno=curl_errno($this->ch))){
            return false;
        }

        throw new dException(80, $curl_errno, curl_error($this->ch));
        return true;
    }

}

?>