<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger;

class Data {

  const STATUS_QUEUED = 'queued';
  
  const STATUS_DONE = 'done';

  const STATUS_HTTP_ERROR = 'http-error';

  const STATUS_API_ERROR = 'api-error';

  const STATUS_ERROR = 'error';

  public $url;

  public $entity;

  public $action;

  public $parameters;

  public $httpMethod;

  public $httpLocation;

  public $httpHeaders;

  public $httpBody;

  public $responseCode;

  public $responseHeaders;

  public $responseBody;

  public $responseData;

  public $erroCode;

  public $error;

  public $status;

  public $date;

  public $apiVersion = 3;

  public function __construct(?string $url = null, ?array $parameters)
  {
    $this->date = date('Y-m-d H:i:s');
    if ($url) {
      $this->url = $url;
    }
    if ($parameters) {
      $this->parameters = $parameters;
      if (is_array($this->parameters) && array_key_exists('key', $this->parameters)  && array_key_exists('api_key', $this->parameters)  && array_key_exists('entity', $this->parameters)  && array_key_exists('action', $this->parameters)) {
        $this->entity = $this->parameters['entity'];
        $this->action = $this->parameters['action'];
        unset($this->parameters['key']);
        unset($this->parameters['api_key']);
        unset($this->parameters['entity']);
        unset($this->parameters['action']);
        unset($this->parameters['version']);
      }
      if (array_key_exists('json', $parameters)) {
        $jsonData = json_decode($parameters['json'], true);
        if (is_array($jsonData)) {
          $this->parameters = $jsonData;
        }
      }
    }
  }

  public function toArray(): array {
    $this->sanitize();
    return [
      'date' => $this->date,
      'status' => $this->status ?? '',
      'apiVersion' => $this->apiVersion,
      'url' => $this->url ?? '',
      'entity' => $this->getEntity(),
      'action' => $this->getAction(),
      'parameters' => $this->parameters ?? [],
      'response' => $this->responseData ?? [],
      'request' => [
        'method' => $this->httpMethod ?? 'UNKNOWN',
        'url' => $this->httpLocation ?? '',
        'headers' => $this->httpHeaders ?? [],
        'body' => $this->httpBody ?? '',
        'responseCode' => $this->responseCode ?? '',
        'responseHeaders' => $this->responseHeaders ?? [],
        'responseBody' => $this->responseBody ?? '', 
      ],
      'errorCode' => $this->errorCode ?? '',
      'error' => $this->error ?? '',
    ];
  }

  protected function sanitize() {
    if (strlen($this->responseBody) && strpos($this->responseBody, '{') === 0) {
      $this->responseData = json_decode($this->responseBody, true);
    } elseif (strlen($this->responseBody)) {
      $xml = simplexml_load_string($this->responseBody, "SimpleXMLElement", LIBXML_NOCDATA);
      $json = json_encode($xml);
      $data = json_decode($json,TRUE);
      if (isset($data['Result'])) {
        $this->responseData = $data['Result'];
      }
    }
    if (is_array($this->responseData) && !empty($this->responseData['is_error'])) {
      $this->status = static::STATUS_API_ERROR;
    }
    if (is_array($this->responseData) && !empty($this->responseData['error_message'])) {
      $this->error = $this->responseData['error_message'];
    }
    if ($this->responseCode && $this->responseCode > 400) {
      $this->status = static::STATUS_HTTP_ERROR;
    }
  }

  public function getEntity():? string {
    return $this->entity;
  }

  public function getAction():? string {
    return $this->action;
  }

  public function isJsonRequest():? bool {
    if (!empty($this->parameters['json'])) {
      return true;
    }
    return false;
  }

}