<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;

final class HistoricalResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetArchiveRequest
    {
        return GetArchiveRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }
}
