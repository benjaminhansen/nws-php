<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class ForecastPeriods extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function count(): int
    {
        return count($this->data);
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
        return new ForecastPeriod($this->data->$i, $this->api);
    }
}
