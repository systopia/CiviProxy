<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy\Api;

class Api {

  private $apiDefinitions = [];

  public function __construct(array $plugins) {
    foreach($plugins as $plugin) {
      foreach($plugin->getApiActionDefinitions() as $apiName => $apiDefinition) {
        $listener = $apiDefinition[0];
        $params = [];
        if (isset($apiDefinition[1])) {
          $params = $apiDefinition[1];
        }
        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
          $listener[0] = $listener[0]();
          $listener[1] = $listener[1] ?? '__invoke';
        }
        $this->apiDefinitions[strtolower($apiName)] = [[$plugin, $listener], $params];
      }
    }
  }

  public function callApi($action) {
    global $proxy_api_key;
    if (!$proxy_api_key || !is_string($proxy_api_key) || !strlen($proxy_api_key)) {
      return null;
    }
    if (isset($this->apiDefinitions[strtolower($action)])) {
      $params = [];
      foreach($this->apiDefinitions[strtolower($action)][1] as $param) {
        if (isset($_REQUEST[$param])) {
          $params[$param] = $_REQUEST[$param];
        }
      }
      if (!$this->verifyJWT($proxy_api_key, $params)) {
        return new ErrorResponse('Access Denied', '403');
      }
      $listener = $this->apiDefinitions[strtolower($action)][0];
      return call_user_func($listener, $params);
    }
  }

  /**
   * Verifies the JWT header
   * @param string $key
   * @param array $requiredClaims
   */
  private function verifyJWT(string $key, array $requiredClaims): bool {
    $token = civiproxy_get_header('X_CIVI_AUTH');
    if (strpos($token, 'Bearer ')!==0) {
      return FALSE;
    }
    $token = substr($token, 7);
    $sections = explode('.', $token);
    if (count($sections) !== 3) {
        return FALSE;
    }
    [$header, $payload, $signature] = $sections;

    if ($remainder = strlen($header) % 4) {
      $paddingLength = 4 - $remainder;
      $header .= str_repeat('=', $paddingLength);
    }
    $decodedHeader = base64_decode(strtr($header, '-_', '+/'));
    $decodedHeader = json_decode($decodedHeader, TRUE);
    if ($decodedHeader === false || !isset($decodedHeader['typ']) || $decodedHeader['typ'] !='JWT') {
      return FALSE;
    }
    if (!isset($decodedHeader['alg']) || $decodedHeader['alg'] != 'HS256') {
      // We only accept the hmac Sha 256 algorithm.
      return FALSE;
    }

    if ($remainder = strlen($signature) % 4) {
      $paddingLength = 4 - $remainder;
      $signature .= str_repeat('=', $paddingLength);
    }
    $signature = base64_decode(strtr($signature, '-_', '+/'));

    $verifiedSignature = hash_hmac('sha256',"$header.$payload", $key, TRUE);
    if ($verifiedSignature != $signature) {
      return FALSE;
    }

    if ($remainder = strlen($payload) % 4) {
      $paddingLength = 4 - $remainder;
      $payload .= str_repeat('=', $paddingLength);
    }
    $payload = base64_decode(strtr($payload, '-_', '+/'));
    $claims = json_decode($payload, TRUE);
    if (json_last_error() !== JSON_ERROR_NONE) {
      return FALSE;
    }
    if (!is_array($claims)) {
      return FALSE;
    }
    if (!isset($claims['exp']) || !isset($claims['sub'])) {
      return FALSE;
    }
    foreach($claims as $key => $value) {
      switch ($key) {
        case 'exp':
          if ((time() - 300) > $value) {
            // The token is expired.
            // We have a skew of 5 minutes (300 seconds)
            return FALSE;
          }
          break;
        case 'sub':
          if (!isset($_REQUEST['action']) || $_REQUEST['action'] != $value) {
            // The subject of the token is the name of the api action.
            // If those are not the same fail the request.
            return FALSE;
          }
          break;
        default:
          // Other claims are about values in the $_REQUEST.
          if (!isset($_REQUEST[$key]) || $_REQUEST[$key] != $value) {
            return FALSE;
          }
          unset($requiredClaims[$key]);
          break;    
      }
    }
    if (count($requiredClaims)) {
      // There are required claims over in the JWT token.
      return FALSE;
    }
    return TRUE;
  }

}