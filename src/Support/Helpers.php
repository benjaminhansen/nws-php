<?php

namespace NWS\Support;

class Helpers
{
    public static function celcius_to_fahrenheit($c)
    {
        if($c) {
            return $c * 1.8 + 32;
        }

        return null;
    }

    public static function meters_to_feet($m)
    {
        return $m * 3.281;
    }

    public static function meters_to_miles($m)
    {
        return $m / 1609;
    }

    public static function pascals_to_millibars($p)
    {
        return $p / 100;
    }

    public static function pascals_to_inches($p)
    {
        return $p / 3386;
    }

    public static function kph_to_mph($k)
    {
        return $k / 1.609;
    }

    public static function degrees_to_cardinals($d)
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = ($d + 11.25) / 22.5;
        return $directions[(int)$index % 16];
    }
}
