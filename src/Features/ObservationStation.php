<?php

namespace BenjaminHansen\NWS\Features;

use DateTimeZone;
use BenjaminHansen\NWS\Api;

class ObservationStation extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function latitude(): string|int|float
    {
        return $this->geometry_coordinates()[1];
    }

    public function longitude(): string|int|float
    {
        return $this->geometry_coordinates()[0];
    }

    public function latestObservations(): LatestObservations
    {
        $base_url = $this->data->properties->{"@id"};
        $observation_url = "{$base_url}/observations/latest";
        return new LatestObservations($this->api->get($observation_url), $this->api);
    }

    public function county(): County
    {
        return new County($this->api->get($this->properties_county()), $this->api);
    }

    public function name(): string
    {
        return $this->properties_name();
    }

    public function id(): string
    {
        return $this->properties_stationIdentifier();
    }

    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->properties_timeZone());
    }

    public function activeAlerts(): Alerts
    {
        $base_url = $this->api->baseUrl();
        $lat = $this->latitude();
        $lon = $this->longitude();
        $request_url = "{$base_url}/alerts/active?point={$lat},{$lon}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
