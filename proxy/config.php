<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/


/****************************************************************
 **                            URLS                            **
 ****************************************************************/
$target_civicrm = 'https://crmtest.muslimehelfen.org';
$proxy_base     = 'https://ssl.webpack.de/wp11230065.server-he.de';
//$proxy_base     = 'http://localhost:8888/proxy';


/****************************************************************
 **                      DEFAULT PATHS                         **
 **                                                            **
 **          set to NULL to disable a feature                  **
 ****************************************************************/

// default paths, override if you want. Set to NULL to disable
$target_rest     = $target_civicrm . '/sites/all/modules/civicrm/extern/rest.php';
$target_url      = $target_civicrm . '/sites/all/modules/civicrm/extern/url.php';
$target_open     = $target_civicrm . '/sites/all/modules/civicrm/extern/open.php';
$target_file     = $target_civicrm . '/sites/default/files/civicrm/persist/';
$target_mail     = $target_civicrm . '/civicrm/mailing/view';

// CAREFUL: only enable on debug systems. Will log all queries to given PUBLIC file
$debug           = NULL; //'debug.log';


/****************************************************************
 **                   File Caching Options                     **
 ****************************************************************/

// API and SITE keys
$api_key_map = array();
$sys_key_map = array();

if (file_exists("secrets.php")) {
  // keys can also be stored in 'secrets.php'
  require_once "secrets.php";
}

// define file cache options, see http://pear.php.net/manual/en/package.caching.cache-lite.cache-lite.cache-lite.php
$file_cache_options = array(
    'cacheDir' => 'file_cache/',
    'lifeTime' => 86400
);

// define regex patterns that shoud NOT be accepted
$file_cache_exclude = array();

// if set, cached file must match at least one of these regex patterns
$file_cache_include = array(
        //'#.+[.](png|jpe?g|gif)#i'           // only media files
    );



/****************************************************************
 **                   REST API OPTIONS                         **
 ****************************************************************/
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
