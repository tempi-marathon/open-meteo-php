<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Marine;

use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class GetMarineRequest extends AbstractCoordinateGetRequest
{
    public function resolveEndpoint(): string
    {
        return 'marine';
    }

    protected function responseClass(): string
    {
        return MarineResponse::class;
    }

    public function dto(): MarineResponse
    {
        return $this->resolveDto(MarineResponse::class);
    }
}
