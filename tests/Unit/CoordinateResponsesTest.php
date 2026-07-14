<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;

covers(
    AirQualityResponse::class,
    ClimateResponse::class,
    EnsembleResponse::class,
    FloodResponse::class,
    ForecastResponse::class,
    HistoricalResponse::class,
    MarineResponse::class,
    SeasonalResponse::class,
);

it('builds forecast responses with hourly data and units', function (): void {
    $response = timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class);

    expect($response->hourly()->count())->toBeGreaterThan(0)
        ->and($response->units->hourlyUnits)->not->toBe([]);
});

it('builds historical responses with hourly data and units', function (): void {
    $response = timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class);

    expect($response->hourly()->count())->toBe(1)
        ->and($response->units->hourlyUnits['temperature_2m'])->toBe('°C');
});

it('builds air quality responses with hourly pollutant values', function (): void {
    $response = timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class);

    expect($response->hourly()->at(0)?->get('european_aqi'))->toBe(22.0);
});

it('builds marine responses with hourly unit metadata', function (): void {
    $response = timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class);

    expect($response->units->hourlyUnits['wave_height'])->toBe('m');
});

it('builds climate responses with daily aggregates', function (): void {
    $response = timeSeriesResponseFromPayload(climatePayload(), ClimateResponse::class);

    expect($response->daily()->at(0)?->get('temperature_2m_max'))->toBe(11.1);
});

it('builds flood responses with daily river discharge', function (): void {
    $response = timeSeriesResponseFromPayload(floodPayload(), FloodResponse::class);

    expect($response->daily()->at(0)?->get('river_discharge'))->toBe(0.03);
});

it('builds ensemble responses with hourly temperature', function (): void {
    $response = timeSeriesResponseFromPayload(ensemblePayload(), EnsembleResponse::class);

    expect($response->hourly()->at(0)?->get('temperature_2m'))->toBe(18.9);
});

it('builds seasonal responses with daily temperature maxima', function (): void {
    $response = timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class);

    expect($response->daily()->at(0)?->get('temperature_2m_max'))->toBe(23.9);
});
