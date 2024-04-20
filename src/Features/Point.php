<?php

namespace NWS\Features;

use DateTimeZone;
use NWS\Traits\IsCallable;
use NWS\Support\UsState;

class Point
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function city()
    {
        return $this->properties_relativeLocation_properties_city();
    }

    public function state()
    {
        return new UsState($this->properties_relativeLocation_properties_state());
    }

    public function cwa()
    {
        $this->properties_cwa();
    }

    public function latitude()
    {
        return $this->geometry_coordinates()[1];
    }

    public function longitude()
    {
        return $this->geometry_coordinates()[0];
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
        $id = $this->radarStationId();
        $base_url = $this->api->getBaseUrl();
        $url = "{$base_url}/radar/stations/{$id}";
        return new RadarStation($this->api->get($url), $this->api);
    }

    public function fireZone(): FireZone
    {
        return new FireZone($this->api->get($this->data->properties->fireWeatherZone), $this->api);
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

    public function latestObservations($observation_station_index = 0): LatestObservations
    {
        return $this->observationStations()->station($observation_station_index)->latestObservations();
    }

    public function activeAlerts(): Alerts
    {
        $base_url = $this->api->getBaseUrl();
        $lat = $this->latitude();
        $lon = $this->longitude();
        $request_url = "{$base_url}/alerts/active?point={$lat},{$lon}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
