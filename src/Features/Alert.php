<?php

namespace BenjaminHansen\NWS\Features;

use BenjaminHansen\NWS\Api;
use BenjaminHansen\NWS\Enums\AlertCategory;
use BenjaminHansen\NWS\Enums\AlertCertainty;
use BenjaminHansen\NWS\Enums\AlertMessageType;
use BenjaminHansen\NWS\Enums\AlertResponse;
use BenjaminHansen\NWS\Enums\AlertSeverity;
use BenjaminHansen\NWS\Enums\AlertStatus;
use BenjaminHansen\NWS\Enums\AlertUrgency;
use BenjaminHansen\NWS\Support\Carbon;
use Illuminate\Support\Collection;

class Alert extends BaseFeature
{
    public function __construct(object $data, Api $api)
    {
        parent::__construct($data, $api);
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
        return (new Carbon($this->properties_sent()))->setTimezoneIfNot($this->api->timezone());
    }

    public function effectiveAt(): Carbon
    {
        return (new Carbon($this->properties_effective()))->setTimezoneIfNot($this->api->timezone());
    }

    public function onsetAt(): Carbon
    {
        return (new Carbon($this->properties_onset()))->setTimezoneIfNot($this->api->timezone());
    }

    public function expiresAt(): Carbon
    {
        return (new Carbon($this->properties_expires()))->setTimezoneIfNot($this->api->timezone());
    }

    public function endsAt(): Carbon
    {
        return (new Carbon($this->properties_ends()))->setTimezoneIfNot($this->api->timezone());
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

    public function affectedZones(): Collection
    {
        $return = [];

        $zones = $this->properties_affectedZones();
        foreach ($zones as $zone_url) {
            $return[] = new ForecastZone($this->api->get($zone_url), $this->api);
        }

        return collect($return);
    }

    public function affectedZone(int $i = 0): ForecastZone
    {
        $zone = $this->properties_affectedZones()[$i];

        return new ForecastZone($this->api->get($zone), $this->api);
    }

    public function response(): AlertResponse
    {
        return AlertResponse::fromName($this->properties_response());
    }
}
