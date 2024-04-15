<?php

namespace NWS\Enums;

use NWS\Traits\CanGetDescription;

enum AlertUrgency: string
{
    use CanGetDescription;

    case Immediate = "Responsive action SHOULD be taken immediately";
    case Expected = "Responsive action SHOULD be taken soon (within next hour)";
    case Future = "Responsive action SHOULD be taken in the near future";
    case Past = "Responsive action is no longer required";
    case Unknown = "Urgency not known";
}
