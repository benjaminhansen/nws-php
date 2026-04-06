<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use DateTimeZone;
use Illuminate\Support\Collection;

class ForecastZone extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function timezone(int $i = 0): DateTimeZone
    {
        return $this->timezones()->get($i);
    }

    public function timezones(): Collection
    {
        $return = [];

        foreach ($this->properties_timeZone() as $timezone) {
            $return[] = new DateTimeZone($timezone);
        }

        return collect($return);
    }

    public function id(): string
    {
        return $this->properties_id();
    }

    public function name(): string
    {
        return $this->properties_name();
    }

    public function radarStation(): string
    {
        return $this->properties_radarStation();
    }

    public function observationStations(): Collection
    {
        $return = [];

        foreach ($this->properties_observationStations() as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return collect($return);
    }

    public function observationStation(int $i = 0): ObservationStation
    {
        return $this->observationStations()->get($i);
    }

    public function forecastOffices(): Collection
    {
        $return = [];

        foreach ($this->properties_forecastOffices() as $office) {
            $return[] = new ForecastOffice($this->api->get($office), $this->api);
        }

        return collect($return);
    }

    public function forecastOffice(int $i = 0): ForecastOffice
    {
        return $this->forecastOffices()->get($i);
    }

    /*
    **  Parse the zone type (e.g. "forecast", "fire", "land") from the zone's @id URL.
    */
    public function type(): string
    {
        $path = parse_url($this->data->properties->{'@id'}, PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));

        // path structure: zones/{type}/{id}
        return $parts[1] ?? 'forecast';
    }

    public function forecast(): ZoneForecast
    {
        $url = "{$this->data->properties->{'@id'}}/forecast";

        return new ZoneForecast($this->api->get($url), $this->api);
    }

    /*
    **  Returns recent observations for this zone. Only applies to zones of type "forecast".
    */
    public function observationsFeed(): Observations
    {
        $url = "{$this->api->baseUrl()}/zones/forecast/{$this->id()}/observations";

        return new Observations($this->api->get($url), $this->api);
    }

    /*
    **  Returns observation stations serving this zone. Only applies to zones of type "forecast".
    */
    public function stationsFeed(): ObservationStations
    {
        $url = "{$this->api->baseUrl()}/zones/forecast/{$this->id()}/stations";

        return new ObservationStations($this->api->get($url), $this->api);
    }
}
