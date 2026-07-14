<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeInterface;
use TempiMarathon\OpenMeteo\Enums\Timezone;

trait BuildsDateAndTimezoneOptions
{
    private Timezone $timezone = Timezone::GMT;

    private ?DateTimeInterface $startDate = null;

    private ?DateTimeInterface $endDate = null;

    public function between(DateTimeInterface $start, DateTimeInterface $end): static
    {
        return clone ($this, [
            'startDate' => $start,
            'endDate' => $end,
        ]);
    }

    public function timezone(Timezone $timezone): static
    {
        return clone ($this, [
            'timezone' => $timezone,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function withDateAndTimezoneQuery(float $latitude, float $longitude): array
    {
        return $this->withDateRange(
            $this->coordinateQueryWithTimezone($latitude, $longitude, $this->timezone),
            $this->startDate,
            $this->endDate,
        );
    }
}
