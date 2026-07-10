<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\MarineResource;
use OpenMeteo\Support\OpenMeteoConfig;

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
