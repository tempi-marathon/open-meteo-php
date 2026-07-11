<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\HistoricalResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class HistoricalConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('historical', 'https://archive-api.open-meteo.com/v1/');
    }

    public function archive(): HistoricalResource
    {
        return new HistoricalResource($this);
    }
}
