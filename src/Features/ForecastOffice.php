<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;

class ForecastOffice
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function phone(): string
    {
        return $this->data->telephone;
    }

    public function fax(): string
    {
        return $this->data->faxNumber;
    }

    public function email(): string
    {
        return $this->data->email;
    }

    public function address(): string
    {
        return $this->data->address;
    }

    public function counties(): array
    {
        $return = [];

        foreach($this->data->responsibleCounties as $county) {
            $return[] = new County($this->api->get($county), $this->api);
        }

        return $return;
    }

    public function forecastZones(): array
    {
        $return = [];

        foreach($this->data->responsibleForecastZones as $zone) {
            $return[] = new ForecastZone($this->api->get($zone), $this->api);
        }

        return $return;
    }

    public function observationStations(): array
    {
        $return = [];

        foreach($this->data->approvedObservationStations as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return $return;
    }

    public function fireZones(): array
    {
        $return = [];

        foreach($this->data->responsibleFireZones as $zone) {
            $return[] = new FireZone($this->api->get($zone), $this->api);
        }

        return $return;
    }
}
