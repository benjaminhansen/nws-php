<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class ObservationStations extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function count(): int
    {
        return count($this->data->features);
    }

    public function get(int $i = null): Collection|ObservationStation
    {
        $return = [];

        foreach($this->data->features as $station) {
            $return[] = new ObservationStation($station, $this->api);
        }

        $collection = collect($return);
        if(is_null($i)) {
            return $collection;
        }

        return $collection->get($i);
    }

    public function station(int $i = 0): ObservationStation
    {
        return $this->get($i);
    }
}
