<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

final readonly class HourlyReading
{
    public function __construct(
        public DateTimeImmutable $datetime,
        public ?WeatherCode $weatherCode = null,
        public ?float $temperature2m = null,
        public ?float $apparentTemperature = null,
        public ?float $windSpeed10m = null,
        public ?int $windDirection10m = null,
        public ?float $precipitation = null,
        public ?bool $isDay = null,
    ) {}
}
