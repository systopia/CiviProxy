# Installation
CiviProxy has to run on its own server (with its own IP address) for maximum effect.

The best option for a server on which you can install CiviProxy is a simple managed webspace, that you can rent cheaply from the hoster of your choice. It should have its own IP address, but other than that there are virtually no restrictions. This approach comes with a lot of advantages:

* Since there are a multitude of clients like you on such a server, the level of security can be expected to be very high, and it should be managed and monitored by professionals 24/7.
* For the same reason, the bandwith and connectivity of such a server should be very high as well.
* The price should not have to be huge.

!!! note
    Obviously there is nothing stopping you from installing CiviProxy on a server you manage yourself, but that then automatically means you have to ensure you maintain a high level of security and update the stuff regularly!

Installing CiviProxy should be pretty straightforward:

* Download the GitHub repository from [https://github.com/systopia/CiviProxy](https://github.com/systopia/CiviProxy).
* In the repository, there are actually two relevant parts:

    * A CiviCRM extension called **de.systopia.civiproxy** that you need to install on your CiviCRM site.
    * A **proxy** folder with the scripts that you need to install on your CiviProxy server.

## Installing the CiviCRM extension on your target CiviCRM
Follow the [general instructions to install extensions]([url](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#how-to-install-an-extension)) in CiviCRM. (Again, the extension consists _only_ of the directory called de.systopia.civiproxy!)

See [Configuring CiviProxy](configuration.md) for futher settings you need to make.

## Installing the Proxy scripts your your CiviProxy server

1. Copy the **proxy** folder into the webspace you want to use for CiviProxy.
2. Create a `config.php` file using `config.dist.php` as a template.

See [Configuring CiviProxy](configuration.md) for details on what you need to include in the `config.php` file.
