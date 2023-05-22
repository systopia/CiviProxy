<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "config.php";
require_once "proxy.php";
require_once "checks.php";

// see if REST API is enabled
if (!$target_rest) civiproxy_http_error("Feature disabled", 405);

// basic check
if (!civiproxy_security_check('rest')) {
  civiproxy_rest_error("Access denied.");
}

// check credentials
$credentials = civiproxy_get_parameters(array('key' => 'string', 'api_key' => 'string'));

civiproxy_map_site_key($credentials, $sys_key_map);
civiproxy_map_api_key($credentials, $api_key_map);

// check if the call itself is allowed
$action = civiproxy_get_parameters(array('entity' => 'string', 'action' => 'string', 'version' => 'int', 'json' => 'int', 'sequential' => 'int'));
if (!isset($action['version']) || $action['version'] != 3) {
  civiproxy_rest_error("API 'version' information missing.");
}

$valid_parameters= civiproxy_get_valid_parameters($action, $rest_allowed_actions);

// extract parameters and add credentials and action data
$parameters = civiproxy_get_parameters($valid_parameters);
foreach ($credentials as $key => $value) {
  $parameters[$key] = $value;
}
foreach ($action as $key => $value) {
  $parameters[$key] = $value;
}

// evaluate the JSON parameter
global $rest_evaluate_json_parameter;
if ($rest_evaluate_json_parameter) {
  if (isset($_REQUEST['json'])) {
    $json_data = json_decode($_REQUEST['json'], true);
    if (!empty($json_data) && is_array($json_data)) {
      $json_parameters = civiproxy_get_parameters($valid_parameters, $json_data);
      $parameters['json'] = json_encode($json_parameters);
    }
  }
}

// finally execute query
civiproxy_log($target_rest);
civiproxy_redirect($target_rest, $parameters);
