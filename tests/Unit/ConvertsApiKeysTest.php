<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Support\ConvertsApiKeys;

enum MutationTestIntBackedEnum: int
{
    case One = 1;
}

covers(ConvertsApiKeys::class);

it('converts camelCase properties to api keys', function (string $property, string $apiKey): void {
    expect(ConvertsApiKeys::propertyToApiKey($property))->toBe($apiKey);
})->with([
    'temperature' => ['temperature2m', 'temperature_2m'],
    'wind direction' => ['windDirection80m', 'wind_direction_80m'],
    'daily max' => ['temperature2mMax', 'temperature_2m_max'],
    'already snake case' => ['temperature_2m', 'temperature_2m'],
    'PascalCase segment' => ['apparentTemperature', 'apparent_temperature'],
]);

it('builds candidate keys from enums', function (): void {
    expect(ConvertsApiKeys::candidateKeys(HourlyVariable::Temperature2m))->toBe(['temperature_2m']);
});

it('casts int-backed enum values to strings when expanding aliases', function (): void {
    expect(ConvertsApiKeys::candidateKeys(MutationTestIntBackedEnum::One))->toBe(['1']);
});

it('builds candidate keys from api key strings', function (): void {
    expect(ConvertsApiKeys::candidateKeys('temperature_2m'))->toBe(['temperature_2m']);
});

it('builds candidate keys from camelCase names', function (): void {
    expect(ConvertsApiKeys::candidateKeys('windDirection10m'))->toBe([
        'wind_direction_10m',
        'winddirection_10m',
    ]);
});

it('expands weather code aliases in both directions', function (): void {
    expect(ConvertsApiKeys::candidateKeys('weathercode'))->toBe(['weathercode', 'weather_code'])
        ->and(ConvertsApiKeys::candidateKeys('weather_code'))->toBe(['weather_code', 'weathercode']);
});

it('deduplicates alias expansion for weathercode keys', function (): void {
    $keys = ConvertsApiKeys::candidateKeys('weathercode');

    expect($keys)->toHaveCount(2)
        ->and($keys)->toBe(['weathercode', 'weather_code']);
});

it('expands wind speed aliases in both directions', function (): void {
    expect(ConvertsApiKeys::candidateKeys('windspeed_10m'))->toBe(['windspeed_10m', 'wind_speed_10m'])
        ->and(ConvertsApiKeys::candidateKeys('wind_speed_10m'))->toBe(['wind_speed_10m', 'windspeed_10m']);
});

it('expands wind direction canonical aliases in both directions', function (): void {
    expect(ConvertsApiKeys::candidateKeys('winddirection_10m'))->toBe(['winddirection_10m', 'wind_direction_10m'])
        ->and(ConvertsApiKeys::candidateKeys('wind_direction_10m'))->toBe(['wind_direction_10m', 'winddirection_10m']);
});

it('exposes the complete api key alias map', function (): void {
    $aliases = (new ReflectionClass(ConvertsApiKeys::class))->getConstant('ALIASES');

    expect($aliases)->toBe([
        'weathercode' => 'weather_code',
        'windspeed_10m' => 'wind_speed_10m',
        'winddirection_10m' => 'wind_direction_10m',
    ]);
});

it('returns deduplicated list keys from enums', function (): void {
    $keys = ConvertsApiKeys::candidateKeys(HourlyVariable::Temperature2m);

    expect($keys)->toBe(['temperature_2m'])
        ->and(array_is_list($keys))->toBeTrue()
        ->and($keys)->toHaveCount(count(array_unique($keys)));
});

it('returns deduplicated list keys from aliased api names', function (): void {
    $keys = ConvertsApiKeys::candidateKeys('weather_code');

    expect($keys)->toBe(['weather_code', 'weathercode'])
        ->and(array_is_list($keys))->toBeTrue()
        ->and($keys)->toHaveCount(2);
});
