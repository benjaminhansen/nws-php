<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;
use Illuminate\Support\Collection;

class ZoneForecast extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->properties_updated()))->setTimezoneIfNot($this->api->timezone());
    }

    public function zones(): Collection
    {
        $return = [];

        foreach ($this->properties_zones() as $zone_url) {
            $return[] = new ForecastZone($this->api->get($zone_url), $this->api);
        }

        return collect($return);
    }

    public function periods(): Collection
    {
        $return = [];

        foreach ($this->properties_periods() as $period) {
            $return[] = new ZoneForecastPeriod($period, $this->api);
        }

        return collect($return);
    }

    public function period(int $i = 0): ZoneForecastPeriod
    {
        return $this->periods()->get($i);
    }
}
