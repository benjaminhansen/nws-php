<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Traits\IsCallable;
use BenjaminHansen\NWS\Support\Helpers;
use BenjaminHansen\NWS\Support\Carbon;

class LatestObservations
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function timestamp(): Carbon
    {
        return (new Carbon($this->data->properties->timestamp))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function temperature(string $unit = "f", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "f" => round(Helpers::celcius_to_fahrenheit($this->data->properties->temperature->value), $decimal_points),
            default => round($this->data->properties->temperature->value, $decimal_points)
        };
    }

    public function dewpoint(string $unit = "f", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "f" => round(Helpers::celcius_to_fahrenheit($this->data->properties->dewpoint->value), $decimal_points),
            default => round($this->data->properties->dewpoint->value, $decimal_points)
        };
    }

    public function windDirectionDegrees(int $decimal_points = 0): int|float
    {
        return round($this->data->properties->windDirection->value, $decimal_points);
    }

    public function windDirectionCardinal(): string
    {
        return Helpers::degrees_to_cardinals($this->data->properties->windDirection->value);
    }

    public function windSpeed(string $unit = "mph", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "mph" => round(Helpers::kph_to_mph($this->data->properties->windSpeed->value), $decimal_points),
            default => round($this->data->properties->windSpeed->value, $decimal_points)
        };
    }

    public function windGust(string $unit = "mph", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "mph" => round(Helpers::kph_to_mph($this->data->properties->windGust->value), $decimal_points),
            default => round($this->data->properties->windGust->value, $decimal_points)
        };
    }

    public function barometricPressure(string $unit = "in", int $decimal_points = 2): int|float
    {
        return match($unit) {
            "mb" => round(Helpers::pascals_to_millibars($this->data->properties->barometricPressure->value), $decimal_points),
            "in" => round(Helpers::pascals_to_inches($this->data->properties->barometricPressure->value), $decimal_points),
            default => round($this->data->properties->barometricPressure->value, $decimal_points)
        };
    }

    public function seaLevelPressure(string $unit = "in", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "mb" => round(Helpers::pascals_to_millibars($this->data->properties->seaLevelPressure->value), $decimal_points),
            "in" => round(Helpers::pascals_to_inches($this->data->properties->seaLevelPressure->value), $decimal_points),
            default => round($this->data->properties->seaLevelPressure->value, $decimal_points)
        };
    }

    public function visibility(string $unit = "mi", int $decimal_points = 2): int|float
    {
        return match($unit) {
            "mi" => round(Helpers::meters_to_miles($this->data->properties->visibility->value), $decimal_points),
            "ft" => round(Helpers::meters_to_feet($this->data->properties->visibility->value), $decimal_points),
            default => round($this->data->properties->visibility->value, $decimal_points)
        };
    }

    public function relativeHumidity(int $decimal_points = 0): int|float
    {
        return round($this->data->properties->relativeHumidity->value, $decimal_points);
    }

    public function windChill(string $unit = "f", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "f" => round(Helpers::celcius_to_fahrenheit($this->data->properties->windChill->value), $decimal_points),
            default => round($this->data->properties->windChill->value, $decimal_points)
        };
    }

    public function heatIndex(string $unit = "f", int $decimal_points = 0): int|float
    {
        return match($unit) {
            "f" => round(Helpers::celcius_to_fahrenheit($this->data->properties->heatIndex->value), $decimal_points),
            default => round($this->data->properties->heatIndex->value, $decimal_points)
        };
    }
}
