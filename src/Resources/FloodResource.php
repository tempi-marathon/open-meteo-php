<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Flood\GetFloodRequest;

final class FloodResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetFloodRequest
    {
        return GetFloodRequest::forCoordinates($latitude, $longitude);
    }
}
