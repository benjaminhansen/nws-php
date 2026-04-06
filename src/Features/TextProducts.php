<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use Illuminate\Support\Collection;

class TextProducts extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function count(): int
    {
        return count($this->data->{'@graph'} ?? []);
    }

    public function get(): Collection
    {
        $return = [];

        foreach ($this->data->{'@graph'} ?? [] as $product) {
            $return[] = new TextProduct($product, $this->api);
        }

        return collect($return);
    }

    public function product(int $i = 0): TextProduct
    {
        return $this->get()->get($i);
    }

    /*
    **  If the API has paginated the results, this returns the next page.
    */
    public function nextPage(): ?self
    {
        $next = $this->data->pagination->next ?? null;

        if ($next) {
            return new self($this->api->get($next), $this->api);
        }

        return null;
    }
}
