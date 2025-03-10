<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\UsState;
use DateTimeZone;

class Point extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function city(): string
    {
        return $this->properties_relativeLocation_properties_city();
    }

    public function state(): UsState
    {
        return new UsState($this->properties_relativeLocation_properties_state());
    }

    public function cwa(): string
    {
        return $this->properties_cwa();
    }

    public function latitude(?float $precision = null): string|int|float
    {
        $latitude = $this->geometry_coordinates()[1];
        if($precision) {
            return round($latitude, $precision);
        }

        return $latitude;
    }

    public function longitude(?float $precision = null): string|int|float
    {
        $longitude = $this->geometry_coordinates()[0];
        if($precision) {
            return round($longitude, $precision);
        }

        return $longitude;
    }

    public function county(): County
    {
        return new County($this->api->get($this->properties_county()), $this->api);
    }

    public function forecastGridData(): ForecastGridData
    {
        return new ForecastGridData($this->api->get($this->properties_forecastGridData()), $this->api);
    }

    public function forecastOffice(): ForecastOffice
    {
        return new ForecastOffice($this->api->get($this->properties_forecastOffice()), $this->api);
    }

    public function forecast(): Forecast
    {
        return new Forecast($this->api->get($this->properties_forecast()), $this->api);
    }

    public function forecastZone(): ForecastZone
    {
        return new ForecastZone($this->api->get($this->properties_forecastZone()), $this->api);
    }

    public function hourlyForecast(): ForecastHourly
    {
        return new ForecastHourly($this->api->get($this->properties_forecastHourly()), $this->api);
    }

    public function observationStations(): ObservationStations
    {
        return new ObservationStations($this->api->get($this->properties_observationStations()), $this->api);
    }

    public function radarStationId(): string
    {
        return $this->properties_radarStation();
    }

    public function radarStation(): RadarStation
    {
        $base_url = $this->api->baseUrl();
        $url = "{$base_url}/radar/stations/{$this->radarStationId()}";

        return new RadarStation($this->api->get($url), $this->api);
    }

    public function fireZone(): FireZone
    {
        return new FireZone($this->api->get($this->properties_fireWeatherZone()), $this->api);
    }

    public function timezone(): DateTimeZone
    {
        return new DateTimeZone($this->properties_timeZone());
    }

    public function gridId(): string
    {
        return $this->properties_gridId();
    }

    public function gridX(): int
    {
        return $this->properties_gridX();
    }

    public function gridY(): int
    {
        return $this->properties_gridY();
    }

    public function latestObservations(int $observation_station_index = 0): LatestObservations
    {
        return $this->observationStations()->station($observation_station_index)->latestObservations();
    }

    public function activeAlerts(): Alerts
    {
        $base_url = $this->api->baseUrl();
        $request_url = "{$base_url}/alerts/active?point={$this->latitude()},{$this->longitude()}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
