<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Resources\AirQualityResource;

covers(
    AirQualityConnector::class,
    AirQualityResource::class,
    GetAirQualityRequest::class,
    ForecastResponse::class,
    ForecastUnits::class,
);

it('fetches air quality data', function (): void {
    MockClient::global([
        GetAirQualityRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new AirQualityConnector;
    $response = $connector->airQuality()->get(52.37, 4.89)
        ->timezone(Timezone::GMT)
        ->between(new DateTimeImmutable('2026-07-01'), new DateTimeImmutable('2026-07-07'))
        ->dto();

    expect($response->timezone)->toBe('Europe/Amsterdam');
});

it('includes custom hourly variables', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->hourly('european_aqi', 'birch_pollen');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['hourly'])->toBe('european_aqi,birch_pollen');
});
