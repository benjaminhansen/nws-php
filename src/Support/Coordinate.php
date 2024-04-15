<?php

namespace NWS\Support;

use NWS\Traits\IsCallable;

class Coordinate
{
    use IsCallable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function latitude(): float
    {
        return $this->data[1];
    }

    public function longitude(): float
    {
        return $this->data[0];
    }
}
