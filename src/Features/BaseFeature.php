<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Traits\IsCallable;
use AllowDynamicProperties;

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
