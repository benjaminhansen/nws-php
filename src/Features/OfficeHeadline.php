<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Support\Carbon;

class OfficeHeadline extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
    }

    public function id(): string
    {
        return $this->data->id;
    }

    public function title(): string
    {
        return $this->data->title;
    }

    public function name(): string
    {
        return $this->data->name;
    }

    public function issuanceTime(): Carbon
    {
        return (new Carbon($this->data->issuanceTime))->setTimezoneIfNot($this->api->timezone());
    }

    public function important(): bool
    {
        return (bool) $this->data->important;
    }

    public function link(): string
    {
        return $this->data->link;
    }

    public function summary(): ?string
    {
        return $this->data->summary ?? null;
    }

    /*
    **  Full content is only available when fetching a specific headline by ID.
    */
    public function content(): ?string
    {
        return $this->data->content ?? null;
    }
}
