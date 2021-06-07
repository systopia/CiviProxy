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

// see if URL tracking is enabled
if (!$target_url) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('url');

// basic restraints
$valid_parameters = array(  'u'   => 'int',
                            'q'   => 'int',
                            'qid' => 'int');

$parameters = civiproxy_get_parameters($valid_parameters);
civiproxy_redirect($target_url, $parameters);
