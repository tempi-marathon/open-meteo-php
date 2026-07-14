<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class EnsembleUnits
{
    use ResolvesVariableUnits;

    /**
     * @param  array<string, string>  $hourlyUnits
     * @param  array<string, string>  $dailyUnits
     */
    public function __construct(
        public array $hourlyUnits = [],
        public array $dailyUnits = [],
    ) {}

    public function hourlyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->hourlyUnits, $variable);
    }

    public function dailyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->dailyUnits, $variable);
    }
}
