<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Support\BuildsCoordinateQuery;

covers(BuildsCoordinateQuery::class);

it('builds coordinate query parameters', function (): void {
    $builder = new class
    {
        use BuildsCoordinateQuery;

        /** @return array<string, string> */
        public function build(float $latitude, float $longitude): array
        {
            return $this->coordinateQuery($latitude, $longitude);
        }
    };

    expect($builder->build(52.37, 4.89))->toBe([
        'latitude' => '52.37',
        'longitude' => '4.89',
    ]);
});

it('builds coordinate query parameters with timezone', function (): void {
    $builder = new class
    {
        use BuildsCoordinateQuery;

        /** @return array<string, string> */
        public function build(float $latitude, float $longitude, Timezone $timezone): array
        {
            return $this->coordinateQueryWithTimezone($latitude, $longitude, $timezone);
        }
    };

    expect($builder->build(52.37, 4.89, Timezone::EuropeAmsterdam))->toBe([
        'latitude' => '52.37',
        'longitude' => '4.89',
        'timezone' => 'Europe/Amsterdam',
    ]);
});

it('appends optional date range parameters', function (): void {
    $builder = new class
    {
        use BuildsCoordinateQuery;

        /** @return array<string, string> */
        public function build(?DateTimeInterface $startDate, ?DateTimeInterface $endDate): array
        {
            return $this->withDateRange(
                $this->coordinateQuery(52.37, 4.89),
                $startDate,
                $endDate,
            );
        }
    };

    expect($builder->build(null, null))->toBe([
        'latitude' => '52.37',
        'longitude' => '4.89',
    ])->and($builder->build(
        new DateTimeImmutable('2026-07-01'),
        new DateTimeImmutable('2026-07-07'),
    ))->toBe([
        'latitude' => '52.37',
        'longitude' => '4.89',
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-07',
    ]);
});

it('appends only provided date range boundaries', function (): void {
    $builder = new class
    {
        use BuildsCoordinateQuery;

        /** @return array<string, string> */
        public function build(?DateTimeInterface $startDate, ?DateTimeInterface $endDate): array
        {
            return $this->withDateRange(['latitude' => '52.37'], $startDate, $endDate);
        }
    };

    expect($builder->build(new DateTimeImmutable('2026-07-01'), null))->toBe([
        'latitude' => '52.37',
        'start_date' => '2026-07-01',
    ])->and($builder->build(null, new DateTimeImmutable('2026-07-07')))->toBe([
        'latitude' => '52.37',
        'end_date' => '2026-07-07',
    ]);
});
