<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class Glossary extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function get(): Collection
    {
        $return = [];

        foreach($this->data->glossary as $entry) {
            $return[] = $entry;
        }

        return collect($return);
    }

    public function term(string $term): null|object
    {
        return $this->get()->where('term', $term)->first();
    }

    public function define(string $term): string|null
    {
        return $this->get()->where('term', $term)->first()?->definition;
    }
}
