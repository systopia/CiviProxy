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

$source = '
<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <title>CiviProxy Version ' . $civiproxy_version . '</title>
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
      width: 300px;
    }

    p {
      font-family: "Open Sans", sans-serif;
      font-size: 160%;
    }

    #info {
      padding-top: 20px;
    }

    #version {
      padding-left: 2px;
    }
  </style>
 </head>
 <body>
  <div id="container">
    <div id="info" class="center">
      <a href="https://www.systopia.de/"><img src="static/images/systopia_logo.png" alt="SYSTOPIA Organisationsberatung"></img></a>
      <p id="version">CiviProxy Version ' . $civiproxy_version . '</p>
    </div>
  </div>
 </body>
</html>
';

echo($source);
