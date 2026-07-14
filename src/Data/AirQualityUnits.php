<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use TempiMarathon\OpenMeteo\Support\ResolvesVariableUnits;

final readonly class AirQualityUnits
{
    use ResolvesVariableUnits;

    /**
     * @param  array<string, string>  $hourlyUnits
     * @param  array<string, string>  $currentUnits
     */
    public function __construct(
        public array $hourlyUnits = [],
        public array $currentUnits = [],
    ) {}

    public function hourlyUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->hourlyUnits, $variable);
    }

    public function currentUnit(BackedEnum|string $variable): ?string
    {
        return self::unitFrom($this->currentUnits, $variable);
    }
}
