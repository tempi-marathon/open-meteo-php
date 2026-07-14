<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Climate\GetClimateRequest;

final class ClimateResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetClimateRequest
    {
        return GetClimateRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetClimateRequest
    {
        return GetClimateRequest::forPoints($points)->using($this->connector);
    }
}
