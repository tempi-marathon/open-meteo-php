<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Data\HourlyReading;
use TempiMarathon\OpenMeteo\Data\HourlyReadingCollection;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

use function Psl\Type\float;
use function Psl\Type\int;
use function Psl\Type\string;
use function Psl\Type\vec;
use function Psl\Vec\enumerate;
use function Psl\Vec\map;

trait ParsesHourlyReadings
{
    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     */
    protected function createHourlyReadingCollection(array $hourly): HourlyReadingCollection
    {
        if ($hourly === []) {
            return new HourlyReadingCollection([]);
        }

        $hourly = $this->normalizeHourlyKeys($hourly);

        if (! isset($hourly['time'])) {
            throw new \InvalidArgumentException('Hourly data must contain a time array.');
        }

        $times = vec(string())->coerce($hourly['time']);

        $readings = map(
            enumerate($times),
            fn (array $entry): HourlyReading => new HourlyReading(
                datetime: new DateTimeImmutable($entry[1]),
                weatherCode: $this->weatherCodeAt($hourly, $entry[0]),
                temperature2m: $this->floatAt($hourly, 'temperature_2m', $entry[0]),
                apparentTemperature: $this->floatAt($hourly, 'apparent_temperature', $entry[0]),
                windSpeed10m: $this->floatAt($hourly, 'windspeed_10m', $entry[0]),
                windDirection10m: $this->intAt($hourly, 'winddirection_10m', $entry[0]),
                precipitation: $this->floatAt($hourly, 'precipitation', $entry[0]),
                isDay: $this->boolAt($hourly, 'is_day', $entry[0]),
            ),
        );

        return new HourlyReadingCollection($readings);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     */
    private function weatherCodeAt(array $hourly, int $index): ?WeatherCode
    {
        if (! isset($hourly['weathercode'][$index])) {
            return null;
        }

        $value = $hourly['weathercode'][$index];
        if (! is_int($value)) {
            return null;
        }

        return WeatherCode::from($value);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     */
    private function floatAt(array $hourly, string $key, int $index): ?float
    {
        if (! array_key_exists($key, $hourly) || ! array_key_exists($index, $hourly[$key])) {
            return null;
        }

        $value = $hourly[$key][$index];
        if ($value === null) {
            return null;
        }

        return float()->coerce($value);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     */
    private function intAt(array $hourly, string $key, int $index): ?int
    {
        if (! array_key_exists($key, $hourly) || ! array_key_exists($index, $hourly[$key])) {
            return null;
        }

        $value = $hourly[$key][$index];
        if ($value === null) {
            return null;
        }

        return int()->coerce($value);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     */
    private function boolAt(array $hourly, string $key, int $index): ?bool
    {
        if (! array_key_exists($key, $hourly) || ! array_key_exists($index, $hourly[$key])) {
            return null;
        }

        $value = $hourly[$key][$index];
        if ($value === null) {
            return null;
        }

        return (bool) int()->coerce($value);
    }

    /**
     * @param  array<string, list<int|float|string|null>>  $hourly
     * @return array<string, list<int|float|string|null>>
     */
    private function normalizeHourlyKeys(array $hourly): array
    {
        $aliases = [
            'weather_code' => 'weathercode',
            'wind_speed_10m' => 'windspeed_10m',
            'wind_direction_10m' => 'winddirection_10m',
        ];

        foreach ($aliases as $from => $to) {
            if (isset($hourly[$from]) && ! isset($hourly[$to])) {
                $hourly[$to] = $hourly[$from];
            }
        }

        return $hourly;
    }
}
