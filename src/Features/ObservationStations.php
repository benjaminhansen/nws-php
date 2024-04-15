<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;

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

    public function get(): array
    {
        $return = [];

        foreach($this->data->features as $station) {
            $return[] = new ObservationStation($station, $this->api);
        }

        return $return;
    }

    public function station(int $i = 0): ObservationStation
    {
        return new ObservationStation($this->data->features[$i], $this->api);
    }
}
