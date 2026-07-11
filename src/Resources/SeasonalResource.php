<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Seasonal\GetSeasonalRequest;

final class SeasonalResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetSeasonalRequest
    {
        return GetSeasonalRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }
}
