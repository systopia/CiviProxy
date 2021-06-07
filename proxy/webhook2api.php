<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2019-2021 SYSTOPIA                        |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "config.php";
require_once "proxy.php";

// first check if webhooks are enabled
if (empty($webhook2api)) civiproxy_http_error("Feature disabled", 405);

// basic check
if (!civiproxy_security_check('webhook2api')) {
  civiproxy_http_error("Access denied", 403);
}

// find the right configuration
if (!empty($_REQUEST['id']) && isset($webhook2api['configurations'][$_REQUEST['id']])) {
  // we found the if in the configurations
  $configurations = [$webhook2api['configurations'][$_REQUEST['id']]];
} elseif (empty($_REQUEST['id']) && isset($webhook2api['configurations']['default'])) {
  // this is teh default configuration
  $configurations = [$webhook2api['configurations']['default']];
} else {
  // use all of them (first one matching is executed)
  $configurations = $webhook2api['configurations'];
}

// read some input
$post_input = @file_get_contents('php://input');

// MAIN: iterate through all (eligible) configurations
$last_error = ["No handler found", 501];
foreach ($configurations as $configuration) {
  $last_error = webhook2api_processConfiguration($configuration, $post_input);
  if ($last_error == NULL) {
    // success!
    break;
  }
}

// finally - if there was only errors, return the last one
if ($last_error) {
  civiproxy_http_error($last_error[0], $last_error[1]);
}




/**
 * Apply the given configuration. If it matches and executes,
 *  it returns NULL, otherwise
 *
 * @param $configuration array configuration/specification
 * @return null|array [status_code, error message]
 */
