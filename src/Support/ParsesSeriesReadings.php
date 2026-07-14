<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Data\CurrentReading;
use TempiMarathon\OpenMeteo\Data\DailyReadingCollection;
use TempiMarathon\OpenMeteo\Data\HourlyReadingCollection;
use TempiMarathon\OpenMeteo\Data\Minutely15ReadingCollection;
use TempiMarathon\OpenMeteo\Data\MonthlyReadingCollection;
use TempiMarathon\OpenMeteo\Data\SeriesReading;

use function Psl\Type\int;
use function Psl\Type\string;
use function Psl\Type\vec;
use function Psl\Vec\enumerate;
use function Psl\Vec\map;

trait ParsesSeriesReadings
{
    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createHourlyReadingCollection(array $payload): HourlyReadingCollection
    {
        return new HourlyReadingCollection($this->createSeriesReadings($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createDailyReadingCollection(array $payload): DailyReadingCollection
    {
        return new DailyReadingCollection($this->createSeriesReadings($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createMinutely15ReadingCollection(array $payload): Minutely15ReadingCollection
    {
        return new Minutely15ReadingCollection($this->createSeriesReadings($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createMonthlyReadingCollection(array $payload): MonthlyReadingCollection
    {
        return new MonthlyReadingCollection($this->createSeriesReadings($payload));
    }

    /**
     * @param  array<string, int|float|string|null>  $payload
     */
    protected function createCurrentReading(array $payload): ?CurrentReading
    {
        if ($payload === []) {
            return null;
        }

        if (! isset($payload['time']) || ! is_string($payload['time'])) {
            throw new \InvalidArgumentException('Current data must contain a time value.');
        }

        $interval = null;
        if (isset($payload['interval'])) {
            $interval = int()->coerce($payload['interval']);
        }

        $values = [];
        foreach ($payload as $key => $value) {
            if (in_array($key, ['time', 'interval'], true)) {
                continue;
            }

            $values[$key] = CoercesVariableValues::coerce($key, $value);
        }

        return new CurrentReading(
            time: new DateTimeImmutable($payload['time']),
            interval: $interval,
            values: $values,
        );
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     * @return list<SeriesReading>
     */
    private function createSeriesReadings(array $payload): array
    {
        if ($payload === []) {
            return [];
        }

        $payload = $this->normalizeSeriesKeys($payload);

        if (! isset($payload['time'])) {
            throw new \InvalidArgumentException('Series data must contain a time array.');
        }

        $times = vec(string())->coerce($payload['time']);
        $variableKeys = array_values(array_filter(
            array_keys($payload),
            static fn (string $key): bool => $key !== 'time',
        ));

        return map(
            enumerate($times),
            function (array $entry) use ($payload, $variableKeys): SeriesReading {
                $values = [];
                foreach ($variableKeys as $key) {
                    if (! array_key_exists($entry[0], $payload[$key])) {
                        continue;
                    }

                    $values[$key] = CoercesVariableValues::coerce($key, $payload[$key][$entry[0]]);
                }

                return new SeriesReading(
                    datetime: new DateTimeImmutable($entry[1]),
                    values: $values,
                );
            },
        );
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     * @return array<string, list<int|float|string|null>>
     */
    private function normalizeSeriesKeys(array $payload): array
    {
        $aliases = [
            'weather_code' => 'weathercode',
            'wind_speed_10m' => 'windspeed_10m',
            'wind_direction_10m' => 'winddirection_10m',
        ];

        foreach ($aliases as $from => $to) {
            if (isset($payload[$from]) && ! isset($payload[$to])) {
                $payload[$to] = $payload[$from];
                unset($payload[$from]);
            }
        }

        return $payload;
    }
}
