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
}
