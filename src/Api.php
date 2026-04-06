<?php

namespace BenjaminHansen\NWS;

use BenjaminHansen\NWS\Enums\CacheDriver;
use BenjaminHansen\NWS\Exceptions\ApiNotOkException;
use BenjaminHansen\NWS\Exceptions\CacheException;
use BenjaminHansen\NWS\Features\Alert;
use BenjaminHansen\NWS\Features\Alerts;
use BenjaminHansen\NWS\Features\ForecastOffice;
use BenjaminHansen\NWS\Features\ForecastZone;
use BenjaminHansen\NWS\Features\Glossary;
use BenjaminHansen\NWS\Features\ObservationStation;
use BenjaminHansen\NWS\Features\ObservationStations;
use BenjaminHansen\NWS\Features\Point;
use BenjaminHansen\NWS\Features\RadarStations;
use BenjaminHansen\NWS\Features\TextProduct;
use BenjaminHansen\NWS\Features\TextProducts;
use BenjaminHansen\NWS\Features\ZoneForecast;
use BenjaminHansen\NWS\Support\Carbon;
use DateInterval;
use DateTimeZone;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Collection;
use Phpfastcache\Helper\Psr16Adapter;

class Api
{
    private ?Psr16Adapter $cache = null;

    private CacheDriver|string $cache_driver = CacheDriver::Files;

    private int|DateInterval $cache_lifetime = 3600;

    private string $base_url = 'https://api.weather.gov';

    private string $user_agent;

    private HttpClient $client;

    private array $cache_exclusions = ['/alerts'];

    private array $acceptable_http_codes = [200];

    private DateTimeZone $timezone;

