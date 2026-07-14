<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class SeasonalUnits
{
    use ResolvesVariableUnits;

    /**
     * @param  array<string, string>  $dailyUnits
     * @param  array<string, string>  $monthlyUnits
     */
    public function __construct(
        public array $dailyUnits = [],
        public array $monthlyUnits = [],
    ) {}

    public function dailyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->dailyUnits, $variable);
    }

    public function monthlyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->monthlyUnits, $variable);
    }
}
