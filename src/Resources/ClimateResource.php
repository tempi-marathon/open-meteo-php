<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Climate\GetClimateRequest;

final class ClimateResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetClimateRequest
    {
        return GetClimateRequest::forCoordinates($latitude, $longitude);
    }
}
