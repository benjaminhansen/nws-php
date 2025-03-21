<?php

namespace BenjaminHansen\NWS\Features;

use AllowDynamicProperties;
use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Traits\IsCallable;

#[AllowDynamicProperties]
class BaseFeature
{
    use IsCallable;

    public function __construct(public object $data, public Api $api) {}
}
