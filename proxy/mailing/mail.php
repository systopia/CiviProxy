<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

ini_set('include_path', dirname(dirname(__FILE__)));
require_once "proxy.php";

// see if mail open tracking is enabled
if (!$target_mail_view) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('mail-view');

// basic restraints
$valid_parameters = array(  'id'   => 'int', 'cid' => 'int', 'cs' => 'string'  );
$parameters = civiproxy_get_parameters($valid_parameters);

// check if id specified
if (empty($parameters['id'])) civiproxy_http_error("Resource not found");

civiproxy_redirect($target_mail_view, $parameters);
