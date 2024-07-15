<?php

namespace BenjaminHansen\NWS\Support;

use Carbon\Carbon as CarbonDist;

class Carbon extends CarbonDist
{
    public function __construct($time = null, $timezone = null)
    {
        parent::__construct($time, $timezone);
    }

    public function setTimezoneIfNot($timezone): self
    {
        if($this->getTimezone()->getName() !== $timezone->getName()) {
            $this->setTimezone($timezone);
        }
        return $this;
    }
}
