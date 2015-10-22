# CiviProxy - Security proxy for CiviCRM

Does your CiviCRM server contain confidential data on your organsiation and your constituents? Couldn't this data be used against you, to scam your donors or simply undermine your constituents' trust in you?

CiviCRM ist *not* an unsafe system, but exposing the whole server with all its components to the internet yields a lot of attack vectors - for targeted attacks and scripted mass-exploits alike.

CiviProxy aims at minimising this exposure.

## How does it work

The basic idea is to first put your CiviCRM server into a VPN. This makes the server virtually inaccessible from the internet, and your users or your whole office will access it via a secure connection.

But what about your public web pages, donation pages, data exchange with other systems? This is where CiviProxy comes in: You get a small, secure, extra server and give it access to both, the internet and your VPN. It will act as a safe gateway for selected features of your CiviCRM that you would like to expose.

![Network Architecture](https://raw.githubusercontent.com/systopia/CiviProxy/master/docs/network.jpg)


## What can it do?

Currently CiviProxy can expose/relay the following CiviCRM functions

1. Serve resources for newsletters and mailings
2. Cache those ressources, taking load off your CiviCRM server
3. Pass-through of tracking data on opening and click-through rates
5. Sign-on and off of your newsletter (Webpage templates)
6. Relay of whitelisted REST API calls for data exchange with other systems
7. Perform input sanitation and parameter whitelisting for the REST API calls

DISCLAIMER: This software has not (yet) been audited for security.


## How to install

The best option to install CiviProxy is a simple managed webspace, that you can rent cheaply from the hoster of your choice. It should have its own IP address, but other than that there are virtually no restrictions. This approach comes with a lot of advantages:

 1. Since there are a multitude of clients like you on such a server, the level of security can be expected to be very high, and it should be managed and monitored by professionals 24/7.
 
 2. For the same reason, the bandwith and connectivity of such a server should be very high as well.
 

### Installation

The installation is as simple as it gets:

 1. Download the ``proxy`` folder of this git repository
 2. Edit the ``config.php`` file. It should be fairly self-explanatory.
 3. Upload the contents of the ``proxy`` folder to your webspace.
 4. If you want to send newsletters from behind the firewall, you will also have to install the ``de.systopia.civiproxy`` extension in your CiviCRM. This extension will automatically modify the URLs of links and resources in your outgoing newsletters, so they point to your proxy instead. You will have to configure this extension via the settings page, which is listed on the CiviCRM administration console.
 5. Done. All you have to do now is put your CiviCRM in a VPN and allow traffic only from the IP of your CiviProxy.

### Requirements

There shouldn't be any requirements that any web hoster wouldn't comply with, but here they are:

 1. PHP 5.3+
 2. Read/write permissions on your webspace
 3. Reasonable amount of protection, i.e. only authorised users (you) can upload/download the files
 4. Ideally with it's own IP address (makes configuring the VPN easier)
 

## Why not an application firewall?

The traditional approach to this problem would be an application firewall / reverse proxy setup. However, CiviCRM can have very complex interactions with other systems (e.g. via the API), and a malicious request can sometimes only be detected by understanding the meaning of the individual parameters.

Creating firewall rules for this level of detail is very complex and is very hard to maintain. 

For this reason we wanted to take another approach and build a simple "bridgehead" system that *understands* CiviCRM, thus making its configuration and maintenance as easy as possible.
