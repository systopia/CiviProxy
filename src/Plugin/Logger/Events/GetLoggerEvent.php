<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger\Events;

use Systopia\CiviProxy\Event;
use Systopia\CiviProxy\Plugin\Logger\LoggerInterface;

class GetLoggerEvent extends Event {

  public string $type;

  public array $configuration = [];

  public ?LoggerInterface $logger = null;

  public function __construct(string $type, array $configuration, ?LoggerInterface $logger = null)
  {
    $this->type = $type;
    $this->configuration = $configuration;
    $this->logger = $logger;
  }

}