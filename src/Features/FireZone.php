<?php

namespace NWS\Features;

use DateTimeZone;
use NWS\Traits\IsCallable;

class FireZone
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->data->properties->timeZone);
    }

    public function forecastOffices(): array
    {
        $return = [];

        foreach($this->data->properties->forecastOffices as $office) {
            $return[] = new ForecastOffice($this->api->get($office), $this->api);
        }

        return $return;
    }

    public function observationStations(): array
    {
        $return = [];

        foreach($this->data->properties->observationStations as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return $return;
    }

    public function radarStation(): RadarStation
    {
        $id = $this->data->properties->radarStation;
        $base_url = $this->api->getBaseUrl();
        $url = "{$base_url}/radar/stations/K{$id}";
        return new RadarStation($this->api->get($url), $this->api);
    }
}
