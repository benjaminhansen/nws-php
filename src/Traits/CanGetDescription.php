<?php

namespace BenjaminHansen\NWS\Traits;

use BenjaminHansen\NWS\Enums\{AlertResponse, AlertUrgency, AlertCertainty, AlertSeverity, AlertCategory, AlertStatus, AlertMessageType};

trait CanGetDescription
{
    public static function fromName(string $name):
    AlertResponse|AlertUrgency|AlertCertainty|AlertSeverity|AlertCategory|AlertStatus|AlertMessageType
    {
        return constant("self::$name");
    }
}
