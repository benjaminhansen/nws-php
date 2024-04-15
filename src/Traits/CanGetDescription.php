<?php

namespace NWS\Traits;

use NWS\Enums\AlertResponse;
use NWS\Enums\AlertUrgency;
use NWS\Enums\AlertCertainty;
use NWS\Enums\AlertSeverity;
use NWS\Enums\AlertCategory;
use NWS\Enums\AlertStatus;
use NWS\Enums\AlertMessageType;

trait CanGetDescription
{
    public static function fromName(string $name):
    AlertResponse|AlertUrgency|AlertCertainty|AlertSeverity|AlertCategory|AlertStatus|AlertMessageType
    {
        return constant("self::$name");
    }
}
