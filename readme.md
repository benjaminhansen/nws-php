# PHP Library for the National Weather Service REST API

Provides an intuitive interface to use the NWS REST API in any PHP application.

## Installation
```
composer require benjaminhansen/nws-php
```

## Usage
Notes
* Requires PHP 8.2 or greater
* All requests can be cached locally to reduce outbound requests to the API endpoint
* All datetime fields are automatically returned as a <code>Carbon</code> object
* Any timezone fields are automatically returned as a PHP <code>DateTimeZone</code> object
* A <code>raw()</code> method is available on most calls to print out the raw API data

### Example

```php
<?php

require 'vendor/autoload.php'; // if your app it not already including this

use BenjaminHansen\NWS\Api;

/*
**  We must provide valid contact information so the National Weather Service
**  can properly identify our application on their end
*/
$app_domain = "website/url";
$app_contact = "email/phone";
$api = new Api($app_domain, $app_contact);


/*
** The cache is opt-in. To cache results locally make sure to enable the cache.
** Sometimes, you may not want to use the cache at all, so this allows it to be toggled on/off.
*/
$api->useCache();
// $api->useCache(lifetime: 3600, driver: 'Files'); // override the default cache lifetime and/or driver, if necessary


/*
** You can make sure that the API's status returns OK before allowing any
** requests to be made. An exception will be thrown if the API status is not OK.
*/
$api->assertOk();
// $api->assertOk(message: 'custom exception message can be placed here');


/*
**  We can set a timezone for the entire API callstack.
**  All datetime objects will be converted to this timestamp.
*/
$timezone = "America/Chicago";
$api->timezone($timezone);


/*
**  We can provide lat/lon coordinates for a specific location
**  Dallas, TX for example
*/
$lat = 32.7767;
$lon = -96.7970;
$location = $api->point(lat: $lat, lon: $lon);


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

echo $observations->temperature(show_units: true);
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


/*
** We can also get a specific forecast office
** Little Rock, Arkansas for example
*/
$office_id = "LZK";
$forecast_office = $api->forecastOffice($office_id);
echo $forecast_office->name();
echo $forecast_office->phone();
echo $forecast_office->email();
echo $forecast_office->addresss();
var_dump($forecast_office->counties());
var_dump($forecast_office->forecastZones());
var_dump($forecast_office->observationStations());
var_dump($forecast_office->fireZones());
var_dump($forecast_office->activeAlerts()->get());


/*
** We can also get a specific observation station
** Little Rock, Arkansas Airport KLIT for example
*/
$station_id = "KLIT";
$observation_station = $api->observationStation($station_id);
echo $observation_station->name();
echo $observation_station->id();
var_dump($observation_station->timezone());
var_dump($observation_station->county());
var_dump($observation_station->latestObservations());
var_dump($observation_station->activeAlerts()->get());
```
