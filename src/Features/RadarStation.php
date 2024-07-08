<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Traits\IsCallable;

class RadarStation
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function type()
    {
        return $this->data->properties->stationType;
    }

    public function name()
    {
        return $this->data->properties->name;
    }
}
