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
        $clone = clone $this;
        $clone->startDate = $start;
        $clone->endDate = $end;

        return $clone;
    }

    public function timezone(Timezone $timezone): static
    {
        $clone = clone $this;
        $clone->timezone = $timezone;

        return $clone;
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
