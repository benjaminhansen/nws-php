<?php

namespace BenjaminHansen\NWS\Support;

use BenjaminHansen\NWS\Traits\IsCallable;

class UsState
{
    use IsCallable;

    public function __construct(private $data) {}

    public function abbreviation(): string
    {
        return $this->data;
    }

    public function name(): string
    {
        return usStates()[$this->data];
    }
}
