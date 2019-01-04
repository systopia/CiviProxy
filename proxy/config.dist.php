<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/


/****************************************************************
 **                      INSTALLATION                          **
 **                                                            **
 **  1. Make a copy of this file called config.php             **
 ****************************************************************/


/****************************************************************
 **                            URLS                            **
 ****************************************************************/
// this should point to the base address of the CiviProxy installation
$proxy_base     = 'https://proxy.yourdomain.org';

// this should point to the target CiviCRM system
$target_civicrm = 'https://your.civicrm.installation.org';


/****************************************************************
 **                FEATURES / DEFAULT PATHS                    **
 **                                                            **
 **          set to NULL to disable a feature                  **
 ****************************************************************/

// default paths, override if you want. Set to NULL to disable
$target_rest      = $target_civicrm . '/sites/all/modules/civicrm/extern/rest.php';
$target_url       = $target_civicrm . '/sites/all/modules/civicrm/extern/url.php';
$target_open      = $target_civicrm . '/sites/all/modules/civicrm/extern/open.php';
$target_file      = $target_civicrm . '/sites/default/files/civicrm/persist/';
$target_mosaico   = NULL; // (disabled by default): $target_civicrm . '/civicrm/mosaico/img?src=';
$target_mail_view = $target_civicrm . '/civicrm/mailing/view';

/****************************************************************
 **                    GENERAL OPTIONS                         **
 ****************************************************************/

// This logo is shown if the proxy server is address with a web browser
//  add your own logo here
$civiproxy_logo    = "<img src='{$proxy_base}/static/images/proxy-logo.png' alt='SYSTOPIA Organisationsberatung'></img>";


// Set api-key for mail subscribe/unsubscribe user
// Set to NULL/FALSE to disable the feature
$mail_subscription_user_key = NULL;

// CAREFUL: only enable temporarily on debug systems.
//  Will log all queries to given PUBLIC file
//  Also: use some random name (not this one!)
$debug                      = NULL; //'LUXFbiaoz4dVWuAHEcuBAe7YQ4YP96rN4MCDmKj89p.log';

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

// if you enable this, the system will also try to
// parse the 'json' parameter, which holds additional
// input data according to the CiviCRM REST API specs
$rest_evaluate_json_parameter = FALSE;

// whitelisting is done per IP address ($_SERVER['REMOTE_ADDR']) with a 'all' for the generic stuff that applies to all IP addresses
// - if a request comes in and the IP is not a key in the array, the whitelisted in 'all' are used
// - if a request comes in and the IP is indeed a key in the array, the whitelisted in the IP are checked first. If nothing is
//   found ,the 'all' ones are checked next.
$rest_allowed_actions = array(
  'all' => array(
    'Contact' => array(
      'getsingle' => array(
        'email' => 'string',
      ),
    ),
  ),
  '123.45.678.1' => array(
    'Contact' => array(
      'getsingle' => array(
        'first_name' => 'string',
        'last_name' => 'string',
        // the following means *all* remaining parameters will be
        //   added and sanitised as 'string'. Better leave it out
        //   if you know which parameters you expect
        '*' => 'string',
      ),
    ),
  ),
);
