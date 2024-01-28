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
if (!$target_rest4) {
  civiproxy_http_error("Feature disabled");
}
$valid_flows = ['header', 'xheader', 'legacyrest', 'param'];
$headers_by_flow = [
 'header' => ['HTTP_AUTHORIZATION', 'HTTP_X_CIVI_KEY'],
 'xheader' => ['HTTP_X_CIVI_AUTH', 'HTTP_X_CIVI_KEY'],
 'legacyrest' => [],
 'param' => [],
];
if (!in_array($authx_internal_flow, $valid_flows)) {
  civiproxy_http_error("Invalid internal auth flow '$authx_internal_flow'", 500);
}
$headers_to_log = [];
foreach ($authx_external_flow as $external_flow) {
  if (!in_array($external_flow, $valid_flows)) {
    civiproxy_http_error("Invalid external auth flow '$external_flow'", 500);
  }
  $headers_to_log = array_merge($headers_to_log, $headers_by_flow[$external_flow]);
}

// basic check
if (!civiproxy_security_check('rest', TRUE, $headers_to_log)) {
  civiproxy_rest_error("Access denied.");
}

$credentials = [];
// Find credentials on the incoming request
foreach ($authx_external_flow as $external_flow) {
  switch($external_flow) {
    case 'header':
      $credentials['api_key'] = civiproxy_get_header('AUTHORIZATION', 'Bearer ');
      $credentials['key'] = civiproxy_get_header('HTTP_X_CIVI_KEY');
      break;
    case 'xheader':
      $credentials['api_key'] = civiproxy_get_header('X_CIVI_AUTH', 'Bearer ');
      $credentials['key'] = civiproxy_get_header('HTTP_X_CIVI_KEY');
      break;
    case 'legacyrest':
      $credentials = civiproxy_get_parameters(array('api_key' => 'string', 'key' => 'string'));
      break;
    case 'param':
      $authx_credentials = civiproxy_get_parameters(array('_authx' => 'string', '_authxSiteKey' => 'string'));
      if (!empty($authx_credentials['_authx'])) {
        // Snip off leading 'Bearer ' or 'Bearer+'
        if (substr($authx_credentials['_authx'], 0, 6) === 'Bearer') {
          $credentials['api_key'] = substr($authx_credentials['_authx'], 7);
        }
      }
      if (!empty($authx_credentials['_authxSiteKey'])) {
        $credentials['key'] = $authx_credentials['_authxSiteKey'];
      }
      break;
  }
  if (!empty($credentials['api_key'])) {
    break;
  }
}

civiproxy_map_api_key($credentials, $api_key_map);
if (!empty($credentials['key'])) {
  civiproxy_map_site_key( $credentials, $sys_key_map);
}

// check if the call itself is allowed
$action = civiproxy_get_parameters(array('entity' => 'string', 'action' => 'string'));

$valid_parameters = civiproxy_get_valid_parameters($action, $rest_allowed_actions);

// extract parameters and add action data
$parameters = civiproxy_get_parameters($valid_parameters, json_decode($_REQUEST['params'], true));

// finally execute query
civiproxy_log($target_rest4);
civiproxy_redirect4($target_rest4 . $action['entity'] . '/' . $action['action'] , $parameters, $credentials);
