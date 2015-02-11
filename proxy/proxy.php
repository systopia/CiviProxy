<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

$civiproxy_version = '0.1';
require_once "config.php";


/**
 * this will redirect the request to another URL,
 *  i.e. will pass the reply on to this request
 * 
 * @see losely based on https://code.google.com/p/php-proxy/
 *
 * @param $url     the URL to which the 
 *                               where type can be 'int', 'string' (unchecked),
 */
function civiproxy_redirect($url_requested, $parameters) {
  error_log('CALLING: '.$url_requested);
  error_log(print_r($parameters,1));

  $url = $url_requested;
  $curlSession = curl_init();

  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // POST requests should be passed on as POST
    $postinfo = '';
    foreach ($parameters as $key=>$value) {
      $postinfo .= $key.'='.urlencode($value).'&';
    }
    rtrim($postinfo,'&');
    curl_setopt ($curlSession, CURLOPT_POST, 1);
    curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $postinfo);
  } else {
    // GET requests will get the parameters as url params
    $urlparams = '';
    foreach ($parameters as $key=>$value) {
      $urlparams .= $key.'='.urlencode($value).'&';
    }
    if (!empty($urlparams)) {
      $url .= '?' . $urlparams;
    }
  }

  curl_setopt ($curlSession, CURLOPT_URL, $url);
  curl_setopt ($curlSession, CURLOPT_HEADER, 1);
  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
  curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

  //Send the request and store the result in an array
  $response = curl_exec($curlSession);

  // Check that a connection was made
  if (curl_error($curlSession)){
    print curl_error($curlSession);

  } else {
    //clean duplicate header that seems to appear on fastcgi with output buffer on some servers!!
    $response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);
    
    // split header / content
    $content = explode("\r\n\r\n", $response, 2);
    $header = $content[0];
    $body = $content[1];

    // handle headers - simply re-outputing them
    $header_ar = split(chr(10), $header);
    foreach ($header_ar as $header_line){
      if (!preg_match("/^Transfer-Encoding/", $header_line)){
        // TODO: replace returned URLs
        //$header_line = str_replace($url, "TODO://", $header_line);
        header(trim($header_line));
      }
    }

    // TODO: do we need this?
    //rewrite all hard coded urls to ensure the links still work!
    //$body = str_replace($base,$mydomain,$body);
    print $body;
  }

  curl_close ($curlSession);
}


/**
 * Will check the incoming connection.
 * This hook allowes for (future) checks for flooding, spoofing, 
 * unauthorized access quantities, etc.
 * 
 * @param $target  
 * @param $quit    if TRUE, quit immediately if access denied
 *
 * @return TRUE if allowed, FALSE if not (or quits if $quit is set)
 */
function civiproxy_security_check($target, $quit=TRUE) {
  // TODO: implement
  return TRUE;
}


/**
 * extract and type check the parameters from the call params
 * 
 * @param $valid_parameters   array '<parameter name> => '<expected type>'
 *                               where type can be 'int', 'string' (unchecked),
 */
function civiproxy_get_parameters($valid_parameters) {
  $result = array();

  foreach ($valid_parameters as $name => $type) {
    if (isset($_REQUEST[$name])) {
      $value = $_REQUEST[$name];
      if ($type=='int') {
        $value = (int) $value;
      } elseif ($type == 'string') {
        // TODO: sanitize? SQL?
        $value = $value;
      } elseif ($type == 'float2') {
        // TODO: check if safe wrt l10n. rather use sprintf
        $value = number_format($value, 2, '.', '');
      } elseif (is_array($type)) {
        // this is a list of valid options
        $requested_value = $value;
        $value = '';
        foreach ($type as $allowed_value) {
          if ($requested_value === $allowed_value) {
            $value = $requested_value;
            break;
          }
        }
      } else {
        error_log("CiviProxy: unknown type '$type'. Ignored.");
        $value = '';
      }
      $result[$name] = $value;
    }
  }

  return $result;
}
