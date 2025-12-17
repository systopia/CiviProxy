<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy;

class EventDispatcher {

  private $eventListeners = [];

  private $sortedListeners = [];

  public function __construct(array $plugins) {
    foreach($plugins as $plugin) {
      foreach ($plugin->getSubscribedEvents() as $eventName => $eventListeners) {
        // $eventListeners could be the function name. In that case it is a string
        // and we use the default priority.
        // $eventListeners could also be an array where the first element is a string
        // which is the function name. The second element is optional but could contain the priority.
        // $eventListeners could also be an array containing multiple functions for the same event.
        if (\is_string($eventListeners)) {
          $this->addListener($eventName, [$plugin, $eventListeners]);
        } elseif (\is_string($eventListeners[0])) {
          $this->addListener($eventName, [$plugin, $eventListeners[0]], $eventListeners[1] ?? 0);
        } else {
          foreach ($eventListeners as $listener) {
            $this->addListener($eventName, [$plugin, $listener[0]], $listener[1] ?? 0);
          }
        }
      }
    }
  }

  /**
   * @param object $event
   */
  public function dispatchEvent($event) {
    $eventName = get_class($event);
    $this->sortListeners($eventName);
    $stoppable = $event instanceof Event;

    foreach ($this->sortedListeners[$eventName] as $listener) {
      if ($stoppable && $event->isPropagationStopped()) {
          break;
      }
      $listener($event);
    }
  }

  /**
   * Add an event listener.
   */
  public function addListener(string $eventName, $listener, int $priority = 0) {
    $this->eventListeners[$eventName][$priority][] = $listener;
    unset($this->sortedListeners[$eventName]);
  }

  /**
   * Sorts the internal list of listeners for the given event by priority.
   */
  private function sortListeners(string $eventName)
  {
    if (isset($this->sortedListeners[$eventName])) {
      return;
    }
    if (empty($this->eventListeners[$eventName])) {
      $this->sortedListeners[$eventName] = [];
      return;
    } 
    krsort($this->eventListeners[$eventName]);
    $this->sortedListeners[$eventName] = [];

    foreach ($this->eventListeners[$eventName] as &$listeners) {
      foreach ($listeners as &$listener) {
        $this->sortedListeners[$eventName][] = $listener;
      }
    }
  }

}