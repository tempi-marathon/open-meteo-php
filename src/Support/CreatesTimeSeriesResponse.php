<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\TimeSeriesResponse;

use function Psl\Type\float;
use function Psl\Type\mixed_dict;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Vec\map;
use function Psl\Vec\values;

trait CreatesTimeSeriesResponse
{
    /**
     * @template T of TimeSeriesResponse
     *
     * @param  array<int|string, mixed>  $data
     * @param  class-string<T>  $responseClass
     * @return T
     */
    protected function createTimeSeriesResponseFromPayload(
        array $data,
        string $responseClass = TimeSeriesResponse::class,
    ): TimeSeriesResponse {
        $root = shape([
            'latitude' => float(),
            'longitude' => float(),
            'timezone' => string(),
        ])->coerce($data);

        /** @var array<string, list<int|float|string|null>> $hourly */
        $hourly = isset($data['hourly']) && is_array($data['hourly'])
            ? mixed_dict()->coerce($data['hourly'])
            : [];
        /** @var array<string, list<int|float|string|null>> $daily */
        $daily = isset($data['daily']) && is_array($data['daily'])
            ? mixed_dict()->coerce($data['daily'])
            : [];
        /** @var array<string, string> $hourlyUnits */
        $hourlyUnits = isset($data['hourly_units']) && is_array($data['hourly_units'])
            ? mixed_dict()->coerce($data['hourly_units'])
            : [];
        /** @var array<string, string> $dailyUnits */
        $dailyUnits = isset($data['daily_units']) && is_array($data['daily_units'])
            ? mixed_dict()->coerce($data['daily_units'])
            : [];

        return new $responseClass(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            hourly: $hourly,
            daily: $daily,
            units: new ForecastUnits(hourlyUnits: $hourlyUnits, dailyUnits: $dailyUnits),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createForecastResponseFromPayload(array $data): ForecastResponse
    {
        return $this->createTimeSeriesResponseFromPayload($data, ForecastResponse::class);
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createForecastResponseCollectionFromPayload(array $data): ForecastResponseCollection
    {
        if (isset($data[0]) && is_array($data[0])) {
            return new ForecastResponseCollection(
                map(
                    values($data),
                    function (mixed $segment): ForecastResponse {
                        if (! is_array($segment)) {
                            throw new \UnexpectedValueException('Expected forecast segment to be an array.');
                        }

                        return $this->createForecastResponseFromPayload($segment);
                    },
                ),
            );
        }

        return new ForecastResponseCollection([$this->createForecastResponseFromPayload($data)]);
    }
}
