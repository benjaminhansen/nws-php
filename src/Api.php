<?php

namespace NWS;

use Phpfastcache\Helper\Psr16Adapter;
use GuzzleHttp\Client as HttpClient;
use NWS\Features\Point;
use NWS\Features\ForecastOffice;
use NWS\Features\ObservationStation;
use NWS\Exceptions\InvalidRequestException;
use NWS\Exceptions\ApiNotOkException;
use NWS\Exceptions\CacheException;
use DateTimeZone;

class Api
{
    protected Psr16Adapter|null $cache = null;
    private string $base_url = "https://api.weather.gov";
    private string $user_agent;
    private HttpClient $client;
    private int $cache_lifetime = 3600;
    private array $cache_exclusions = ['/alerts'];
    private array $acceptable_http_codes = [200];
    private string $timezone = 'UTC';
    private string $cache_driver = 'Files';

    public function __construct(string $domain, string $email)
    {
        // derive our user agent for the API requests
        $this->setUserAgent($domain, $email);

        // build up our HTTP client for making requests to the API
        $this->client = new HttpClient([
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->getUserAgent()
            ]
        ]);
    }

    public function getUserAgent(): string
    {
        return $this->user_agent;
    }

    public function setUserAgent(string $domain, string $email): self
    {
        $this->user_agent = "({$domain}, {$email})";
        return $this;
    }

    public function getCacheLifetime(): int
    {
        return $this->cache_lifetime;
    }

    public function setCacheLifetime(int $lifetime): self
    {
        $this->cache_lifetime = $lifetime;
        return $this;
    }

    public function getCacheDriver(): string
    {
        return $this->cache_driver;
    }

    public function setCacheDriver(string $driver): self
    {
        $this->cache_driver = $driver;
        return $this;
    }

    public function useCache(): self
    {
        // build up our cache to store data locally for a period of time
        $this->cache = new Psr16Adapter($this->getCacheDriver());
        $this->cache_lifetime = $this->getCacheLifetime();
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->base_url;
    }

    public function setBaseUrl(string $base_url): self
    {
        $this->base_url = $base_url;
        return $this;
    }

    public function clearCache(): self
    {
        if($this->cache->clear()) {
            return $this;
        }

        throw new CacheException("Failed to clear the cache!");
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getTimezone(): DateTimeZone
    {
        return new DateTimeZone($this->timezone);
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
        return strtolower($this->status()) == "ok";
    }

    public function ensureApiIsOk(): self
    {
        if(!$this->ok()) {
            $status = $this->status();
            throw new ApiNotOkException("NWS API is not OK: {$status}");
        }

        return $this;
    }

    public function getLocation(float $lat = null, float $lon = null, string $observation_station = null, string $forecast_office = null): Point|ObservationStation|ForecastOffice
    {
        if($lat && $lon) {
            $url = "{$this->base_url}/points/{$lat},{$lon}";
            return new Point($this->get($url), $this);
        }

        if($observation_station) {
            $observation_station = strtoupper($observation_station);
            $url = "{$this->base_url}/stations/{$observation_station}";
            return new ObservationStation($this->get($url), $this);
        }

        if($forecast_office) {
            $forecast_office = strtoupper($forecast_office);
            $url = "{$this->base_url}/offices/{$forecast_office}";
            return new ForecastOffice($this->get($url), $this);
        }

        throw new InvalidRequestException("Invalid API request!");
    }
}
