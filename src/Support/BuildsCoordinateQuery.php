<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeInterface;
use TempiMarathon\OpenMeteo\Enums\Timezone;

trait BuildsCoordinateQuery
{
    /**
     * @return array<string, string>
     */
    protected function coordinateQuery(float $latitude, float $longitude): array
    {
        return [
            'latitude' => (string) $latitude,
            'longitude' => (string) $longitude,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function coordinateQueryWithTimezone(float $latitude, float $longitude, Timezone $timezone): array
    {
        return [
            ...$this->coordinateQuery($latitude, $longitude),
            'timezone' => $timezone->value,
        ];
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withDateRange(array $query, ?DateTimeInterface $startDate, ?DateTimeInterface $endDate): array
    {
        if ($startDate !== null) {
            $query['start_date'] = $startDate->format('Y-m-d');
        }

        if ($endDate !== null) {
            $query['end_date'] = $endDate->format('Y-m-d');
        }

        return $query;
    }
}
