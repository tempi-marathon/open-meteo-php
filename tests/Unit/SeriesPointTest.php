<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\SeriesPoint;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\WindDirection;

covers(SeriesPoint::class);

it('reads values by enum, api key, and legacy aliases', function (): void {
    $reading = new SeriesPoint(
        datetime: new DateTimeImmutable('2026-07-11T00:00'),
        values: [
            'temperature_2m' => 21.2,
            'weathercode' => WeatherCode::CLEAR,
            'winddirection_10m' => WindDirection::fromDegrees(35),
        ],
    );

    expect($reading->get(HourlyVariable::Temperature2m))->toBe(21.2)
        ->and($reading->get('temperature_2m'))->toBe(21.2)
        ->and($reading->get('weather_code'))->toBe(WeatherCode::CLEAR)
        ->and($reading->get('wind_direction_10m'))->toBeInstanceOf(WindDirection::class)
        ->and($reading->get('wind_direction_10m')?->label())->toBe('NE');
});

it('stores an optional interval on series points', function (): void {
    $point = new SeriesPoint(
        datetime: new DateTimeImmutable('2026-07-11T12:00'),
        values: ['temperature_2m' => 21.5],
        interval: 900,
    );

    expect($point->interval)->toBe(900);
});

it('returns null for absent values', function (): void {
    $reading = new SeriesPoint(
        datetime: new DateTimeImmutable('2026-07-11T00:00'),
        values: ['temperature_2m' => 18.0],
    );

    expect($reading->get('precipitation'))->toBeNull()
        ->and($reading->get(HourlyVariable::WeatherCode))->toBeNull();
});
