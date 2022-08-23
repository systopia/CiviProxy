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
civiproxy_security_check('mail-unsubscribe');

// basic restraints
$valid_parameters = array(    'jid'          => 'int',
                              'qid'          => 'int', 
                              'h'            => 'hex');
$parameters = civiproxy_get_parameters($valid_parameters);

// check if parameters specified
if (empty($parameters['jid'])) civiproxy_http_error("Missing/invalid parameter 'jid'.");
if (empty($parameters['qid'])) civiproxy_http_error("Missing/invalid parameter 'qid'.");
if (empty($parameters['h']))   civiproxy_http_error("Missing/invalid parameter 'h'.");

// PERFORM UNSUBSCRIBE
$group_query = civicrm_api3('MailingEventUnsubscribe', 'create', 
                          array( 'job_id'         => $parameters['jid'],
                                 'event_queue_id' => $parameters['qid'],
                                 'hash'           => $parameters['h'],
                                 'api_key'        => $mail_subscription_user_key,
                                ));
if (!empty($group_query['is_error'])) {
  civiproxy_http_error($group_query['error_message'], 500);
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
    </div>
    <div id="content" class="center">
      <p>Thank you. You have been successfully unsubscribed.</a>
    </div>
  </div>
 </body>
</html>
