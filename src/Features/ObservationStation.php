<?php

namespace NWS\Features;

use DateTimeZone;
use NWS\Traits\IsCallable;

class ObservationStation
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->api = $api;
        $this->data = $data;
    }

    public function latestObservations(): LatestObservations
    {
        $base_url = $this->data->properties->{"@id"};
        $observation_url = "{$base_url}/observations/latest";
        return new LatestObservations($this->api->get($observation_url), $this->api);
    }

    public function county(): County
    {
        return new County($this->api->get($this->data->properties->county), $this->api);
    }

    public function name(): string
    {
        return $this->data->properties->name;
    }

    public function id(): string
    {
        return $this->data->properties->stationIdentifier;
    }

    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->data->properties->timeZone);
    }
}
