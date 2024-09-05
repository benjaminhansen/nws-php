<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\{Helpers, Carbon};
use Illuminate\Support\Collection;

class ForecastGridData extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->properties_updateTime()))->setTimezoneIfNot($this->api->timezone());
    }

    public function validTimes(): string
    {
        return $this->properties_validTimes();
    }

    public function gridId(): string
    {
        return $this->properties_gridId();
    }

    public function gridX(): int
    {
        return $this->properties_gridX();
    }

    public function gridY(): int
    {
        return $this->properties_gridY();
    }

    public function temeratures(string $unit = 'f', int $decimal_points = 0): Collection
    {
        $return = [];

        foreach($this->properties_temperature()->values as $temperature) {
            $validTime = (new Carbon(explode("/", $temperature->validTime)[0]))->setTimezoneIfNot($this->api->timezone());
            $value = match($unit) {
                'f' => round(Helpers::celcius_to_fahrenheit($temperature->value), $decimal_points),
                default => round($temperature->value, $decimal_points)
            };

            $return[] = (object)[
                'validTime' => $validTime,
                'value' => $value
            ];
        }

        return collect($return);
    }
}
