# Future enhancements for CiviProxy
The one future enhancement we have identified is the ability to whitelist API requests for a certain IP address. 

Right now if we whitelist for example the `contact getsingle` API with the parameters `email`, all IP addresses accessing CiviProxy would be able to use that API request.

The desired future feature would be to be able to whitelist an API call for a specific IP address. 
For example the `Contact getsingle` with the parameter `email` is only whitelisted for IP address 123.45.678.1, and the `Contact getsingle` with the parameters `first_name` and `last_name` are whitelisted for IP address 123.45.678.2.

The enhancement is registered [here](https://github.com/systopia/CiviProxy/issues/12)

!!! tip
    If you want to report bugs or suggest future enhancements please do so on the [GitHub repository](https://github.com/systopia/CiviProxy/issues).