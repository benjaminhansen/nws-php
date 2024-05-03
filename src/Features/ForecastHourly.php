<?php

namespace NWS\Features;

use NWS\Support\Coordinate;
use NWS\Traits\IsCallable;
use NWS\Support\Carbon;
use Illuminate\Support\Collection;

class ForecastHourly
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
        return (new Carbon($this->data->properties->updated))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function createdAt(): Carbon
    {
        return (new Carbon($this->data->properties->generatedAt))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function elevation()
    {
        return $this->data->properties->elevation->value;
    }

    public function periods(): ForecastPeriods
    {
        return new ForecastPeriods($this->data->properties->periods, $this->api);
    }

    public function period($i): ForecastPeriod
    {
        return (new ForecastPeriods($this->data->properties->periods, $this->api))->period($i);
    }

    public function coordinates(): Collection
    {
        $return = [];

        foreach($this->data->geometry->coordinates as $i => $coordinate_block) {
            $return[$i] = [];

            foreach($coordinate_block as $coordinate) {
                $return[$i][] = new Coordinate($coordinate);
            }
        }

        return collect($return);
    }
}
