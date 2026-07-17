<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Connectors\ClimateConnector;
use TempiMarathon\OpenMeteo\Connectors\ElevationConnector;
use TempiMarathon\OpenMeteo\Connectors\EnsembleConnector;
use TempiMarathon\OpenMeteo\Connectors\FloodConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Connectors\SeasonalConnector;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\ElevationResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Enums\MarineCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\MarineMinutely15Variable;
use TempiMarathon\OpenMeteo\Enums\MonthlyVariable;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\MalformedPayloadException;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Requests\Climate\GetClimateRequest;
use TempiMarathon\OpenMeteo\Requests\Elevation\GetElevationRequest;
use TempiMarathon\OpenMeteo\Requests\Ensemble\GetEnsembleRequest;
use TempiMarathon\OpenMeteo\Requests\Flood\GetFloodRequest;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Requests\Seasonal\GetSeasonalRequest;
use TempiMarathon\OpenMeteo\Resources\AirQualityResource;
use TempiMarathon\OpenMeteo\Resources\ClimateResource;
use TempiMarathon\OpenMeteo\Resources\ElevationResource;
use TempiMarathon\OpenMeteo\Resources\EnsembleResource;
use TempiMarathon\OpenMeteo\Resources\FloodResource;
use TempiMarathon\OpenMeteo\Resources\MarineResource;
use TempiMarathon\OpenMeteo\Resources\SeasonalResource;

covers(
    AirQualityConnector::class,
    AirQualityResource::class,
    GetAirQualityRequest::class,
    AirQualityResponse::class,
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
        ->and($response->hourly()->at(0)?->get('wave_height'))->toBeNull();
});

it('builds marine current and minutely 15 query parameters', function (): void {
    $request = GetMarineRequest::forCoordinates(52.37, 4.89)
        ->current(MarineCurrentVariable::WaveHeight)
        ->minutely15(MarineMinutely15Variable::SeaLevelHeightMsl);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['current'])->toBe('wave_height')
        ->and($query['minutely_15'])->toBe('sea_level_height_msl');
});

it('builds batch elevation requests from the resource', function (): void {
    $connector = new ElevationConnector;
    $request = $connector->elevation()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetElevationRequest::class);
});

it('builds batch forecast requests from the resource', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->forPoints([[52.5, 13.4], [48.1, 11.6]]);

    expect($request)->toBeInstanceOf(GetForecastRequest::class);
});

it('builds batch ensemble requests from the resource', function (): void {
    $connector = new EnsembleConnector;
    $request = $connector->ensemble()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetEnsembleRequest::class);
});

it('builds batch marine requests from the resource', function (): void {
    $request = (new MarineConnector)->marine()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetMarineRequest::class);
});

it('builds batch air quality requests from the resource', function (): void {
    $request = (new AirQualityConnector)->airQuality()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetAirQualityRequest::class);
});

it('builds batch climate requests from the resource', function (): void {
    $request = (new ClimateConnector)->climate()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetClimateRequest::class);
});

it('builds batch flood requests from the resource', function (): void {
    $request = (new FloodConnector)->flood()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetFloodRequest::class);
});

it('builds batch seasonal requests from the resource', function (): void {
    $request = (new SeasonalConnector)->seasonal()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetSeasonalRequest::class);
});

it('fetches climate data', function (): void {
    MockClient::global([GetClimateRequest::class => mockOk(climatePayload())]);
    $connector = new ClimateConnector;

    $response = $connector->climate()->get(52.37, 4.89)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->dto();

    expect($response)->toBeInstanceOf(ClimateResponse::class)
        ->and($response->longitude)->toBe(4.900009)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['temperature_2m_max'])->toBe('°C')
        ->and($response->daily()->at(0)?->get('temperature_2m_max'))->toBe(11.1);
});

it('fetches flood data', function (): void {
    MockClient::global([GetFloodRequest::class => mockOk(floodPayload())]);
    $connector = new FloodConnector;

    $response = $connector->flood()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(FloodResponse::class)
        ->and($response->longitude)->toBe(4.8750153)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['river_discharge'])->toBe('m³/s')
        ->and($response->daily()->at(0)?->get('river_discharge'))->toBe(0.03);
});

it('fetches ensemble data', function (): void {
    MockClient::global([GetEnsembleRequest::class => mockOk(ensemblePayload())]);
    $connector = new EnsembleConnector;

    $response = $connector->ensemble()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(EnsembleResponse::class)
        ->and($response->longitude)->toBe(5.0)
        ->and($response->timezone)->toBe('Europe/Amsterdam')
        ->and($response->units->hourlyUnits['temperature_2m'])->toBe('°C')
        ->and($response->hourly()->at(0)?->get('temperature_2m'))->toBe(18.9);
});

it('fetches seasonal data', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(seasonalPayload())]);
    $connector = new SeasonalConnector;

    $response = $connector->seasonal()->get(52.37, 4.89)->dto();

    expect($response)->toBeInstanceOf(SeasonalResponse::class)
        ->and($response->longitude)->toBe(4.5652175)
        ->and($response->timezone)->toBe('GMT')
        ->and($response->units->dailyUnits['temperature_2m_max'])->toBe('°C')
        ->and($response->daily()->at(0)?->get('temperature_2m_max'))->toBe(23.9);
});

it('parses seasonal monthly readings', function (): void {
    MockClient::global([GetSeasonalRequest::class => mockOk(seasonalPayload())]);
    $connector = new SeasonalConnector;

    $request = $connector->seasonal()->get(52.37, 4.89)->monthly(MonthlyVariable::Temperature2mMean);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['monthly'])->toBe('temperature_2m_mean');
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
        ->toThrow(MalformedPayloadException::class);
});

it('builds elevation query from coordinates', function (): void {
    $request = GetElevationRequest::forCoordinates(52.37, 4.89);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89');
});

it('validates coordinates on elevation requests', function (): void {
    expect(fn () => GetElevationRequest::forCoordinates(0.0, 181.0))
        ->toThrow(InvalidCoordinateException::class, 'longitude must be between');
});
