<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015 SYSTOPIA                            |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

require_once 'civiproxy.civix.php';

use \Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * We will provide our own Mailer (wrapping the original one).
 * so we can mend all the URLs in outgoing emails
 */
function civiproxy_civicrm_alterMailer(&$mailer, $driver, $params) {
  $mailer = new CRM_Civiproxy_Mailer($mailer);
}

/**
 * Implements hook_civicrm_container()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_container/
 */
function civiproxy_civicrm_container(ContainerBuilder $container) {
  $container->addCompilerPass(new Civi\CiviProxy\CompilerPass());
}

/**
 * Implementation of hook_civicrm_config
 */
function civiproxy_civicrm_config(&$config) {
  _civiproxy_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function civiproxy_civicrm_xmlMenu(&$files) {
  _civiproxy_civix_civicrm_xmlMenu($files);
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
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function civiproxy_civicrm_managed(&$entities) {
  return _civiproxy_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function civiproxy_civicrm_caseTypes(&$caseTypes) {
  _civiproxy_civix_civicrm_caseTypes($caseTypes);
}

/**
* Implementation of hook_civicrm_alterSettingsFolders
*
* Scan for settings in custom folder and import them
*
*/
function civiproxy_civicrm_alterSettingsFolders(&$metaDataFolders = NULL){
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'settings';
  if(!in_array($extDir, $metaDataFolders)){
    $metaDataFolders[] = $extDir;
  }
}
