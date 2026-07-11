<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Connectors\AirQualityConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\GeocodingLocationCollection;
use TempiMarathon\OpenMeteo\Enums\AirQualityHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;

covers(
    ForecastConnector::class,
    AirQualityConnector::class,
    GeocodingConnector::class,
    ForecastResponse::class,
    AirQualityResponse::class,
    GeocodingLocationCollection::class,
);

it('fetches a live forecast from Open-Meteo', function (): void {
    $response = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m)
        ->forecastDays(1)
        ->dto();

    expect($response)->toBeInstanceOf(ForecastResponse::class)
        ->and($response->hourly)->toHaveKey('temperature_2m');
})->group('live');

it('fetches live air quality from Open-Meteo', function (): void {
    $response = (new AirQualityConnector)
        ->airQuality()
        ->get(52.37, 4.89)
        ->hourly(AirQualityHourlyVariable::EuropeanAqi)
        ->dto();

    expect($response)->toBeInstanceOf(AirQualityResponse::class)
        ->and($response->hourly)->toHaveKey('european_aqi');
})->group('live');

it('searches live geocoding results from Open-Meteo', function (): void {
    $response = (new GeocodingConnector)
        ->locations()
        ->search('Amsterdam')
        ->count(1)
        ->dto();

    expect($response)->toBeInstanceOf(GeocodingLocationCollection::class)
        ->and($response->count())->toBeGreaterThan(0);
})->group('live');
