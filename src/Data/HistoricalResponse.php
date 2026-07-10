<?php

declare(strict_types=1);

namespace OpenMeteo\Data;

use OpenMeteo\Support\ParsesHourlySlots;

final readonly class HistoricalResponse
{
    use ParsesHourlySlots;

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     * @param  array<string, list<int|float|string|null>>  $daily
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public array $hourly,
        public array $daily,
        public ForecastUnits $units,
    ) {}

    public function hourlySlots(): HourlySlotCollection
    {
        return $this->createHourlySlotCollection($this->hourly);
    }
}
