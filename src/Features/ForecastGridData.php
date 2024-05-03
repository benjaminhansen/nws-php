<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;
use NWS\Support\Helpers;
use NWS\Support\Carbon;
use Illuminate\Support\Collection;

class ForecastGridData
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->data->properties->updateTime))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function validTimes(): string
    {
        return $this->data->properties->validTimes;
    }

    public function gridId(): string
    {
        return $this->data->properties->gridId;
    }

    public function gridX(): int
    {
        return $this->data->properties->gridX;
    }

    public function gridY(): int
    {
        return $this->data->properties->gridY;
    }

    public function temeratures(string $unit = 'f', int $decimal_points = 0): Collection
    {
        $return = [];

        foreach($this->data->properties->temperature->values as $temperature) {
            $validTime = (new Carbon(explode("/", $temperature->validTime)[0]))->setTimezoneIfNot($this->api->getTimezone());
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
