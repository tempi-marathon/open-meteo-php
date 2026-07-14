<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Elevation\GetElevationRequest;

final class ElevationResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetElevationRequest
    {
        return GetElevationRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetElevationRequest
    {
        return GetElevationRequest::forPoints($points)->using($this->connector);
    }
}
