<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy\Api;

class JsonResponse extends Response {

  /**
   * @param string $response
   */
  public function __construct($response) {
    $this->response = json_encode($response);
    $this->headers[] = 'Content-Type: application/json';
    $this->httpCode = '200';
  }

}