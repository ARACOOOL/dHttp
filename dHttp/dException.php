<?php
/**
 * Exception class for dHttp
 */
require_once('dException.php');

class dException extends Exception {

	// Errors ids
	private $exception_codes = array(
			1=>'Connection error',
			10=>'Curl extension not loaded',
			15=>'Could not initialize CURL',
			20=>'Invalid handler method name',
			21=>'Error assigning the output stream for headers',
			22=>'Error setting CURL timeout',
			23=>'Error setting URL to connect to',
			24=>'Error headers, it must be array',
			50=>'Invalid request method',
			51=>'Invalid request parameters, it\'s must be array',
			60=>'Out of memory',
			70=>'Headers already sent to the user agent',
			80=>'CURL reported error',
			90=>'Invalid delay value',
			110=>'Non-HTTP response headers',
			115=>'Curl returned empty result after execution',
			120=>'Invalid host of the requested URI',
			125=>'Invalid URI',
			130=>'Redirects limit reached');

	function __construct($code){
		$message = $this->exception_codes[$code];
		parent::__construct($message, $code);
	}
}
?>