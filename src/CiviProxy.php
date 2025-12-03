<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace systopia\CiviProxy;

use systopia\CiviProxy\Api\Api;

class CiviProxy {

  /**
   * @var CiviProxy
   */
  private static $instance;

  private $plugins = [];

  private $apiFactory;

  private $eventListeners = [];

  private $sortedListeners = [];

  /**
   * @return CiviProxy
   */
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new CiviProxy();
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->initializePlugins();
  }

  private function initializePlugins() {
    global $plugins;
    foreach($plugins as $pluginClass) {
      if (class_exists($pluginClass)) {
        $plugin = new $pluginClass();
        foreach ($plugin->getSubscribedEvents() as $eventName => $params) {
          if (\is_string($params)) {
            $this->addListener($eventName, [$plugin, $params]);
          } elseif (\is_string($params[0])) {
            $this->addListener($eventName, [$plugin, $params[0]], $params[1] ?? 0);
          } else {
            foreach ($params as $listener) {
              $this->addListener($eventName, [$plugin, $listener[0]], $listener[1] ?? 0);
            }
          }
        }
        $this->plugins[] = $plugin;
      }
    }
  }

  public function addListener(string $eventName, $listener, int $priority = 0) {
    $this->eventListeners[$eventName][$priority][] = $listener;
    unset($this->sortedListeners[$eventName]);
  }

  public static function callApi($action) {
    $civiproxy = CiviProxy::getInstance();
    if (!$civiproxy->apiFactory) {
      $civiproxy->apiFactory = new Api($civiproxy->plugins);
    }
    return $civiproxy->apiFactory->callApi($action);
  }

  /**
   * @param object $event
   */
  public static function dispatchEvent($event) {
    $eventName = get_class($event);

    $civiproxy = CiviProxy::getInstance();

    if (empty($civiproxy->eventListeners[$eventName])) {
      return;
    }

    if (!isset($civiproxy->sorted[$eventName])) {
      $civiproxy->sortListeners($eventName);
    }
    $stoppable = $event instanceof Event;

    foreach ($civiproxy->sortedListeners[$eventName] as $listener) {
      if ($stoppable && $event->isPropagationStopped()) {
          break;
      }
      $listener($event);
    }
  }

      /**
     * Sorts the internal list of listeners for the given event by priority.
     */
    private function sortListeners(string $eventName)
    {
      if (empty($this->eventListeners[$eventName])) {
        $this->sortedListeners[$eventName] = [];
      } 
      krsort($this->eventListeners[$eventName]);
      $this->sortedListeners[$eventName] = [];

      foreach ($this->eventListeners[$eventName] as &$listeners) {
        foreach ($listeners as $k => &$listener) {
          if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
            $listener[0] = $listener[0]();
            $listener[1] = $listener[1] ?? '__invoke';
          }
          $this->sortedListeners[$eventName][] = $listener;
        }
      }
    }


}
