<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\AirQuality\GetAirQualityRequest;

final class AirQualityResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetAirQualityRequest
    {
        return GetAirQualityRequest::forCoordinates($latitude, $longitude);
    }
}
