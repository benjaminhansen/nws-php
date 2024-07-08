<?php

namespace BenjaminHansen\NWS\Traits;

use BenjaminHansen\NWS\Enums\AlertResponse;
use BenjaminHansen\NWS\Enums\AlertUrgency;
use BenjaminHansen\NWS\Enums\AlertCertainty;
use BenjaminHansen\NWS\Enums\AlertSeverity;
use BenjaminHansen\NWS\Enums\AlertCategory;
use BenjaminHansen\NWS\Enums\AlertStatus;
use BenjaminHansen\NWS\Enums\AlertMessageType;

trait CanGetDescription
{
    public static function fromName(string $name):
    AlertResponse|AlertUrgency|AlertCertainty|AlertSeverity|AlertCategory|AlertStatus|AlertMessageType
    {
        return constant("self::$name");
    }
}
