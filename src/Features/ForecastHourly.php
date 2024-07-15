<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Coordinate;
use BenjaminHansen\NWS\Support\Carbon;
use Illuminate\Support\Collection;

class ForecastHourly extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function updatedAt(): Carbon
    {
        return (new Carbon($this->properties_updated()))->setTimezoneIfNot($this->api->timezone());
    }

    public function createdAt(): Carbon
    {
        return (new Carbon($this->properties_generatedAt()))->setTimezoneIfNot($this->api->timezone());
    }

    public function elevation()
    {
        return $this->properties_elevation()->value;
    }

    public function periods(): ForecastPeriods
    {
        return new ForecastPeriods($this->properties_periods(), $this->api);
    }

    public function period($i): ForecastPeriod
    {
        return (new ForecastPeriods($this->properties_periods(), $this->api))->period($i);
    }

    public function coordinates(): Collection
    {
        $return = [];

        foreach($this->data->geometry->coordinates as $i => $coordinate_block) {
            $return[$i] = [];

            foreach($coordinate_block as $coordinate) {
                $return[$i][] = new Coordinate($coordinate);
            }
        }

        return collect($return);
    }
}
