<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertResponse: string
{
    use CanGetDescription;

    case Shelter = 'Take shelter in place or per the given instruction(s).';
    case Evacuate = 'Relocate as instructed in the given instruction(s).';
    case Prepare = 'Make preparations per the given instruction(s).';
    case Execute = 'Execute a pre-planned activity identified in the given instruction(s).';
    case Avoid = 'Avoid the subject event as per the given instruction(s).';
    case Monitor = 'Attend to information sources as described in the given instruction(s).';
    case Assess = 'Evaluate the information in this message.';
    case AllClear = 'The subject event no longer poses a threat or concern and any follow on action is described in the given instruction(s).';
    case None = 'No action recommended.';
}
