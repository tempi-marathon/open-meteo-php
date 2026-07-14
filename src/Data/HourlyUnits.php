<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class HourlyUnits
{
    use ResolvesVariableUnits;

    /** @param  array<string, string>  $hourlyUnits */
    public function __construct(public array $hourlyUnits = []) {}

    public function hourlyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->hourlyUnits, $variable);
    }
}
