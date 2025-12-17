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

class PrepareRedirectEvent extends Event {

  /**
   * @var string
   */
  public $url;

  /**
   * @var array
   */
  public $parameters;

  /**
   * @var string
   */
  public $body;

  /**
   * @var bool
   */
  public $hasResponse = FALSE;

  /**
   * @var string
   */
  public $response;

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
  public function __construct($url, $parameters, $body, $apiVersion = 3)
  {
    $this->url = $url;
    $this->parameters = $parameters;
    $this->body = $body;
    $this->apiVersion = $apiVersion;
  }



}
