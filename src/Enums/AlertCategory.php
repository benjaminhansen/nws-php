<?php

namespace BenjaminHansen\NWS\Enums;

use BenjaminHansen\NWS\Traits\CanGetDescription;

enum AlertCategory: string
{
    use CanGetDescription;

    case Geo = "Geophysical (inc. landslide)";
    case Met = "Meteorological (inc. flood)";
    case Safety = "General emergency and public safety";
    case Security = "Law enforcement, military, homeland and local/private security";
    case Rescue = "Rescue and recovery";
    case Fire = "Fire suppression and rescue";
    case Health = "Medical and public health";
    case Env = "Pollution and other environmental";
    case Transport = "Public and private transportation";
    case Infra = "Utility, telecommunication, other non-transport infrastructure";
    case CBRNE = "Chemical, Biological, Radiological, Nuclear or High-Yield Explosive threat or attack";
    case Other = "Other events";
}