    public function __construct(string $domain, string $email, string|DateTimeZone|null $timezone = null)
    {
        $this->timezone($timezone ?? 'UTC'); // set the timezone that was provided, or default to a reasonable value
        $this->userAgent($domain, $email); // set our user agent for the API requests

        // build up our HTTP client for making requests to the API
        $this->client = new HttpClient([
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->userAgent(),
            ],
        ]);
    }

    /*
    **  get/set the user_agent for the API http client
    */
    private function userAgent(?string $domain = null, ?string $email = null): string|self
    {
        if ($domain && $email) {
            $this->user_agent = "({$domain}, {$email})";

            return $this;
        }

        return $this->user_agent;
    }

    /*
    **  get/set the cache_lifetime used for the API's caching layer, if you opted for it.
    */
    public function cacheLifetime(int|DateInterval|null $lifetime = null): string|self|DateInterval
    {
        if ($lifetime) {
            $this->cache_lifetime = $lifetime;

            return $this;
        }

        return $this->cache_lifetime;
    }

    /*
    **  get/set the cache_driver used for the caching layer, if you opted for it.
    */
    public function cacheDriver(?string $driver = null): string|self
    {
        if ($driver) {
            $this->cache_driver = $driver;

            return $this;
        }

        if ($this->cache_driver instanceof CacheDriver) {
            return $this->cache_driver->value;
        }

        return $this->cache_driver;
    }

    /*
    **  Opt-in for using the caching layer. This is STRONGLY recommended!
    */
    public function useCache(?int $lifetime = null, ?string $driver = null): self
    {
        if ($lifetime) {
            $this->cacheLifetime($lifetime);
        }

        if ($driver) {
            $this->cacheDriver($driver);
        }

        // build up our cache to store data locally for a period of time
        $this->cache = new Psr16Adapter($this->cacheDriver());
        $this->cache_lifetime = $this->cacheLifetime();

        return $this;
    }

    /*
    **  Opt-out of using the caching layer. This is NOT recommended for production usage!
    */
    public function noCache(): self
    {
        $this->cache = null;

        return $this;
    }

    /*
    **  get/set the base_url for all API requests made the NWS
    */
    public function baseUrl(?string $base_url = null): string|self
    {
        if ($base_url) {
            $this->base_url = $base_url;

            return $this;
        }

        return $this->base_url;
    }

    /*
    **  Clear the local cache
    */
    public function clearCache(): self
    {
        if (($this->cache && $this->cache->clear()) || ! $this->cache) {
            return $this;
        }

        throw new CacheException('Failed to clear the cache!');
    }

    /*
    **  get/set the timezone used to cast all datetime values returned from the API
    */
    public function timezone(string|DateTimeZone|null $timezone = null): DateTimeZone|self
    {
        if ($timezone) {
            if (is_string($timezone)) {
                // cast the timezone string to an object
                $timezone = new DateTimeZone($timezone);
            }

            $this->timezone = $timezone;

            return $this;
        }

        return $this->timezone;
    }

    /*
    **  The main meat and potatoes of this project. Each request URL must pass through this method and the results
    **  are either returned from the local cache or are retrieved from the NWS API endpoint to be returned directly
    **  or stashed in the cache first and then returned to the user.
    */
    public function get(string $url): object|bool|null
    {
        // the user did not opt to use the cache, so make a direct request to the URL endpoint
        // and bypass all the following cache-related code, returning early
        if (! $this->cache) {
            $http_request = $this->client->get($url);
            $http_response_code = $http_request->getStatusCode();

            if (! in_array($http_response_code, $this->acceptable_http_codes)) {
                // sometimes the NWS API likes to include URLs that are not actually valid, so those throw 404s
                // this accounts for those
                return false;
            }

            return json_decode($http_request->getBody()->getContents());
        }

        // slugify the request URL to use as the unique ID in our cache
        // thanks Laravel for the wonderful string helpers!!! :)
        $key = str_slug($url);

        // if there is a value in the cache for the given URL, return the cached data
        // returning early and bypassing the remaining code
        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        // request a fresh copy of the data from the API endpoint, and store the data in the cache
        $http_request = $this->client->get($url);
        $http_response_code = $http_request->getStatusCode();

        if (! in_array($http_response_code, $this->acceptable_http_codes)) {
            // sometimes the NWS API likes to include URLs that are not actually valid, so those throw 404s
            // this accounts for those
            return false;
        }

        $data = json_decode($http_request->getBody()->getContents());
        $expires_timestamp = $http_request->getHeader('Expires')[0] ?? null;
        if ($expires_timestamp) {
            // we have the expiration header from the API request so we can derive
            // a more specific cache lifetime for our own caching layer
            $expires = new Carbon($expires_timestamp);
            $expires->setTimezoneIfNot($this->timezone());

            $now = new Carbon;
            $now->setTimezoneIfNot($this->timezone());

            $diff_interval = $now->diff($expires)->toDateInterval();
            $this->cacheLifetime($diff_interval);
        }

        // if the URL is not in our cache exclusion array, we should cache it
        if (! stripos_array($url, $this->cache_exclusions)) {
            $this->cache->set($key, $data, $this->cacheLifetime());
        }

        return $data;
    }

    /*
    **  Return the status of the NWS API endpoint
    */
    public function status(): string
    {
        $http_request = $this->client->get($this->baseUrl());
        $http_response_code = $http_request->getStatusCode();

        return $http_response_code === 200 ? 'OK' : 'NOT OK';
    }

    /*
    **  Check if the API status is OK and return a boolean
    */
    public function ok(): bool
    {
        return strtolower($this->status()) === 'ok';
    }

    /*
    **  Make sure that the API status is OK, otherwise bail out
    */
    public function assertOk(?string $message = null): self
    {
        if (! $this->ok()) {
            throw new ApiNotOkException($message ?? "NWS API is not OK: {$this->status()}");
        }

        return $this;
    }

    /*
    **  Get a specific lat/lon point and its corresponding weather data
    */
    public function point(float $lat, float $lon): Point
    {
        $url = "{$this->baseUrl()}/points/{$lat},{$lon}";

        return new Point($this->get($url), $this);
    }

    /*
    **  Alias for point()
    */
    public function getLocation(float $lat, float $lon): Point
    {
        return $this->point($lat, $lon);
    }

    /*
    **  Alias for baseUrl()
    */
    public function getBaseUrl(): string
    {
        return $this->baseUrl();
    }

    /*
    **  Get a specific observation station and its corresponding weather data
    */
    public function observationStation(string $observation_station): ObservationStation
    {
        $observation_station = strtoupper($observation_station);
        $url = "{$this->baseUrl()}/stations/{$observation_station}";

        return new ObservationStation($this->get($url), $this);
    }

    /*
    **  Get ALL observation stations and their corresponding weather data
    */
    public function observationStations(): ObservationStations
    {
        $url = "{$this->baseUrl()}/stations";

        return new ObservationStations($this->get($url), $this);
    }

    /*
    **  Get a specific Weather Forecast Office and its pertinent information
    */
    public function forecastOffice(string $forecast_office): ForecastOffice
    {
        $forecast_office = strtoupper($forecast_office);
        $url = "{$this->baseUrl()}/offices/{$forecast_office}";

        return new ForecastOffice($this->get($url), $this);
    }

    /*
    **  Get the NWS API's glossary for term lookups, etc.
    */
    public function glossary(): Glossary
    {
        $url = "{$this->baseUrl()}/glossary";

        return new Glossary($this->get($url), $this);
    }

    /*
    ** Get a specific Forecast Zone
    */
    public function zone(string $zone_id): ForecastZone
    {
        $zone_id = strtoupper($zone_id);
        $url = "{$this->baseUrl()}/zones?id={$zone_id}";

        return new ForecastZone($this->get($url)->features[0], $this);
    }

    /*
    ** Get all available Forecast Zones
    */
    public function zones(): Collection
    {
        $url = "{$this->baseUrl()}/zones";

        return collect($this->get($url)->features);
    }

    /*
    **  Get the zone forecast for a specific zone type and ID
    */
    public function zoneForecast(string $type, string $zone_id): ZoneForecast
    {
        $type = strtolower($type);
        $zone_id = strtoupper($zone_id);
        $url = "{$this->baseUrl()}/zones/{$type}/{$zone_id}/forecast";

        return new ZoneForecast($this->get($url), $this);
    }

    /*
    **  Get a specific alert by its ID
    */
    public function alert(string $id): Alert
    {
        $url = "{$this->baseUrl()}/alerts/{$id}";

        return new Alert($this->get($url), $this);
    }

    /*
    **  Query all alerts (active and inactive) with optional filters.
    **  Supported params: active, start, end, status, message_type, event, code, area, point, region, region_type, zone, urgency, severity, certainty, limit, cursor
    */
    public function alerts(array $params = []): Alerts
    {
        $url = "{$this->baseUrl()}/alerts";

        if (! empty($params)) {
            $url .= '?'.http_build_query($params);
        }

        return new Alerts($this->get($url), $this);
    }

    /*
    **  Get a count of currently active alerts broken down by category
    */
    public function alertsCount(): object
    {
        $url = "{$this->baseUrl()}/alerts/active/count";

        return $this->get($url);
    }

    /*
    **  Get active alerts for a US state or territory (e.g. "CA", "TX")
    */
    public function alertsForArea(string $area): Alerts
    {
        $area = strtoupper($area);
        $url = "{$this->baseUrl()}/alerts/active/area/{$area}";

        return new Alerts($this->get($url), $this);
    }

    /*
    **  Get active alerts for a marine region (e.g. "AL", "AT", "GL", "GM", "PA", "PI")
    */
    public function alertsForRegion(string $region): Alerts
    {
        $region = strtoupper($region);
        $url = "{$this->baseUrl()}/alerts/active/region/{$region}";

        return new Alerts($this->get($url), $this);
    }

    /*
    **  Get all recognized alert event type strings
    */
    public function alertTypes(): Collection
    {
        $url = "{$this->baseUrl()}/alerts/types";

        return collect($this->get($url)->eventTypes);
    }

    /*
    **  Get a specific text product by its ID
    */
    public function textProduct(string $id): TextProduct
    {
        $url = "{$this->baseUrl()}/products/{$id}";

        return new TextProduct($this->get($url), $this);
    }

    /*
    **  Query text products with optional filters.
    **  Supported params: location, start, end, office, wmoid, productCode, limit
    */
    public function textProducts(array $params = []): TextProducts
    {
        $url = "{$this->baseUrl()}/products";

        if (! empty($params)) {
            $url .= '?'.http_build_query($params);
        }

        return new TextProducts($this->get($url), $this);
    }

    /*
    **  Get all valid text product type codes (e.g. "AFD", "HWO")
    */
    public function textProductTypes(): Collection
    {
        $url = "{$this->baseUrl()}/products/types";

        return collect($this->get($url)->{'@graph'});
    }

    /*
    **  Get all valid text product issuing locations, keyed by location code
    */
    public function textProductLocations(): Collection
    {
        $url = "{$this->baseUrl()}/products/locations";

        return collect((array) $this->get($url)->locations);
    }

    /*
    **  Get all text products of a given type code (e.g. "AFD")
    */
    public function textProductsByType(string $type): TextProducts
    {
        $type = strtoupper($type);
        $url = "{$this->baseUrl()}/products/types/{$type}";

        return new TextProducts($this->get($url), $this);
    }

    /*
    **  Get text products of a given type issued by a specific office location
    */
    public function textProductsByTypeAndLocation(string $type, string $location): TextProducts
    {
        $type = strtoupper($type);
        $location = strtoupper($location);
        $url = "{$this->baseUrl()}/products/types/{$type}/locations/{$location}";

        return new TextProducts($this->get($url), $this);
    }

    /*
    **  Get the latest text products of a given type from a specific office location
    */
    public function latestTextProducts(string $type, string $location): TextProducts
    {
        $type = strtoupper($type);
        $location = strtoupper($location);
        $url = "{$this->baseUrl()}/products/types/{$type}/locations/{$location}/latest";

        return new TextProducts($this->get($url), $this);
    }

    /*
    **  Get all radar stations
    */
    public function radarStations(): RadarStations
    {
        $url = "{$this->baseUrl()}/radar/stations";

        return new RadarStations($this->get($url), $this);
    }
}
