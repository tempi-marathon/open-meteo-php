<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Data\DailySeries;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Data\Minutely15Series;
use TempiMarathon\OpenMeteo\Data\MonthlySeries;
use TempiMarathon\OpenMeteo\Data\SeriesPoint;
use TempiMarathon\OpenMeteo\Data\WeeklySeries;
use TempiMarathon\OpenMeteo\Exceptions\MissingCurrentTimeException;
use TempiMarathon\OpenMeteo\Exceptions\MissingSeriesTimeException;

trait BuildsSeries
{
    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createHourlySeries(array $payload): HourlySeries
    {
        return new HourlySeries($this->createSeriesPoints($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createDailySeries(array $payload): DailySeries
    {
        return new DailySeries($this->createSeriesPoints($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createMinutely15Series(array $payload): Minutely15Series
    {
        return new Minutely15Series($this->createSeriesPoints($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createMonthlySeries(array $payload): MonthlySeries
    {
        return new MonthlySeries($this->createSeriesPoints($payload));
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     */
    protected function createWeeklySeries(array $payload): WeeklySeries
    {
        return new WeeklySeries($this->createSeriesPoints($payload));
    }

    /**
     * @param  array<string, int|float|string|null>  $payload
     */
    protected function createCurrentSeries(array $payload): CurrentSeries
    {
        if ($payload === []) {
            return new CurrentSeries([]);
        }

        if (! isset($payload['time']) || ! is_string($payload['time'])) {
            throw new MissingCurrentTimeException;
        }

        $interval = null;
        if (isset($payload['interval'])) {
            $interval = Coerce::toInt($payload['interval']);
        }

        $values = [];
        foreach ($payload as $key => $value) {
            if (in_array($key, ['time', 'interval'], true)) {
                continue;
            }

            $values[$key] = CoercesVariableValues::coerce($key, $value);
        }

        return new CurrentSeries([
            new SeriesPoint(
                datetime: new DateTimeImmutable($payload['time']),
                values: $values,
                interval: $interval,
            ),
        ]);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $payload
     * @return list<SeriesPoint>
     */
    private function createSeriesPoints(array $payload): array
    {
        if ($payload === []) {
            return [];
        }

        $payload = $this->normalizeSeriesKeys($payload);

        if (! isset($payload['time'])) {
            throw new MissingSeriesTimeException;
        }

        $times = [];
        foreach ($payload['time'] as $value) {
            $times[] = Coerce::toString($value);
        }

        $variableKeys = array_values(array_filter( // @pest-mutate-ignore: UnwrapArrayFilter, UnwrapArrayValues
            array_keys($payload),
            static fn (string $key): bool => $key !== 'time',
        ));

        $points = [];
        foreach ($times as $index => $time) {
            $values = [];
            foreach ($variableKeys as $key) {
                if (! array_key_exists($index, $payload[$key])) {
                    continue;
                }

                $values[$key] = CoercesVariableValues::coerce($key, $payload[$key][$index]);
            }

            $points[] = new SeriesPoint(
                datetime: new DateTimeImmutable($time),
                values: $values,
            );
        }

        return $points;
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
