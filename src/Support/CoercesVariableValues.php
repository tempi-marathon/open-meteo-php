<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\WindDirection;

use function Psl\Type\float;
use function Psl\Type\int;

final class CoercesVariableValues
{
    public static function coerce(string $key, mixed $value): float|bool|string|WindDirection|WeatherCode|DateTimeImmutable|null
    {
        if ($value === null) {
            return null; // @pest-mutate-ignore: RemoveEarlyReturn
        }

        if ($value instanceof WindDirection || $value instanceof WeatherCode || $value instanceof DateTimeImmutable) {
            return $value;
        }

        if (self::isAbsoluteDirectionField($key)) {
            if (is_int($value) || is_float($value)) {
                return WindDirection::fromDegrees($value);
            }

            return null;
        }

        if ($key === 'weathercode' || $key === 'weather_code') {
            if (! is_int($value) && ! is_float($value)) {
                return null;
            }

            return WeatherCode::tryFrom((int) $value);
        }

        if ($key === 'is_day') {
            if (! is_int($value) && ! is_float($value) && ! is_string($value)) {
                return null;
            }

            return (bool) int()->coerce($value);
        }

        if (in_array($key, ['sunrise', 'sunset'], true) && is_string($value)) {
            return new DateTimeImmutable($value);
        }

        if (is_int($value) || is_float($value)) {
            return float()->coerce($value);
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $value;
        }

        return null;
    }

    private static function isAbsoluteDirectionField(string $key): bool
    {
        if (str_contains($key, 'anomaly')) {
            return false;
        }

        return str_contains($key, 'direction');
    }
}
