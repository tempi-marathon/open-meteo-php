<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;

trait ResolvesVariableUnits
{
    /**
     * @param  array<string, string>  $units
     */
    private static function unitFrom(array $units, BackedEnum|string $variable): ?string
    {
        $key = is_string($variable) ? $variable : $variable->value;

        return $units[$key] ?? null;
    }
}
