<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Data\SeriesPoint;
use TempiMarathon\OpenMeteo\Enums\ForecastCurrentVariable;
use TempiMarathon\OpenMeteo\WindDirection;

covers(CurrentSeries::class);

it('wraps the current snapshot as a single-item series', function (): void {
    $current = new CurrentSeries([
        new SeriesPoint(
            datetime: new DateTimeImmutable('2026-07-11T12:00'),
            values: [
                'temperature_2m' => 21.5,
                'wind_direction_10m' => WindDirection::fromDegrees(90),
            ],
            interval: 900,
        ),
    ]);

    expect($current->count())->toBe(1)
        ->and($current->first()?->datetime->format('Y-m-d\TH:i'))->toBe('2026-07-11T12:00')
        ->and($current->first()?->interval)->toBe(900)
        ->and($current->first()?->get(ForecastCurrentVariable::Temperature2m))->toBe(21.5)
        ->and($current->first()?->get('wind_direction_10m')?->label())->toBe('E');
});

it('returns an empty current series when no snapshot is present', function (): void {
    $current = new CurrentSeries([]);

    expect($current->count())->toBe(0)
        ->and($current->first())->toBeNull()
        ->and($current->at(0))->toBeNull();
});
