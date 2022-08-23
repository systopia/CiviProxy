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

// see if mailing subscribe feature is enabled
if (empty($mail_subscription_user_key)) civiproxy_http_error("Feature disabled", 405);

// basic check
civiproxy_security_check('mail-subscribe');

// LOAD VISIBLE GROUPS
$group_query = civicrm_api3('Group', 'get', 
                          array( 'visibility' => 'Public Pages',
                                 'is_hidden'  => 0,
                                 'is_active'  => 1,
                                 'api_key'    => $mail_subscription_user_key,
                                ));
if (!empty($group_query['is_error'])) {
  civiproxy_http_error($group_query['error_message'], 500);
} else {
  $groups = $group_query['values'];
  if (empty($groups)) {
    civiproxy_http_error("No newsletter groups found!", 500);
  }
}

// VERIFY / CHECK PARAMETERS
$parameter_errors = array();
if (!empty($_REQUEST['email'])) {
  // get parameters
  $email = $_REQUEST['email'];
  if (!preg_match("#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$#i", $email)) civiproxy_http_error("'$email' is not a valid email address.", 500);

  if (empty($_REQUEST['group_id'])) civiproxy_http_error("No newsletter group selected!", 500);
  $group_id = $_REQUEST['group_id'];

  // ALL FINE. SUBSCRIBE USER!
  // first, get/create the contact
  $contact_query = civicrm_api3('Contact', 'create', 
                                array( 'email'        => $email,
                                       'contact_type' => 'Individual',
                                       'dupe_check'   => 1,
                                       'api_key'      => $mail_subscription_user_key,
                                ));
  if (!empty($contact_query['is_error'])) {
    // an error occured during contact generation/identification
    if ($contact_query['error_code'] == 'duplicate') {
      // there have been multiple duplicates found
      $contact_id = $contact_query['ids'][0];
    } else {
      civiproxy_http_error($contact_query['error_message'], 500);
    }
  } else {
    $contact_id = $contact_query['id'];
  }

  // then: subscribe
  $subscribe_query = civicrm_api3('MailingEventSubscribe', 'create', 
                                array( 'email'        => $email,
                                       'contact_id'   => $contact_id,
                                       'group_id'     => $group_id,
                                       'api_key'      => $mail_subscription_user_key,
                                ));
  if (!empty($subscribe_query['is_error'])) {
    // an error occured during the actual subscription
    civiproxy_http_error($subscribe_query['error_message'], 500);
  }

}
?>


<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <title>CiviProxy Version <?php echo $civiproxy_version;?></title>
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
if (empty($_REQUEST['email']) || !empty($parameter_errors)) {
  // TODO: show error if no group found
  // TODO: show error if email not valid
  print "
  <form id='subscribe' method='POST'>
    <label for='email'>Your email Address:</label>
    <input type='text' name='email'></input>
    <h3>Select the newsletter you would like to subscribe to:</h3>
    <select name='group_id'>
    ";
  foreach ($groups as $group_id => $group) {
    print "<option value='{$group_id}'>{$group['title']}</input>";
  }
  print "
    </select>
    <input type='submit' value='Subscribe' />
  </form>";
} else {
  // the subscription was complete
  print "<p>Thank you. You will receive an email asking you to confirm your subscription.</p>";
}
?>
    </div>
  </div>
 </body>
</html>
