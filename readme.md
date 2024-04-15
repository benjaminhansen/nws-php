# PHP Library for the Nationanl Weather Service REST API

Provides an intuitive interface to use the NWS REST API in any PHP application.

## Installation
```composer require benjaminhansen/nws-php```

## Usage
Setup the API

```
use NWS\Api;


$app_domain = "website/url";
$app_contact = "email/phone";
$api = new Api($app_domain, $app_contact);


$timezone = "America/Chicago";
$api->setTimezone($timezone);


$lat = 32.7767;
$lon = -96.7970;
$location = $api->getLocation($lat, $lon);


$county = $location->county();
echo $county->name();
echo $county->id();
echo $county->state()->name();
echo $county->state()->abbreviation();
var_dump($county->timezone());
var_dump($county->timezones());


$alerts = $location->activeAlerts()->get();
foreach($alerts as $alert) {
    // do something with each alert
}


$observations = $location->latestObservations();
$raw_data = $observations->raw();
var_dump($raw_data);

echo $observations->temperature();
echo $observations()->dewpoint();
```
