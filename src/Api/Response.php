<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy\Api;

class Response {

  /**
   * @var array
   */
  public $headers = [];

  /**
   * @var string
   */
  public $response = '';

  /**
   * @var string
   */
  public $httpCode = '200';

  /**
   * @param string $response
   * @param array $headers
   * @param string $httpCode
   */
  public function __construct($response, $headers = [], $httpCode = '200') {
    $this->response = $response;
    $this->headers = $headers;
    $this->httpCode = $httpCode;
  }

}