<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "config.php";
require_once "../vendor/autoload.php";

use Systopia\CiviProxy\Api\Request;
use Systopia\CiviProxy\CiviProxy;

// see if CiviProxy API is enabled
if (!isset($proxyApiKey)) {
  civiproxy_http_error("Feature disabled", 405);
}

if (!isset($_REQUEST['action'])) {
  civiproxy_rest_error("Incorrect call.");
}

$request = new Request($_GET, $_POST, $_FILES, $_SERVER, $_COOKIE);
try {
  $return = CiviProxy::instance()->callApi($request);
  http_response_code($return->httpCode);
  foreach ($return->headers as $header_line) {
    header(trim($header_line));
  }
  echo $return->response;
  exit();
}
catch (Exception $e) {
  // @ignoreException
}

civiproxy_rest_error('Incorrect call.');
