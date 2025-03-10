<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertStatus: string
{
    use CanGetDescription;

    case Actual = 'Actionable by all targeted recipients';
    case Exercise = 'Actionable only by designated exercise participants';
    case System = 'For messages that support alert network internal functions';
    case Test = 'Technical testing only, all recipients disregard';
    case Draft = 'A preliminary template or draft, not actionable in its current form';
}
