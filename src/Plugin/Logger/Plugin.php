<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger;

use Systopia\CiviProxy\Api\JsonResponse;
use Systopia\CiviProxy\Events\FinishRedirectEvent;
use Systopia\CiviProxy\Events\PrepareRedirectEvent;
use Systopia\CiviProxy\Events\RedirectErrorEvent;
use Systopia\CiviProxy\PluginInterface;

class Plugin implements PluginInterface {

  /**
   * Data
   */
  private $data;

  /**
   * array
   */
  private $pluginConfiguration;

  /**
   * @var LogInterface
   */
  private $primaryLogger;

  /**
   * @var LogInterface
   */
  private $fallbackLogger;

  private $isInitialized = FALSE;

  /**
   * Get subscribed events.
   * 
   * @return array
   */
  public function getSubscribedEvents() {
    return [
      PrepareRedirectEvent::class => 'onPrepareRedirect',
      RedirectErrorEvent::class => 'onRedirectError',
      FinishRedirectEvent::class => 'onFinishRedirect',
    ];
  }

  /**
   * Get api's.
   * 
   * @return array
   */
  public function getApiActionDefinitions() {
    return [
      'readlog' => ['invokeReadLog'],
    ];
  }

  /**
   * Invoke the read log API
   */
  public function invokeReadLog() {
    $this->initializeLogger();
    $data = [];
    if ($this->primaryLogger) {
      $data = array_merge($data, $this->primaryLogger->readLog());
    }
    if ($this->fallbackLogger) {
      $data = array_merge($data, $this->fallbackLogger->readLog());
    }
    return new JsonResponse($data);
  }

  /**
   * On prepare redirect event handler.
   * 
   * Creates the data object. Which is written to the log upon error or upon success.
   */
  public function onPrepareRedirect(PrepareRedirectEvent $event) {
    $this->initializeLogger();
    $this->data = new Data($event->url, $event->parameters);
    $this->data->httpMethod = $_SERVER['REQUEST_METHOD'] == 'POST' ? 'POST' : 'GET';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->data->httpBody = $event->body;
    }
    if (LoggerUtil::isQueued($this->data, $this->pluginConfiguration)) {
      $this->data->status = Data::STATUS_QUEUED;
      $this->writeLog();
      $event->response = LoggerUtil::getQueueResponse($this->data, $this->pluginConfiguration);
      $event->hasResponse = TRUE;
      if ($this->data->isJsonRequest()) {
        $event->responseHeaders[] = 'Content-Type: application/json';
      }
    }
  }

  /**
   * On redirect error event handler.
   * 
   * Write the data to the log.
   */
  public function onRedirectError(RedirectErrorEvent $event) {
    if ($this->data) {
      $this->data->responseCode = $event->httpCode;
      $this->data->errorCode = $event->errorCode;
      $this->data->error = $event->error;
      $this->data->status = Data::STATUS_ERROR;
      $this->writeLog();
      unset($this->data);
    }
  }

  /**
   * On finish redirect event handler.
   * 
   * Write the data to the log.
   */
  public function onFinishRedirect(FinishRedirectEvent $event) {
    if ($this->data) {
      $this->data->responseBody = $event->responseBody;
      $this->data->responseHeaders = $event->responseHeaders;
      $this->data->responseCode = $event->httpCode;
      $this->data->status = Data::STATUS_DONE;
      $this->writeLog();
      unset($this->data);
    }
  }

  /**
   * Write to the primary logger
   * When writing to the primary logger fails then write to the fallback logger.
   */
  protected function writeLog() {
    $this->initializeLogger();
    $entityConfig = LoggerUtil::getEntityConfig($this->data, $this->pluginConfiguration);
    if ($entityConfig === null) {
      return;
    }

    try {
      if ($this->primaryLogger && $this->primaryLogger->writeToLog($this->data)) {
        return;
      }
    } catch (\Throwable $t) {
      // Do nothing
    }

    if ($this->fallbackLogger) {
      $this->fallbackLogger->writeToLog($this->data);
    }
  }

  /**
   * Initialize the primary logger and the fallback logger.
   */
  protected function initializeLogger() {
    global $loggerPluginConfiguration;
    if ($this->isInitialized) {
      return;
    }

    $this->pluginConfiguration = $loggerPluginConfiguration;
    $this->primaryLogger = LoggerUtil::getLogger($loggerPluginConfiguration['primaryLogger'], $loggerPluginConfiguration);
    if (!empty($loggerPluginConfiguration['fallbackLogger']) && $loggerPluginConfiguration['fallbackLogger'] != $loggerPluginConfiguration['primaryLogger']) {
      $this->fallbackLogger = LoggerUtil::getLogger($loggerPluginConfiguration['fallbackLogger'], $loggerPluginConfiguration);
    }

    $this->isInitialized = TRUE;
  }

}
