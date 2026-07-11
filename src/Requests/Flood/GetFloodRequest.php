<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Flood;

use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class GetFloodRequest extends AbstractCoordinateGetRequest
{
    public function resolveEndpoint(): string
    {
        return 'flood';
    }

    protected function responseClass(): string
    {
        return FloodResponse::class;
    }

    public function dto(): FloodResponse
    {
        return $this->resolveDto(FloodResponse::class);
    }
}
