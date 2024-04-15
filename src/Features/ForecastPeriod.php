<?php

namespace NWS\Features;

use NWS\Support\Helpers;
use NWS\Traits\IsCallable;
use NWS\Support\Carbon;

class ForecastPeriod
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function startTime(): Carbon
    {
        return (new Carbon($this->data->startTime))->setTimezoneIfNot($this->api->timezone);
    }

    public function endTime(): Carbon
    {
        return (new Carbon($this->data->endTime))->setTimezoneIfNot($this->api->timezone);
    }

    public function daytime(): bool
    {
        return $this->data->isDaytime ?? false;
    }

    public function temperature()
    {
        return $this->data->temperature;
    }

    public function temperatureUnit()
    {
        return $this->data->temperatureUnit;
    }

    public function windSpeed()
    {
        return $this->data->windSpeed;
    }

    public function windDirection()
    {
        return $this->data->windDirection;
    }

    public function shortForecast()
    {
        return $this->data->shortForecast;
    }

    public function longForecast()
    {
        return $this->data->detailedForecast;
    }

    public function dewpoint(string $unit = "f", int $decimal_points = 0)
    {
        return match($unit) {
            "f" => round(Helpers::celcius_to_fahrenheit($this->data->dewpoint->value), $decimal_points),
            default => round($this->data->dewpoint->value, $decimal_points)
        };
    }

    public function relativeHumidity()
    {
        return $this->data->relativeHumidity->value;
    }

    public function chanceOfPrecip()
    {
        return $this->data->probabilityOfPrecipitation->value;
    }

    public function icon()
    {
        return $this->data->icon;
    }
}
