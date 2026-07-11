<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\EnsembleResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class EnsembleConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('ensemble', 'https://ensemble-api.open-meteo.com/v1/');
    }

    public function ensemble(): EnsembleResource
    {
        return new EnsembleResource($this);
    }
}
