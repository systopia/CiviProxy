<?php

/*
* Settings metadata file
*/

return array(
  'proxy_enabled' => array(
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_enabled',
    'type' => 'Integer',
    'html_type' => 'Select',
    'default' => 0,
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Enables or disables the proxy',
    'help_text' => 'TODO',
  ),
  'proxy_url' => array(
    'group_name' => 'CiviProxy Settings',
    'group' => 'de.systopia',
    'name' => 'proxy_url',
    'type' => 'String',
    'default' => "",
    'add' => '4.3',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The URL from which the proxy will be available for requests',
    'help_text' => 'TODO',
  ),
 );
