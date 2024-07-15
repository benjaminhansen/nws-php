<?php

namespace BenjaminHansen\NWS\Features;

use DateTimeZone;
use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\UsState;
use Illuminate\Support\Collection;

class County extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function id(): string
    {
        return $this->properties_id();
    }

    public function name(): string
    {
        return $this->properties_name();
    }

    public function state(): UsState
    {
        return new UsState($this->properties_state());
    }

    public function timezone(int $i = 0): DateTimeZone
    {
        return new DateTimeZone($this->properties_timeZone()[$i]);
    }

    public function timezones(): Collection
    {
        $return = [];

        foreach($this->properties_timeZone() as $timezone) {
            $return[] = new DateTimeZone($timezone);
        }

        return collect($return);
    }

    public function activeAlerts(): Alerts
    {
        $request_url = "{$this->api->baseUrl()}/alerts/active/zone/{$this->id()}";

        return new Alerts($this->api->get($request_url), $this->api);
    }
}
