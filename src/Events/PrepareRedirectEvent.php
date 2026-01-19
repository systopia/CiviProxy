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

  public string $url;

  public array $parameters;

  public string $body;

  public bool $hasResponse = FALSE;

  public string $response;

  public array $responseHeaders = [];

  public int $apiVersion = 3;

  public function __construct(string $url, array $parameters, string $body, int $apiVersion = 3) {
    $this->url = $url;
    $this->parameters = $parameters;
    $this->body = $body;
    $this->apiVersion = $apiVersion;
  }

}
