<?php

namespace NWS\Features;

use NWS\Support\Helpers;
use NWS\Traits\IsCallable;
use NWS\Support\Carbon;

class Forecast
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->properties_updated()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function createdAt(): Carbon
    {
        return (new Carbon($this->properties_generatedAt()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function elevation(string $unit = "ft", int $decimal_places = 0): int|float
    {
        return match($unit) {
            "ft" => round(Helpers::meters_to_feet($this->properties_elevation()->value), $decimal_places),
            default => round($this->properties_elevation()->value, $decimal_places)
        };
    }

    public function periods(): ForecastPeriods
    {
        return new ForecastPeriods($this->properties_periods(), $this->api);
    }

    public function period(int $i = 0): ForecastPeriod
    {
        return (new ForecastPeriods($this->properties_periods(), $this->api))->period($i);
    }
}
