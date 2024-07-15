<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Traits\IsCallable;

class BaseFeature
{
    use IsCallable;

    public $data;
    public $api;

    public function __construct(object $data, Api $api)
    {
        $this->data = $data;
        $this->api = $api;
    }
}
