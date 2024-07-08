<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertCertainty: string
{
    use CanGetDescription;

    case Observed = "Determined to have occurred or to be ongoing";
    case Likely = "Likely (p > ~50%)";
    case Possible = "Possible but not likely (p <= ~50%)";
    case Unlikely = "Not expected to occur (p ~ 0)";
    case Unknown = "Certainty unknown";
}
