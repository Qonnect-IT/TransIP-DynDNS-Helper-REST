# TransIP-DynDNS-Helper-REST
A TransIP Dynamic DNS helper

```
This is a remake of the previous TransIP DynDNS Helper which used the old API. The old SOAP Api will be terminated early 2022. This version of the DynDNS helper uses REST, like the "rest" of the world does ;).
```

This project can be used to automaticly update DNS Subdomain records via the TransIP API by utilizing a Cronjob on a remote device to "call in" the external IP address for that device.
Optional, Pushover can be used to send a message if there was a change in IP Address.

## Before use ##
Retrieve your TransIP API credentials via the TransIP website. Then copy the config.inc.php.template to config.inc.php and fill the variables.

Composer should be ran to install the required dependencies. This can be done by running <em>composer install</em>
