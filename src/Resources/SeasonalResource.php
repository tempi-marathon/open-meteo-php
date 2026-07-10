<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Seasonal\GetSeasonalRequest;

final class SeasonalResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetSeasonalRequest
    {
        return GetSeasonalRequest::forCoordinates($latitude, $longitude);
    }
}
