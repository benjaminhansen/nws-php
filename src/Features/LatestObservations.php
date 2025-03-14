<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;
use BenjaminHansen\NWS\Support\Helpers;

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

    public function temperature(string $unit = 'F', int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match ($unit) {
            'f', 'F' => round(Helpers::celcius_to_fahrenheit($this->properties_temperature()->value), $decimal_points),
            default => round($this->properties_temperature()->value, $decimal_points)
        };

        $value = number_format($value, $decimal_points);

        if ($show_unit) {
            $value = "{$value}°{$unit}";
        }

        return $value;
    }

    public function dewpoint(string $unit = 'F', int $decimal_points = 0, bool $show_unit = false): int|float|string
    {
        $value = match ($unit) {
            'f', 'F' => round(Helpers::celcius_to_fahrenheit($this->properties_dewpoint()->value), $decimal_points),
            default => round($this->properties_dewpoint()->value, $decimal_points)
        };

        $value = number_format($value, $decimal_points);

        if ($show_unit) {
            $value = "{$value}°{$unit}";
        }

        return $value;
    }

    public function windDirectionDegrees(int $decimal_points = 0): int|float|null
    {
        if ($degrees = $this->properties_windDirection()->value) {
            return round($degrees, $decimal_points);
        }

        return null;
    }

    public function windDirectionCardinal(): ?string
    {
        if ($direction = $this->properties_windDirection()->value) {
            return Helpers::degrees_to_cardinals($direction);
        }

        return null;
    }

    public function windSpeed(string $unit = 'MPH', int $decimal_points = 0, bool $show_unit = false): int|float|string|null
    {
        if ($speed = $this->properties_windSpeed()->value) {
            $value = match ($unit) {
                'mph', 'MPH' => round(Helpers::kph_to_mph($speed), $decimal_points),
                default => round($this->properties_windSpeed()->value, $decimal_points)
            };

            $value = number_format($value, $decimal_points);

            if ($show_unit) {
                $value = "{$value}{$unit}";
            }

            return $value;
        }

        return null;
    }

    public function windGust(string $unit = 'MPH', int $decimal_points = 0, bool $show_unit = false): int|float|string|null
    {
        if ($gust = $this->properties_windGust()->value) {
            $value = match ($unit) {
                'mph', 'MPH' => round(Helpers::kph_to_mph($gust), $decimal_points),
                default => round($this->properties_windGust()->value, $decimal_points)
            };

            $value = number_format($value, $decimal_points);

            if ($show_unit) {
                $value = "{$value}{$unit}";
            }

            return $value;
        }

        return null;
    }

    public function barometricPressure(string $unit = 'IN', int $decimal_points = 2, bool $show_unit = false): int|float|string|null
    {
        if ($pressure = $this->properties_barometricPressure()->value) {
            $value = match ($unit) {
                'mb', 'MB' => round(Helpers::pascals_to_millibars($pressure), $decimal_points),
                'in', 'IN' => round(Helpers::pascals_to_inches($pressure), $decimal_points),
                default => round($this->properties_barometricPressure()->value, $decimal_points)
            };

            $value = number_format($value, $decimal_points);

            if ($show_unit) {
                $value = "{$value}{$unit}";
            }

            return $value;
        }

        return null;
    }

    public function seaLevelPressure(string $unit = 'IN', int $decimal_points = 2, bool $show_unit = false): int|float|string
    {
        $value = match ($unit) {
            'mb', 'MB' => round(Helpers::pascals_to_millibars($this->properties_seaLevelPressure()->value), $decimal_points),
            'in', 'IN' => round(Helpers::pascals_to_inches($this->properties_seaLevelPressure()->value), $decimal_points),
            default => round($this->properties_seaLevelPressure()->value, $decimal_points)
        };

        $value = number_format($value, $decimal_points);

        if ($show_unit) {
            $value = "{$value}{$unit}";
        }

        return $value;
    }

    public function visibility(string $unit = 'MI', int $decimal_points = 2, bool $show_unit = false): int|float|string
    {
        $value = match ($unit) {
            'mi', 'MI' => round(Helpers::meters_to_miles($this->properties_visibility()->value), $decimal_points),
            'ft', 'FT' => round(Helpers::meters_to_feet($this->properties_visibility()->value), $decimal_points),
            default => round($this->properties_visibility()->value, $decimal_points)
        };

        $value = number_format($value, $decimal_points);

        if ($show_unit) {
            $value = "{$value}{$unit}";
        }

        return $value;
    }

    public function relativeHumidity(int $decimal_points = 0, bool $show_unit = false, string $unit = '%'): int|float|string
    {
        $value = round($this->properties_relativeHumidity()->value, $decimal_points);

        if ($show_unit) {
            $value = "{$value}{$unit}";
        }

        return $value;
    }

    public function windChill(string $unit = 'F', int $decimal_points = 0, bool $show_unit = false): int|float|string|null
    {
        if (! $this->properties_windChill()->value) {
            return null;
        }

        $value = match ($unit) {
            'f', 'F' => round(Helpers::celcius_to_fahrenheit($this->properties_windChill()->value), $decimal_points),
            default => round($this->properties_windChill()->value, $decimal_points)
        };

        if ($show_unit) {
            $value = "{$value}°{$unit}";
        }

        return $value;
    }

    public function heatIndex(string $unit = 'F', int $decimal_points = 0, bool $show_unit = false): int|float|string|null
    {
        if ($index = $this->properties_heatIndex()->value) {
            $value = match (trim(strtoupper($unit))) {
                'F' => round(Helpers::celcius_to_fahrenheit($index), $decimal_points),
                default => round($index, $decimal_points)
            };

            if ($show_unit) {
                $value = "{$value}°{$unit}";
            }

            return $value;
        }

        return null;
    }

    public function windDescription(bool $show_unit = false): string
    {
        if ($this->windSpeed() == 0) {
            return 'Calm';
        }

        $cardinal = $this->windDirectionCardinal();
        $speed = $this->windSpeed(show_unit: $show_unit);

        return "{$cardinal} {$speed}";
    }

    public function toObject(bool $show_units = true, string $temperature_unit = 'F', string $speed_unit = 'mph', string $distance_unit = 'mi', string $pressure_unit = 'in', string $datetime_format = 'Y-m-d G:i:s'): object
    {
        return (object) [
            'conditions' => $this->conditions(),
            'created_at' => $this->timestamp()->format($datetime_format),
            'temperature' => $this->temperature(show_unit: $show_units, unit: $temperature_unit),
            'dewpoint' => $this->dewpoint(show_unit: $show_units, unit: $temperature_unit),
            'windDirectionDegrees' => $this->windDirectionDegrees(),
            'windDirectionCardinal' => $this->windDirectionCardinal(),
            'windSpeed' => $this->windSpeed(show_unit: $show_units, unit: $speed_unit),
            'windGust' => $this->windGust(show_unit: $show_units, unit: $speed_unit),
            'barometricPressure' => $this->barometricPressure(show_unit: $show_units, unit: $pressure_unit),
            'seaLevelPressure' => $this->seaLevelPressure(show_unit: $show_units, unit: $pressure_unit),
            'visibility' => $this->visibility(show_unit: $show_units, unit: $distance_unit),
            'relativeHumidity' => $this->relativeHumidity(show_unit: $show_units),
            'windChill' => $this->windChill(show_unit: $show_units, unit: $temperature_unit),
            'heatIndex' => $this->heatIndex(show_unit: $show_units, unit: $temperature_unit),
        ];
    }

    public function toJson(bool $show_units = true, string $temperature_unit = 'F', string $speed_unit = 'mph', string $distance_unit = 'mi', string $pressure_unit = 'in', string $datetime_format = 'Y-m-d G:i:s')
    {
        return json_encode($this->toObject($show_units, $temperature_unit, $speed_unit, $distance_unit, $pressure_unit, $datetime_format));
    }
}
