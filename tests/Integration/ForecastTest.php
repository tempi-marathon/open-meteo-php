<?php

declare(strict_types=1);

use Psl\Type\Exception\CoercionException;
use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\BaseConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\TimeSeriesResponse;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Resources\ForecastResource;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\Support\ParsesHourlyReadings;

covers(
    BaseConnector::class,
    BaseResource::class,
    ForecastConnector::class,
    ForecastResource::class,
    ForecastResponse::class,
    ForecastUnits::class,
    GetForecastRequest::class,
    CreatesTimeSeriesResponse::class,
    TimeSeriesResponse::class,
    ParsesHourlyReadings::class,
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

    expect($forecast->latitude)->toBe(52.366)
        ->and($forecast->timezone)->toBe('Europe/Amsterdam')
        ->and($forecast->hourlyReadings()->count())->toBe(1)
        ->and($forecast->hourlyReadings()->closestTo(new DateTimeImmutable('2026-07-11T00:00'))?->temperature2m)->toBe(21.2)
        ->and($forecast->units->hourlyUnits['temperature_2m'])->toBe('°C');
});

it('parses hourly readings from partial hourly payloads', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'temperature_2m' => [18.0],
            ],
            'hourly_units' => ['time' => 'iso8601', 'temperature_2m' => '°C'],
        ]),
    ]);

    $connector = new ForecastConnector;
    $reading = $connector->weather()->get(52.37, 4.89)->dto()->hourlyReadings()->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->temperature2m)->toBe(18.0)
        ->and($reading?->weatherCode)->toBeNull()
        ->and($reading?->windSpeed10m)->toBeNull();
});

it('returns an empty hourly reading collection when hourly data is missing', function (): void {
    $response = new ForecastResponse(
        latitude: 52.37,
        longitude: 4.89,
        timezone: 'Europe/Amsterdam',
        hourly: [],
        daily: [],
        units: new ForecastUnits(hourlyUnits: [], dailyUnits: []),
    );

    expect($response->hourlyReadings()->count())->toBe(0);
});

it('tolerates null and invalid optional hourly values', function (): void {
    MockClient::global([
        GetMarineRequest::class => mockOk(marinePayload()),
    ]);

    $reading = (new MarineConnector)
        ->marine()
        ->get(52.37, 4.89)
        ->dto()
        ->hourlyReadings()
        ->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($reading?->datetime->format('Y-m-d\TH:i'))->toBe('2026-07-11T00:00')
        ->and($reading?->temperature2m)->toBeNull();
});

it('throws when hourly readings are requested without time data', function (): void {
    $response = new ForecastResponse(
        latitude: 52.37,
        longitude: 4.89,
        timezone: 'Europe/Amsterdam',
        hourly: ['temperature_2m' => [18.0]],
        daily: [],
        units: new ForecastUnits(hourlyUnits: [], dailyUnits: []),
    );

    expect(fn () => $response->hourlyReadings())->toThrow(InvalidArgumentException::class, 'Hourly data must contain a time array.');
});

it('ignores non-integer weather codes when building hourly readings', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => array_replace(forecastPayload()['hourly'], [
                'weathercode' => ['invalid'],
            ]),
        ])),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourlyReadings()
        ->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($reading?->weatherCode)->toBeNull();
});

it('preserves null values for optional hourly fields', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'temperature_2m' => [null],
                'winddirection_10m' => [null],
                'is_day' => [null],
            ],
            'hourly_units' => [],
            'daily_units' => [],
            'daily' => [],
        ]),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourlyReadings()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->temperature2m)->toBeNull()
        ->and($reading?->windDirection10m)->toBeNull()
        ->and($reading?->isDay)->toBeNull();
});

it('throws when forecast payload is malformed', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(['latitude' => 'invalid']),
    ]);

    $connector = new ForecastConnector;

    expect(fn () => $connector->weather()->get(52.37, 4.89)->dto())
        ->toThrow(CoercionException::class);
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
        ->and($collection->first()?->latitude)->toBe(52.366);
});

it('validates forecast day ranges', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89);

    expect(fn () => $request->forecastDays(17))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $request->pastDays(93))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $request->forecastHours(385))->toThrow(InvalidArgumentException::class);
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
