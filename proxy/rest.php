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
if (!civiproxy_security_check('rest')) {
  civiproxy_rest_error("Access denied.");
}

// check credentials
error_log(print_r($_REQUEST,1));
$credentials = civiproxy_get_parameters(array('key' => 'string', 'api_key' => 'string'));
error_log(print_r($credentials,1));
if (empty($credentials['key'])) {
  civiproxy_rest_error("No site key given");
} else {
  if (isset($sys_key_map[$credentials['key']])) {
    $credentials['key'] = $sys_key_map[$credentials['key']];
  } else {
    civiproxy_rest_error("Invalid site key");
  }
}

if (empty($credentials['api_key'])) {
  civiproxy_rest_error("No API key given");
} else {
  if (isset($api_key_map[$credentials['api_key']])) {
    $credentials['api_key'] = $api_key_map[$credentials['api_key']];
  } else {
    civiproxy_rest_error("Invalid api key");
  }
}

// check if the call itself is allowed
$action = civiproxy_get_parameters(array('entity' => 'string', 'action' => 'string', 'version' => 'int', 'json' => 'int', 'sequential' => 'int'));
if (!isset($action['version']) || $action['version'] != 3) {
  civiproxy_rest_error("Invalid entity/action.");
}
if (isset($rest_allowed_actions[$action['entity']]) && isset($rest_allowed_actions[$action['entity']][$action['action']])) {
  $valid_parameters = $rest_allowed_actions[$action['entity']][$action['action']];
} else {
  civiproxy_rest_error("Invalid entity/action.");
}

// extract parameters and add credentials and action data
$parameters = civiproxy_get_parameters($valid_parameters);
foreach ($credentials as $key => $value) {
  $parameters[$key] = $value;
}
foreach ($action as $key => $value) {
  $parameters[$key] = $value;
}

// finally execute query
civiproxy_redirect($target_rest, $parameters);


/**
 * generates a CiviCRM REST API compliant error
 * and ends processing
 */
function civiproxy_rest_error($message) {
  $error = array( 'is_error'      => 1,
                  'error_message' => $message);
  // TODO: Implement
  //header();
  print json_encode($error);
  exit();
}
