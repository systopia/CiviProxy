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

  public const STATUS_UNKOWN = 'unknown';
  
  public const STATUS_QUEUED = 'queued';
  
  public const STATUS_DONE = 'done';

  public const STATUS_HTTP_ERROR = 'http-error';

  public const STATUS_API_ERROR = 'api-error';

  public const STATUS_ERROR = 'error';

  public ?string $url = NULL;

  public ?string $entity = NULL;

  public ?string $action = NULL;

  public ?array $parameters = NULL;

  public ?string $httpMethod = NULL;

  public ?string $httpLocation = NULL;

  public ?array $httpHeaders = NULL;

  public ?string $httpBody = NULL;

  public ?int $responseCode = NULL;

  public ?array $responseHeaders = NULL;

  public ?string $responseBody = NULL;

  public ?array $responseData = NULL;

  public ?int $errorCode = NULL;

  public ?string $error = NULL;

  public string $status = 'unknown';

  public ?string $date = NULL;

  public ?int $apiVersion = 3;

  public function __construct(?string $url = NULL, ?array $parameters = NULL)
  {
    $this->date = date('Y-m-d H:i:s');
    if (NULL !== $url) {
      $this->url = $url;
    }
    if (is_array($parameters)) {
      if (array_key_exists('json', $parameters)) {
        $jsonData = json_decode($parameters['json'], TRUE);
        if (is_array($jsonData)) {
          $this->parameters = $jsonData;
        }
      } else {
        $this->parameters = $parameters; 
      }
      if (array_key_exists('entity', $parameters)) {
        $this->entity = $parameters['entity'];
      }  
      if (array_key_exists('action', $this->parameters)) {
        $this->action = $parameters['action'];
      }
      if (array_key_exists('version', $this->parameters)) {
        $this->apiVersion = $parameters['version'];
      }
      unset($this->parameters['action']);
      unset($this->parameters['entity']);
      unset($this->parameters['version']);
      // We do not want to log the SITE KEY nor the API KEY. 
      // Those are sensitive data and known by the system administrator.
      unset($this->parameters['key']);
      unset($this->parameters['api_key']);
    }
  }

  public function toArray(): array {
    $this->sanitize();
    return [
      'date' => $this->date,
      'status' => $this->status,
      'apiVersion' => $this->apiVersion,
      'url' => $this->url,
      'entity' => $this->getEntity(),
      'action' => $this->getAction(),
      'parameters' => $this->parameters ?? [],
      'response' => $this->responseData ?? [],
      'request' => [
        'method' => $this->httpMethod ?? 'UNKNOWN',
        'url' => $this->httpLocation,
        'headers' => $this->httpHeaders ?? [],
        'body' => $this->httpBody,
        'responseCode' => $this->responseCode,
        'responseHeaders' => $this->responseHeaders ?? [],
        'responseBody' => $this->responseBody, 
      ],
      'errorCode' => $this->errorCode,
      'error' => $this->error,
    ];
  }

  protected function sanitize() {
    if (str_starts_with($this->responseBody, '{')) {
      $this->responseData = json_decode($this->responseBody, TRUE);
    } elseif ('' !== $this->responseBody) {
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
    if (NULL !== $this->responseCode && $this->responseCode > 400) {
      $this->status = static::STATUS_HTTP_ERROR;
    }
  }

  public function getEntity(): ?string {
    return $this->entity;
  }

  public function getAction(): ?string {
    return $this->action;
  }

  public function isJsonRequest(): ?bool {
    if (!empty($this->parameters['json'])) {
      return TRUE;
    }
    return FALSE;
  }

}
