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

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetArchiveRequest
    {
        return GetArchiveRequest::forPoints($points)->using($this->connector);
    }
}
