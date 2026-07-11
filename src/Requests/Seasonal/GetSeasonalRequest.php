<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Seasonal;

use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class GetSeasonalRequest extends AbstractCoordinateGetRequest
{
    public function resolveEndpoint(): string
    {
        return 'seasonal';
    }

    protected function responseClass(): string
    {
        return SeasonalResponse::class;
    }

    public function dto(): SeasonalResponse
    {
        return $this->resolveDto(SeasonalResponse::class);
    }
}
