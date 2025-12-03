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

use systopia\CiviProxy\Api\Response;
use systopia\CiviProxy\CiviProxy;

// see if CiviProxy API is enabled
if (!$proxy_api_key) civiproxy_http_error("Feature disabled", 405);

if (!isset($_REQUEST['action'])) {
  civiproxy_rest_error("Incorrect call.");
}

$return = CiviProxy::callApi($_REQUEST['action']);
if ($return instanceof Response) {
  http_response_code($return->httpCode);
  foreach($return->headers as $header_line) {
    header(trim($header_line));
  }
  echo $return->response;
  exit();
}

civiproxy_rest_error("Incorrect call.");
