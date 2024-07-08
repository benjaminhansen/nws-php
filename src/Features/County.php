<?php

namespace BenjaminHansen\NWS\Features;

use DateTimeZone;
use BenjaminHansen\NWS\Traits\IsCallable;
use BenjaminHansen\NWS\Support\UsState;

class County
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

    public function activeAlerts(): Alerts
    {
        $base_url = $this->api->getBaseUrl();
        $zone_id = $this->data->properties->id;
        $request_url = "{$base_url}/alerts/active/zone/{$zone_id}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
