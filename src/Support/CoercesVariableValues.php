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
    /**
     * @return float|int|bool|string|WindDirection|WeatherCode|DateTimeImmutable|null
     */
    public static function coerce(string $key, mixed $value): float|int|bool|string|WindDirection|WeatherCode|DateTimeImmutable|null
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof WindDirection || $value instanceof WeatherCode || $value instanceof DateTimeImmutable) {
            return $value;
        }

        if (WrapsWindDirections::isAbsoluteDirectionField($key)) {
            if (is_int($value) || is_float($value)) {
                return WindDirection::fromDegrees($value);
            }

            if ($value instanceof WindDirection) {
                return $value;
            }

            return is_string($value) ? $value : null;
        }

        if ($key === 'weathercode' || $key === 'weather_code') {
            if (! is_int($value)) {
                return null;
            }

            return WeatherCode::from($value);
        }

        if ($key === 'is_day') {
            if (! is_int($value) && ! is_float($value) && ! is_string($value)) {
                return null;
            }

            return (bool) int()->coerce($value);
        }

        if (in_array($key, ['sunrise', 'sunset', 'time'], true) && is_string($value)) {
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
}
