<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;

final class ForecastResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetForecastRequest
    {
        return GetForecastRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }
}
