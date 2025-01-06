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
 * Implementation of hook_civicrm_enable
 */
function civiproxy_civicrm_enable() {
  return _civiproxy_civix_civicrm_enable();
}

/**
* Implementation of hook_civicrm_alterSettingsFolders
*
* Scan for settings in custom folder and import them
*
*/
