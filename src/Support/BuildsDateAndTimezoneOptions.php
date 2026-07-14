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
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withDateAndTimezoneQuery(array $query): array
    {
        return $this->withDateRange(
            [
                ...$query,
                'timezone' => $this->timezone->value,
            ],
            $this->startDate,
            $this->endDate,
        );
    }
}
