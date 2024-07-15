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

        foreach($this->data->glossary as $glossary) {
            $return[] = $glossary;
        }

        return collect($return);
    }

    public function term(string $term): string|null
    {
        return $this->get()->where('term', $term)->first()?->definition;
    }
}
