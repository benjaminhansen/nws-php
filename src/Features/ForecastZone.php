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
}
