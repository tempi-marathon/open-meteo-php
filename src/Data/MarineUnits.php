<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class MarineUnits
{
    use ResolvesVariableUnits;

    /**
     * @param  array<string, string>  $hourlyUnits
     * @param  array<string, string>  $currentUnits
     * @param  array<string, string>  $minutely15Units
     */
    public function __construct(
        public array $hourlyUnits = [],
        public array $currentUnits = [],
        public array $minutely15Units = [],
    ) {}

    public function hourlyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->hourlyUnits, $variable);
    }

    public function currentUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->currentUnits, $variable);
    }

    public function minutely15Unit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->minutely15Units, $variable);
    }
}
