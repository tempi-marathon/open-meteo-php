<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Data\HourlyReading;
use TempiMarathon\OpenMeteo\Data\HourlyReadingCollection;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

use function Psl\Type\float;
use function Psl\Type\int;
use function Psl\Type\shape;
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
        $hourly = $this->normalizeHourlyKeys($hourly);

        $coerced = shape([
            'time' => vec(string()),
            'temperature_2m' => vec(float()),
            'apparent_temperature' => vec(float()),
            'precipitation' => vec(float()),
            'weathercode' => vec(int()),
            'windspeed_10m' => vec(float()),
            'winddirection_10m' => vec(int()),
            'is_day' => vec(int()),
        ])->coerce($hourly);

        $readings = map(
            enumerate($coerced['time']),
            static fn (array $entry): HourlyReading => new HourlyReading(
                datetime: new DateTimeImmutable($entry[1]),
                weatherCode: WeatherCode::from($coerced['weathercode'][$entry[0]]),
                temperature2m: $coerced['temperature_2m'][$entry[0]],
                apparentTemperature: $coerced['apparent_temperature'][$entry[0]],
                windSpeed10m: $coerced['windspeed_10m'][$entry[0]],
                windDirection10m: $coerced['winddirection_10m'][$entry[0]],
                precipitation: $coerced['precipitation'][$entry[0]],
                isDay: (bool) $coerced['is_day'][$entry[0]],
            ),
        );

        return new HourlyReadingCollection($readings);
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
