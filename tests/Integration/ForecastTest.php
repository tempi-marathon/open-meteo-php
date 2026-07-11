<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\BaseConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Resources\ForecastResource;
use TempiMarathon\OpenMeteo\Support\CreatesForecastResponse;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    BaseConnector::class,
    BaseResource::class,
    ForecastConnector::class,
    ForecastResource::class,
    ForecastResponse::class,
    ForecastUnits::class,
    GetForecastRequest::class,
    CreatesForecastResponse::class,
);

it('fetches a forecast', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new ForecastConnector;
    $forecast = $connector->weather()->get(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m, HourlyVariable::WeatherCode)
        ->daily(DailyVariable::Temperature2mMax)
        ->timezone(Timezone::EuropeAmsterdam)
        ->forecastDays(7)
        ->pastDays(1)
        ->forecastHours(48)
        ->dto();

    expect($forecast->latitude)->toBe(52.37)
        ->and($forecast->timezone)->toBe('Europe/Amsterdam')
        ->and($forecast->hourlyReadings()->count())->toBe(1)
        ->and($forecast->units->hourlyUnits['temperature_2m'])->toBe('°C');
});

it('normalizes modern hourly response keys', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'temperature_2m' => [18.0],
                'apparent_temperature' => [17.0],
                'precipitation' => [0.0],
                'weather_code' => [0],
                'wind_speed_10m' => [5.5],
                'wind_direction_10m' => [90],
                'is_day' => [1],
            ],
        ])),
    ]);

    $connector = new ForecastConnector;
    $forecast = $connector->weather()->get(52.37, 4.89)->dto();

    expect(iterator_to_array($forecast->hourlyReadings())[0]->windSpeed10m)->toBe(5.5);
});

it('builds a debug url', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89)->timezone(Timezone::GMT);

    expect($connector->weather()->debugUrl($request))
        ->toContain('forecast?')
        ->toContain('latitude=52.37');
});

it('parses multi-location responses', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            forecastPayload(),
            array_replace(forecastPayload(), ['latitude' => 48.85, 'longitude' => 2.35]),
        ]),
    ]);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $collection = $request->createDtoCollectionFromResponse($request->send());

    expect($collection->count())->toBe(2)
        ->and($collection->first()?->latitude)->toBe(52.37);
});

it('validates forecast day ranges', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89);

    expect(fn () => $request->forecastDays(17))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $request->pastDays(93))->toThrow(InvalidArgumentException::class);
});

it('includes api key from config', function (): void {
    OpenMeteoConfig::configure(['apikey' => 'secret-key']);

    $request = GetForecastRequest::forCoordinates(52.37, 4.89);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['apikey'])->toBe('secret-key');
});

it('includes api key from fluent builder', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)->apiKey('inline-key');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['apikey'])->toBe('inline-key');
});

it('supports date ranges', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->between(new DateTimeImmutable('2026-07-01'), new DateTimeImmutable('2026-07-07'));
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['start_date'])->toBe('2026-07-01')
        ->and($query['end_date'])->toBe('2026-07-07');
});
