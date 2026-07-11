<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Ensemble;

use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class GetEnsembleRequest extends AbstractCoordinateGetRequest
{
    public function resolveEndpoint(): string
    {
        return 'ensemble';
    }

    protected function responseClass(): string
    {
        return EnsembleResponse::class;
    }

    public function dto(): EnsembleResponse
    {
        return $this->resolveDto(EnsembleResponse::class);
    }
}
