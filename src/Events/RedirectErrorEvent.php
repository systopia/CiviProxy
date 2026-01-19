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

class RedirectErrorEvent extends Event {

  public int $httpCode;

  public string $error = '';

  public int $errorCode = 0;

  public int $apiVersion = 3;

  public function __construct(int $httpCode, string $error = '', int $errorCode = 0, int $apiVersion = 3) {
    $this->httpCode = $httpCode;
    $this->error = $error;
    $this->errorCode = $errorCode;
    $this->apiVersion = $apiVersion;
  }

}
