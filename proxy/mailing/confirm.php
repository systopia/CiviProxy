<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "../config.php";
require_once "../proxy.php";

// see if mail open tracking is enabled
if (!$target_mail_base) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('mail-confirm');

// basic restraints
$valid_parameters = array(    'sid'          => 'int',
                              'cid'          => 'int', 
                              'h'            => 'hex');
$parameters = civiproxy_get_parameters($valid_parameters);

// check if parameters specified
if (empty($parameters['sid'])) civiproxy_http_error("Missing/invalid parameter 'sid'.");
if (empty($parameters['cid'])) civiproxy_http_error("Missing/invalid parameter 'cid'.");
if (empty($parameters['h']))   civiproxy_http_error("Missing/invalid parameter 'h'.");

civiproxy_redirect($target_mail_base . '/confirm', $parameters);
