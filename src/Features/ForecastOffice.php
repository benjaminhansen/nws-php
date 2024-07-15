<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class ForecastOffice extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
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

    public function counties(): Collection
    {
        $return = [];

        foreach($this->data->responsibleCounties as $county) {
            $return[] = new County($this->api->get($county), $this->api);
        }

        return collect($return);
    }

    public function forecastZones(): Collection
    {
        $return = [];

        foreach($this->data->responsibleForecastZones as $zone) {
            $return[] = new ForecastZone($this->api->get($zone), $this->api);
        }

        return collect($return);
    }

    public function observationStations(): Collection
    {
        $return = [];

        foreach($this->data->approvedObservationStations as $station) {
            $return[] = new ObservationStation($this->api->get($station), $this->api);
        }

        return collect($return);
    }

    public function fireZones(): Collection
    {
        $return = [];

        foreach($this->data->responsibleFireZones as $zone) {
            $return[] = new FireZone($this->api->get($zone), $this->api);
        }

        return collect($return);
    }
}
