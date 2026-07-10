<?php

declare(strict_types=1);

namespace OpenMeteo\Connectors;

use OpenMeteo\Resources\FloodResource;
use OpenMeteo\Support\OpenMeteoConfig;

final class FloodConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('flood', 'https://flood-api.open-meteo.com/v1/');
    }

    public function flood(): FloodResource
    {
        return new FloodResource($this);
    }
}
