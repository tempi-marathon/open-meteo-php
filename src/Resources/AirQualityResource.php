<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;

final class AirQualityResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetAirQualityRequest
    {
        return GetAirQualityRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }
}
