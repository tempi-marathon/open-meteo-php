<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Historical\GetArchiveRequest;

final class HistoricalResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetArchiveRequest
    {
        return GetArchiveRequest::forCoordinates($latitude, $longitude);
    }
}
