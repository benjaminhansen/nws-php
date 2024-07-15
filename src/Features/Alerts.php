<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;
use Illuminate\Support\Collection;

class Alerts extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function title(): string
    {
        return $this->data->title;
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->data->updated))->setTimezoneIfNot($this->api->timezone());
    }

    public function hasAlerts(): bool
    {
        return (bool) $this->count();
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
