<?php

require_once 'civiproxy.civix.php';

/**
 * In outgoing emails, replace the various resources and trackers with proxied versions
 */
function civiproxy_civicrm_alterMailParams( &$params, $context ) {
  // check if the proxy is enabled
  $enabled = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled');
  if (!$enabled) return;

  // get the URLs
  $config      = CRM_Core_Config::singleton();
  $system_base = $config->userFrameworkBaseURL;
  $proxy_base  = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url');

  // fields to replace:
  $external_urls  = array('url.php', 'open.php');
  $fields2replace = array('html', 'text');
  foreach ($fields2replace as $field) {
    $value = $params[$field];
    $value = preg_replace("#{$system_base}sites/all/modules/civicrm/extern/url.php#i",  $proxy_base.'/url.php',      $value);
    $value = preg_replace("#{$system_base}sites/all/modules/civicrm/extern/open.php#i", $proxy_base.'/open.php',     $value);
    $value = preg_replace("#{$system_base}sites/default/files/civicrm/persist/#i",      $proxy_base.'/file.php?id=', $value);
    $value = preg_replace("#{$system_base}civicrm/mailing/view#i",                      $proxy_base.'/mail.php',     $value);
    $value = preg_replace("#{$system_base}civicrm/mailing/optout#i",                    $proxy_base.'/index.php',     $value);
    $params[$field] = $value;
  }
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
