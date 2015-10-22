# CiviProxy - Security proxy for CiviCRM

Does your CiviCRM host confidential data on you organsiation and your constituents? Couldn't this data be used for campaigning against you, scamming your donors or simply undermine your constituents' trust in you?

CiviCRM ist *not* an unsafe system, but exposing the whole server with all its components to the internet yields a lot of attack vectors - for targeted attacks and scripted mass-exploits alike.

CiviProxy aims at minimising this exposure.

## How does it work

The basic idea is to first put your CiviCRM server into a VPN. This makes the server virtually inaccessible to the "outside" internet, and your users or your whole office will have a VPN access. 

But what about your public web pages, donation pages, data exchange with other systems? This is where CiviProxy comes in: You get a small, secure extra server and give it access to both, the internet and your VPN. It will act as a safe gateway to selected features of your CiviCRM you would like to expose.

[IMAGE]


## What can it do?

Currently CiviProxy can expose the following CiviCRM functions

1. Serving ressources for newsletters and mailings
2. Caching those ressources, taking load off your CiviCRM server
3. Pass-through of tracking data on opening and click-through rates

[TODO]



## How to install

The best option to install this is a simply a managed webspace, that you can rent cheaply from the hoster of your choice. It should have its own IP address, but other than this there are virtually no restrictions. This usually comes with a lot of advantages:

 1. Since there are a multitude of clients like you on such a server, the level of security can be expected to be very high, and it should be managed and monitored by professionals 24/7.
 
 2. For the same reason, the bandwith and connectivity of such a server should be very high as well.

### Installation

### Requirements
1. 

## Why not an application firewall?


