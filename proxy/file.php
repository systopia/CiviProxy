<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/
require_once "config.php";
require_once "proxy.php";

// basic restraints
$valid_parameters = array( 'id'   => 'string', 'mosaico'  => 'int' );
$parameters = civiproxy_get_parameters($valid_parameters);
// see if file caching is enabled
if ((!$target_file && !$parameters['mosaico']) || (!$target_mosaico_file && $parameters['mosaico'])) {
  civiproxy_http_error("Feature disabled", 405);
}

// basic check
civiproxy_security_check('file');

// check if id specified
if (empty($parameters['id'])) civiproxy_http_error("Resource not found");

// check restrictions
if (!empty($file_cache_exclude)) {
  foreach ($file_cache_exclude as $pattern) {
    if (preg_match($pattern, $parameters['id'])) {
      civiproxy_http_error("Invalid Resource", 403);
    }
  }
}
if (!empty($file_cache_include)) {
  $accept_id = FALSE;
  foreach ($file_cache_include as $pattern) {
    if (preg_match($pattern, $parameters['id'])) {
      $accept_id = TRUE;
    }
  }
  if (!$accept_id) {
    civiproxy_http_error("Invalid Resource", 403);
  }
}

// load PEAR file cache
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . 'libs');
if (!file_exists($file_cache_options['cacheDir'])) mkdir($file_cache_options['cacheDir']);
require_once('Cache/Lite.php');
$file_cache = new Cache_Lite($file_cache_options);

// look up the required resource
$header_key = 'header&' . $parameters['id'];
$data_key   = 'data&'   . $parameters['id'];

$header = $file_cache->get($header_key);
$data   = $file_cache->get($data_key);

if ($header && $data) {
  // error_log("CACHE HIT");
  $header_lines = json_decode($header);
  foreach ($header_lines as $header_line) {
    header($header_line);
  }

  print $data;
  exit();
}
// if we get here, we have a cache miss => load
if ($parameters['mosaico'] == 1) {
  $url = $target_mosaico_file . $parameters['id'];
}
else {
  $url = $target_file . $parameters['id'];
}
// error_log("CACHE MISS. LOADING $url");

$curlSession = curl_init();
curl_setopt($curlSession, CURLOPT_URL, $url);
curl_setopt($curlSession, CURLOPT_HEADER, 1);
curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
if (!empty($target_interface)) {
  curl_setopt($curlSession, CURLOPT_INTERFACE, $target_interface);
}
if (file_exists(dirname(__FILE__).'/target.pem')) {
  curl_setopt($curlSession, CURLOPT_CAINFO, dirname(__FILE__).'/target.pem');
}

//Send the request and store the result in an array
$response = curl_exec($curlSession);

// Check that a connection was made
if (curl_error($curlSession)) {
  error_log(curl_error($curlSession));
  civiproxy_http_error(curl_error($curlSession), curl_errno($curlSession));
}
// process the results
$content = explode("\r\n\r\n", $response, 2);
$header  = $content[0];
$body    = $content[1];

// extract headers
$header_lines = explode(chr(10), $header);

// Remove chunked encoding header
foreach ($header_lines as $k => $header_line) {
  if(strpos($header_line,'Transfer-Encoding: chunked') !== FALSE) {
    unset($header_lines[$k]);
  }
}

// store the information in the cache
$file_cache->save(json_encode($header_lines), $header_key);
$file_cache->save($body, $data_key);

// and reply
foreach ($header_lines as $header_line) {
  header($header_line);
}

print $body;
curl_close ($curlSession);
