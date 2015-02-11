<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "config.php";
require_once "proxy.php";

// basic check
civiproxy_security_check('open');

// basic restraints
$valid_parameters = array(  'q'   => 'int'  );

$parameters = civiproxy_get_parameters($valid_parameters);
civiproxy_redirect($target_open, $parameters);
