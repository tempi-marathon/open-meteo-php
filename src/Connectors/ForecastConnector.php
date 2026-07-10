<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\ForecastResource;
use OpenMeteo\Support\OpenMeteoConfig;

final class ForecastConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/');
    }

    public function weather(): ForecastResource
    {
        return new ForecastResource($this);
    }
}
