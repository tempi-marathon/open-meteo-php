<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Support\CoercesVariableValues;
use TempiMarathon\OpenMeteo\WindDirection;

covers(CoercesVariableValues::class);

it('returns null for null values', function (): void {
    expect(CoercesVariableValues::coerce('temperature_2m', null))->toBeNull();
});

it('returns already coerced value objects unchanged', function (): void {
    $direction = WindDirection::fromDegrees(90);
    $weatherCode = WeatherCode::CLEAR;
    $datetime = new DateTimeImmutable('2026-07-11T12:00');

    expect(CoercesVariableValues::coerce('wind_direction_10m', $direction))->toBe($direction)
        ->and(CoercesVariableValues::coerce('weathercode', $weatherCode))->toBe($weatherCode)
        ->and(CoercesVariableValues::coerce('sunrise', $datetime))->toBe($datetime);
});

it('coerces absolute direction fields to wind direction', function (): void {
    $direction = CoercesVariableValues::coerce('wind_direction_80m', 225);

    expect($direction)->toBeInstanceOf(WindDirection::class)
        ->and($direction?->getRaw())->toBe(225)
        ->and($direction?->label())->toBe('SW');
});

it('coerces float direction degrees', function (): void {
    $direction = CoercesVariableValues::coerce('wave_direction', 90.0);

    expect($direction)->toBeInstanceOf(WindDirection::class)
        ->and($direction?->getRaw())->toBe(90.0);
});

it('returns null for non-numeric direction values', function (): void {
    expect(CoercesVariableValues::coerce('wind_direction_10m', 'invalid'))->toBeNull()
        ->and(CoercesVariableValues::coerce('ocean_current_direction', []))->toBeNull();
});

it('keeps direction anomaly fields as numeric floats', function (): void {
    expect(CoercesVariableValues::coerce('wind_direction_10m_anomaly', 12.5))->toBe(12.5);
});

it('coerces weather codes from int and float', function (): void {
    expect(CoercesVariableValues::coerce('weathercode', 0))->toBe(WeatherCode::CLEAR)
        ->and(CoercesVariableValues::coerce('weather_code', 3.0))->toBe(WeatherCode::CLOUDY);
});

it('returns null for invalid weather code values', function (): void {
    expect(CoercesVariableValues::coerce('weathercode', 'invalid'))->toBeNull()
        ->and(CoercesVariableValues::coerce('weathercode', 999))->toBeNull()
        ->and(CoercesVariableValues::coerce('weather_code', true))->toBeNull();
});

it('coerces is_day to bool', function (): void {
    expect(CoercesVariableValues::coerce('is_day', 1))->toBeTrue()
        ->and(CoercesVariableValues::coerce('is_day', 0))->toBeFalse()
        ->and(CoercesVariableValues::coerce('is_day', '1'))->toBeTrue();
});

it('returns null for invalid is_day values', function (): void {
    expect(CoercesVariableValues::coerce('is_day', true))->toBeNull()
        ->and(CoercesVariableValues::coerce('is_day', []))->toBeNull();
});

it('coerces sunrise and sunset to datetimes', function (): void {
    $sunrise = CoercesVariableValues::coerce('sunrise', '2026-07-11T06:00');

    expect($sunrise)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($sunrise?->format('Y-m-d\TH:i'))->toBe('2026-07-11T06:00')
        ->and(CoercesVariableValues::coerce('sunset', '2026-07-11T21:00'))->toBeInstanceOf(DateTimeImmutable::class);
});

it('coerces generic numeric values to float', function (): void {
    expect(CoercesVariableValues::coerce('temperature_2m', 21))->toBe(21.0)
        ->and(CoercesVariableValues::coerce('temperature_2m', 21.2))->toBe(21.2);
});

it('passes through bool and string scalars', function (): void {
    expect(CoercesVariableValues::coerce('enabled', true))->toBeTrue()
        ->and(CoercesVariableValues::coerce('timezone', 'Europe/Amsterdam'))->toBe('Europe/Amsterdam');
});

it('returns null for unsupported value types', function (): void {
    expect(CoercesVariableValues::coerce('temperature_2m', ['unexpected' => true]))->toBeNull()
        ->and(CoercesVariableValues::coerce('temperature_2m', new stdClass))->toBeNull();
});
