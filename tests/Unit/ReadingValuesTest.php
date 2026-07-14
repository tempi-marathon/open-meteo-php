<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Support\ReadingValues;

covers(ReadingValues::class);

it('resolves the first matching candidate key', function (): void {
    $values = [
        'weathercode' => 0,
        'temperature_2m' => 19.0,
    ];

    expect(ReadingValues::get($values, 'weather_code'))->toBe(0)
        ->and(ReadingValues::get($values, 'missing'))->toBeNull();
});
