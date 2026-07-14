<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;

final class MarineResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetMarineRequest
    {
        return GetMarineRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetMarineRequest
    {
        return GetMarineRequest::forPoints($points)->using($this->connector);
    }
}
