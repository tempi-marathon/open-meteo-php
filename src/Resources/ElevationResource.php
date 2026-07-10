<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Elevation\GetElevationRequest;

final class ElevationResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetElevationRequest
    {
        return GetElevationRequest::forCoordinates($latitude, $longitude);
    }
}
