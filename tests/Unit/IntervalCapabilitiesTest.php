<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasCurrent;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Contracts\HasMinutely15;
use TempiMarathon\OpenMeteo\Contracts\HasMonthly;
use TempiMarathon\OpenMeteo\Contracts\HasWeekly;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;

covers(
    ForecastResponse::class,
    HistoricalResponse::class,
    AirQualityResponse::class,
    ClimateResponse::class,
    EnsembleResponse::class,
    FloodResponse::class,
    MarineResponse::class,
    SeasonalResponse::class,
);

it('implements coordinate metadata on every response', function (callable $factory, float $latitude, float $longitude, string $timezone): void {
    $response = $factory();

    expect($response)->toBeInstanceOf(CoordinateResponse::class)
        ->and($response->latitude)->toBe($latitude)
        ->and($response->longitude)->toBe($longitude)
        ->and($response->timezone)->toBe($timezone);
})->with([
    'forecast' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class),
        52.366,
        4.901,
        'Europe/Amsterdam',
    ],
    'historical' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class),
        52.40773,
        4.842301,
        'Europe/Amsterdam',
    ],
    'air quality' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class),
        52.4,
        4.8999996,
        'Europe/Amsterdam',
    ],
    'marine' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class),
        52.375008,
        4.8750153,
        'Europe/Amsterdam',
    ],
    'climate' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(climatePayload(), ClimateResponse::class),
        52.40001,
        4.900009,
        'GMT',
    ],
    'flood' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(floodPayload(), FloodResponse::class),
        52.375,
        4.8750153,
        'GMT',
    ],
    'ensemble' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(ensemblePayload(), EnsembleResponse::class),
        52.25,
        5.0,
        'Europe/Amsterdam',
    ],
    'seasonal' => [
        fn (): CoordinateResponse => timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class),
        52.412178,
        4.5652175,
        'GMT',
    ],
]);

it('declares hourly capability only on supported responses', function (): void {
    $supported = [
        timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class),
        timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class),
        timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class),
        timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class),
        timeSeriesResponseFromPayload(ensemblePayload(), EnsembleResponse::class),
    ];

    foreach ($supported as $response) {
        expect($response)->toBeInstanceOf(HasHourly::class)
            ->and(hourlyPointCount($response))->toBeGreaterThanOrEqual(0);
    }

    expect(timeSeriesResponseFromPayload(climatePayload(), ClimateResponse::class))->not->toBeInstanceOf(HasHourly::class)
        ->and(timeSeriesResponseFromPayload(floodPayload(), FloodResponse::class))->not->toBeInstanceOf(HasHourly::class)
        ->and(timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class))->toBeInstanceOf(HasHourly::class);
});

it('declares daily capability only on supported responses', function (): void {
    $supported = [
        timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class),
        timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class),
        timeSeriesResponseFromPayload(climatePayload(), ClimateResponse::class),
        timeSeriesResponseFromPayload(floodPayload(), FloodResponse::class),
        timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class),
        timeSeriesResponseFromPayload(ensemblePayload(), EnsembleResponse::class),
    ];

    foreach ($supported as $response) {
        expect($response)->toBeInstanceOf(HasDaily::class)
            ->and(dailyPointCount($response))->toBeGreaterThanOrEqual(0);
    }
});

it('declares current capability only on supported responses', function (): void {
    $supported = [
        timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class),
        timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class),
        timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class),
    ];

    foreach ($supported as $response) {
        expect($response)->toBeInstanceOf(HasCurrent::class)
            ->and(currentPointCount($response))->toBeGreaterThanOrEqual(0);
    }

    expect(timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class))->not->toBeInstanceOf(HasCurrent::class);
});

it('declares minutely 15 capability only on supported responses', function (): void {
    $supported = [
        timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class),
        timeSeriesResponseFromPayload(marinePayload(), MarineResponse::class),
    ];

    foreach ($supported as $response) {
        expect($response)->toBeInstanceOf(HasMinutely15::class)
            ->and(minutely15PointCount($response))->toBeGreaterThanOrEqual(0);
    }

    expect(timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class))->not->toBeInstanceOf(HasMinutely15::class);
});

it('declares weekly capability only on seasonal responses', function (): void {
    $seasonal = timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class);

    expect($seasonal)->toBeInstanceOf(HasWeekly::class)
        ->and($seasonal->weekly()->count())->toBeGreaterThanOrEqual(0)
        ->and(timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class))->not->toBeInstanceOf(HasWeekly::class);
});

it('declares monthly capability only on seasonal responses', function (): void {
    $seasonal = timeSeriesResponseFromPayload(seasonalPayload(), SeasonalResponse::class);

    expect($seasonal)->toBeInstanceOf(HasMonthly::class)
        ->and(monthlyPointCount($seasonal))->toBeGreaterThanOrEqual(0)
        ->and(timeSeriesResponseFromPayload(forecastPayload(), ForecastResponse::class))->not->toBeInstanceOf(HasMonthly::class);
});

function hourlyPointCount(HasHourly $response): int
{
    return $response->hourly()->count();
}

function dailyPointCount(HasDaily $response): int
{
    return $response->daily()->count();
}

function currentPointCount(HasCurrent $response): int
{
    return $response->current()->count();
}

function minutely15PointCount(HasMinutely15 $response): int
{
    return $response->minutely15()->count();
}

function monthlyPointCount(HasMonthly $response): int
{
    return $response->monthly()->count();
}
