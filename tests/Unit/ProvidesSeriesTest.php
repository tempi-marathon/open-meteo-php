<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Data\DailySeries;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Data\Minutely15Series;
use TempiMarathon\OpenMeteo\Data\MonthlySeries;
use TempiMarathon\OpenMeteo\Data\SeriesPoint;
use TempiMarathon\OpenMeteo\Support\ProvidesCurrentSeries;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesMinutely15Series;
use TempiMarathon\OpenMeteo\Support\ProvidesMonthlySeries;
use TempiMarathon\OpenMeteo\Tests\Support\CurrentSeriesStub;
use TempiMarathon\OpenMeteo\Tests\Support\DailySeriesStub;
use TempiMarathon\OpenMeteo\Tests\Support\HourlySeriesStub;
use TempiMarathon\OpenMeteo\Tests\Support\Minutely15SeriesStub;
use TempiMarathon\OpenMeteo\Tests\Support\MonthlySeriesStub;

covers(
    ProvidesCurrentSeries::class,
    ProvidesDailySeries::class,
    ProvidesHourlySeries::class,
    ProvidesMinutely15Series::class,
    ProvidesMonthlySeries::class,
);

it('exposes hourly series through ProvidesHourlySeries', function (): void {
    $series = new HourlySeries([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-06T10:00'), values: ['temperature_2m' => 16.0]),
    ]);

    expect((new HourlySeriesStub($series))->hourly())->toBe($series);
});

it('exposes daily series through ProvidesDailySeries', function (): void {
    $series = new DailySeries([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-06T00:00'), values: ['temperature_2m_max' => 20.0]),
    ]);

    expect((new DailySeriesStub($series))->daily())->toBe($series);
});

it('exposes current series through ProvidesCurrentSeries', function (): void {
    $series = new CurrentSeries([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-06T12:00'), values: ['temperature_2m' => 21.0]),
    ]);

    expect((new CurrentSeriesStub($series))->current())->toBe($series);
});

it('exposes minutely 15 series through ProvidesMinutely15Series', function (): void {
    $series = new Minutely15Series([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-06T12:00'), values: ['temperature_2m' => 20.0]),
    ]);

    expect((new Minutely15SeriesStub($series))->minutely15())->toBe($series);
});

it('exposes monthly series through ProvidesMonthlySeries', function (): void {
    $series = new MonthlySeries([
        new SeriesPoint(datetime: new DateTimeImmutable('2026-07-01T00:00'), values: ['temperature_2m_mean' => 18.0]),
    ]);

    expect((new MonthlySeriesStub($series))->monthly())->toBe($series);
});
