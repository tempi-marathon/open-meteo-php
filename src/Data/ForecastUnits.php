<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

final readonly class ForecastUnits
{
    /**
     * @param  array<string, string>  $hourlyUnits
     * @param  array<string, string>  $dailyUnits
     */
    public function __construct(
        public array $hourlyUnits,
        public array $dailyUnits,
    ) {}
}
