<?php

namespace NWS\Features;

use DateTimeZone;
use NWS\Traits\IsCallable;
use NWS\Support\UsState;

class County
{
    use IsCallable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function id(): string
    {
        return $this->data->properties->id;
    }

    public function name(): string
    {
        return $this->data->properties->name;
    }

    public function state(): UsState
    {
        return new UsState($this->data->properties->state);
    }

    public function timezone(int $i = 0): DateTimeZone
    {
        return new DateTimeZone($this->data->properties->timeZone[$i]);
    }

    public function timezones(): array
    {
        $return = [];

        foreach($this->data->properties->timeZone as $timezone) {
            $return[] = new DateTimeZone($timezone);
        }

        return $return;
    }
}
