<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Support\ConvertsApiKeys;

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
