<?php

namespace NWS\Features;

use NWS\Enums\AlertResponse;
use NWS\Enums\AlertUrgency;
use NWS\Enums\AlertCertainty;
use NWS\Enums\AlertSeverity;
use NWS\Enums\AlertCategory;
use NWS\Enums\AlertStatus;
use NWS\Enums\AlertMessageType;
use NWS\Traits\IsCallable;
use NWS\Support\Carbon;

class Alert
{
    use IsCallable;

    private $data;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function id(): string
    {
        return $this->properties_id();
    }

    public function areaDescription(): string
    {
        return $this->properties_areaDesc();
    }

    public function sentAt(): Carbon
    {
        return (new Carbon($this->properties_sent()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function effectiveAt(): Carbon
    {
        return (new Carbon($this->properties_effective()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function onsetAt(): Carbon
    {
        return (new Carbon($this->properties_onset()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function expiresAt(): Carbon
    {
        return (new Carbon($this->properties_expires()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function endsAt(): Carbon
    {
        return (new Carbon($this->properties_ends()))->setTimezoneIfNot($this->api->getTimezone());
    }

    public function status(): AlertStatus
    {
        return AlertStatus::fromName($this->properties_status());
    }

    public function messageType(): AlertMessageType
    {
        return AlertMessageType::fromName($this->properties_messageType());
    }

    public function category(): AlertCategory
    {
        return AlertCategory::fromName($this->properties_category());
    }

    public function severity(): AlertSeverity
    {
        return AlertSeverity::fromName($this->properties_severity());
    }

    public function certainty(): AlertCertainty
    {
        return AlertCertainty::fromName($this->properties_certainty());
    }

    public function urgency(): AlertUrgency
    {
        return AlertUrgency::fromName($this->properties_urgency());
    }

    public function event(): string
    {
        return $this->properties_event();
    }

    public function senderEmail(): string
    {
        return $this->properties_sender();
    }

    public function senderName(): string
    {
        return $this->properties_senderName();
    }

    public function headline(): string
    {
        return $this->properties_headline();
    }

    public function description(): string
    {
        return $this->properties_description();
    }

    public function instruction(): string
    {
        return $this->properties_instruction();
    }

    public function response(): AlertResponse
    {
        return AlertResponse::fromName($this->properties_response());
    }
}
