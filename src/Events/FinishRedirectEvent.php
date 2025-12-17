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

  /**
   * @var string
   */
  public $responseBody;

  /**
   * @var array
   */
  public $responseHeaders = [];

  /**
   * @var int
   */
  public $httpCode;

  /**
   * @var bool
   */
  public $hasResponse = FALSE;

  /**
   * @var string
   */
  public $response;

  public $apiVersion = 3;

  /**
   * @param array $responseHeaders
   * @param string $responseBody
   * @param int $httpCode
   * @param int $apiVersion
   */
  public function __construct($responseHeaders, $responseBody, $httpCode = 200, $apiVersion = 3)
  {
    $this->responseHeaders = $responseHeaders;
    $this->responseBody = $responseBody;
    $this->httpCode = $httpCode;
    $this->apiVersion = $apiVersion;
  }



}
