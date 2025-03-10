<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertSeverity: string
{
    use CanGetDescription;

    case Extreme = 'Extraordinary threat to life or property';
    case Severe = 'Significant threat to life or property';
    case Moderate = 'Possible threat to life or property';
    case Minor = 'Minimal to no known threat to life or property';
    case Unknown = 'Severity unknown';
}
