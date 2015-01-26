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

// basic restraints
$valid_parameters = array(  'u'   => 'int',
                            'q'   => 'int',
                            'qid' => 'int');

$parameters = civiproxy_get_parameters($valid_parameters);
civiproxy_redirect($target_url, $parameters);
