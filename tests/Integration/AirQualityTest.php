<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Enums\AirQualityHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Resources\AirQualityResource;

covers(
    AirQualityConnector::class,
    AirQualityResource::class,
    GetAirQualityRequest::class,
    AirQualityResponse::class,
    ForecastUnits::class,
    AirQualityHourlyVariable::class,
);

it('fetches air quality data', function (): void {
    MockClient::global([
        GetAirQualityRequest::class => mockOk(airQualityPayload()),
    ]);

    $connector = new AirQualityConnector;
    $response = $connector->airQuality()->get(52.37, 4.89)
        ->timezone(Timezone::GMT)
        ->between(new DateTimeImmutable('2026-07-01'), new DateTimeImmutable('2026-07-07'))
        ->dto();

    expect($response)->toBeInstanceOf(AirQualityResponse::class)
        ->and($response->timezone)->toBe('Europe/Amsterdam')
        ->and($response->hourly)->toHaveKey('european_aqi');
});

it('includes custom hourly variables', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->hourly(AirQualityHourlyVariable::EuropeanAqi, AirQualityHourlyVariable::BirchPollen);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['hourly'])->toBe('european_aqi,birch_pollen');
});
