<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;

final class SeriesValues
{
    /**
     * @param  array<string, mixed>  $values
     */
    public static function get(array $values, BackedEnum|string $variable): mixed
    {
        foreach (ConvertsApiKeys::candidateKeys($variable) as $key) {
            if (array_key_exists($key, $values)) {
                return $values[$key];
            }
        }

        return null;
    }
}
