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

// TODO: check for flooding, spoofing, etc.

// check credentials
$credentials = civiproxy_get_parameters(array('site_key' => 'string', 'api_key' => 'string'));
if (isset($sys_key_map[$credentials['site_key']])) {
  $credentials['site_key'] = $credentials['site_key'];
} else {
  civiproxy_rest_error("Invalid site key");
}
if (isset($api_key_map[$credentials['api_key']])) {
  $credentials['api_key'] = $credentials['api_key'];
} else {
  civiproxy_rest_error("Invalid api key");
}

// check if the call itself is allowed
$action = civiproxy_get_parameters(array('entity' => 'string', 'action' => 'string', 'version' => 'int'));
if (!isset($action['version']) || $action['version'] != 3) {
  civiproxy_rest_error("Invalid entity/action.");
}
if (isset($rest_allowed_actions[$action['entity']]) && isset($rest_allowed_actions[$action['entity']][$action['action']]) {
  $valid_parameters = $rest_allowed_actions[$action['entity']][$action['action']];
} else {
  civiproxy_rest_error("Invalid entity/action.");
}

$parameters = civiproxy_get_parameters($valid_parameters);
civiproxy_redirect($target_rest, $parameters);
