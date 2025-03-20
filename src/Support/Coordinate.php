<?php

namespace BenjaminHansen\NWS\Support;

use BenjaminHansen\NWS\Traits\IsCallable;

class Coordinate
{
    use IsCallable;

    public function __construct(private $data) {}

    public function latitude(): float
    {
        return $this->data[1];
    }

    public function longitude(): float
    {
        return $this->data[0];
    }
}
