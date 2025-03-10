<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;
use BenjaminHansen\NWS\Support\Helpers;

class Forecast extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->properties_updated()))->setTimezoneIfNot($this->api->timezone());
    }

    public function createdAt(): Carbon
    {
        return (new Carbon($this->properties_generatedAt()))->setTimezoneIfNot($this->api->timezone());
    }

    public function elevation(string $unit = 'FT', int $decimal_places = 0, bool $show_unit = false): int|float|string
    {
        $value = match (strtoupper($unit)) {
            'FT' => round(Helpers::meters_to_feet($this->properties_elevation()->value), $decimal_places),
            default => round($this->properties_elevation()->value, $decimal_places)
        };

        if ($show_unit) {
            $value = "{$value} {$unit}";
        }

        return $value;
    }

    public function periods(): ForecastPeriods
    {
        return new ForecastPeriods((object) $this->properties_periods(), $this->api);
    }

    public function period(int $i = 0): ForecastPeriod
    {
        return $this->periods()->period($i);
    }
}
