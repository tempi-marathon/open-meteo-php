<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Marine\GetMarineRequest;

final class MarineResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetMarineRequest
    {
        return GetMarineRequest::forCoordinates($latitude, $longitude);
    }
}
