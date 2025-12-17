<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy;

use Systopia\CiviProxy\Api\Api;
use Systopia\CiviProxy\Api\Request;
use Systopia\CiviProxy\Api\Response;
use Systopia\CiviProxy\Plugin\PluginNotFoundException;

class CiviProxy {

  /**
   * @var CiviProxy
   */
  private static $instance;

  private $plugins = [];

  /**
   * @var Systopia\CiviProxy\Api\Api
   */
  private $api;

  /**
   * @var Systopia\CiviProxy\EventDispatcher
   */
  private $eventDispatcher;

  /**
   * @return CiviProxy
   */
  public static function instance() {
    return self::$instance ??= new self();
  }

  private function __construct()
  {
    $this->initializePlugins();
  }

  private function initializePlugins() {
    global $plugins;
    foreach($plugins as $pluginClass) {
      if (!class_exists($pluginClass)) {
        throw new PluginNotFoundException($pluginClass);
      }
      $this->plugins[] = new $pluginClass();
    }
  }

  /**
   * Call the api
   * 
   * @param Request $request
   */
  public function callApi(Request $request): Response {
    $this->api ??= new Api($this->plugins);
    return $this->api->callApi($request);
  }

  /**
   * @param object $event
   */
  public function dispatchEvent($event) {
    $this->eventDispatcher ??= new EventDispatcher($this->plugins);
    $this->eventDispatcher->dispatchEvent($event);
  }



}
