<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\WindDirection;

covers(WindDirection::class);

it('returns null from tryFrom when degrees are null', function (): void {
    expect(WindDirection::tryFrom(null))->toBeNull();
});

it('labels cardinal wind directions', function (int|float $degrees, string $label): void {
    $direction = WindDirection::fromDegrees($degrees);

    expect($direction->label())->toBe($label)
        ->and($direction->getRaw())->toBe($degrees)
        ->and((string) $direction)->toBe($label);
})->with([
    'north at 0' => [0, 'N'],
    'north at 360' => [360, 'N'],
    'east' => [90, 'E'],
    'south' => [180, 'S'],
    'west' => [270, 'W'],
    'northeast' => [45, 'NE'],
]);

it('normalizes out-of-range degrees', function (int|float $degrees, string $label): void {
    expect(WindDirection::fromDegrees($degrees)->label())->toBe($label);
})->with([
    'negative west' => [-90, 'W'],
    'wrapped north' => [720, 'N'],
    'just past north' => [380, 'NNE'],
]);

it('rounds to the nearest compass point at boundaries', function (): void {
    expect(WindDirection::fromDegrees(11)->label())->toBe('N')
        ->and(WindDirection::fromDegrees(12)->label())->toBe('NNE');
});

it('accepts float degrees from the api', function (): void {
    $direction = WindDirection::fromDegrees(89.7);

    expect($direction->getRaw())->toBe(89.7)
        ->and($direction->label())->toBe('E');
});
