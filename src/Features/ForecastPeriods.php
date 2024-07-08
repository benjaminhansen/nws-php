<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Traits\IsCallable;
use Illuminate\Support\Collection;

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

    public function get(): Collection
    {
        $return = [];

        foreach($this->data as $period) {
            $return[] = new ForecastPeriod($period, $this->api);
        }

        return collect($return);
    }

    public function period(int $i): ForecastPeriod
    {
        return new ForecastPeriod($this->data[$i], $this->api);
    }
}
