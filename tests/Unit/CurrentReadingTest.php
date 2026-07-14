<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\CurrentReading;
use TempiMarathon\OpenMeteo\Enums\CurrentVariable;
use TempiMarathon\OpenMeteo\WindDirection;

covers(CurrentReading::class);

it('reads current values by enum and api key', function (): void {
    $reading = new CurrentReading(
        time: new DateTimeImmutable('2026-07-11T12:00'),
        interval: 900,
        values: [
            'temperature_2m' => 21.5,
            'wind_direction_10m' => WindDirection::fromDegrees(90),
        ],
    );

    expect($reading->time->format('Y-m-d\TH:i'))->toBe('2026-07-11T12:00')
        ->and($reading->interval)->toBe(900)
        ->and($reading->get(CurrentVariable::Temperature2m))->toBe(21.5)
        ->and($reading->get('wind_direction_10m')?->label())->toBe('E');
});

it('returns null for absent current values', function (): void {
    $reading = new CurrentReading(
        time: new DateTimeImmutable('2026-07-11T12:00'),
        interval: null,
        values: [],
    );

    expect($reading->get(CurrentVariable::Temperature2m))->toBeNull();
});
