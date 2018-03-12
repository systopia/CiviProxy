<?php

function civiproxy_callback_validate_request_method($expected, $actual){
  if($expected != $actual){
    civiproxy_http_error("Invalid request method.", 405);
  }
}

function civiproxy_callback_validate_content_type($expected, $actual){
  if($expected != $actual){
    civiproxy_http_error("Forbidden content type.", 403);
  }
}

function civiproxy_callback_validate_body($expected, $actual, $content_type){
  switch ($content_type) {
    case 'application/json':
      civiproxy_callback_validate_body_json($expected, $actual);
      break;
    case 'application/x-www-form-urlencoded':
      civiproxy_callback_validate_body_xwwwformurlencoded($expected, $actual);
      break;
    default:
      civiproxy_http_error("Forbidden content type (expecting {$expected}).", 403);
  }
}

function civiproxy_callback_validate_body_json($expected, $actual) {
  //TODO
}

function civiproxy_callback_validate_body_xwwwformurlencoded($expected, $actual) {
  //TODO
}

// For now, I have written this 'placeholder' method to pass on post requests.
// Sparkpost says that it works OK. Might be a good idea to refactor/improve
// civiproxy_redirect() instead/as well.
function civiproxy_callback_redirect($target_path, $method) {
  switch ($method) {
    case 'POST':
      civiproxy_callback_redirect_post($target_path);
      break;
  }
  exit;
}

// Change the URL, forward the body. Respond with the response
function civiproxy_callback_redirect_post($target_path) {
  global $target_civicrm;
  $target_url = "$target_civicrm/{$target_path}";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
  curl_setopt($ch, CURLOPT_URL, $target_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  $response = curl_exec($ch);
  if (curl_error($ch)){
    civiproxy_http_error("CURL error (" . curl_errno($ch) . ") ".curl_error($ch) , 501);
  }

  // I think that most callbacks just want a response code
  http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));

  // But some might be interested in the response.
  echo $response;
}
