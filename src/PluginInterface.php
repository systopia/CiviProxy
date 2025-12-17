<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy;

interface PluginInterface {

  /**
   * Get subscribed events.
   * 
   * The return should look something like:
   * 
   * return [
   *   'Systopia\CiviProxy\Events\FinishRedirect' => 'onFinishRedirect',
   *   'Systopia\CiviProxy\Events\RedirectError' => ['onRedirectError', 999],
   *   'Systopia\CiviProxy\Events\PrepareRedirect' => [
   *      ['onPrepareRedirectRunFirst', 1],
   *      ['onPrepareRedirectRunSecond', 0],
   *    ],
   * ];
   * 
   * In the above example the finish redirect is a simple implementation just returning 
   * the name of the listener function. You should implment this function in the plugin class.
   * 
   * The redirect error also provides a priority. A higher number means running early
   * The PrepareRedirect consist of two listener functions.
   * 
   * @return array
   */
  public function getSubscribedEvents();

  /**
   * Returns possible CiviProxy api's.
   * 
   * An example return looks like:
   * 
   * return [
   *  'readLog' => ['invokeReadLog', ['logFileName']],
   * ];
   * 
   * The key of the array is the api action. The value is an array containing the name of the function.
   * And an array containing the parameters for the function.
   * 
   * Below an example implementation of the function
   * 
   * function invokeReadLog(string $logFileName): \Systopia\CiviProxy\Api\Response {
   *   $content = file_get_contents($logFileName);
   *   return new \Systopia\CiviProxy\Api\Response($content);
   * }
   * 
   * @return array
   */
  public function getApiActionDefinitions();

}