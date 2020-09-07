<?php
/*-------------------------------------------------------+
| CiviProxy                                              |
| Copyright (C) 2015 SYSTOPIA                            |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/

/**
 * Wrapper for CiviCRM Mailer
 */
class CRM_Civiproxy_Mailer {

  /**
   * this is the orginal, wrapped mailer
   */
  protected $mailer = NULL;

  /**
   * construct this mailer wrapping another one
   */
  public function __construct($mailer) {
    $this->mailer = $mailer;
  }

  /**
   * Send an email via the wrapped mailer,
   *  mending the URLs contained
   */
  function send($recipients, $headers, $body) {
    foreach ($headers as &$header) {
      CRM_CiviProxy_Mailer::mendURLs($header);
    }
    CRM_CiviProxy_Mailer::mendURLs($body);
    $this->mailer->send($recipients, $headers, $body);
  }


  /**
   * This function will manipulate the URLs in Emails, so they point
   *  to the correct proxy addresses
   */
  static function mendURLs(&$value) {
    // check if the proxy is enabled
    $enabled = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled');
    if (!$enabled) return;

    // get the URLs
    $config      = CRM_Core_Config::singleton();
    $system_base = $config->userFrameworkBaseURL;
    $proxy_base  = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url');

    // General external functions
    $value = preg_replace("#{$system_base}civicrm/mailing/url#i",                       $proxy_base.'/url.php',         $value);
    $value = preg_replace("#{$system_base}sites/all/modules/civicrm/extern/url.php#i",  $proxy_base.'/url.php',         $value);
    $value = preg_replace("#{$system_base}civicrm/mailing/open#i",                      $proxy_base.'/open.php',        $value);
    $value = preg_replace("#{$system_base}sites/all/modules/civicrm/extern/open.php#i", $proxy_base.'/open.php',        $value);
    $value = preg_replace("#{$system_base}sites/default/files/civicrm/persist/#i",      $proxy_base.'/file.php?id=',    $value);
    $value = preg_replace("#{$system_base}civicrm/mosaico/img\?src=#i",                 $proxy_base.'/mosaico.php?id=', $value);

    // Mailing related functions
    $value = preg_replace("#{$system_base}civicrm/mailing/view#i",                      $proxy_base.'/mailing/mail.php', $value);
    $custom_mailing_base = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'custom_mailing_base');
    $other_mailing_functions = array('subscribe', 'confirm', 'unsubscribe', 'resubscribe', 'optout');
    foreach ($other_mailing_functions as $function) {
      if (empty($custom_mailing_base)) {
        $new_url = "{$proxy_base}/mailing/{$function}.php";
      } else {
        $new_url = "{$custom_mailing_base}/{$function}.php";
      }
      $value = preg_replace("#{$system_base}civicrm/mailing/{$function}#i", $new_url, $value);
    }
  }
}
