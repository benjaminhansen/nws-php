# PHP Library for the Nationanl Weather Service REST API

Provides an intuitive interface to use the NWS REST API in any PHP application.

## Installation
```
composer require benjaminhansen/nws-php
```

## Usage
Notes
* All datetime fields are automatically returned as Carbon objects
* A <code>raw()</code> method is available on most calls to print out the raw API data

Setup the API

```
use NWS\Api;

/*
**  We must provide valid contact information so the National Weather Service
**  can properly identify our application on their end
*/
$app_domain = "website/url";
$app_contact = "email/phone";
$api = new Api($app_domain, $app_contact);

/*
**  We can set a timezone for the entire API callstack.
**  All datetime objects will be converted to this timestamp.
*/
$timezone = "America/Chicago";
$api->setTimezone($timezone);

/*
**  We can provide lat/lon coordinates for a specific location
**  Dallas, TX for example
*/
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
echo $observations->dewpoint();
```
