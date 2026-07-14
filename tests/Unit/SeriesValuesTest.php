<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Support\SeriesValues;

covers(SeriesValues::class);

it('resolves the first matching candidate key', function (): void {
    $values = [
        'weathercode' => 0,
        'temperature_2m' => 19.0,
    ];

    expect(SeriesValues::get($values, 'weather_code'))->toBe(0)
        ->and(SeriesValues::get($values, 'missing'))->toBeNull();
});
