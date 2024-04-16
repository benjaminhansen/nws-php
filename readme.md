# PHP Library for the Nationanl Weather Service REST API

Provides an intuitive interface to use the NWS REST API in any PHP application.

## Installation
```
composer require benjaminhansen/nws-php
```

## Usage
Notes
* Requires PHP 8.1 or greater
* All datetime fields are automatically returned as a <code>Carbon</code> object
* Any timezone fields are automatically returned as a PHP <code>DateTimeZone</code> object
* A <code>raw()</code> method is available on most calls to print out the raw API data

### Example

```php
<?php

require 'vendor/autoload.php'; // if your app it not already including this

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


/*
**  Get the location's county information
*/
$county = $location->county();
echo $county->name();
echo $county->id();
echo $county->state()->name();
echo $county->state()->abbreviation();
var_dump($county->timezone());
var_dump($county->timezones());


/*
** Get information about the location
*/
echo $location->radarStationId();
echo $location->city();
var_dump($location->state());


/*
** Get any active alerts for the location and loop through them
*/
$alerts = $location->activeAlerts()->get();
foreach($alerts as $alert) {
    var_dump($alert);
}


/*
** Get current observations from the nearest observation station to your location
*/
$observations = $location->latestObservations();
$raw_data = $observations->raw();
var_dump($raw_data);

echo $observations->temperature();
echo $observations->dewpoint();
// other methods are available for the other data points as well


/*
** Get the current forecast for the location
*/
$forecast = $location->forecast();
var_dump($forecast->raw());
var_dump($foreast->periods()->get());


/*
** Get the current hourly forecast for the location
*/
$hourly_forecast = $location->hourlyForecast();
var_dump($hourly_forecast->raw());
var_dump($hourly_forecast->periods()->get());
```

## Packages Used
* <code>guzzlehttp/guzzle</code> for all HTTP requests
* <code>nesbot/carbon</code> for date/time parsing and formatting
* <code>phpfastcache/phpfastcache</code> for local caching of API data to reduce direct calls to the API endpoint
