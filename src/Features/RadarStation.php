<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;

class RadarStation extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }
}
