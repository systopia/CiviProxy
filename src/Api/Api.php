<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Api;

use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\JwtFacade;
use Systopia\CiviProxy\JWT\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class Api {

  private $apiDefinitions = [];

  public function __construct(array $plugins) {
    foreach($plugins as $plugin) {
      foreach($plugin->getApiActionDefinitions() as $apiName => $apiDefinition) {
        $handler = $apiDefinition[0];
        $params = [];
        if (isset($apiDefinition[1])) {
          $params = $apiDefinition[1];
        }
        if (array_key_exists(strtolower($apiName), $this->apiDefinitions)) {
          $existingApi = $this->apiDefinitions[strtolower($apiName)];
          throw new InvalidApiException('API '. $apiName . 'already exists in ' . implode("::", $existingApi[0]));
        }
        $this->apiDefinitions[strtolower($apiName)] = [[$plugin, $handler], $params];
      }
    }
  }

  public function callApi(Request $request): Response {
    global $proxyApiKey;
    if (!$proxyApiKey || !is_string($proxyApiKey) || !strlen($proxyApiKey)) {
      return new ErrorResponse('Not Implemented', 501);
    }

    $action = strtolower($request->get('action') ?? '');
    if ($action == '') {
      return new ErrorResponse('Invalid API invocation', 500);
    }

    $xCiviAuth = $request->getHeader('X_CIVI_AUTH');
    if (strpos($xCiviAuth, 'Bearer ')!==0) {
      return new ErrorResponse('Access Denied', 403);
    }
    $jwt = substr($xCiviAuth, 7);
    if (isset($this->apiDefinitions[$action])) {
      $params = [];
      foreach($this->apiDefinitions[$action][1] as $param) {
        if ($request->hasParameter($param)) {
          $params[$param] = $request->get($param);
        }
      }
      if (!$this->verifyJWT($jwt, $proxyApiKey, $request->get('action'), $params)) {
        return new ErrorResponse('Access Denied', 403);
      }
      $handler = $this->apiDefinitions[$action][0];
      $response = call_user_func($handler, $params);
      if ($response instanceof Response) {
        return $response;
      }
      return new ErrorResponse('Invalid response received', 500);
    }
    return new ErrorResponse('Not Implemented', 501);
  }

  /**
   * Verifies the JWT header
   * @param string $jwt
   * @param string $proxyApiKey
   * @param string $apiAction
   * @param array $requiredClaims
   */
  private function verifyJWT(string $jwt, string $proxyApiKey, string $apiAction, array $requiredClaims): bool {
    $timezone = date_default_timezone_get();
    $signedWith = new SignedWith(new Sha256(), InMemory::plainText($proxyApiKey));
    $validAt = new LooseValidAt(new SystemClock(new DateTimeZone($timezone)), new \DateInterval('PT5M'));
    $constraints[] = new RelatedTo($apiAction);
    foreach($requiredClaims as $claim => $expectedValue) {
      $constraints[] = new HasClaimWithValue($claim, $expectedValue);
    }
    $jwtFacade = new JwtFacade();
    try {
      $token = $jwtFacade->parse($jwt, $signedWith, $validAt, ...$constraints);
    } catch (\Exception $e) {
      return FALSE;
    }
    if (!$token instanceof UnencryptedToken) {
      return FALSE;
    }
    return TRUE;
  }

}