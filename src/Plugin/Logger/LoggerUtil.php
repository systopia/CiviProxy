<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger;

use Systopia\CiviProxy\CiviProxy;
use Systopia\CiviProxy\Plugin\Logger\Events\GetLoggerEvent;

class LoggerUtil {

  /**
   * Returns whether the request is queued.
   * 
   * A queued request is not send to civicrm but stored in the log.
   * 
   * @param Data $data
   * @param array $configuration
   * @return bool
   */
  public static function isQueued(Data $data, array $configuration): bool {
    $entityConfig = static::getEntityConfig($data, $configuration);
    if ($entityConfig && array_key_exists('queueOnly', $entityConfig)) {
      return $entityConfig['queueOnly'];
    }
    return FALSE;
  }

  /**
   * Returns the queue response for the request.
   * 
   * A queued request is not send to civicrm but stored in the log.
   * 
   * @param Data $data
   * @param array $configuration
   * @return string
   */
  public static function getQueueResponse(Data $data, array $configuration): string {
    $entityConfig = static::getEntityConfig($data, $configuration);
    $response = ['is_error' => '0'];
    if ($entityConfig && array_key_exists('queueReponse', $entityConfig)) {
      $response = $entityConfig['queueReponse'];
    }
    if ($data->isJsonRequest()) {
      return json_encode($response);
    }

    $count = "";
    if (isset($response['count'])) {
      $count = ' count="' . $response['count'] . '" ';
    }
    $xml = "<?xml version=\"1.0\"?><ResultSet xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" $count>";
    if (isset($response['values'])) {
      foreach($response['values'] as $v) {
        $xml .= '<Result>' . static::arrayToXml($v) . '</Result>';
      }
    } else {
      $xml .= '<Result>' . static::arrayToXml($response) . '</Result>';
    }
    return $xml;
  }

  /**
   * Returns a logger.
   * 
   * @param string $type
   *   The type of the logger.
   * @param array $configuration
   *   Configuration for the logger.
   * @return LoggerInterface
   */
  public static function getLogger(string $type, array $configuration): ?LoggerInterface {
    $logger = NULL;
    switch ($type) {
      case 'filesystem':
        $logger = new FileSystemLogger($configuration[$type]);
        break;
      case 'redis':
        $logger = new RedisLogger($configuration[$type]);
        break;
    }

    // Call the event dispatcher so a plugin can implement their own logger.
    // A plugin should set the logger property to their own implementation.
    $event = new GetLoggerEvent($type, $configuration, $logger);
    CiviProxy::instance()->dispatchEvent($event);
    return $event->logger;
  }

  /**
   * Returns the configuration for this entity.
   * 
   * @param Data $data
   * @param array $configuration
   * @return array
   */
  public static function getEntityConfig(Data $data, array $configuration): ?array {
    $entity = $data->getEntity();
    $action = $data->getAction();
    if (!$entity || !$action) {
      return null;
    }
    foreach($configuration['entities'] as $entityConfig) {
      if (strtolower($entityConfig['entity']) != strtolower($entity)) {
        continue;
      }
      if ($entityConfig['action'] == '*' || strtolower($entityConfig['action']) == strtolower($action)) {
        return $entityConfig;
      }
    }
    return null;
  }

  /**
   * Builds an XML fragment representing an array.
   *
   * Depending on the nature of the keys of the array (and its sub-arrays,
   * if any) the XML fragment may not be valid.
   *
   * @param array $array
   *   The array to be serialized.
   * @param int $depth
   *   (optional) Indentation depth counter.
   *
   * @return string
   *   XML fragment representing $array.
   */
  private static function arrayToXml(&$array, $depth = 1) {
    $xml = '';
    foreach ($array as $name => $value) {
      $xml .= str_repeat(' ', $depth * 4);
      if (is_array($value)) {
        $xml .= "<{$name}>\n";
        $xml .= static::arrayToXml($value, $depth + 1);
        $xml .= str_repeat(' ', $depth * 4);
        $xml .= "</{$name}>\n";
      }
      else {
        // make sure we escape value
        $value = str_replace(['&', '<', '>', ''], ['&amp;', '&lt;', '&gt;', ','], $value);
        $xml .= "<{$name}>$value</{$name}>\n";
      }
    }
    return $xml;
  }

}