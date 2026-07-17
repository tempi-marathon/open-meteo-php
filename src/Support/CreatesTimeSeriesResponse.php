<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\AirQualityResponseCollection;
use TempiMarathon\OpenMeteo\Data\AirQualityUnits;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponseCollection;
use TempiMarathon\OpenMeteo\Data\CoordinateMetadata;
use TempiMarathon\OpenMeteo\Data\CoordinateResponseCollection;
use TempiMarathon\OpenMeteo\Data\DailyUnits;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponseCollection;
use TempiMarathon\OpenMeteo\Data\EnsembleUnits;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponseCollection;
use TempiMarathon\OpenMeteo\Data\HistoricalUnits;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponseCollection;
use TempiMarathon\OpenMeteo\Data\MarineUnits;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponseCollection;
use TempiMarathon\OpenMeteo\Data\SeasonalUnits;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastSegmentException;
use TempiMarathon\OpenMeteo\Exceptions\UnsupportedResponseClassException;

trait CreatesTimeSeriesResponse
{
    use BuildsSeries;

    /**
     * @param  array<int|string, mixed>  $data
     * @param  class-string<CoordinateResponse>  $responseClass
     */
    protected function createTimeSeriesResponseFromPayload(
        array $data,
        string $responseClass,
    ): CoordinateResponse {
        return match ($responseClass) {
            ForecastResponse::class => $this->createForecastResponseFromPayload($data),
            HistoricalResponse::class => $this->createHistoricalResponseFromPayload($data),
            AirQualityResponse::class => $this->createAirQualityResponseFromPayload($data),
            MarineResponse::class => $this->createMarineResponseFromPayload($data),
            ClimateResponse::class => $this->createClimateResponseFromPayload($data),
            FloodResponse::class => $this->createFloodResponseFromPayload($data),
            EnsembleResponse::class => $this->createEnsembleResponseFromPayload($data),
            SeasonalResponse::class => $this->createSeasonalResponseFromPayload($data),
            default => throw new UnsupportedResponseClassException($responseClass),
        };
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createForecastResponseFromPayload(array $data): ForecastResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new ForecastResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            minutely15: $this->createMinutely15Series($this->seriesPayload($data, 'minutely_15')),
            current: $this->createCurrentSeries($this->currentPayload($data)),
            units: new ForecastUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
                currentUnits: $this->unitsPayload($data, 'current_units'),
                minutely15Units: $this->unitsPayload($data, 'minutely_15_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createHistoricalResponseFromPayload(array $data): HistoricalResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new HistoricalResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            units: new HistoricalUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createAirQualityResponseFromPayload(array $data): AirQualityResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new AirQualityResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            current: $this->createCurrentSeries($this->currentPayload($data)),
            units: new AirQualityUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                currentUnits: $this->unitsPayload($data, 'current_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createMarineResponseFromPayload(array $data): MarineResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new MarineResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            minutely15: $this->createMinutely15Series($this->seriesPayload($data, 'minutely_15')),
            current: $this->createCurrentSeries($this->currentPayload($data)),
            units: new MarineUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
                currentUnits: $this->unitsPayload($data, 'current_units'),
                minutely15Units: $this->unitsPayload($data, 'minutely_15_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createClimateResponseFromPayload(array $data): ClimateResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new ClimateResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            units: new DailyUnits(
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createFloodResponseFromPayload(array $data): FloodResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new FloodResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            units: new DailyUnits(
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createEnsembleResponseFromPayload(array $data): EnsembleResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new EnsembleResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            units: new EnsembleUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createSeasonalResponseFromPayload(array $data): SeasonalResponse
    {
        $root = $this->coordinateRootFromPayload($data);

        return new SeasonalResponse(
            latitude: $root['latitude'],
            longitude: $root['longitude'],
            timezone: $root['timezone'],
            metadata: $root['metadata'],
            hourly: $this->createHourlySeries($this->seriesPayload($data, 'hourly')),
            daily: $this->createDailySeries($this->seriesPayload($data, 'daily')),
            weekly: $this->createWeeklySeries($this->seriesPayload($data, 'weekly')),
            monthly: $this->createMonthlySeries($this->seriesPayload($data, 'monthly')),
            units: new SeasonalUnits(
                hourlyUnits: $this->unitsPayload($data, 'hourly_units'),
                dailyUnits: $this->unitsPayload($data, 'daily_units'),
                weeklyUnits: $this->unitsPayload($data, 'weekly_units'),
                monthlyUnits: $this->unitsPayload($data, 'monthly_units'),
            ),
        );
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function createForecastResponseCollectionFromPayload(array $data): ForecastResponseCollection
    {
        /** @var ForecastResponseCollection */
        return $this->createResponseCollectionFromPayload($data, ForecastResponse::class);
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @param  class-string<CoordinateResponse>  $responseClass
     */
    protected function createResponseCollectionFromPayload(
        array $data,
        string $responseClass,
    ): CoordinateResponseCollection {
        return match ($responseClass) {
            ForecastResponse::class => new ForecastResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, ForecastResponse::class),
            ),
            HistoricalResponse::class => new HistoricalResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, HistoricalResponse::class),
            ),
            AirQualityResponse::class => new AirQualityResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, AirQualityResponse::class),
            ),
            MarineResponse::class => new MarineResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, MarineResponse::class),
            ),
            ClimateResponse::class => new ClimateResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, ClimateResponse::class),
            ),
            FloodResponse::class => new FloodResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, FloodResponse::class),
            ),
            EnsembleResponse::class => new EnsembleResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, EnsembleResponse::class),
            ),
            SeasonalResponse::class => new SeasonalResponseCollection(
                $this->collectTypedTimeSeriesResponses($data, SeasonalResponse::class),
            ),
            default => throw new UnsupportedResponseClassException($responseClass),
        };
    }

    /**
     * @template T of CoordinateResponse
     *
     * @param  array<int|string, mixed>  $data
     * @param  class-string<T>  $responseClass
     * @return list<T>
     */
    protected function collectTypedTimeSeriesResponses(array $data, string $responseClass): array
    {
        /** @var list<T> */
        return $this->collectTimeSeriesResponsesFromPayload($data, $responseClass);
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @param  class-string<CoordinateResponse>  $responseClass
     */
    protected function createTimeSeriesResponseCollectionFromPayload(
        array $data,
        string $responseClass,
    ): CoordinateResponseCollection {
        return $this->createResponseCollectionFromPayload($data, $responseClass);
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @param  class-string<CoordinateResponse>  $responseClass
     * @return list<CoordinateResponse>
     */
    protected function collectTimeSeriesResponsesFromPayload(array $data, string $responseClass): array
    {
        if ($this->isSegmentedCoordinatePayload($data)) {
            $responses = [];
            foreach ($data as $segment) {
                if (! is_array($segment)) {
                    throw new InvalidForecastSegmentException;
                }

                $responses[] = $this->createTimeSeriesResponseFromPayload($segment, $responseClass);
            }

            return $responses;
        }

        return [$this->createTimeSeriesResponseFromPayload($data, $responseClass)];
    }

    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function isSegmentedCoordinatePayload(array $data): bool
    {
        return isset($data[0]) && is_array($data[0]);
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @return array{latitude: float, longitude: float, timezone: string, metadata: CoordinateMetadata}
     */
    private function coordinateRootFromPayload(array $data): array
    {
        return [
            'latitude' => Coerce::toFloat($data['latitude'] ?? null),
            'longitude' => Coerce::toFloat($data['longitude'] ?? null),
            'timezone' => Coerce::toString($data['timezone'] ?? null),
            'metadata' => CoordinateMetadata::fromPayload($data),
        ];
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @return array<string, list<int|float|string|null>>
     */
    private function seriesPayload(array $data, string $key): array
    {
        if (! isset($data[$key]) || ! is_array($data[$key])) {
            return [];
        }

        $series = [];
        /** @var mixed $column */
        foreach ($data[$key] as $name => $column) {
            $series[Coerce::toString($name)] = Coerce::toSeriesColumn($column);
        }

        return $series;
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @return array<string, int|float|string|null>
     */
    private function currentPayload(array $data): array
    {
        if (! isset($data['current']) || ! is_array($data['current'])) {
            return [];
        }

        $current = [];
        /** @var mixed $value */
        foreach ($data['current'] as $name => $value) {
            $current[Coerce::toString($name)] = Coerce::toSeriesValue($value);
        }

        return $current;
    }

    /**
     * @param  array<int|string, mixed>  $data
     * @return array<string, string>
     */
    private function unitsPayload(array $data, string $key): array
    {
        if (! isset($data[$key]) || ! is_array($data[$key])) {
            return [];
        }

        $units = [];
        /** @var mixed $value */
        foreach ($data[$key] as $name => $value) {
            $units[Coerce::toString($name)] = Coerce::toString($value);
        }

        return $units;
    }
}
