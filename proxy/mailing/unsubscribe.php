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
civiproxy_security_check('mail-unsubscribe');

// basic restraints
$valid_parameters = array(    'jid'          => 'int',
                              'qid'          => 'int', 
                              'h'            => 'hex');
$parameters = civiproxy_get_parameters($valid_parameters);

// check if parameters specified
if (empty($parameters['jid'])) civiproxy_http_error("Missing/invalid parameter 'jid'.");
if (empty($parameters['qid'])) civiproxy_http_error("Missing/invalid parameter 'qid'.");
if (empty($parameters['h']))   civiproxy_http_error("Missing/invalid parameter 'h'.");

civiproxy_redirect($target_mail_base . '/unsubscribe', $parameters);
