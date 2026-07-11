<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

final readonly class HourlyReading
{
    public function __construct(
        public DateTimeImmutable $datetime,
        public WeatherCode $weatherCode,
        public float $temperature2m,
        public float $apparentTemperature,
        public float $windSpeed10m,
        public int $windDirection10m,
        public float $precipitation,
        public bool $isDay,
    ) {}
}
