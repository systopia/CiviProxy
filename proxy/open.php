<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "config.php";
require_once "proxy.php";

// see if mail open tracking is enabled
if (!$target_open) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('open');

$parameters = civiproxy_get_parameters($valid_open_parameters);
civiproxy_redirect($target_open, $parameters);
