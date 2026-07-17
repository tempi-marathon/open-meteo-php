<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Connectors\ClimateConnector;
use TempiMarathon\OpenMeteo\Connectors\EnsembleConnector;
use TempiMarathon\OpenMeteo\Connectors\FloodConnector;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Connectors\SeasonalConnector;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\AirQualityResponseCollection;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponseCollection;
use TempiMarathon\OpenMeteo\Data\CoordinateResponseCollection;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponseCollection;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponseCollection;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponseCollection;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponseCollection;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponseCollection;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Requests\Climate\GetClimateRequest;
use TempiMarathon\OpenMeteo\Requests\Ensemble\GetEnsembleRequest;
use TempiMarathon\OpenMeteo\Requests\Flood\GetFloodRequest;
use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Requests\Seasonal\GetSeasonalRequest;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Tests\Support\StubCoordinateGetRequest;

covers(
    AirQualityResponseCollection::class,
    ClimateResponseCollection::class,
    EnsembleResponseCollection::class,
    FloodResponseCollection::class,
    ForecastResponseCollection::class,
    HistoricalResponseCollection::class,
    MarineResponseCollection::class,
    SeasonalResponseCollection::class,
    GetAirQualityRequest::class,
    GetClimateRequest::class,
    GetEnsembleRequest::class,
    GetFloodRequest::class,
    GetArchiveRequest::class,
    GetMarineRequest::class,
    GetSeasonalRequest::class,
    AbstractCoordinateGetRequest::class,
);

it('builds typed response collections from payloads', function (
    string $responseClass,
    string $collectionClass,
    callable $payloadFactory,
): void {
    $collection = (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param array<int|string, mixed> $data */
        public function make(array $data, string $responseClass): mixed
        {
            return $this->createResponseCollectionFromPayload($data, $responseClass);
        }
    })->make([$payloadFactory(), $payloadFactory()], $responseClass);

    expect($collection)->toBeInstanceOf($collectionClass)
        ->and($collection->count())->toBe(2)
        ->and($collection->first())->toBeInstanceOf($responseClass)
        ->and(iterator_to_array($collection))->toHaveCount(2);
})->with([
    'air quality' => [AirQualityResponse::class, AirQualityResponseCollection::class, fn () => airQualityPayload()],
    'climate' => [ClimateResponse::class, ClimateResponseCollection::class, fn () => climatePayload()],
    'ensemble' => [EnsembleResponse::class, EnsembleResponseCollection::class, fn () => ensemblePayload()],
    'flood' => [FloodResponse::class, FloodResponseCollection::class, fn () => floodPayload()],
    'historical' => [HistoricalResponse::class, HistoricalResponseCollection::class, fn () => historicalPayload()],
    'marine' => [MarineResponse::class, MarineResponseCollection::class, fn () => marinePayload()],
    'seasonal' => [SeasonalResponse::class, SeasonalResponseCollection::class, fn () => seasonalPayload()],
]);

it('resolves typed dto collections from mocked responses', function (
    string $requestClass,
    object $connector,
    callable $makeRequest,
    array $payload,
    string $collectionClass,
    string $responseClass,
): void {
    MockClient::global([$requestClass => mockOk($payload)]);

    $request = $makeRequest($connector)->using($connector);
    $collection = $request->createDtoCollectionFromResponse($request->send());

    expect($collection)->toBeInstanceOf($collectionClass)
        ->and($collection->count())->toBe(1)
        ->and($collection->first())->toBeInstanceOf($responseClass)
        ->and(iterator_to_array($collection))->toHaveCount(1);

    MockClient::global([$requestClass => mockOk($payload)]);

    expect($makeRequest($connector)->using($connector)->dtoCollection())
        ->toBeInstanceOf($collectionClass);
})->with([
    'air quality' => [
        GetAirQualityRequest::class,
        new AirQualityConnector,
        fn (AirQualityConnector $connector) => $connector->airQuality()->get(52.37, 4.89),
        airQualityPayload(),
        AirQualityResponseCollection::class,
        AirQualityResponse::class,
    ],
    'climate' => [
        GetClimateRequest::class,
        new ClimateConnector,
        fn (ClimateConnector $connector) => $connector->climate()->get(52.37, 4.89)
            ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')),
        climatePayload(),
        ClimateResponseCollection::class,
        ClimateResponse::class,
    ],
    'ensemble' => [
        GetEnsembleRequest::class,
        new EnsembleConnector,
        fn (EnsembleConnector $connector) => $connector->ensemble()->get(52.37, 4.89),
        ensemblePayload(),
        EnsembleResponseCollection::class,
        EnsembleResponse::class,
    ],
    'flood' => [
        GetFloodRequest::class,
        new FloodConnector,
        fn (FloodConnector $connector) => $connector->flood()->get(52.37, 4.89),
        floodPayload(),
        FloodResponseCollection::class,
        FloodResponse::class,
    ],
    'historical' => [
        GetArchiveRequest::class,
        new HistoricalConnector,
        fn (HistoricalConnector $connector) => $connector->archive()->get(52.37, 4.89)
            ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')),
        historicalPayload(),
        HistoricalResponseCollection::class,
        HistoricalResponse::class,
    ],
    'marine' => [
        GetMarineRequest::class,
        new MarineConnector,
        fn (MarineConnector $connector) => $connector->marine()->get(52.37, 4.89),
        marinePayload(),
        MarineResponseCollection::class,
        MarineResponse::class,
    ],
    'seasonal' => [
        GetSeasonalRequest::class,
        new SeasonalConnector,
        fn (SeasonalConnector $connector) => $connector->seasonal()->get(52.37, 4.89),
        seasonalPayload(),
        SeasonalResponseCollection::class,
        SeasonalResponse::class,
    ],
]);

it('parses multi-location payloads into typed collections', function (): void {
    MockClient::global([
        GetMarineRequest::class => mockOk([marinePayload(), marinePayload()]),
    ]);

    $connector = new MarineConnector;
    $collection = $connector->marine()->forPoints([[52.37, 4.89], [48.1, 11.6]])->dtoCollection();

    expect($collection)->toBeInstanceOf(MarineResponseCollection::class)
        ->and($collection->count())->toBe(2);
});

it('uses abstract coordinate dtoCollection for stub requests', function (): void {
    MockClient::global([
        StubCoordinateGetRequest::class => mockOk(marinePayload()),
    ]);

    $connector = new MarineConnector;
    $collection = StubCoordinateGetRequest::forCoordinates(52.37, 4.89)
        ->using($connector)
        ->dtoCollection();

    expect($collection->count())->toBe(1)
        ->and($collection->first())->toBeInstanceOf(MarineResponse::class);
});

it('returns null from first when the collection holds a mismatched response type', function (
    string $collectionClass,
    string $wrongResponseClass,
    callable $payloadFactory,
): void {
    $collection = (new ReflectionClass($collectionClass))->newInstanceWithoutConstructor();
    $property = new ReflectionProperty(CoordinateResponseCollection::class, 'responses');
    $property->setValue($collection, [
        timeSeriesResponseFromPayload($payloadFactory(), $wrongResponseClass),
    ]);

    expect($collection->first())->toBeNull();
})->with([
    'air quality' => [AirQualityResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'climate' => [ClimateResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'ensemble' => [EnsembleResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'flood' => [FloodResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'forecast' => [ForecastResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'historical' => [HistoricalResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
    'marine' => [MarineResponseCollection::class, FloodResponse::class, fn () => floodPayload()],
    'seasonal' => [SeasonalResponseCollection::class, MarineResponse::class, fn () => marinePayload()],
]);
