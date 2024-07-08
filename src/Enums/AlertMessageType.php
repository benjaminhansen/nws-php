<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertMessageType: string
{
    use CanGetDescription;

    case Alert = "Initial information requiring attention by targeted recipients";
    case Update = "Updates and supercedes the earlier message(s) identified in previous messages.";
    case Cancel = "Cancels the earlier message(s)";
    case Ack = "Acknowledges receipt and acceptance of the message(s)";
    case Error = "Indicates rejection of the message(s)";
}
