<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015-2021 SYSTOPIA                       |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

require_once 'civiproxy.civix.php';

/**
 * We will provide our own Mailer (wrapping the original one).
 * so we can mend all the URLs in outgoing emails
 */
function civiproxy_civicrm_alterMailer(&$mailer, $driver, $params) {
  $mailer = new CRM_Civiproxy_Mailer($mailer, $driver, $params);
}

/**
 * Implementation of hook_civicrm_config
 */
function civiproxy_civicrm_config(&$config) {
  _civiproxy_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_install
 */
function civiproxy_civicrm_install() {
  return _civiproxy_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function civiproxy_civicrm_uninstall() {
  return _civiproxy_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function civiproxy_civicrm_enable() {
  return _civiproxy_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function civiproxy_civicrm_disable() {
  return _civiproxy_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function civiproxy_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civiproxy_civix_civicrm_upgrade($op, $queue);
}

/**
* Implementation of hook_civicrm_alterSettingsFolders
*
* Scan for settings in custom folder and import them
*
*/

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function civiproxy_civicrm_postInstall() {
  _civiproxy_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function civiproxy_civicrm_entityTypes(&$entityTypes) {
  _civiproxy_civix_civicrm_entityTypes($entityTypes);
}
