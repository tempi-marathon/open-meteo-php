<?php

declare(strict_types=1);

use Psl\Type\Exception\CoercionException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Connectors\ClimateConnector;
use TempiMarathon\OpenMeteo\Connectors\ElevationConnector;
use TempiMarathon\OpenMeteo\Connectors\EnsembleConnector;
use TempiMarathon\OpenMeteo\Connectors\FloodConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Connectors\SeasonalConnector;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\ElevationResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Requests\Climate\GetClimateRequest;
use TempiMarathon\OpenMeteo\Requests\Elevation\GetElevationRequest;
use TempiMarathon\OpenMeteo\Requests\Ensemble\GetEnsembleRequest;
use TempiMarathon\OpenMeteo\Requests\Flood\GetFloodRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Requests\Seasonal\GetSeasonalRequest;
use TempiMarathon\OpenMeteo\Resources\ClimateResource;
use TempiMarathon\OpenMeteo\Resources\ElevationResource;
use TempiMarathon\OpenMeteo\Resources\EnsembleResource;
use TempiMarathon\OpenMeteo\Resources\FloodResource;
use TempiMarathon\OpenMeteo\Resources\MarineResource;
use TempiMarathon\OpenMeteo\Resources\SeasonalResource;

covers(
    MarineConnector::class,
    MarineResource::class,
    GetMarineRequest::class,
    MarineResponse::class,
    ClimateConnector::class,
    ClimateResource::class,
    GetClimateRequest::class,
    ClimateResponse::class,
    FloodConnector::class,
    FloodResource::class,
    GetFloodRequest::class,
    FloodResponse::class,
    EnsembleConnector::class,
    EnsembleResource::class,
    GetEnsembleRequest::class,
    EnsembleResponse::class,
    SeasonalConnector::class,
    SeasonalResource::class,
    GetSeasonalRequest::class,
    SeasonalResponse::class,
    ElevationConnector::class,
    ElevationResource::class,
    GetElevationRequest::class,
    ElevationResponse::class,
);

it('fetches marine data', function (): void {
    MockClient::global([GetMarineRequest::class => mockOk(marinePayload())]);
    $connector = new MarineConnector;

    $response = $connector->marine()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(MarineResponse::class)
        ->and($response->latitude)->toBe(52.375008)
        ->and($response->longitude)->toBe(4.8750153)
        ->and($response->timezone)->toBe('Europe/Amsterdam')
        ->and($response->units->hourlyUnits['wave_height'])->toBe('m')
        ->and($response->hourly)->toHaveKey('wave_height');
});

it('fetches climate data', function (): void {
    MockClient::global([GetClimateRequest::class => mockOk(climatePayload())]);
    $connector = new ClimateConnector;

    $response = $connector->climate()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(ClimateResponse::class)
        ->and($response->longitude)->toBe(4.900009)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['temperature_2m_max'])->toBe('°C')
        ->and($response->daily)->toHaveKey('temperature_2m_max');
});

it('fetches flood data', function (): void {
    MockClient::global([GetFloodRequest::class => mockOk(floodPayload())]);
    $connector = new FloodConnector;

    $response = $connector->flood()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(FloodResponse::class)
        ->and($response->longitude)->toBe(4.8750153)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['river_discharge'])->toBe('m³/s')
        ->and($response->daily)->toHaveKey('river_discharge');
});

it('fetches ensemble data', function (): void {
    MockClient::global([GetEnsembleRequest::class => mockOk(ensemblePayload())]);
    $connector = new EnsembleConnector;

    $response = $connector->ensemble()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(EnsembleResponse::class)
        ->and($response->longitude)->toBe(5.0)
        ->and($response->timezone)->toBe('Europe/Amsterdam')
        ->and($response->units->hourlyUnits['temperature_2m'])->toBe('°C')
        ->and($response->hourly)->toHaveKey('temperature_2m');
});

it('fetches seasonal data', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(seasonalPayload())]);
    $connector = new SeasonalConnector;

    $response = $connector->seasonal()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(SeasonalResponse::class)
        ->and($response->longitude)->toBe(4.5652175)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['temperature_2m_max'])->toBe('°C')
        ->and($response->daily)->toHaveKey('temperature_2m_max');
});

it('fetches elevation data', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk(elevationPayload())]);
    $connector = new ElevationConnector;

    expect($connector->elevation()->get(52.37, 4.89)->dto()->elevation)->toBe([11.0]);
});

it('returns empty elevation when key is missing', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk([])]);
    $connector = new ElevationConnector;

    expect($connector->elevation()->get(52.37, 4.89)->dto()->elevation)->toBe([]);
});

it('throws when elevation values are malformed', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk(['elevation' => ['invalid']])]);
    $connector = new ElevationConnector;
    $request = $connector->elevation()->get(52.37, 4.89);
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode(['elevation' => ['invalid']], JSON_THROW_ON_ERROR)),
        new PendingRequest($connector, $request),
        (new PendingRequest($connector, $request))->createPsrRequest(),
    );

    expect(fn () => $request->createDtoFromResponse($response))
        ->toThrow(CoercionException::class);
});

it('builds elevation query from coordinates', function (): void {
    $request = GetElevationRequest::forCoordinates(52.37, 4.89);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89');
});

it('validates coordinates on elevation requests', function (): void {
    expect(fn () => GetElevationRequest::forCoordinates(0.0, 181.0))
        ->toThrow(InvalidArgumentException::class, 'longitude must be between');
});
