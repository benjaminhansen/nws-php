<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Helpers;
use BenjaminHansen\NWS\Support\Carbon;

class ForecastPeriod extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function startTime(): Carbon
    {
        return (new Carbon($this->data->startTime))->setTimezoneIfNot($this->api->timezone());
    }

    public function endTime(): Carbon
    {
        return (new Carbon($this->data->endTime))->setTimezoneIfNot($this->api->timezone());
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

    public function windDirectionDegrees()
    {
        return $this->data->windDirection;
    }

    public function windDirectionCardinal(): string
    {
        return Helpers::degrees_to_cardinals($this->data->windDirection);
    }

    public function shortForecast(): string
    {
        return $this->data->shortForecast;
    }

    public function longForecast(): string|null
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

    public function chanceOfPrecip(): float
    {
        return $this->data->probabilityOfPrecipitation->value;
    }

    public function icon(): string
    {
        return $this->data->icon;
    }
}
