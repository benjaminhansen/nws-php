<?php

namespace NWS;

use Phpfastcache\Helper\Psr16Adapter;
use GuzzleHttp\Client as HttpClient;
use NWS\Features\Point;
use NWS\Features\ForecastOffice;
use NWS\Features\ObservationStation;
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
        $this->user_agent = "($domain, $email)";

        // build up our HTTP client for making requests to the API
        $this->client = new HttpClient([
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->user_agent
            ]
        ]);
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

    public function clearCache(): bool|self
    {
        if($this->cache->clear()) {
            return $this;
        } else {
            return false;
        }
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
        if(!$this->cache) {
            // the user did not opt to use the cache
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
        $key = urlencode($url);

        if($this->cache->has($key)) {
            $data = $this->cache->get($key);
        } else {
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
        }

        return $data;
    }

    public function status(): string
    {
        return $this->get($this->base_url)->status;
    }

    public function ok(): bool
    {
        return strtolower($this->status()) == "ok";
    }

    public function getLocation(float $lat = null, float $lon = null, string $observation_station = null, string $forecast_office = null): Point|ObservationStation|ForecastOffice
    {
        if($lat && $lon) {
            $url = "{$this->base_url}/points/{$lat},{$lon}";
            return new Point($this->get($url), $this);
        }

        if($observation_station) {
            $url = "{$this->base_url}/stations/{$observation_station}";
            return new ObservationStation($this->get($url), $this);
        }

        if($forecast_office) {
            $url = "{$this->base_url}/offices/{$forecast_office}";
            return new ForecastOffice($this->get($url), $this);
        }
    }
}
