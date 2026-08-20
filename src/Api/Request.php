<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Api;

class Request {

  public array $query;

  public array $request = [];

  public array $files = [];

  public array $server = [];

  public array $headers = [];

  public array $cookies = [];

  public static function create(): Request {
    $request = new Request($_GET, $_POST, $_FILES, $_SERVER, $_COOKIE);
    // We use getallheaders because it could be that not
    // all headers are set in $_SERVER.
    // For example the Authorization header.
    foreach(getallheaders() as $header => $headerValue) {
      $request->headers[$header] = $headerValue;
    }
    return $request;
  }

  public function __construct(array $query, array $request = [], array $files = [], array $server = [], array $cookies = []) {
    $this->query = $query;
    $this->request = $request;
    $this->files = $files;
    $this->server = $server;
    foreach ($server as $header => $headerValue) {
      if (stripos($header, 'HTTP_') === 0) {
        //In $_SERVER the HTTP headers look like HTTP_CONTENT_LENGTH.
        // We translate this to Content-Length
        $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($header, 5)))));
        $this->headers[$key] = $headerValue;
      }
    }
    $this->cookies = $cookies;
  }

  public function get(string $key): mixed {
    if (array_key_exists($key, $this->query)) {
      return $this->query[$key];
    }
    if (array_key_exists($key, $this->request)) {
      return $this->request[$key];
    }
    return NULL;
  }

  public function getHeader(string $header): mixed {
    if (array_key_exists($header, $this->headers)) {
      return $this->headers[$header];
    }
    return NULL;
  }

  public function hasParameter(string $key): bool {
    if (array_key_exists($key, $this->query)) {
      return TRUE;
    }
    if (array_key_exists($key, $this->request)) {
      return TRUE;
    }
    return FALSE;
  }

}
