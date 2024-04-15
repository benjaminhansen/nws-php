<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;
use NWS\Support\Carbon;

class Alerts
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function title(): string
    {
        return $this->data->title;
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->data->updated))->setTimezoneIfNot($this->api->timezone);
    }

    public function hasAlerts(): bool
    {
        return (boolean)count($this->data->features);
    }

    public function count(): int
    {
        return count($this->data->features);
    }

    public function get(): array
    {
        $return = [];

        foreach($this->data->features as $alert) {
            $return[] = new Alert($alert, $this->api);
        }

        return $return;
    }
}
