# What to do if an outside application wants to communicate with CiviProxy
In most cases when an outside application (for example the public website, or maybe even a  center webservice) wants to access CiviProxy to be able to retreive data from CiviCRM or send data to CiviCRM they will want to use the API. 

In that case you should provide them with the Site key and an API key they can use. They will have to use your CiviProxy URL in their REST request, and you will need to provide them with that URL.