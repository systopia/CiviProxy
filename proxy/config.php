<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

// this is the primary variable that you would want to change
$target_civicrm = 'https://civicrm.muslimehelfen.org';
//$target_civicrm = 'http://localhost:8888/mh';


// default paths, override if you want
$target_rest = $target_civicrm . '/sites/all/modules/civicrm/extern/rest.php';
$target_url  = $target_civicrm . '/sites/all/modules/civicrm/extern/url.php';
$target_open = $target_civicrm . '/sites/all/modules/civicrm/extern/open.php';



$api_key_map = array(
  '' => ''
  );

$sys_key_map = array(
  '' => ''
  );