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
            // @pest-mutate-ignore
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

        if (is_float($value) && self::isWholeNumber($value)) {
            return (int) $value;
        }

        if (is_string($value) && is_numeric($value) && self::isWholeNumber((float) $value)) {
            return (int) $value;
        }

        throw new MalformedPayloadException(
            sprintf('Expected an int value, got %s.', get_debug_type($value)),
        );
    }

    /**
     * Whether a float is a whole number that fits losslessly in PHP's int range.
     *
     * Casting to int truncates the fractional part and clamps out-of-range
     * values, so a value only survives the round-trip unchanged when it is both
     * integral (e.g. 1234.0, not 1234.5) and within int bounds.
     */
    private static function isWholeNumber(float $value): bool
    {
        return (float) (int) $value === $value;
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

        $column = [];
        /** @var mixed $cell */
        foreach ($value as $cell) {
            $column[] = self::toSeriesValue($cell);
        }

        return $column;
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

        $floats = [];
        /** @var mixed $item */
        foreach ($value as $item) {
            $floats[] = self::toFloat($item);
        }

        return $floats;
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

        $strings = [];
        /** @var mixed $item */
        foreach ($value as $item) {
            $strings[] = self::toString($item);
        }

        return $strings;
    }
}
