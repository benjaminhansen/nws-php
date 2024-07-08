<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Traits\IsCallable;
use BenjaminHansen\NWS\Support\Carbon;
use Illuminate\Support\Collection;

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
        return (new Carbon($this->data->updated))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function hasAlerts(): bool
    {
        return (boolean) $this->count();
    }

    public function count(): int
    {
        return count($this->data->features);
    }

    public function get(): Collection
    {
        $return = [];

        foreach($this->data->features as $alert) {
            $return[] = new Alert($alert, $this->api);
        }

        return collect($return);
    }
}
