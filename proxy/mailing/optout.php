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
if (!$mail_subscription_user_key) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('mail-optout');

civiproxy_http_error("Sorry, opt-out not yet implemented", 405);
