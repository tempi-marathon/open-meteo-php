<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Support\ParsesHourlyReadings;

final readonly class ForecastResponse
{
    use ParsesHourlyReadings;

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

    public function hourlyReadings(): HourlyReadingCollection
    {
        return $this->createHourlyReadingCollection($this->hourly);
    }
}
