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

  /**
   * @var int
   */
  public $httpCode;

  /**
   * @var string
   */
  public $error = '';

  /**
   * @var int
   */
  public $errorCode = 0;

  public $apiVersion = 3;

  /**
   * @param string $httpCode
   * @param string $error
   * @param int $errorCode
   * @param int $apiVersion
   */
  public function __construct($httpCode, $error = '', $errorCode = 0, $apiVersion = 3)
  {
    $this->httpCode = $httpCode;
    $this->error = $error;
    $this->errorCode = $errorCode;
    $this->apiVersion = $apiVersion;
  }



}
