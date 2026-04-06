<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class Observations extends BaseFeature
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

        foreach ($this->data->features ?? [] as $observation) {
            $return[] = new LatestObservations($observation, $this->api);
        }

        return collect($return);
    }

    public function observation(int $i = 0): LatestObservations
    {
        return $this->get()->get($i);
    }
}
