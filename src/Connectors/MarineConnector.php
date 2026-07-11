<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\MarineResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class MarineConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('marine', 'https://marine-api.open-meteo.com/v1/');
    }

    public function marine(): MarineResource
    {
        return new MarineResource($this);
    }
}
