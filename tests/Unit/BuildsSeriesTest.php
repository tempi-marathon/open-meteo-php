<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Data\DailySeries;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Data\Minutely15Series;
use TempiMarathon\OpenMeteo\Data\MonthlySeries;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Exceptions\MissingCurrentTimeException;
use TempiMarathon\OpenMeteo\Exceptions\MissingSeriesTimeException;
use TempiMarathon\OpenMeteo\Support\BuildsSeries;
use TempiMarathon\OpenMeteo\WindDirection;

covers(BuildsSeries::class);

it('builds hourly, daily, minutely 15, and monthly series from payload arrays', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, list<int|float|string|null>> $payload */
        public function hourly(array $payload): HourlySeries
        {
            return $this->createHourlySeries($payload);
        }

        /** @param array<string, list<int|float|string|null>> $payload */
        public function daily(array $payload): DailySeries
        {
            return $this->createDailySeries($payload);
        }

        /** @param array<string, list<int|float|string|null>> $payload */
        public function minutely15(array $payload): Minutely15Series
        {
            return $this->createMinutely15Series($payload);
        }

        /** @param array<string, list<int|float|string|null>> $payload */
        public function monthly(array $payload): MonthlySeries
        {
            return $this->createMonthlySeries($payload);
        }
    };

    $hourlyPayload = [
        'time' => ['2026-07-06T10:00', '2026-07-06T11:00'],
        'temperature_2m' => [16.0, 17.0],
        'weather_code' => [0, 3],
    ];

    $hourly = $builder->hourly($hourlyPayload);

    expect($hourly->count())->toBe(2)
        ->and($hourly->at(0)?->get('temperature_2m'))->toBe(16.0)
        ->and($hourly->at(0)?->get('weathercode'))->toBe(WeatherCode::CLEAR)
        ->and($builder->daily(['time' => ['2026-07-06'], 'temperature_2m_max' => [20.0]])->at(0)?->get('temperature_2m_max'))->toBe(20.0)
        ->and($builder->minutely15(['time' => ['2026-07-06T12:00'], 'temperature_2m' => [20.0]])->at(0)?->get('temperature_2m'))->toBe(20.0)
        ->and($builder->monthly(['time' => ['2026-07-01'], 'temperature_2m_mean' => [18.0]])->at(0)?->get('temperature_2m_mean'))->toBe(18.0);
});

it('builds an empty series from empty payload arrays', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, list<int|float|string|null>> $payload */
        public function hourly(array $payload): HourlySeries
        {
            return $this->createHourlySeries($payload);
        }
    };

    expect($builder->hourly([])->count())->toBe(0);
});

it('builds a current series from a snapshot payload', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, int|float|string|null> $payload */
        public function current(array $payload): CurrentSeries
        {
            return $this->createCurrentSeries($payload);
        }
    };

    $current = $builder->current([
        'time' => '2026-07-11T12:00',
        'interval' => 900,
        'temperature_2m' => 21.5,
        'wind_direction_10m' => 90,
    ]);

    expect($current->count())->toBe(1)
        ->and($current->first()?->datetime->format('Y-m-d\TH:i'))->toBe('2026-07-11T12:00')
        ->and($current->first()?->interval)->toBe(900)
        ->and($current->first()?->get('temperature_2m'))->toBe(21.5)
        ->and($current->first()?->get('wind_direction_10m'))->toBeInstanceOf(WindDirection::class);
});

it('returns an empty current series when no snapshot is present', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, int|float|string|null> $payload */
        public function current(array $payload): CurrentSeries
        {
            return $this->createCurrentSeries($payload);
        }
    };

    expect($builder->current([])->count())->toBe(0);
});

it('requires series payloads to include a time array', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, list<int|float|string|null>> $payload */
        public function hourly(array $payload): HourlySeries
        {
            return $this->createHourlySeries($payload);
        }
    };

    expect(fn () => $builder->hourly(['temperature_2m' => [16.0]]))
        ->toThrow(MissingSeriesTimeException::class);
});

it('requires current payloads to include a time value', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, int|float|string|null> $payload */
        public function current(array $payload): CurrentSeries
        {
            return $this->createCurrentSeries($payload);
        }
    };

    expect(fn () => $builder->current(['temperature_2m' => 21.5]))
        ->toThrow(MissingCurrentTimeException::class);
});

it('normalizes legacy api keys while building series points', function (): void {
    $builder = new class
    {
        use BuildsSeries;

        /** @param array<string, list<int|float|string|null>> $payload */
        public function hourly(array $payload): HourlySeries
        {
            return $this->createHourlySeries($payload);
        }
    };

    $point = $builder->hourly([
        'time' => ['2026-07-06T12:00'],
        'wind_speed_10m' => [3.0],
        'wind_direction_10m' => [180],
        'weather_code' => [61],
    ])->at(0);

    expect($point?->get('windspeed_10m'))->toBe(3.0)
        ->and($point?->get('winddirection_10m'))->toBeInstanceOf(WindDirection::class)
        ->and($point?->get('weathercode'))->toBe(WeatherCode::LIGHT_RAIN);
});
