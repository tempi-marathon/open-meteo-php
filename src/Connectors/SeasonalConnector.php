<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\SeasonalResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class SeasonalConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('seasonal', 'https://seasonal-api.open-meteo.com/v1/');
    }

    public function seasonal(): SeasonalResource
    {
        return new SeasonalResource($this);
    }
}
