<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class RadarStations extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function count(): int
    {
        return count($this->data->features ?? []);
    }

    public function get(): Collection
    {
        $return = [];

        foreach ($this->data->features ?? [] as $station) {
            $return[] = new RadarStation($station, $this->api);
        }

        return collect($return);
    }

    public function station(int $i = 0): RadarStation
    {
        return $this->get()->get($i);
    }
}
