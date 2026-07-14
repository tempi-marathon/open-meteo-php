<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Contracts\HasCurrent;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Contracts\HasMinutely15;
use TempiMarathon\OpenMeteo\Contracts\HasMonthly;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\ProvidesMonthlySeries;

covers(
    ProvidesMonthlySeries::class,
    CreatesTimeSeriesResponse::class,
);

it('exposes only supported intervals per response type', function (): void {
    $forecast = timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class);
    $historical = timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class);
    $airQuality = timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class);
    $marine = timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class);
    $climate = timeSeriesResponseFromPayload(climatePayload(), ClimateResponse::class);
    $flood = timeSeriesResponseFromPayload(floodPayload(), FloodResponse::class);
    $ensemble = timeSeriesResponseFromPayload(ensemblePayload(), EnsembleResponse::class);
    $seasonal = timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class);

    expect($forecast)->toBeInstanceOf(HasHourly::class)
        ->and($forecast)->toBeInstanceOf(HasDaily::class)
        ->and($forecast)->toBeInstanceOf(HasCurrent::class)
        ->and($forecast)->toBeInstanceOf(HasMinutely15::class)
        ->and($forecast)->not->toBeInstanceOf(HasMonthly::class)
        ->and($historical)->toBeInstanceOf(HasHourly::class)
        ->and($historical)->toBeInstanceOf(HasDaily::class)
        ->and($historical)->not->toBeInstanceOf(HasCurrent::class)
        ->and($airQuality)->toBeInstanceOf(HasHourly::class)
        ->and($airQuality)->toBeInstanceOf(HasCurrent::class)
        ->and($airQuality)->not->toBeInstanceOf(HasDaily::class)
        ->and($marine)->toBeInstanceOf(HasHourly::class)
        ->and($marine)->toBeInstanceOf(HasCurrent::class)
        ->and($marine)->toBeInstanceOf(HasMinutely15::class)
        ->and($marine)->not->toBeInstanceOf(HasDaily::class)
        ->and($climate)->toBeInstanceOf(HasDaily::class)
        ->and($climate)->not->toBeInstanceOf(HasHourly::class)
        ->and($flood)->toBeInstanceOf(HasDaily::class)
        ->and($ensemble)->toBeInstanceOf(HasHourly::class)
        ->and($ensemble)->not->toBeInstanceOf(HasDaily::class)
        ->and($seasonal)->toBeInstanceOf(HasDaily::class)
        ->and($seasonal)->toBeInstanceOf(HasMonthly::class);
});

it('parses seasonal monthly readings', function (): void {
    $response = timeSeriesResponseFromPayload(array_replace(seasonalPayload(), [
        'monthly' => [
            'time' => ['2026-07-01'],
            'temperature_2m_mean' => [18.5],
        ],
        'monthly_units' => [
            'time' => 'iso8601',
            'temperature_2m_mean' => '°C',
        ],
    ]), SeasonalResponse::class);

    expect($response->monthly()->at(0)?->get('temperature_2m_mean'))->toBe(18.5)
        ->and($response->units->monthlyUnits['temperature_2m_mean'])->toBe('°C');
});

it('rejects unsupported response classes', function (): void {
    expect(fn () => timeSeriesResponseFromPayload(forecastPayload(), stdClass::class))
        ->toThrow(InvalidArgumentException::class, 'Unsupported response class');
});

it('requires current data to include a time value', function (): void {
    expect(fn () => timeSeriesResponseFromPayload(array_replace(forecastPayload(), [
        'current' => ['temperature_2m' => 21.5],
    ]), ForecastResponse::class))
        ->toThrow(InvalidArgumentException::class, 'Current data must contain a time value.');
});
