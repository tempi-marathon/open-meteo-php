<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\WindDirection;

use function Psl\Vec\map;

final class WrapsWindDirections
{
    public static function isAbsoluteDirectionField(string $key): bool
    {
        if (str_contains($key, 'anomaly')) {
            return false;
        }

        return str_contains($key, 'direction');
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $series
     * @return array<string, list<int|float|string|null|WindDirection>>
     */
    public static function wrapDirectionValues(array $series): array
    {
        foreach ($series as $key => $values) {
            if (! self::isAbsoluteDirectionField($key)) {
                continue;
            }

            $series[$key] = map(
                $values,
                static function (int|float|string|null $value): int|float|string|null|WindDirection {
                    if ($value === null) {
                        return null;
                    }

                    if ($value instanceof WindDirection) {
                        return $value;
                    }

                    if (! is_int($value) && ! is_float($value)) {
                        return $value;
                    }

                    return WindDirection::fromDegrees($value);
                },
            );
        }

        return $series;
    }
}
