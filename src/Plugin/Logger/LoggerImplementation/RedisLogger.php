<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger\LoggerImplementation;

class RedisLogger implements LoggerInterface {

  /**
   * @var String
   */
  protected $host;

  /**
   * @var String
   */
  protected $port;

  /**
   * @var String
   */
  protected $stream;

  /**
   * @var float
   */
  protected $timeout = 0;

  /**
   * @var array
   */
  protected $auth = [];

  public function __construct(array $configuration) {
    $this->stream = $configuration['stream'];
    $this->host = $configuration['host'];
    $this->port = '6379';
    if (!empty($configuration['port'])) {
      $this->host = $configuration['port'];
    }
    if (array_key_exists('username', $configuration) && is_array($configuration['auth'])) {
      $this->auth = $configuration['auth'];
    }
    if (array_key_exists('timeout', $configuration)) {
      $this->timeout = $configuration['timeout'];
    }
  }

  /**
   * Writes data to the Redis log.
   * 
   * @param Data $data
   * @return bool
   *   Return true when data is sucessfully written
   */
  public function writeToLog(Data $data): bool {
    $redis = $this->getRedisConnection();
    if (!$redis) {
      return FALSE;
    }
    $redis->xAdd($this->stream, '*', json_encode($data->toArray()));
    return TRUE;
  }

  public function readLog(): array
  {
    $return = [];
    $redis = $this->getRedisConnection();
    if ($redis) {
      $len = $redis->xLen($this->stream);
      if ($len) {
        $data = $redis->xRange($this->stream, '-', '+');
        $data = array_map(function($item) {
          if (is_array($item)) {
            return $item;
          }
          return json_decode($item, TRUE);
        }, $data);
        $return = array_filter($data, 'is_array');
        $keys = array_keys($data);
        $redis->xDel($this->stream, $keys);
      }
    }
    return $return;
  }

  protected function getRedisConnection(): ?\Redis {
    static $redis = null;
    if (!$redis) {
      $redis = new \Redis();
      if (!$redis->connect($this->host, $this->port, $this->timeout)) {
        $redis = null;
        return null;
      }
      if (!empty($this->auth)) {
        if (!$redis->auth($this->auth)) {
          $redis = null;
          return null;
        }
      }
    } 
    return $redis;
  }

}
