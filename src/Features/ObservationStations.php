<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Traits\IsCallable;
use Illuminate\Support\Collection;

class ObservationStations
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

        foreach($this->data->features as $station) {
            $return[] = new ObservationStation($station, $this->api);
        }

        return collect($return);
    }

    public function station(int $i = 0): ObservationStation
    {
        return new ObservationStation($this->data->features[$i], $this->api);
    }
}
