<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy\Events;

use systopia\CiviProxy\Event;

class finishRedirect extends Event {

  /**
   * @var string
   */
  public $responseBody;

  /**
   * @var array
   */
  public $responseHeaders = [];

  /**
   * @var \CurlHandle
   */
  public $curlHandle;

  /**
   * @var bool
   */
  public $hasRepsone = false;

  /**
   * @var string
   */
  public $repsone;

  public $apiVersion = 3;

  /**
   * @param array $responseHeaders
   * @param string $responseBody
   * @param \CurlHandle $curl
   * @param int $apiVersion
   */
  public function __construct($responseHeaders, $responseBody, $curl, $apiVersion = 3)
  {
    $this->responseHeaders = $responseHeaders;
    $this->responseBody = $responseBody;
    $this->curlHandle = $curl;
    $this->apiVersion = $apiVersion;
  }



}
