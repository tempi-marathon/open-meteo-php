<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\HistoricalResource;
use OpenMeteo\Support\OpenMeteoConfig;

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
