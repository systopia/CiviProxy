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
// this should point to the base address of the CiviProxy installation
$proxy_base     = 'https://proxy.yourdomain.org';

// this should point to the target CiviCRM system
$target_civicrm = 'https://your.civicrm.installation.org';


/****************************************************************
 **                      DEFAULT PATHS                         **
 **                                                            **
 **          set to NULL to disable a feature                  **
 ****************************************************************/

// default paths, override if you want. Set to NULL to disable
$target_rest      = $target_civicrm . '/sites/all/modules/civicrm/extern/rest.php';
$target_url       = $target_civicrm . '/sites/all/modules/civicrm/extern/url.php';
$target_open      = $target_civicrm . '/sites/all/modules/civicrm/extern/open.php';
$target_file      = $target_civicrm . '/sites/default/files/civicrm/persist/';
$target_mail_view = $target_civicrm . '/civicrm/mailing/view';
$target_mosaico_file = $target_civicrm . '/civicrm/mosaico/img?src=';

// Set api-key for mail subscribe/unsubscribe user
// Set to NULL/FALSE to disable the feature
$mail_subscription_user_key = NULL;

// CAREFUL: only enable temporarily on debug systems. Will log all queries to given PUBLIC file
$debug                      = NULL; //'debug.log';

// Local network interface or IP to be used for the relayed query 
// This is usefull in some VPN configurations (see CURLOPT_INTERFACE)
$target_interface           = NULL;

/****************************************************************
 **                   File Caching Options                     **
 ****************************************************************/

// API and SITE keys
$api_key_map = array();
$sys_key_map = array();

if (file_exists(dirname(__FILE__)."/secrets.php")) {
  // keys can also be stored in 'secrets.php'
  require "secrets.php";
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
  // this is an example:
  'Contact' => array(
      'getsingle'      => array(
                            'email' => 'string'
                            ),
    )
  );
