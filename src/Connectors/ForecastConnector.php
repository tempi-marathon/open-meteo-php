<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\ForecastResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

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
