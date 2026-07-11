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
}
