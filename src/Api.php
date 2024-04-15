<?php

namespace NWS;

use Phpfastcache\Helper\Psr16Adapter;
use GuzzleHttp\Client as HttpClient;
use NWS\Features\Point;
use Dotenv\Dotenv;
use NWS\Features\ForecastOffice;
use NWS\Features\ObservationStation;

class Api
{
    private $base_url;
    private $user_agent;
    private $client;
    protected $config;
    protected $cache;
    private $cache_lifetime;
    private $cache_exclusions = ['/alerts'];
    private $acceptable_http_codes = [200];
    public $timezone = 'UTC';

    public function __construct(string $domain, string $email, string $cache_driver = 'Files', int $cache_lifetime = 3600)
    {
        // build up our config library
        $this->config = Dotenv::createImmutable(__DIR__."/../");
        $this->config->load();

        // get our API base URL and store that in our cache exclusions array
        // we always want to be able to query the API status code live
        $this->base_url = env('BASE_URL', 'https://api.weather.gov');
        array_push($this->cache_exclusions, $this->base_url);

        // derive our user agent for the API requests
        $this->user_agent = "($domain, $email)";

        // build up our HTTP client for making requests to the API
        $this->client = new HttpClient([
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $this->user_agent
            ]
        ]);

        // build up our cache to store data locally for a period of time
        $this->cache = new Psr16Adapter($cache_driver);
        $this->cache_lifetime = $cache_lifetime;
    }

    public function getBaseUrl(): string
    {
        return $this->base_url;
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

    public function get($url): object|bool
    {
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

    public function getLocation(float $lat, float $lon): Point
    {
        $url = "{$this->base_url}/points/{$lat},{$lon}";
        return new Point($this->get($url), $this);
    }

    public function getObservationStation(string $station_id)
    {
        $url = "{$this->base_url}/stations/{$station_id}";
        return new ObservationStation($this->get($url), $this);
    }

    public function getForecastOffice(string $office_id)
    {
        $url = "{$this->base_url}/offices/{$office_id}";
        return new ForecastOffice($this->get($url));
    }
}
