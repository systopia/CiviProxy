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

// basic check
civiproxy_security_check('file');

// basic restraints
$valid_parameters = array(
  'id'   => 'string',
  'eid'  => 'int',
  'fcs'  => 'string'
);
$parameters = civiproxy_get_parameters($valid_parameters);

// check if id specified
if (empty($parameters['id'])) civiproxy_http_error("Resource not found");

$static_file = true;
if (isset($parameters['eid']) && isset($parameters['fcs'])) {
  $static_file = false;
  // see if file caching is enabled
  if (!$target_download_file) civiproxy_http_error("Feature disabled", 405);
} else {
  // see if file caching is enabled
  if (!$target_static_file && isset($target_file)) {
    $target_static_file = $target_file; // Backwards compatibility.
  }
  if (!$target_static_file) civiproxy_http_error("Feature disabled", 405);
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
}

// load PEAR file cache
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . 'libs');
if (!file_exists($file_cache_options['cacheDir'])) mkdir($file_cache_options['cacheDir']);
require_once('Cache/Lite.php');
$file_cache = new Cache_Lite($file_cache_options);

// look up the required resource
$header_key = 'header&' . $parameters['id'];
$data_key   = 'data&'   . $parameters['id'];
if (!$static_file) {
  $header_key .= '&eid='.$parameters['eid'];
  $data_key .= '&eid='.$parameters['eid'];
}

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
$url = $target_static_file . $parameters['id'];
if (!$static_file) {
  $url = $target_download_file .'?reset=1&id='.$parameters['id'].'&eid='.$parameters['eid'].'&fcs='.$parameters['fcs'];
}
// error_log("CACHE MISS. LOADING $url");

$curlSession = curl_init();
curl_setopt($curlSession, CURLOPT_URL, $url);
curl_setopt($curlSession, CURLOPT_HEADER, 1);
curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);
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
// Check whether the Content-Disposition header is available but only when it is
// a dynamic file.
$content_disposition_header_present = FALSE;
foreach ($header_lines as $header_line) {
  if (stripos($header_line, 'Content-Disposition')===0) {
    $content_disposition_header_present = TRUE;
  }
}
if (!$static_file && !$content_disposition_header_present) {
  // check whether the content disposition header is available.
  // If not we are dealing with an invalid file.
  // And CiviCRM does a redirect to the login page however we
  // dont want to expose that through CiviProxy so we will return an error message instead.
  civiproxy_http_error("Invalid Resource", 403);
}

// store the information in the cache
$file_cache->save(json_encode($header_lines), $header_key);
$file_cache->save($body, $data_key);

// and reply
$content_disposition_header_present = FALSE;
foreach ($header_lines as $header_line) {
  header($header_line);
}

print $body;
curl_close ($curlSession);
