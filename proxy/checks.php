<?php

/**
 * generates a CiviCRM REST API compliant error
 * and ends processing
 */
function civiproxy_rest_error($message) {
  $error = array( 'is_error'      => 1,
                  'error_message' => $message);
  // TODO: Implement header();
  print json_encode($error);
  exit();
}

/**
 * Updates $credentials['api_key'] in-place, or displays an error if api key
 * is missing or does not correspond to an entry in $api_key_map (which should
 * be set in config.php).
 * @param array $credentials
 * @param array $api_key_map
 */
function civiproxy_map_api_key(array &$credentials, array $api_key_map) {
  if (empty($credentials['api_key'])) {
    civiproxy_rest_error("No API key given");
  }
  else {
    if (isset($api_key_map[$credentials['api_key']])) {
      $credentials['api_key'] = $api_key_map[$credentials['api_key']];
    }
    else {
      civiproxy_rest_error("Invalid api key");
    }
  }
}

/**
 * Updates $credentials['key'] in-place, or displays an error if site key
 * is missing or does not correspond to an entry in $sys_key_map (which should
 * be set in config.php).
 * @param array $credentials
 * @param array $sys_key_map
 */
function civiproxy_map_site_key(array &$credentials, array $sys_key_map) {
  if (empty($credentials['key'])) {
    civiproxy_rest_error("No site key given");
  }
  else {
    if (isset($sys_key_map[$credentials['key']])) {
      $credentials['key'] = $sys_key_map[$credentials['key']];
    }
	else {
      civiproxy_rest_error("Invalid site key");
    }
  }
}

/**
 * @param array $action should have both 'entity' and 'action' keys set
 * @param array $rest_allowed_actions from config.php
 * @return array
 */
function civiproxy_get_valid_parameters(array $action, array $rest_allowed_actions) {
  // in release 0.4, allowed entity/actions per IP were introduced. To introduce backward compatibility,
  // the previous test is still used when no 'all' key is found in the array
  if (isset($rest_allowed_actions['all'])) {
    // get valid key for the rest_allowed_actions
    $valid_allowed_key = civiproxy_get_valid_allowed_actions_key($action, $rest_allowed_actions);
    $valid_parameters = civiproxy_retrieve_api_parameters($valid_allowed_key, $action['entity'], $action['action'], $rest_allowed_actions);
    if (!$valid_parameters) {
      civiproxy_rest_error("Invalid entity/action.");
    }
  }
  else {
    if (isset($rest_allowed_actions[$action['entity']]) && isset($rest_allowed_actions[$action['entity']][$action['action']])) {
      $valid_parameters = $rest_allowed_actions[$action['entity']][$action['action']];
    }
    else {
      civiproxy_rest_error("Invalid entity/action.");
    }
  }
  return $valid_parameters;
}
