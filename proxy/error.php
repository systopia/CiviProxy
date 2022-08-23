<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: B. Endres (endres -at- systopia.de)             |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

require_once "proxy.php";
?>

<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <title>CiviProxy Error</title>
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }

    .container {
        position: relative;
        width: 100%;
    }

    .center-small {
      margin-left: auto;
      margin-right: auto;
      width: 300px;
    }

    .center {
      margin-left: auto;
      margin-right: auto;
      width: 970px;
    }

    body {
      font-family: "Open Sans", sans-serif;
    }

    #info p {
      font-size: 160%;
    }

    #info {
      padding-top: 20px;
      vertical-align: top;
      text-align: center;
      width: 462px;
    }

    .bs-callout {
      padding: 20px;
      margin: 20px 0;
      border: 1px solid #eee;
      border-left-width: 5px;
      border-radius: 3px;
    }
    .bs-callout h4 {
        margin-top: 0;
        margin-bottom: 5px;
    }
    .bs-callout p:last-child {
        margin-bottom: 0;
    }
    .bs-callout code {
        border-radius: 3px;
    }
    .bs-callout+.bs-callout {
        margin-top: -5px;
    }
    .bs-callout-default {
        border-left-color: #777;
    }
    .bs-callout-default h4 {
        color: #777;
    }
    .bs-callout-primary {
        border-left-color: #428bca;
    }
    .bs-callout-primary h4 {
        color: #428bca;
    }
    .bs-callout-success {
        border-left-color: #5cb85c;
    }
    .bs-callout-success h4 {
        color: #5cb85c;
    }
    .bs-callout-danger {
        border-left-color: #d9534f;
    }
    .bs-callout-danger h4 {
        color: #d9534f;
    }
    .bs-callout-warning {
        border-left-color: #f0ad4e;
    }
    .bs-callout-warning h4 {
        color: #f0ad4e;
    }
    .bs-callout-info {
        border-left-color: #5bc0de;
    }
    .bs-callout-info h4 {
        color: #5bc0de;
    }
  </style>
 </head>
 <body>
  <div id="container">
    <div id="info" class="center-small">
      <a href="https://www.systopia.de/"><?php echo $civiproxy_logo;?></a>
      <p id="version">CiviProxy Version <?php echo $civiproxy_version;?></p>      
    </div>
    <div id="error-container" class="center">
      <?php if(isset($civiproxy_error_message)):?>
      <div class="bs-callout bs-callout-danger">
        <h4>An error has occurred while processing your request:</h4>
        <?php echo($civiproxy_error_message); ?>
      </div>
      <?php endif;?>
    </div>
  </div>
 </body>
</html>
