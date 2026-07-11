<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;

use function Psl\Type\float;
use function Psl\Type\mixed_dict;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Vec\map;
use function Psl\Vec\values;

trait CreatesForecastResponse
{
    use ParsesHourlyReadings;

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createForecastResponseFromPayload(array $data): ForecastResponse
    {
        $root = shape([
            'latitude' => float(),
            'longitude' => float(),
            'timezone' => string(),
            'hourly' => mixed_dict(),
            'daily' => mixed_dict(),
            'hourly_units' => mixed_dict(),
            'daily_units' => mixed_dict(),
        ])->coerce($data);

        /** @var array<string, list<int|float|string|null>> $hourly */
        $hourly = $root['hourly'];
        /** @var array<string, list<int|float|string|null>> $daily */
        $daily = $root['daily'];
        /** @var array<string, string> $hourlyUnits */
        $hourlyUnits = $root['hourly_units'];
        /** @var array<string, string> $dailyUnits */
        $dailyUnits = $root['daily_units'];

        return new ForecastResponse(
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
