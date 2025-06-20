# What to do if an outside application wants to communicate with CiviProxy
In most cases when an outside application (for example the public website, or maybe even a  center webservice) wants to access CiviProxy to be able to retreive data from CiviCRM or send data to CiviCRM they will want to use the API. 

In that case you should provide them with the Site key and an API key they can use. We recommend you do NOT use the CiviCRM Site Key and API Key.

!!! tip
    If you do not know how to do this check [Configuring CiviProxy](configuration.md).
 
They will have to use your CiviProxy URL in their REST request, and you will need to provide them with that URL. In my local example the call to the CiviProxy server would be:
```
http://localhost/proxy/rest.php?entity=Contact&action=getsingle&key=erikhtestkey&version=3&json=1&api_key=testerikhapikey&email=pipotest@example.org
```
## Twingle
For general information how to connect Twingle to CiviCRM, please refer to the [general Twingle documentation (german)](https://support.twingle.de/faq/de-de/9-anbindung-externer-systeme/46-wie-kann-ich-civicrm-mit-twingle-nutzen).

Between Twingle and CiviProxy, use the [legacyrest flow](https://docs.civicrm.org/dev/en/latest/framework/authx/#flows):
* set `$authx_external_flow = ['legacyrest'];` in CiviProxy's `config.php`
* add `&version=3` to the URL endpoint in Twingle's endpoint URL field, for example `https://civiproxy.example.com/rest.php?version=3`

Twingle sends the parameters packed into a json parameter, for that reason you have to
* set `$rest_evaluate_json_parameter = TRUE;` in CiviProxy's `config.php`

For all data synchronisation from Twingle to CiviCRM, including Twingle shop orders, only the CiviCRM-API-Entity `TwingleDonation` and there only the action `submit` is used by Twingle. You may want to replace `'all'` in the following example by the IPs used by Twingle, if you don't already implement this restriction in your apache2 config:
```
$rest_allowed_actions = [
  'all' => [
    'TwingleDonation' => [
      'submit' => [
        '*' => 'string',
      ],
    ],
  ],
];
```
