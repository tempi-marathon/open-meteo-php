<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\AirQualityUnits;
use TempiMarathon\OpenMeteo\Enums\AirQualityCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\AirQualityHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Resources\AirQualityResource;

covers(
    AirQualityConnector::class,
    AirQualityResource::class,
    GetAirQualityRequest::class,
    AirQualityResponse::class,
    AirQualityUnits::class,
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
        ->and($response->hourly()->at(0)?->get('european_aqi'))->toBe(22.0);
});

it('includes custom hourly variables', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->hourly(AirQualityHourlyVariable::EuropeanAqi, AirQualityHourlyVariable::BirchPollen);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['hourly'])->toBe('european_aqi,birch_pollen');
});

it('builds air quality query with all options', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->timezone(Timezone::EuropeAmsterdam)
        ->between(new DateTimeImmutable('2026-07-01'), new DateTimeImmutable('2026-07-07'))
        ->current(AirQualityCurrentVariable::EuropeanAqi);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89')
        ->and($query['timezone'])->toBe('Europe/Amsterdam')
        ->and($query['start_date'])->toBe('2026-07-01')
        ->and($query['end_date'])->toBe('2026-07-07')
        ->and($query['current'])->toBe('european_aqi');
});

it('validates coordinates on air quality requests', function (): void {
    expect(fn () => GetAirQualityRequest::forCoordinates(-91.0, 4.89))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between');
});
