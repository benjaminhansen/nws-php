<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;

class ZoneForecastPeriod extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function number(): int
    {
        return $this->data->number;
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function forecast(): string
    {
        return $this->data->detailedForecast;
    }
}
