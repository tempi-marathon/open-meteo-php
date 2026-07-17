<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Exceptions\MalformedPayloadException;

/**
 * Minimal, dependency-free coercion helpers for Open-Meteo JSON payloads.
 *
 * These mirror the strict "coerce or throw" behaviour the SDK relies on when
 * turning decoded JSON into typed value objects.
 */
final class Coerce
{
    public static function toFloat(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        throw new MalformedPayloadException(
            sprintf('Expected a float value, got %s.', get_debug_type($value)),
        );
    }

    public static function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value) && (float) (int) $value === $value) {
            return (int) $value;
        }

        if (is_string($value) && is_numeric($value) && (float) (int) $value === (float) $value) {
            return (int) $value;
        }

        throw new MalformedPayloadException(
            sprintf('Expected an int value, got %s.', get_debug_type($value)),
        );
    }

    public static function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        throw new MalformedPayloadException(
            sprintf('Expected a string value, got %s.', get_debug_type($value)),
        );
    }

    /**
     * Coerce a single time-series cell to the union the API can return.
     */
    public static function toSeriesValue(mixed $value): int|float|string|null
    {
        if ($value === null || is_int($value) || is_float($value) || is_string($value)) {
            return $value;
        }

        throw new MalformedPayloadException(
            sprintf('Expected a scalar series value, got %s.', get_debug_type($value)),
        );
    }

    /**
     * Coerce a full time-series column into a list of scalar cells.
     *
     * @return list<int|float|string|null>
     */
    public static function toSeriesColumn(mixed $value): array
    {
        if (! is_array($value)) {
            throw new MalformedPayloadException(
                sprintf('Expected a series column array, got %s.', get_debug_type($value)),
            );
        }

        return array_values(array_map(
            static fn (mixed $cell): int|float|string|null => self::toSeriesValue($cell),
            $value,
        ));
    }

    /**
     * @return list<float>
     */
    public static function toFloatList(mixed $value): array
    {
        if (! is_array($value)) {
            throw new MalformedPayloadException(
                sprintf('Expected a list of floats, got %s.', get_debug_type($value)),
            );
        }

        return array_values(array_map(
            static fn (mixed $item): float => self::toFloat($item),
            $value,
        ));
    }

    /**
     * @return list<string>
     */
    public static function toStringList(mixed $value): array
    {
        if (! is_array($value)) {
            throw new MalformedPayloadException(
                sprintf('Expected a list of strings, got %s.', get_debug_type($value)),
            );
        }

        return array_values(array_map(
            static fn (mixed $item): string => self::toString($item),
            $value,
        ));
    }
}
