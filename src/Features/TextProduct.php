<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;

class TextProduct extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function wmoCollectiveId(): ?string
    {
        return $this->data->wmoCollectiveId ?? null;
    }

    public function issuingOffice(): string
    {
        return $this->data->issuingOffice;
    }

    public function issuanceTime(): Carbon
    {
        return (new Carbon($this->data->issuanceTime))->setTimezoneIfNot($this->api->timezone());
    }

    public function code(): string
    {
        return $this->data->productCode;
    }

    public function name(): string
    {
        return $this->data->productName;
    }

    /*
    **  The full text of the product. Only available when fetching a single product by ID,
    **  not when returned from a list query.
    */
    public function text(): ?string
    {
        return $this->data->productText ?? null;
    }
}
