<?php

namespace NWS\Support;

use NWS\Traits\IsCallable;

class UsState
{
    use IsCallable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function abbreviation(): string
    {
        return $this->data;
    }

    public function name(): string
    {
        return usStates()[$this->data];
    }
}
