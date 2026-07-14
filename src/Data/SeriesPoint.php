<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Support\SeriesValues;
use TempiMarathon\OpenMeteo\WindDirection;

readonly class SeriesPoint
{
    /**
     * @param  array<string, float|int|bool|string|WindDirection|WeatherCode|DateTimeImmutable|null>  $values
     */
    public function __construct(
        public DateTimeImmutable $datetime,
        private array $values,
        public ?int $interval = null,
    ) {}

    public function get(BackedEnum|string $variable): mixed
    {
        return SeriesValues::get($this->values, $variable);
    }
}
