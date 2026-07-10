<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\SeasonalResource;
use OpenMeteo\Support\OpenMeteoConfig;

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
