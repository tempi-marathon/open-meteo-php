<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class DailyUnits
{
    use ResolvesVariableUnits;

    /** @param  array<string, string>  $dailyUnits */
    public function __construct(public array $dailyUnits = []) {}

    public function dailyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->dailyUnits, $variable);
    }
}
