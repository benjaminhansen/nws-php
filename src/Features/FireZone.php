<?php

namespace BenjaminHansen\NWS\Features;

use DateTimeZone;
use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class FireZone extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function id(): string
    {
        return $this->properties_id();
    }

    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->properties_timeZone());
    }

    public function forecastOffices(): Collection
    {
        $return = [];

        foreach($this->properties_forecastOffices() as $office) {
            $return[] = new ForecastOffice($this->api->get($office), $this->api);
        }

        return collect($return);
    }

    public function forecastOffice(int $i = 0): ForecastOffice
    {
        return $this->forecastOffices()->get($i);
    }

    public function observationStations(): ObservationStations
    {
        return new ObservationStations($this->api->get($this->properties_observationStations()), $this->api);
    }

    public function observationStation(int $i = 0): Collection|ObservationStation
    {
        return $this->observationStations()->station($i);
    }

    public function radarStation(): RadarStation
    {
        $id = $this->properties_radarStation();
        $base_url = $this->api->baseUrl();
        $url = "{$base_url}/radar/stations/K{$id}";

        return new RadarStation($this->api->get($url), $this->api);
    }

    public function activeAlerts(): Alerts
    {
        $request_url = "{$this->api->baseUrl()}/alerts/active/zone/{$this->id()}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
