<?php

namespace BenjaminHansen\NWS\Features;

use AllowDynamicProperties;
use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Traits\IsCallable;

#[AllowDynamicProperties]
class BaseFeature
{
    use IsCallable;

    public object $data;

    public Api $api;

    public function __construct(object $data, Api $api)
    {
        $this->data = $data;
        $this->api = $api;
    }
}
