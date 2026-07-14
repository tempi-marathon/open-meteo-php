<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Flood\GetFloodRequest;

final class FloodResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetFloodRequest
    {
        return GetFloodRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetFloodRequest
    {
        return GetFloodRequest::forPoints($points)->using($this->connector);
    }
}
