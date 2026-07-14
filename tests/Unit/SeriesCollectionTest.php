<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Data\DailySeries;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Data\Minutely15Series;
use TempiMarathon\OpenMeteo\Data\MonthlySeries;
use TempiMarathon\OpenMeteo\Data\SeriesCollection;
use TempiMarathon\OpenMeteo\Data\SeriesPoint;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

covers(
    SeriesCollection::class,
    HourlySeries::class,
    DailySeries::class,
    Minutely15Series::class,
    MonthlySeries::class,
    CurrentSeries::class,
);

it('counts, indexes, and iterates series points', function (string $seriesClass): void {
    $points = [
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T10:00'),
            values: ['temperature_2m' => 16.0, 'weathercode' => WeatherCode::CLEAR],
        ),
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T12:00'),
            values: ['temperature_2m' => 18.0, 'weathercode' => WeatherCode::RAIN],
        ),
    ];

    /** @var SeriesCollection $series */
    $series = new $seriesClass($points);

    expect($series->count())->toBe(2)
        ->and($series->at(0)?->get('temperature_2m'))->toBe(16.0)
        ->and($series->at(1)?->get('weathercode'))->toBe(WeatherCode::RAIN)
        ->and($series->at(2))->toBeNull()
        ->and(iterator_to_array($series))->toHaveCount(2);
})->with([
    'hourly' => [HourlySeries::class],
    'daily' => [DailySeries::class],
    'minutely 15' => [Minutely15Series::class],
    'monthly' => [MonthlySeries::class],
    'current' => [CurrentSeries::class],
]);

it('returns the first point from a series', function (): void {
    $series = new HourlySeries([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-06T10:00'), values: ['temperature_2m' => 16.0]),
    ]);

    expect($series->first()?->get('temperature_2m'))->toBe(16.0)
        ->and((new HourlySeries([]))->first())->toBeNull();
});

it('finds the closest point to a target time', function (): void {
    $series = new HourlySeries([
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T10:00'),
            values: ['weathercode' => WeatherCode::CLEAR],
        ),
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T12:00'),
            values: ['weathercode' => WeatherCode::RAIN],
        ),
    ]);

    expect($series->closestTo(new DateTimeImmutable('2026-07-06T11:30'))?->get('weathercode'))->toBe(WeatherCode::RAIN)
        ->and($series->closestTo(new DateTimeImmutable('2026-07-06T10:15'))?->get('weathercode'))->toBe(WeatherCode::CLEAR);
});

it('returns null when finding the closest point in an empty series', function (): void {
    expect((new DailySeries([]))->closestTo(new DateTimeImmutable('2026-07-06T12:00')))->toBeNull();
});

it('prefers the first point when distances are equal', function (): void {
    $series = new HourlySeries([
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T10:00'),
            values: ['weathercode' => WeatherCode::CLEAR],
        ),
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-06T14:00'),
            values: ['weathercode' => WeatherCode::RAIN],
        ),
    ]);

    expect($series->closestTo(new DateTimeImmutable('2026-07-06T12:00'))?->get('weathercode'))->toBe(WeatherCode::CLEAR);
});
