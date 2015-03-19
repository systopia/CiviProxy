<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require "config.php";
$civiproxy_version = '0.3';
$civiproxy_logo    = "<img src='{$proxy_base}/static/images/proxy-logo.png' alt='SYSTOPIA Organisationsberatung'></img>";

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
  // error_log('CALLING: '.$url_requested);
  // error_log(print_r($parameters,1));

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

  curl_setopt($curlSession, CURLOPT_URL, $url);
  curl_setopt($curlSession, CURLOPT_HEADER, 1);
  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
  curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($curlSession, CURLOPT_CAINFO, 'target.pem');

  //Send the request and store the result in an array
  $response = curl_exec($curlSession);

  // Check that a connection was made
  if (curl_error($curlSession)){
    civiproxy_http_error(curl_error($curlSession), curl_errno($curlSession));

  } else {
    //clean duplicate header that seems to appear on fastcgi with output buffer on some servers!!
    $response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);
    
    // split header / content
    $content = explode("\r\n\r\n", $response, 2);
    $header = $content[0];
    $body = $content[1];

    // handle headers - simply re-outputing them
    $header_ar = explode(chr(10), $header);
    foreach ($header_ar as $header_line){
      if (!preg_match("/^Transfer-Encoding/", $header_line)){
        civiproxy_mend_URLs($header_line);
        header(trim($header_line));
      }
    }

    //rewrite all hard coded urls to ensure the links still work!
    civiproxy_mend_URLs($body);

    print $body;
  }

  curl_close ($curlSession);
}


/**
 * Will mend all the URLs in the string that point to the target,
 *  so they will point to this proxy instead
 */
function civiproxy_mend_URLs(&$string) {
  global $target_rest, $target_url, $target_open, $target_file, $target_mail, $proxy_base;

  if ($target_rest) {
    $string = preg_replace("#$target_rest#", $proxy_base . '/rest.php', $string);
  }
  if ($target_url) {
    $string = preg_replace("#$target_url#",  $proxy_base . '/url.php', $string); 
  }
  if ($target_open) {
    $string = preg_replace("#$target_open#", $proxy_base . '/open.php', $string); 
  }
  if ($target_mail) {
    $string = preg_replace("#$target_mail#", $proxy_base . '/mail.php', $string); 
  }
  if ($target_file) {
    $string = preg_replace("#$target_file#", $proxy_base . '/file.php?id=', $string); 
  }
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
  global $debug;
  if (!empty($debug)) {
    $file = fopen($debug, 'a');
    fwrite($file, "REQUEST FROM " . $_SERVER['REMOTE_ADDR'] . " ON " . date('Y-m-d H:i:s') . ' -- ' . print_r($_REQUEST,1));
    fclose($file);
  }
  
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
      } elseif ($type == 'hex') {
        // hex code
        if (!preg_match("#^[0-9a-f]*$#gi", $value)) {
          error_log("CiviProxy: removed invalid hex parameter");
          $value = '';
        }
      } elseif ($type == 'email') {
        // valid email
        // TODO: regex email address
        // if (!preg_match("#^[0-9a-f]*$#gi", $value)) {
        //   error_log("CiviProxy: removed invalid hex parameter");
        //   $value = '';
        // }
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

/**
 * generates a CiviCRM REST API compliant error
 * and ends processing
 */
function civiproxy_http_error($message, $code = 404) {
  global $civiproxy_version;
  global $error_message;

  $civiproxy_error_message = $message;

  header("HTTP/1.1 $code $civiproxy_error_message (CiviProxy {$civiproxy_version})");
  require "error.php";

  exit();
}


/**
 * call the CiviCRM REST API via CURL
 */
function civicrm_api3($entity, $action, $data) {
  global $target_rest, $sys_key_map;

  // extract site key
  $site_keys = array_values($sys_key_map);
  if (empty($site_keys)) civiproxy_http_error('No site key set.');
  
  $query = $data;    // array copy(!)
  $query['key']        = $site_keys[0];
  $query['json']       = 1;
  $query['version']    = 3;
  $query['entity']     = $entity;
  $query['action']     = $action;

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_POST,           1);
  curl_setopt($curl, CURLOPT_POSTFIELDS,     $query);
  curl_setopt($curl, CURLOPT_URL,            $target_rest);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSLVERSION,     1);

  $response = curl_exec($curl);
  
  if (curl_error($curl)){
    civiproxy_http_error(curl_error($curl));
  } else {
    return json_decode($response, true);
  }
}

