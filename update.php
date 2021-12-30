<?php

use Transip\Api\Library\TransipAPI;
use Transip\Api\Library\Entity\Domain\DnsEntry;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.inc.php');

// Default Variables
$ip_changed = false;
$remote_ip = getRemoteIP();

$remote_token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_ENCODED);

$api = new TransipAPI(
    API_LOGIN_NAME,
    API_PRIVATE_KEY,
    API_WHITELIST_TOKENS_ONLY
);

if(isset($_GET['token'])) {
  foreach(DNS_ARRAY as $token => $subdomain) {
    $currentEntry = '';

    if($remote_token === $token) {
      if(DEBUG == true) { echo "Received token is known in our DB"; }
      
      $domainRecords = $api->domainDns()->getByDomainName(DNS_DOMAIN);
      // Filter the "dynamic" record
      //
      foreach ( $domainRecords as $domainRecord ) {
        if($domainRecord->getName() == $subdomain) {
          // We have now found the correct dynamic record
          $currentEntry = $domainRecord->getContent();
          if($currentEntry == $remote_ip) {
            if(DEBUG == true) { echo "Record is unchanged"; }
            syslog(LOG_INFO, ucfirst(DNS_DOMAIN) . " DynDNS - " . $subdomain . " Record Unchanged at ". $remote_ip);
          } else {
            if(DEBUG == true) { echo "Record will be changed"; }
            syslog(LOG_INFO, ucfirst(DNS_DOMAIN) . " DynDNS - " . $subdomain . " Record not pointing to: " . $remote_ip);
            $ip_changed = true;

          }
          if($ip_changed) {
            try {
              $dnsEntry = new DnsEntry();
              $dnsEntry->setName($domainRecord->getName());
              $dnsEntry->setExpire('60');
              $dnsEntry->setType('A');
              $dnsEntry->setContent($remote_ip);

              $api->domainDns()->updateEntry(DNS_DOMAIN, $dnsEntry);

              if(DEBUG == true) { echo "Dynamic record updated succesfully"; }
              syslog(LOG_INFO, ucfirst(DNS_DOMAIN) . " DynDNS - " . $subdomain . " DNS Update succesfull");
            }
            catch(Exception $e) {
              syslog(LOG_INFO, ucfirst(DNS_DOMAIN) . " DynDNS - " . $subdomain . " DNS Update failed");
              syslog(LOG_INFO, ucfirst(DNS_DOMAIN) . " DynDNS - " . $e);
            }
          }
        }
      }
    } else {
      if(DEBUG == true) { echo "Received token is unknown in our DB"; }
    }
  }
} else {
  $external_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo '
<html>
  <head>
    <title>' . ucfirst(DNS_DOMAIN) . ' Dynamic DNS Helper</title>
  </head>
  <body>
    <H1>
      This page should not be called without proper client side configuration.  
    </H1>
    <br />
    Please configure your cronscript as follows;
    <pre>
crontab -e or vim /etc/cron.d/dyndnsupdater

*/5 * * * * curl -4 '. $external_url . '?token=TOKEN
    </pre>
    A token can be requested via ' . htmlSpecialChars(DNS_CONTACT) . '
    <br />
    <br />
    This cronscript will update your Dynamic DNS record every 5 minutes. It is advised to leave this configured at 5 minute interval to avoid stressing the DNS API.
  </body>
</html>';
}




function getRemoteIP() {
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    if($_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER["REMOTE_ADDR"]) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
    $ip = $_SERVER["REMOTE_ADDR"];
    }
  } else {
    $ip = $_SERVER["REMOTE_ADDR"];
  }

  return $ip;
}

