<?php

namespace NWS\Features;

use DateTimeZone;
use NWS\Traits\IsCallable;

class ForecastZone
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function timezone(int $i = 0): DateTimeZone
    {
        return new DateTimeZone($this->data->properties->timeZone[$i]);
    }

    public function timezones(): array
    {
        $return = [];

        foreach($this->data->properties->timeZone as $timezone) {
            $return[] = new DateTimeZone($timezone);
        }

        return $return;
    }

    public function id(): string
    {
        return $this->data->properties->id;
    }

    public function name(): string
    {
        return $this->data->properties->name;
    }

    public function radarStation(): string
    {
        return $this->data->properties->radarStation;
    }

    public function observationStations(): array
    {
        $return = [];

        foreach($this->data->properties->observationStations as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return $return;
    }

    public function observationStation(int $i = 0)
    {
        return $this->observationStations()[$i];
    }

    public function forecastOffices(): array
    {
        $return = [];

        foreach($this->data->properties->forecastOffices as $office) {
            $return[] = new ForecastOffice($this->api->get($office));
        }

        return $return;
    }

    public function forecastOffice(int $i = 0)
    {
        return $this->forecastOffices()[$i];
    }
}
