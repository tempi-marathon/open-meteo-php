<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
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
        ->and($response->hourly)->toHaveKey('wave_height');
});

it('fetches climate data', function (): void {
    MockClient::global([GetClimateRequest::class => mockOk(climatePayload())]);
    $connector = new ClimateConnector;

    $response = $connector->climate()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(ClimateResponse::class)
        ->and($response->daily)->toHaveKey('temperature_2m_max');
});

it('fetches flood data', function (): void {
    MockClient::global([GetFloodRequest::class => mockOk(floodPayload())]);
    $connector = new FloodConnector;

    $response = $connector->flood()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(FloodResponse::class)
        ->and($response->daily)->toHaveKey('river_discharge');
});

it('fetches ensemble data', function (): void {
    MockClient::global([GetEnsembleRequest::class => mockOk(ensemblePayload())]);
    $connector = new EnsembleConnector;

    $response = $connector->ensemble()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(EnsembleResponse::class)
        ->and($response->hourly)->toHaveKey('temperature_2m');
});

it('fetches seasonal data', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(seasonalPayload())]);
    $connector = new SeasonalConnector;

    $response = $connector->seasonal()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(SeasonalResponse::class)
        ->and($response->daily)->toHaveKey('temperature_2m_max');
});

it('fetches elevation data', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk(elevationPayload())]);
    $connector = new ElevationConnector;

    expect($connector->elevation()->get(52.37, 4.89)->dto()->elevation)->toBe([11.0]);
});
