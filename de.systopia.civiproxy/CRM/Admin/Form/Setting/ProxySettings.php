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
    $this->addElement('checkbox','image_cache_enabled');
    $this->addElement('text', 'proxy_url', ts('Proxy URL'));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    $this->registerRule('onlyValidURL', 'callback', 'validateURL', 'CRM_Admin_Form_Setting_ProxySettings');
  }

  function addRules() {
    $this->addRule('proxy_url', ts('This may only contain a valid URL'), 'onlyValidURL');
  }

  function preProcess() {
    $this->assign('proxy_enabled', CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled'));
    $this->assign('image_cache_enabled', CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'image_cache_enabled'));
    $this->setDefaults(array(
        'proxy_url' => CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url'),
      ));
  }

  function postProcess() {
    // process all form values and save valid settings
    $values = $this->exportValues();

    // checkboxes
    CRM_Core_BAO_Setting::setItem(!empty($values['proxy_enabled']),'CiviProxy Settings', 'proxy_enabled');
    CRM_Core_BAO_Setting::setItem(!empty($values['image_cache_enabled']),'CiviProxy Settings', 'image_cache_enabled');

    // text
    if ($values['proxy_url']){
      CRM_Core_BAO_Setting::setItem($values['proxy_url'],'CiviProxy Settings', 'proxy_url');
    }

    // give feedback to user
    $session = CRM_Core_Session::singleton();
    $session->setStatus(ts("Settings successfully saved"), ts('Settings'), 'success');
    $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin/setting/civiproxy'));
  }

  static function validateURL($value) {
    return preg_match("/^(http(s?):\/\/)?(((www\.)?+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,6})+)|(localhost)|(\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b))(:[0-9]{1,5})?(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/",$value);
  }

}
