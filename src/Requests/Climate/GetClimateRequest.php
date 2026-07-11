<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Climate;

use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class GetClimateRequest extends AbstractCoordinateGetRequest
{
    public function resolveEndpoint(): string
    {
        return 'climate';
    }

    protected function responseClass(): string
    {
        return ClimateResponse::class;
    }

    public function dto(): ClimateResponse
    {
        return $this->resolveDto(ClimateResponse::class);
    }
}
