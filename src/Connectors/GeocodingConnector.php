<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\GeocodingResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class GeocodingConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('geocoding', 'https://geocoding-api.open-meteo.com/v1/');
    }

    public function locations(): GeocodingResource
    {
        return new GeocodingResource($this);
    }
}
