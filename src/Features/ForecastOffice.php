<?php

namespace NWS\Features;

use NWS\Traits\IsCallable;

class ForecastOffice
{
    use IsCallable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function phone(): string
    {
        return $this->data->telephone;
    }

    public function fax(): string
    {
        return $this->data->faxNumber;
    }

    public function email(): string
    {
        return $this->data->email;
    }

    public function address(): string
    {
        return $this->data->address;
    }
}
