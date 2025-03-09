# Cloudflare DNS Updater

A library for updating DNS records via the Cloudflare API.

## Installation

Install the package via Composer:

```
composer require fcosantos/cloudflare-dns
````

## Usage

````
<?php
require 'vendor/autoload.php';

use fcosantos\CloudflareDns\CloudflareDNSUpdater;

// Instantiate the updater with your Cloudflare credentials and base domain
$updater = new CloudflareDNSUpdater(
    'your_zone_id', 
    'your_email@example.com', 
    'your_api_key', 
    'example.com'
);

// Update a specific DNS record for "subdomain.example.com" (default type "A")
$updater->updateDns('subdomain', 'record_id_for_subdomain', 120, true);

// Update a DNS record of type CNAME for "www.example.com"
$updater->updateDns('www', 'record_id_for_www', 120, true, 'CNAME');
````
