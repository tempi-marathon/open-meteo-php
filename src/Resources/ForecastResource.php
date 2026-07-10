<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Forecast\GetForecastRequest;

final class ForecastResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetForecastRequest
    {
        return GetForecastRequest::forCoordinates($latitude, $longitude);
    }
}
