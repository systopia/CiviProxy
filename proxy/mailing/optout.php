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

// see if mailing opt-out feature is enabled
if (empty($mail_subscription_user_key)) {
  civiproxy_http_error("Feature disabled", 405);
}

// basic check
civiproxy_security_check('mail-optout');

// basic restraints
$valid_parameters = [
  'jid' => 'int',
  'qid' => 'int',
  'h' => 'string',
];
$parameters = civiproxy_get_parameters($valid_parameters);

// check if parameters specified
if (empty($parameters['jid'])) {
  civiproxy_http_error("Missing/invalid parameter 'jid'.");
}
if (empty($parameters['qid'])) {
  civiproxy_http_error("Missing/invalid parameter 'qid'.");
}
if (empty($parameters['h'])) {
  civiproxy_http_error("Missing/invalid parameter 'h'.");
}

// PERFORM OPT-OUT ON POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // org_unsubscribe: opt out of all mailings (like CiviCRM's own opt-out page)
  $optout_query = civicrm_api3('MailingEventUnsubscribe', 'create',
    [
      'job_id' => $parameters['jid'],
      'event_queue_id' => $parameters['qid'],
      'hash' => $parameters['h'],
      'org_unsubscribe' => 1,
      'api_key' => $mail_subscription_user_key,
    ]);
  if (!is_array($optout_query)) {
    civiproxy_http_error("Invalid response from CiviCRM.", 502);
  }
  elseif (!empty($optout_query['is_error'])) {
    civiproxy_http_error($optout_query['error_message'], 500);
  }
  else {
    $success = TRUE;
  }
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>CiviProxy</title>
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }

    .btn {
      padding: 10px;
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
    <a href="https://www.systopia.de/"><?php echo $civiproxy_logo; ?></a>
  </div>
  <div id="content" class="center">
    <?php if (!empty($success)): ?>
      <p>Thank you. You have been opted out of all mailings.</p>
    <?php else: ?>
      <p>Please confirm that you want to opt out of all mailings.</p>
      <form method="post" action="">
        <input type="hidden" name="jid"
               value="<?= htmlspecialchars($parameters['jid']) ?>">
        <input type="hidden" name="qid"
               value="<?= htmlspecialchars($parameters['qid']) ?>">
        <input type="hidden" name="h"
               value="<?= htmlspecialchars($parameters['h']) ?>">
        <button type="submit" class="btn">Yes, opt out</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
