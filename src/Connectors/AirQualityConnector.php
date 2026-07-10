<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\AirQualityResource;
use OpenMeteo\Support\OpenMeteoConfig;

final class AirQualityConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('air_quality', 'https://air-quality-api.open-meteo.com/v1/');
    }

    public function airQuality(): AirQualityResource
    {
        return new AirQualityResource($this);
    }
}
