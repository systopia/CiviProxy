<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Events;

use Systopia\CiviProxy\Event;

class FinishRedirectEvent extends Event {

  public string $responseBody;

  public array $responseHeaders = [];

  public int $httpCode;

  public bool $hasResponse = FALSE;

  public string $response;

  public int $apiVersion = 3;

  public function __construct(array|string $responseHeaders, string $responseBody, int $httpCode = 200, int $apiVersion = 3) {
    if (is_string($responseHeaders)) {
      $responseHeaders = explode(chr(10), $responseHeaders);
    }
    $this->responseHeaders = $responseHeaders;
    $this->responseBody = $responseBody;
    $this->httpCode = $httpCode;
    $this->apiVersion = $apiVersion;
  }

}
