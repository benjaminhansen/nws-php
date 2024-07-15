<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Helpers;
use BenjaminHansen\NWS\Support\Carbon;

class LatestObservations extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function icon(): string
    {
        return $this->properties_icon();
    }

    public function conditions(): string
    {
        return $this->properties_textDescription();
    }

    public function rawMessage(): string
    {
        return $this->properties_rawMessage();
    }

    public function timestamp(): Carbon
    {
        return (new Carbon($this->properties_timestamp()))->setTimezoneIfNot($this->api->timezone());
    }

    public function temperature(string $unit = "F", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "f", "F" => round(Helpers::celcius_to_fahrenheit($this->properties_temperature()->value), $decimal_points),
            default => round($this->properties_temperature()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function dewpoint(string $unit = "F", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "f", "F" => round(Helpers::celcius_to_fahrenheit($this->properties_dewpoint()->value), $decimal_points),
            default => round($this->properties_dewpoint()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function windDirectionDegrees(int $decimal_points = 0): int|float
    {
        return round($this->properties_windDirection()->value, $decimal_points);
    }

    public function windDirectionCardinal(): string
    {
        return Helpers::degrees_to_cardinals($this->properties_windDirection()->value);
    }

    public function windSpeed(string $unit = "MPH", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "mph", "MPH" => round(Helpers::kph_to_mph($this->properties_windSpeed()->value), $decimal_points),
            default => round($this->properties_windSpeed()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function windGust(string $unit = "MPH", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "mph", "MPH" => round(Helpers::kph_to_mph($this->properties_windGust()->value), $decimal_points),
            default => round($this->properties_windGust()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function barometricPressure(string $unit = "IN", int $decimal_points = 2, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "mb", "MB" => round(Helpers::pascals_to_millibars($this->properties_barometricPressure()->value), $decimal_points),
            "in", "IN" => round(Helpers::pascals_to_inches($this->properties_barometricPressure()->value), $decimal_points),
            default => round($this->properties_barometricPressure()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function seaLevelPressure(string $unit = "IN", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "mb", "MB" => round(Helpers::pascals_to_millibars($this->properties_seaLevelPressure()->value), $decimal_points),
            "in", "IN" => round(Helpers::pascals_to_inches($this->properties_seaLevelPressure()->value), $decimal_points),
            default => round($this->properties_seaLevelPressure()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function visibility(string $unit = "MI", int $decimal_points = 2, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "mi", "MI" => round(Helpers::meters_to_miles($this->properties_visibility()->value), $decimal_points),
            "ft", "FT" => round(Helpers::meters_to_feet($this->properties_visibility()->value), $decimal_points),
            default => round($this->properties_visibility()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function relativeHumidity(int $decimal_points = 0): int|float
    {
        return round($this->properties_relativeHumidity()->value, $decimal_points);
    }

    public function windChill(string $unit = "F", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "f", "F" => round(Helpers::celcius_to_fahrenheit($this->properties_windChill()->value), $decimal_points),
            default => round($this->properties_windChill()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function heatIndex(string $unit = "F", int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match($unit) {
            "f", "F" => round(Helpers::celcius_to_fahrenheit($this->properties_heatIndex()->value), $decimal_points),
            default => round($this->properties_heatIndex()->value, $decimal_points)
        };

        if($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }
}
