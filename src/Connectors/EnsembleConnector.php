<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\EnsembleResource;
use OpenMeteo\Support\OpenMeteoConfig;

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
