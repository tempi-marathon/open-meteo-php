<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo;

use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Connectors\ClimateConnector;
use TempiMarathon\OpenMeteo\Connectors\ElevationConnector;
use TempiMarathon\OpenMeteo\Connectors\EnsembleConnector;
use TempiMarathon\OpenMeteo\Connectors\FloodConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Connectors\SeasonalConnector;

/**
 * Optional entry point that delegates to Saloon connectors.
 *
 * Connectors remain public for direct use, testing, and dependency injection.
 */
final class OpenMeteo
{
    private ?AirQualityConnector $airQuality = null;

    private ?ClimateConnector $climate = null;

    private ?ElevationConnector $elevation = null;

    private ?EnsembleConnector $ensemble = null;

    private ?FloodConnector $flood = null;

    private ?ForecastConnector $forecast = null;

    private ?GeocodingConnector $geocoding = null;

    private ?HistoricalConnector $historical = null;

    private ?MarineConnector $marine = null;

    private ?SeasonalConnector $seasonal = null;

    public function airQuality(): AirQualityConnector
    {
        return $this->airQuality ??= new AirQualityConnector;
    }

    public function climate(): ClimateConnector
    {
        return $this->climate ??= new ClimateConnector;
    }

    public function elevation(): ElevationConnector
    {
        return $this->elevation ??= new ElevationConnector;
    }

    public function ensemble(): EnsembleConnector
    {
        return $this->ensemble ??= new EnsembleConnector;
    }

    public function flood(): FloodConnector
    {
        return $this->flood ??= new FloodConnector;
    }

    public function forecast(): ForecastConnector
    {
        return $this->forecast ??= new ForecastConnector;
    }

    public function geocoding(): GeocodingConnector
    {
        return $this->geocoding ??= new GeocodingConnector;
    }

    public function historical(): HistoricalConnector
    {
        return $this->historical ??= new HistoricalConnector;
    }

    public function marine(): MarineConnector
    {
        return $this->marine ??= new MarineConnector;
    }

    public function seasonal(): SeasonalConnector
    {
        return $this->seasonal ??= new SeasonalConnector;
    }
}
