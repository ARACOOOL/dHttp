dHttp is library to work with Curl.
Easy-to-use library!

## A Few Examples

### GET request:

```php
include_once('dHttp/dHttp.php');
include_once('dHttp/dResponse.php');

$http = new dHttp\dHttp('http://website.com');

$resp = $http->get();
// Get response code
var_dump($resp->http_code);
// Get response body
var_dump($resp->body);
// Get request errors
var_dump($resp->errors);
```

### POST request:

```php
include_once('dHttp/dHttp.php');
include_once('dHttp/dResponse.php');

$http = new dHttp\dHttp('http://website.com');

$http->add_options(array(CURLOPT_RETURNTRANSFER => false))
	->set_cookie('/tmp/cookie.txt')
	->set_user_agent('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31')
	->post(array(
		'field1' => 'value1',
		'field2' => 'value2',
	));
```

## LICENSE

Phabricator is released under the Apache 2.0 license except as otherwise noted.
http://www.apache.org/licenses/LICENSE-2.0