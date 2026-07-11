<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\ClimateConnector;
use TempiMarathon\OpenMeteo\Connectors\ElevationConnector;
use TempiMarathon\OpenMeteo\Connectors\EnsembleConnector;
use TempiMarathon\OpenMeteo\Connectors\FloodConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Connectors\SeasonalConnector;
use TempiMarathon\OpenMeteo\Data\ElevationResponse;
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
    ClimateConnector::class,
    ClimateResource::class,
    GetClimateRequest::class,
    FloodConnector::class,
    FloodResource::class,
    GetFloodRequest::class,
    EnsembleConnector::class,
    EnsembleResource::class,
    GetEnsembleRequest::class,
    SeasonalConnector::class,
    SeasonalResource::class,
    GetSeasonalRequest::class,
    ElevationConnector::class,
    ElevationResource::class,
    GetElevationRequest::class,
    ElevationResponse::class,
);

it('fetches marine data', function (): void {
    MockClient::global([GetMarineRequest::class => mockOk(forecastPayload())]);
    $connector = new MarineConnector;

    expect($connector->marine()->get(52.37, 4.89)->dto()->latitude)->toBe(52.37);
});

it('fetches climate data', function (): void {
    MockClient::global([GetClimateRequest::class => mockOk(forecastPayload())]);
    $connector = new ClimateConnector;

    expect($connector->climate()->get(52.37, 4.89)->dto()->latitude)->toBe(52.37);
});

it('fetches flood data', function (): void {
    MockClient::global([GetFloodRequest::class => mockOk(forecastPayload())]);
    $connector = new FloodConnector;

    expect($connector->flood()->get(52.37, 4.89)->dto()->latitude)->toBe(52.37);
});

it('fetches ensemble data', function (): void {
    MockClient::global([GetEnsembleRequest::class => mockOk(forecastPayload())]);
    $connector = new EnsembleConnector;

    expect($connector->ensemble()->get(52.37, 4.89)->dto()->latitude)->toBe(52.37);
});

it('fetches seasonal data', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(forecastPayload())]);
    $connector = new SeasonalConnector;

    expect($connector->seasonal()->get(52.37, 4.89)->dto()->latitude)->toBe(52.37);
});

it('fetches elevation data', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk(['elevation' => [4.0]])]);
    $connector = new ElevationConnector;

    expect($connector->elevation()->get(52.37, 4.89)->dto()->elevation)->toBe([4.0]);
});
