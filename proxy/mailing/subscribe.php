<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015 SYSTOPIA                             |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require "../proxy.php";

// see if mailing subscribe feature is enabled
if (empty($mail_subscription_user_key)) civiproxy_http_error("Feature disabled", 405);

// get the groups you could subscribe to
$group_query = civicrm_api3('Group', 'get', array( 'visibility' => 'Public Pages',
                                                   'is_hidden'  => 0,
                                                   'is_active'  => 1,
                                                   'api_key'    => $mail_subscription_user_key,
                                                    ));

if (!empty($group_query['is_error'])) {
  civiproxy_http_error($group_query['error_message'], 500);
} else {
  $groups = $group_query['values'];
}
error_log(print_r($groups,1));
?>

<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <title>CiviProxy Version <?php echo $civiproxy_version;?></title>
  <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }

    .container {
        position: relative;
        width: 100%;
    }

    .center {
      margin-left: auto;
      margin-right: auto;
      width: 970px;
    }

    p {
      font-family: "Open Sans", sans-serif;
      font-size: 160%;
    }

    #info {
      padding-top: 20px;
      vertical-align: top;
      text-align: center;
      width: 462px;
    }
    
  </style>
 </head>
 <body>
  <div id="container">
    <div id="info" class="center">
      <a href="https://www.systopia.de/"><?php echo $civiproxy_logo;?></a>
      <p id="version">Subscribe to Newsletters</p>
    </div>
    <div id="content" class="center">
<?php
/*********************************************
 **         main processing routine         **
 ********************************************/
$parameter_errors = array();
if (!empty($_REQUEST['email'])) {
  // get parameters
  $email = $_REQUEST['email'];
  $group_ids = array();
  foreach ($_REQUEST as $key => $value) {
    error_log(substr($key, 0, 6));
    if (substr($key, 0, 6) == 'group_') {
      $group_ids[] = $value;
    }
  }

  // TODO: verify email is valid, otherwise set $parameter_errors['email']
  // TODO: verify at least one group is selected, otherwise set $parameter_errors['group_id']
}

if (empty($_REQUEST['email']) || !empty($parameter_errors)) {
  // TODO: if 

  print "
  <form id='subscribe' method='POST'>
    <label for='email'>Your email Address:</label>
    <input type='text' name='email'></input>
    <h3>Select the newsletter you would like to subscribe to:</h3>
    <ul>
    ";
  foreach ($groups as $group_id => $group) {
    print "
      <li>
        <input type='checkbox' name='group_{$group_id}' value='{$group_id}'>{$group['name']}</input>
        <p>{$group['description']}</p>
      </li>";
  }
  print "
    </ul>
  </form>";

} else {


  print_r($_REQUEST);

}

?>
    </div>
  </div>
 </body>
</html>
