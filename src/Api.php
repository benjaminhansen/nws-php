<?php

namespace BenjaminHansen\NWS;

use Phpfastcache\Helper\Psr16Adapter;
use GuzzleHttp\Client as HttpClient;
use BenjaminHansen\NWS\Features\Point;
use BenjaminHansen\NWS\Features\ForecastOffice;
use BenjaminHansen\NWS\Features\ObservationStation;
use BenjaminHansen\NWS\Exceptions\ApiNotOkException;
use BenjaminHansen\NWS\Exceptions\CacheException;
use BenjaminHansen\NWS\Features\ObservationStations;
use BenjaminHansen\NWS\Features\Glossary;
use BenjaminHansen\NWS\Support\Carbon;
use DateInterval;
use DateTimeZone;

class Api
{
    private Psr16Adapter|null $cache = null;
    private string $base_url = "https://api.weather.gov";
    private string $user_agent;
    private HttpClient $client;
    private int|DateInterval $cache_lifetime = 3600;
    private array $cache_exclusions = ['/alerts'];
    private array $acceptable_http_codes = [200];
    private string $timezone = 'UTC';
    private string $cache_driver = 'Files';

    public function __construct(string $domain, string $email)
    {
        // set our user agent for the API requests
        $this->userAgent($domain, $email);

        // build up our HTTP client for making requests to the API
        $this->client = new HttpClient([
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->userAgent()
            ]
        ]);
    }

    private function userAgent(string $domain = null, string $email = null): string|self
    {
        if($domain && $email) {
            $this->user_agent = "({$domain}, {$email})";
            return $this;
        } else {
            return $this->user_agent;
        }
    }

    public function cacheLifetime(int|DateInterval $lifetime = null): string|self|DateInterval
    {
        if($lifetime) {
            $this->cache_lifetime = $lifetime;
            return $this;
        } else {
            return $this->cache_lifetime;
        }
    }

    public function cacheDriver(string $driver = null): string|self
    {
        if($driver) {
            $this->cache_driver = $driver;
            return $this;
        } else {
            return $this->cache_driver;
        }
    }

    public function useCache(int $lifetime = null, string $driver = null): self
    {
        if(!is_null($lifetime)) {
            $this->cacheLifetime($lifetime);
        }

        if(!is_null($driver)) {
            $this->cacheDriver($driver);
        }

        // build up our cache to store data locally for a period of time
        $this->cache = new Psr16Adapter($this->cacheDriver());
        $this->cache_lifetime = $this->cacheLifetime();
        return $this;
    }

    public function baseUrl(string $base_url = null): string|self
    {
        if($base_url) {
            $this->base_url = $base_url;
            return $this;
        } else {
            return $this->base_url;
        }
    }

    public function clearCache(): self
    {
        if($this->cache->clear()) {
            return $this;
        }

        throw new CacheException("Failed to clear the cache!");
    }

    public function timezone(string $timezone = null): DateTimeZone|self
    {
        if($timezone) {
            $this->timezone = $timezone;
            return $this;
        } else {
            return new DateTimeZone($this->timezone);
        }
    }

    public function get($url): object|bool
    {
        // the user did not opt to use the cache
        // so make a direct request to the URL
        if(!$this->cache) {
            $http_request = $this->client->get($url);
            $http_response_code = $http_request->getStatusCode();

            if(!in_array($http_response_code, $this->acceptable_http_codes)) {
                // sometimes the NWS API likes to include URLs that are not actually valid, so those throw 404s
                // this accounts for those
                return false;
            }

            return json_decode($http_request->getBody()->getContents());
        }

        // key-ify the request URL to use as the unique ID in our cache
        $key = str_slug($url);

        // if there is a value in the cache for the given URL, return the cached data
        if($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        // request a fresh copy of the data from the API endpoint, and store the data in the cache
        $http_request = $this->client->get($url);
        $http_response_code = $http_request->getStatusCode();

        if(!in_array($http_response_code, $this->acceptable_http_codes)) {
            // sometimes the NWS API likes to include URLs that are not actually valid, so those throw 404s
            // this accounts for those
            return false;
        }

        $data = json_decode($http_request->getBody()->getContents());
        $expires_timestamp = $http_request->getHeader('Expires')[0] ?? null;
        if($expires_timestamp && $this->cacheLifetime() !== 3600) {
            // we have the expiration header from the API request and a custom lifetime
            // has not been configured so we can derive a more specific cache lifetime
            // for our own caching layer

            $expires = new Carbon($expires_timestamp);
            $expires->setTimezoneIfNot($this->timezone());

            $now = new Carbon();
            $now->setTimezoneIfNot($this->timezone());

            $diff_interval = $expires->diff($now)->toDateInterval();
            $this->cacheLifetime($diff_interval);
        }

        // if the URL is not in our cache exclusion array, we should cache it
        if(!stripos_array($url, $this->cache_exclusions)) {
            $this->cache->set($key, $data, $this->cache_lifetime);
        }

        return $data;
    }

    public function status(): string
    {
        return trim($this->get($this->base_url)->status);
    }

    public function ok(): bool
    {
        return strtolower($this->status()) === "ok";
    }

    public function assertOk(string $message = null): self
    {
        if(!$this->ok()) {
            if($message) {
                throw new ApiNotOkException($message);
            }

            $status = $this->status();
            throw new ApiNotOkException("NWS API is not OK: {$status}");
        }

        return $this;
    }

    public function point(float $lat, float $lon): Point
    {
        $url = "{$this->baseUrl()}/points/{$lat},{$lon}";
        return new Point($this->get($url), $this);
    }

    public function observationStation(string $observation_station): ObservationStation
    {
        $observation_station = strtoupper($observation_station);
        $url = "{$this->baseUrl()}/stations/{$observation_station}";
        return new ObservationStation($this->get($url), $this);
    }

    public function observationStations(): ObservationStations
    {
        $url = "{$this->baseUrl()}/stations";
        return new ObservationStations($this->get($url), $this);
    }

    public function forecastOffice(string $forecast_office): ForecastOffice
    {
        $forecast_office = strtoupper($forecast_office);
        $url = "{$this->baseUrl()}/offices/{$forecast_office}";
        return new ForecastOffice($this->get($url), $this);
    }

    public function glossary(): Glossary
    {
        $url = "{$this->baseUrl()}/glossary";
        return new Glossary($this->get($url), $this);
    }
}
