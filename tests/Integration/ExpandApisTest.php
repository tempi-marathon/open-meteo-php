<?php

declare(strict_types=1);

use OpenMeteo\Connectors\ClimateConnector;
use OpenMeteo\Connectors\ElevationConnector;
use OpenMeteo\Connectors\EnsembleConnector;
use OpenMeteo\Connectors\FloodConnector;
use OpenMeteo\Connectors\MarineConnector;
use OpenMeteo\Connectors\SeasonalConnector;
use OpenMeteo\Data\ElevationResponse;
use OpenMeteo\Requests\Climate\GetClimateRequest;
use OpenMeteo\Requests\Elevation\GetElevationRequest;
use OpenMeteo\Requests\Ensemble\GetEnsembleRequest;
use OpenMeteo\Requests\Flood\GetFloodRequest;
use OpenMeteo\Requests\Marine\GetMarineRequest;
use OpenMeteo\Requests\Seasonal\GetSeasonalRequest;
use OpenMeteo\Resources\ClimateResource;
use OpenMeteo\Resources\ElevationResource;
use OpenMeteo\Resources\EnsembleResource;
use OpenMeteo\Resources\FloodResource;
use OpenMeteo\Resources\MarineResource;
use OpenMeteo\Resources\SeasonalResource;
use Saloon\Http\Faking\MockClient;

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

    expect($connector->send($connector->marine()->get(52.37, 4.89))->dto()->latitude)->toBe(52.37);
});

it('fetches climate data', function (): void {
    MockClient::global([GetClimateRequest::class => mockOk(forecastPayload())]);
    $connector = new ClimateConnector;

    expect($connector->send($connector->climate()->get(52.37, 4.89))->dto()->latitude)->toBe(52.37);
});

it('fetches flood data', function (): void {
    MockClient::global([GetFloodRequest::class => mockOk(forecastPayload())]);
    $connector = new FloodConnector;

    expect($connector->send($connector->flood()->get(52.37, 4.89))->dto()->latitude)->toBe(52.37);
});

it('fetches ensemble data', function (): void {
    MockClient::global([GetEnsembleRequest::class => mockOk(forecastPayload())]);
    $connector = new EnsembleConnector;

    expect($connector->send($connector->ensemble()->get(52.37, 4.89))->dto()->latitude)->toBe(52.37);
});

it('fetches seasonal data', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(forecastPayload())]);
    $connector = new SeasonalConnector;

    expect($connector->send($connector->seasonal()->get(52.37, 4.89))->dto()->latitude)->toBe(52.37);
});

it('fetches elevation data', function (): void {
    MockClient::global([GetElevationRequest::class => mockOk(['elevation' => [4.0]])]);
    $connector = new ElevationConnector;

    expect($connector->send($connector->elevation()->get(52.37, 4.89))->dto()->elevation)->toBe([4.0]);
});
