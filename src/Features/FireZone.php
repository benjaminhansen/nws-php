<?php

namespace BenjaminHansen\NWS\Features;

use DateTimeZone;
use BenjaminHansen\NWS\Traits\IsCallable;
use Illuminate\Support\Collection;

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

    public function forecastOffices(): Collection
    {
        $return = [];

        foreach($this->data->properties->forecastOffices as $office) {
            $return[] = new ForecastOffice($this->api->get($office), $this->api);
        }

        return collect($return);
    }

    public function forecastOffice(int $i = 0): ForecastOffice
    {
        return $this->forecastOffices()[$i];
    }

    public function observationStations(): Collection
    {
        $return = [];

        foreach($this->data->properties->observationStations as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return collect($return);
    }

    public function observationStation(int $i = 0): ObservationStation
    {
        return $this->observationStations()[$i];
    }

    public function radarStation(): RadarStation
    {
        $id = $this->data->properties->radarStation;
        $base_url = $this->api->getBaseUrl();
        $url = "{$base_url}/radar/stations/K{$id}";

        return new RadarStation($this->api->get($url), $this->api);
    }

    public function activeAlerts(): Alerts
    {
        $base_url = $this->api->getBaseUrl();
        $zone_id = $this->data->properties->id;
        $request_url = "{$base_url}/alerts/active/zone/{$zone_id}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
