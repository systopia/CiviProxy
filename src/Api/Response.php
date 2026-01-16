<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Api;

class Response {

  public array $headers = [];

  public string $response = '';

  public int $httpCode = 200;

  public function __construct(string $response, array $headers = [], int $httpCode = 200) {
    $this->response = $response;
    $this->headers = $headers;
    $this->httpCode = $httpCode;
  }

}