function webhook2api_processConfiguration($configuration, $post_input) {
  // check the IP/range restrictions
  if (!empty($configuration['ip_sources']) && is_array($configuration['ip_sources'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_granted = FALSE;
    foreach ($configuration['ip_sources'] as $netmask) {
      // copied from https://secure.php.net/manual/de/ref.network.php
      list ($net, $mask) = explode("/", $netmask);
      $ip_net = ip2long ($net);
      $ip_mask = ~((1 << (32 - $mask)) - 1);
      $ip_ip = ip2long ($ip);
      $ip_ip_net = $ip_ip & $ip_mask;
      if ($ip_ip_net == $ip_net) {
        $access_granted = TRUE;
        break;
      }
    }
    if (!$access_granted) {
      // this configuration is not eligible
      return ["Access denied", 403];
    }
  }

  // gather source data
  $data = [];
  if (!empty($configuration['data_sources']) && is_array($configuration['data_sources'])) {
    foreach ($configuration['data_sources'] as $data_source) {
      switch ($data_source) {
        case 'POST/json': # JSON data in POST field
          $more_data = json_decode($post_input, TRUE);
          $data = array_merge_recursive($data, $more_data);
          break;
        case 'REQUEST': # simple request parameters
          $data = array_merge_recursive($data, $_REQUEST);
          break;
        default:
          civiproxy_log("Webhook2API[{$configuration['name']}]: unknown source '{$data_source}' in configuration. Ignored.");
      }
    }
  }

  // default return code if everything goes according to plan
  $http_code = 200;
  // check if we have a json_array and react accordingly
  if (isset($data[0]) && is_array($data[0])) {
    foreach ($data as $d) {
      $result = webhook2api_callCiviApi($configuration, $d);
      if(isset($result['internal_error'])) {
        // internal communication Error occurred. Aborting process
        civiproxy_log("Webhook2API[{$configuration['name']}]: internal error occurred: " . json_encode($result['internal_error']));
        return $result['internal_error'];
      }
      if (!empty($result['values']['http_code'])) {
        $http_code = $result['values']['http_code'];
      } else {
        $http_code = 403;
        break;
      }
    }
  } else {
    $result = webhook2api_callCiviApi($configuration, $data);
    if(isset($result['internal_error'])) {
      // internal communication Error occurred. Aborting process
      civiproxy_log("Webhook2API[{$configuration['name']}]: internal error occurred: " . json_encode($result['internal_error']));
      return $result['internal_error'];
    }
    if (!empty($result['values']['http_code'])) {
      $http_code = $result['values']['http_code'];
    } else {
      $http_code = 403;
    }
  }
  if ($http_code != '200') {
    // we received and parsed the webhook event successfully, but an error occurred with civicrm:
    civiproxy_log("Webhook2API[{$configuration['name']}]: Internal CiviCRM Error. Error Code: {$http_code}. Full Message: " . json_encode($result));
  }

  // process result
  if (!empty($configuration['response_mapping']) && is_array($configuration['response_mapping'])) {
    // TODO: implement
    http_response_code('200');
    civiproxy_log("Webhook successful, response mapped.");
  } else {
    // default behaviour:
    http_response_code('200');
    civiproxy_log("Webhook successful.");
  }
  // all done
  exit();
}


/**
 * Parse Configuration and given data set, apply it and send it to civicrm.
 * Returns an internal error if communication to civicrm isn't successful
 *
 * @param $configuration
 * @param $data
 *
 * @return array|mixed|void
 * @throws \CiviCRM_API3_Exception
 */
function webhook2api_callCiviApi($configuration, $data) {
  // evaluate sentinels
  if (!empty($configuration['sentinel']) && is_array($configuration['sentinel'])) {
    foreach ($configuration['sentinel'] as $sentinel) {
      list($value_source, $check) = $sentinel;
      $value = webhook2api_getValue($data, $value_source);
      if (substr($check, 0, 6) == "equal:") {
        // check if terms a equal
        if (substr($check, 6) != $value) {
          return ["internal_error" => "Access denied", 403];
        }
      } else {
        echo "Error";
        // unknown instruction
        civiproxy_log("Webhook2API[{$configuration['name']}]: don't understand sentinel '{$check}'. Ignored.");
      }
    }
  }

  // compile API query
  $params = [];
  if (!empty($configuration['parameter_mapping']) && is_array($configuration['parameter_mapping'])) {
    foreach ($configuration['parameter_mapping'] as $mapping) {
      $source_path = $mapping[0];
      $target_path = $mapping[1];
      $modifiers   = isset($mapping[2]) ? $mapping[2] : [];

      // get value
      $value = webhook2api_getValue($data, $source_path);

      // run modifiers
      foreach ($modifiers as $modifier) {
        // TODO: implement

        civiproxy_log("Webhook2API.modifiers: not implemented!");
      }

      // set to target
      webhook2api_setValue($params, $target_path, $value);
    }
  } else {
    $params = $data;
  }

  // sanitise data
  if (!empty($configuration['parameter_sanitation']) && is_array($configuration['parameter_sanitation'])) {
    // TODO: implement
    civiproxy_log("Webhook2API.sanitation: not implemented!");
  }

  // send to target REST API
  if (empty($configuration['entity']) || empty($configuration['action'])) {
    civiproxy_log("Webhook2API[{$configuration['name']}]: Missing entity/action.");
    return ["internal_error" => "Configuration error", 403];
  }
  if (empty($configuration['api_key'])) {
    civiproxy_log("Webhook2API[{$configuration['name']}]: Missing api_key.");
    return ["internal_error" => "Configuration error", 403];
  }
  $params['api_key'] = $configuration['api_key'];

  // run API call
  return civicrm_api3($configuration['entity'], $configuration['action'], $params);

}


/**
 * Get the value from a multidimensional array,
 *  specified by the path
 *
 * @param $data array         multidimensional data array
 * @param $path array|string  path description
 * @return mixed value
 */
function webhook2api_getValue($data, $path) {
  if (is_string($path)) {
    if (isset($data[$path])) {
      return $data[$path];
    } else {
      return NULL;
    }
  } elseif (is_array($path)) {
    if (count($path) == 0) {
      return NULL;
    } elseif (count($path) == 1) {
      return webhook2api_getValue($data, $path[0]);
    } else {
      $path_element = array_shift($path);
      $sub_data = webhook2api_getValue($data, $path_element);
      if (is_array($sub_data)) {
        return webhook2api_getValue($sub_data, $path);
      } else {
        return NULL;
      }
    }
  }
}

/**
 * Set the value from a multidimensional array as specified by the path
 *
 * @param $data        array the data
 * @param $target_path array destination
 * @param $value       mixed value
 */
function webhook2api_setValue(&$data, $target_path, $value) {
  if (is_array($target_path)) {
    if (count($target_path) == 0) {
      civiproxy_log("Webhook2API.setValue: Empty target path!");
      return;

    } elseif (count($target_path) == 1) {
      // last element -> set value
      $data[$target_path[0]] = $value;

    } else {
      // not last element
      $element = array_shift($target_path);
      if (!isset($data[$element])) {
        $data[$element] = [];
      }
      if (is_array($data[$element])) {
        webhook2api_setValue($data[$element], $target_path, $value);
      } else {
        civiproxy_log("Webhook2API.setValue: path node is not an array!");
      }
    }

  } elseif (is_string($target_path)) {
    webhook2api_setValue($data, [$target_path], $value);

  } else {
    civiproxy_log("Webhook2API.setValue: path neither string nor array!");
  }
}