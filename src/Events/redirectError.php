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

class redirectError extends Event {

  /**
   * @var \CurlHandle
   */
  public $curlHandle;

  public $apiVersion = 3;

  /**
   * @param \CurlHandle $curl
   * @param int $apiVersion
   */
  public function __construct($curl, $apiVersion = 3)
  {
    $this->curlHandle = $curl;
    $this->apiVersion = $apiVersion;
  }



}
