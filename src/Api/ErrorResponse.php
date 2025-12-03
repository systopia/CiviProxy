<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy\Api;

class ErrorResponse extends Response {

    /**
   * @param string $error_message
   * @param string $httpCode
   */
  public function __construct($error_message, $httpCode = '500') {
    $this->response = json_encode(['is_error' => '1', 'error_message' => $error_message]);
    $this->headers[] = 'Content-Type: application/json';
    $this->httpCode = $httpCode;
  }

}