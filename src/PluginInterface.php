<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy;

interface PluginInterface {

  /**
   * Get subscribed events.
   * 
   * @return array
   */
  public function getSubscribedEvents();

  /**
   * Get api's.
   * 
   * @return array
   */
  public function getApiActionDefinitions();

}