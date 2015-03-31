<?php

namespace dHttp;

/**
 * dHttp - http client based curl
 */
class Url
{
	/**
	 * Validate URL
	 *
	 * @param $url
	 * @return string
	 * @throws \Exception
	 */
	public static function validateUrl($url)
	{
		if (!trim($url)) {
			throw new \Exception("Provided URL '$url' cannot be empty");
		}

		// Split URL into parts first
		$parts = parse_url($url);

		if (!$parts) {
			throw new \Exception("Error parsing URL '$url'");
		}

		if (!array_key_exists('host', $parts)) {
			throw new \Exception("Provided URL '$url' doesn't contain a hostname");
		}

		// Rebuild the URL
		$url = self::buildUrl($parts);

		return $url;
	}

	/**
	 * Re-build a URL based on an array of parts
	 *
	 * @param array $parts
	 * @return string
	 */
	public static function buildUrl(array $parts)
	{
		$url = '';
		$url .= (!empty($parts['scheme'])) ? $parts['scheme'] . '://' : '';
		$url .= (!empty($parts['user'])) ? $parts['user'] : '';
		$url .= (!empty($parts['pass'])) ? ':' . $parts['pass'] : '';
		//If we have a user or pass, make sure to add an "@"
		$url .= (!empty($parts['user']) || !empty($parts['pass'])) ? '@' : '';
		$url .= (!empty($parts['host'])) ? $parts['host'] : '';
		$url .= (!empty($parts['port'])) ? ':' . $parts['port'] : '';
		$url .= (!empty($parts['path'])) ? $parts['path'] : '';
		$url .= (!empty($parts['query'])) ? '?' . $parts['query'] : '';
		$url .= (!empty($parts['fragment'])) ? '#' . $parts['fragment'] : '';

		return $url;
	}

	/**
	 * Checks a passed in IP against a CIDR.
	 *
	 * @param string $ip
	 * @param string|array $ranges
	 * @return bool
	 */
	public static function cidr_match($ip, $ranges)
	{
		$ranges = is_array($ranges) ? $ranges : (array)$ranges;
		foreach ($ranges as $range) {
			list($subnet, $mask) = explode('/', $range);
			if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet)) {
				return true;
			}
		}

		return false;
	}
}
