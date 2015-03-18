<?php

require_once 'CRM/Admin/Form/Setting.php';
require_once 'CRM/Core/BAO/CustomField.php';

/*
  CiviProxy Settings Form
*/
class CRM_Admin_Form_Setting_ProxySettings extends CRM_Admin_Form_Setting
{
  function buildQuickForm( ) {
    CRM_Utils_System::setTitle(ts('CiviProxy - Settings'));

    // add all required elements
    $this->addElement('checkbox','proxy_enabled');
    $this->addElement('text', 'proxy_url', ts('Proxy URL'), array('disabled' => 'disabled'));
    $this->addElement('static', 'proxy_version', ts('Proxy version'));

    $this->addElement('text', 'custom_mailing_base', ts('Custom Subscribe/Unsubscribe Pages'), array('disabled' => 'disabled'));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    $this->registerRule('onlyValidURL', 'callback', 'validateURL', 'CRM_Admin_Form_Setting_ProxySettings');
  }

  function addRules() {
    $this->addRule('proxy_url', ts('This may only contain a valid URL'), 'onlyValidURL');
    $this->addRule('custom_mailing_base', ts('This may only contain a valid URL'), 'onlyValidURL');
  }

  function preProcess() {
    $this->assign('proxy_enabled', CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled'));
    $proxyUrl = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url');
    $proxyVersion = "-";

    if($proxyUrl) {
      // try to get the current proxy version
      $response = $this->requestProxyVersion($proxyUrl);
      if ($response['is_error']) {
          $proxyVersion = $response['message'];
          CRM_Core_BAO_Setting::setItem(NULL,'CiviProxy Settings', 'proxy_version');
      }else{
          $proxyVersion = $response['version'];
          CRM_Core_BAO_Setting::setItem($proxyVersion,'CiviProxy Settings', 'proxy_version');
      }
    }

    $this->setDefaults(array(
        'proxy_url'     => $proxyUrl,
        'proxy_version' => $proxyVersion, // watch out, this might contain an error message
        'custom_mailing_base' => CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'custom_mailing_base')
      ));
  }

  function postProcess() {
    // process all form values and save valid settings
    $values = $this->exportValues();

    // checkboxes
    CRM_Core_BAO_Setting::setItem(!empty($values['proxy_enabled']),'CiviProxy Settings', 'proxy_enabled');

    // text
    if (isset($values['proxy_url'])) {
      CRM_Core_BAO_Setting::setItem($values['proxy_url'],'CiviProxy Settings', 'proxy_url');
    }
    if (isset($values['custom_mailing_base'])) {
      // check if it is simply default ({$proxy_url}/mailing)
      if ($values['custom_mailing_base'] == $values['proxy_url'] . '/mailing') {
        // ...in which case we'll simply set it to ''
        $values['custom_mailing_base'] = '';
      }
      CRM_Core_BAO_Setting::setItem($values['custom_mailing_base'],'CiviProxy Settings', 'custom_mailing_base');
    }

    // give feedback to user
    $session = CRM_Core_Session::singleton();
    $session->setStatus(ts("Settings successfully saved"), ts('Settings'), 'success');
    $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin/setting/civiproxy'));
  }

  static function validateURL($value) {
    return preg_match("/^(http(s?):\/\/)?(((www\.)?+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,6})+)|(localhost)|(\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b))(:[0-9]{1,5})?(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/",$value);
  }

 /* Performs an http request to the specified url and tries
  * to parse the response in order to get the current proxy
  * version
  *
  * @param $url url of the proxy to use
  * @return array(int is_error, [string message || string version])
  */
  function requestProxyVersion($url) {
    $response = @file_get_contents($url);
    if($response === FALSE) {
      return array('is_error' => 1, 'message' => sprintf(ts('Error: cannot access "%s"'), $url));
    }else{
      $result = preg_match("/<p id=\"version\">CiviProxy Version ([0-9]+\.[0-9]+|[0-9]+\.[0-9]+\.[0-9]+)<\/p>/", $response, $output_array);
      if ($result === FALSE || $result === 0){
        return array('is_error' => 1, 'message' => sprintf(ts('Error: failed to parse version information'), $url));
      }else{
        return array('is_error' => 0, 'version' => $output_array[1]);
      }
    }
  }

}
