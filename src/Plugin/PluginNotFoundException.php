<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin;

use Exception;

class PluginNotFoundException extends Exception {

  public function __construct(string $pluginClassName) {
    parent::__construct(
      'Plugin ' . $pluginClassName . ' could not be found. Is this a configuration issue in your config.php?'
    );
  }

}
