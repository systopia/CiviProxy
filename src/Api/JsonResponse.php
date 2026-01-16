<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Api;

class JsonResponse extends Response {

  public function __construct(string|array $response, int $httpCode = 200) {
    parent::__construct(
      json_encode($response),
      ['Content-Type: application/json'],
      $httpCode
    );
  }

}
