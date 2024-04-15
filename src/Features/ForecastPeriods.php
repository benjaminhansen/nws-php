<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;

class ForecastPeriods
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function get(): array
    {
        $return = [];

        foreach($this->data as $period) {
            $return[] = new ForecastPeriod($period, $this->api);
        }

        return $return;
    }

    public function period(int $i): ForecastPeriod
    {
        return new ForecastPeriod($this->data[$i], $this->api);
    }
}
