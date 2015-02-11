<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/


// this is the primary variable that you would want to change
//$target_civicrm = 'https://civicrm.muslimehelfen.org';
$target_civicrm = 'http://localhost:8888/mh';

// API and SITE keys
$api_key_map = array();
$sys_key_map = array();

if (file_exists("secrets.php")) {
  // keys can also be stored in secrets php
  require_once "secrets.php";
}


// default paths, override if you want
$target_rest = $target_civicrm . '/sites/all/modules/civicrm/extern/rest.php';
$target_url  = $target_civicrm . '/sites/all/modules/civicrm/extern/url.php';
$target_open = $target_civicrm . '/sites/all/modules/civicrm/extern/open.php';

// define the REST actions that will be allowed
$rest_allowed_actions = array(
  'MhApi' => array(
      'getcontact'      => array(
                            'email'                 => 'string',
                            'first_name'            => 'string',
                            'last_name'             => 'string',
                            'organization_name'     => 'string',
                            'contact_type'          => array('Individual', 'Organization'),
                            'prefix'                => 'string',
                            'street_address'        => 'string',
                            'country'               => 'string',
                            'postal_code'           => 'string',
                            'city'                  => 'string',
                            'phone'                 => 'string',
                            'create_if_not_found'   => 'int',
                            'source'                => 'string',
                            ),
      'addcontribution'     => array(
                            'contact_id'            => 'int',
                            'financial_type_id'     => 'int',
                            'financial_type'        => 'string',
                            'payment_instrument'    => 'string',
                            'contribution_campaign' => 'string',
                            'total_amount'          => 'float2',
                            'currency'              => 'string',
                            'contribution_status'   => 'string',
                            'is_test'               => 'int',
                            'iban'                  => 'string',
                            'bic'                   => 'string',
                            'source'                => 'string',
                            'datum'                 => 'string',
                            'notes'                 => 'string',
                            ),
      'addactivity'     => array(
                            'contact_id'            => 'int',
                            'type_id'               => 'int',
                            'subject'               => 'string',
                            ),
    )
  );
