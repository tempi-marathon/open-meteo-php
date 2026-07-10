<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\ElevationResource;
use OpenMeteo\Support\OpenMeteoConfig;

final class ElevationConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('elevation', 'https://api.open-meteo.com/v1/');
    }

    public function elevation(): ElevationResource
    {
        return new ElevationResource($this);
    }
}
