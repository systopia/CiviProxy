<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Api;

class ErrorResponse extends JsonResponse {

  public function __construct(string $errorMessage, int $httpCode = 500) {
    parent::__construct(['is_error' => '1', 'error_message' => $errorMessage], $httpCode);
  }

}
