# Technical Requirements

## Proxy Server (Public-Facing)

- Simple managed webspace with dedicated IP address
- PHP 7.4+ (PHP 8.0+ recommended)
- PHP cURL extension enabled
- HTTPS/SSL support
- Basic web server (Apache/Nginx)

## CiviCRM Server (Private/Protected)

- Existing CiviCRM 5.0+ installation
- Located in VPN or private network
- Firewall allowing connections only from proxy server IP
- API access enabled (depending on your use case)

## Network Architecture

- **Proxy Server**: Internet-accessible, minimal attack surface
- **CiviCRM Server**: Protected network, accessible only via proxy server IP and internal networks
- **Communication**: HTTPS between proxy and CiviCRM server
