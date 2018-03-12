<?php
// Handles callback URLs as follows:
// 1. Validates callback
// 2. Passes to civicrm if the payload passes validation
// 2. Returns an appropriate response (an HTML code)
// Note: valid callbacks should be defined as elements of a $callbacks variable
// in config.php.
// Callback URL should be configured in the service as follows:
// "{$proxy_base}/callback.php?source={$callbacks[$key]}&secret={$callbacks[$key]['secret']}"
// where $key is a key defined in the $callbacks variable in config.php.

require_once "config.php";
require_once "proxy.php";
require_once "callback.functions.php";

civiproxy_security_check('callback');

if (!isset($callbacks_enabled) || $callbacks_enabled !==true){
  civiproxy_http_error("Feature disabled", 403);
}

// Check that this callback has been defined
parse_str($_SERVER['QUERY_STRING'], $query_params);
if(isset($callbacks[$query_params['source']])){
  //Retrieve definition from config
  $definition = $callbacks[$query_params['source']];
}else{
  civiproxy_http_error("Undefined callback", 403);
}

// Check that a secret has been defined
if(!isset($definition['secret'])){
  civiproxy_http_error("No secret defined for this callback", 501);
}

// Check secret has been sent
if(!isset($query_params['secret'])){
  civiproxy_http_error("Secret missing from query parameters", 403);
}

// Check secret
if(!isset($query_params['secret']) || $definition['secret'] !== $query_params['secret'] ){
  civiproxy_http_error("Invalid secret", 403);
}

// Check this is a supported request method
if(!in_array($_SERVER['REQUEST_METHOD'], ['POST'])){
  civiproxy_http_error("Unsupported request method", 501);
}

// If a request method has been defined, validate it
if(isset($definition['request_method'])){
  civiproxy_callback_validate_request_method($definition['request_method'], $_SERVER['REQUEST_METHOD']);
}

// Check this is a supported content type
if(!in_array($_SERVER['CONTENT_TYPE'], ['application/json', 'application/x-www-form-urlencoded'])){
  civiproxy_http_error("Unsupported content type", 501);
}

// If a content type has been defined, validate it
if(isset($definition['content_type'])){
  civiproxy_callback_validate_content_type($definition['content_type'], $_SERVER['CONTENT_TYPE']);
}

// TODO? implement body validation
if(isset($validator['body'])){
  civiproxy_callback_validate_body($validator['body'], file_get_contents("php://input"), $_SERVER['CONTENT_TYPE']);
}

// We have passed all the validators, forward the request
civiproxy_callback_redirect($definition['target_path'], $_SERVER['REQUEST_METHOD']);
