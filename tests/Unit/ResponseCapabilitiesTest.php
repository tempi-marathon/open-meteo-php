<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\CoordinateResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponseCollection;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastSegmentException;
use TempiMarathon\OpenMeteo\Exceptions\UnsupportedResponseClassException;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;

covers(
    CreatesTimeSeriesResponse::class,
    CoordinateResponseCollection::class,
    HistoricalResponseCollection::class,
);

it('parses historical responses from payloads', function (): void {
    $response = timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class);

    expect($response)->toBeInstanceOf(HistoricalResponse::class)
        ->and($response->hourly())->not->toBeEmpty();
});

it('parses air quality responses from payloads', function (): void {
    $response = timeSeriesResponseFromPayload(airQualityPayload(), AirQualityResponse::class);

    expect($response)->toBeInstanceOf(AirQualityResponse::class)
        ->and($response->hourly())->not->toBeEmpty();
});

it('ignores non-array current payloads when building forecast responses', function (): void {
    $response = timeSeriesResponseFromPayload(array_replace(forecastPayload(), [
        'current' => 'invalid',
    ]), ForecastResponse::class);

    expect($response)->toBeInstanceOf(ForecastResponse::class)
        ->and($response->current()->count())->toBe(0);
});

it('parses seasonal weekly series from payloads', function (): void {
    $response = timeSeriesResponseFromPayload(array_replace(seasonalPayload(), [
        'weekly' => [
            'time' => ['2026-07-01'],
            'wind_speed_10m_mean' => [5.2],
        ],
        'weekly_units' => [
            'time' => 'iso8601',
            'wind_speed_10m_mean' => 'km/h',
        ],
    ]), SeasonalResponse::class);

    expect($response->weekly()->at(0)?->get('wind_speed_10m_mean'))->toBe(5.2)
        ->and($response->units->weeklyUnits['wind_speed_10m_mean'])->toBe('km/h');
});

it('parses seasonal monthly series from payloads', function (): void {
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

it('builds historical response collections from segmented payloads', function (): void {
    $collection = (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param array<int|string, mixed> $data */
        public function make(array $data): HistoricalResponseCollection
        {
            return $this->createResponseCollectionFromPayload($data, HistoricalResponse::class);
        }
    })->make([
        historicalPayload(),
        historicalPayload(),
    ]);

    expect($collection)->toHaveCount(2)
        ->and($collection->first())->toBeInstanceOf(HistoricalResponse::class);
});

it('iterates coordinate response collections', function (): void {
    $collection = new CoordinateResponseCollection([
        timeSeriesResponseFromPayload(historicalPayload(), HistoricalResponse::class),
    ]);

    expect(iterator_to_array($collection))->toHaveCount(1);
});

it('rejects unsupported response classes', function (): void {
    expect(fn () => timeSeriesResponseFromPayload(forecastPayload(), stdClass::class))
        ->toThrow(UnsupportedResponseClassException::class);
});

it('builds forecast response collections from segmented payloads', function (): void {
    $collection = (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param array<int|string, mixed> $data */
        public function make(array $data): ForecastResponseCollection
        {
            return $this->createForecastResponseCollectionFromPayload($data);
        }
    })->make([
        forecastPayload(),
        forecastPayload(),
    ]);

    expect($collection)->toHaveCount(2);
});

it('wraps a single forecast payload in a collection', function (): void {
    $collection = (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param array<int|string, mixed> $data */
        public function make(array $data): ForecastResponseCollection
        {
            return $this->createForecastResponseCollectionFromPayload($data);
        }
    })->make(forecastPayload());

    expect($collection)->toHaveCount(1);
});

it('throws when a forecast collection segment is malformed', function (): void {
    expect(fn () => (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param array<int|string, mixed> $data */
        public function make(array $data): ForecastResponseCollection
        {
            return $this->createForecastResponseCollectionFromPayload($data);
        }
    })->make([forecastPayload(), 'invalid']))
        ->toThrow(InvalidForecastSegmentException::class, 'Expected forecast segment to be an array.');
});
