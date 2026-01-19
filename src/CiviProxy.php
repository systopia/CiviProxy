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

  private static self $instance;

  private array $plugins = [];

  private Api $api;

  private EventDispatcher $eventDispatcher;

  /**
   * @throws \Systopia\CiviProxy\Plugin\PluginNotFoundException
   */
  private function __construct() {
    $this->initializePlugins();
  }

  /**
   * @throws \Systopia\CiviProxy\Plugin\PluginNotFoundException
   */
  private function initializePlugins(): void {
    global $plugins;
    foreach ($plugins as $pluginClass) {
      if (!class_exists($pluginClass)) {
        throw new PluginNotFoundException($pluginClass);
      }
      $this->plugins[] = new $pluginClass();
    }
  }

  /**
   * @return \Systopia\CiviProxy\CiviProxy
   */
  public static function instance(): self {
    return self::$instance ??= new self();
  }

  /**
   * @throws \Systopia\CiviProxy\Api\InvalidApiException
   */
  public function callApi(Request $request): Response {
    $this->api ??= new Api($this->plugins);
    return $this->api->callApi($request);
  }

  public function dispatchEvent(Event $event): void {
    $this->eventDispatcher ??= new EventDispatcher($this->plugins);
    $this->eventDispatcher->dispatchEvent($event);
  }

}
