<?php

declare(strict_types=1);

namespace OpenMeteo\Data;

final readonly class ForecastUnits
{
    /**
     * @param  array<string, string>  $hourly
     * @param  array<string, string>  $daily
     */
    public function __construct(
        public array $hourly,
        public array $daily,
    ) {}
}
