<?php

namespace BenjaminHansen\NWS\Traits;

trait IsCallable
{
    public function raw(): object
    {
        return $this->data;
    }

    public function __call($name, $args)
    {
        $method_parts = explode("_", $name);
        $return = $this->data;

        foreach($method_parts as $part) {
            $return = $return->{$part};
        }

        return $return;
    }
}
