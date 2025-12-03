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

class prepareRedirect extends Event {

  /**
   * @var string
   */
  public $url;

  /**
   * @var array
   */
  public $parameters;

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

    /**
   * @var array
   */
  public $responseHeaders = [];

  public $apiVersion = 3;

  /**
   * @param string $url
   * @param array $parameters
   * @param \CurlHandle $curl
   * @param int $apiVersion
   */
  public function __construct($url, $parameters, $curl, $apiVersion = 3)
  {
    $this->url = $url;
    $this->parameters = $parameters;
    $this->curlHandle = $curl;
    $this->apiVersion = $apiVersion;
  }



}
