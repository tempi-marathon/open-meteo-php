<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use TempiMarathon\OpenMeteo\Resources\ClimateResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class ClimateConnector extends BaseConnector
{
    public function resolveBaseUrl(): string
    {
        return OpenMeteoConfig::host('climate', 'https://climate-api.open-meteo.com/v1/');
    }

    public function climate(): ClimateResource
    {
        return new ClimateResource($this);
    }
}
