<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Enums\AirQualityHourlyVariable;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastParameterException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingSearchException;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;
use TempiMarathon\OpenMeteo\Support\ValidatesGeocodingSearchName;

covers(
    ValidatesCoordinates::class,
    ValidatesGeocodingSearchName::class,
    AirQualityHourlyVariable::class,
);

it('validates latitude range', function (): void {
    expect(fn () => ValidatesCoordinates::assert(91.0, 0.0))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between -90 and 90')
        ->and(fn () => ValidatesCoordinates::assert(-91.0, 0.0))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between -90 and 90');
});

it('validates longitude range', function (): void {
    expect(fn () => ValidatesCoordinates::assert(0.0, 181.0))
        ->toThrow(InvalidCoordinateException::class, 'longitude must be between -180 and 180')
        ->and(fn () => ValidatesCoordinates::assert(0.0, -181.0))
        ->toThrow(InvalidCoordinateException::class, 'longitude must be between -180 and 180');
});

it('accepts boundary coordinates', function (): void {
    $request = GetForecastRequest::forCoordinates(90.0, 180.0);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('90')
        ->and($query['longitude'])->toBe('180');
});

it('accepts minimum boundary coordinates', function (): void {
    $request = GetForecastRequest::forCoordinates(-90.0, -180.0);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('-90')
        ->and($query['longitude'])->toBe('-180');
});

it('rejects empty geocoding search names', function (): void {
    expect(fn () => ValidatesGeocodingSearchName::normalize('   '))
        ->toThrow(InvalidGeocodingSearchException::class, 'name must not be empty');
});

it('rejects overly long geocoding search names', function (): void {
    expect(fn () => ValidatesGeocodingSearchName::normalize(str_repeat('a', 257)))
        ->toThrow(InvalidGeocodingSearchException::class, 'name must not exceed 256 characters');
});

it('trims geocoding search names', function (): void {
    expect(ValidatesGeocodingSearchName::normalize('  Berlin  '))->toBe('Berlin');
});

it('rejects names at the maximum allowed length boundary', function (): void {
    expect(ValidatesGeocodingSearchName::normalize(str_repeat('a', 256)))->toBe(str_repeat('a', 256));
});

it('validates forecast hour ranges', function (): void {
    expect(fn () => GetForecastRequest::forCoordinates(52.37, 4.89)->forecastHours(385))
        ->toThrow(InvalidForecastParameterException::class, 'forecast_hours must be between');
});

it('validates coordinates on forecast requests', function (): void {
    expect(fn () => (new ForecastConnector)->weather()->get(100.0, 4.89))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between');
});

it('validates geocoding search names at construction', function (): void {
    expect(fn () => new SearchRequest(''))
        ->toThrow(InvalidGeocodingSearchException::class, 'name must not be empty');
});

it('builds air quality hourly queries from enums', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->hourly(AirQualityHourlyVariable::EuropeanAqi, AirQualityHourlyVariable::BirchPollen);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['hourly'])->toBe('european_aqi,birch_pollen');
});

it('covers generated air quality enum values', function (): void {
    expect(AirQualityHourlyVariable::EuropeanAqi->value)->toBe('european_aqi')
        ->and(count(AirQualityHourlyVariable::cases()))->toBeGreaterThan(40);
});
