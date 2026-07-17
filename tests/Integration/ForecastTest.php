<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\BaseConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\ForecastCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\ForecastMinutely15Variable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastParameterException;
use TempiMarathon\OpenMeteo\Exceptions\MalformedPayloadException;
use TempiMarathon\OpenMeteo\Exceptions\MissingSeriesTimeException;
use TempiMarathon\OpenMeteo\Exceptions\MultiCoordinateResponseException;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Resources\ForecastResource;
use TempiMarathon\OpenMeteo\Support\BuildsSeries;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\WindDirection;

covers(
    BaseConnector::class,
    BaseResource::class,
    ForecastConnector::class,
    ForecastResource::class,
    ForecastResponse::class,
    ForecastUnits::class,
    GetForecastRequest::class,
    CreatesTimeSeriesResponse::class,
    BuildsSeries::class,
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

    $reading = $forecast->hourly()->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($forecast->latitude)->toBe(52.366)
        ->and($forecast->longitude)->toBe(4.901)
        ->and($forecast->timezone)->toBe('Europe/Amsterdam')
        ->and($forecast->hourly()->count())->toBe(1)
        ->and($reading?->get('temperature_2m'))->toBe(21.2)
        ->and($reading?->get('weathercode'))->toBe(WeatherCode::CLEAR)
        ->and($reading?->get('is_day'))->toBeFalse()
        ->and($reading?->get('apparent_temperature'))->toBe(21.9)
        ->and($reading?->get('wind_direction_10m')?->getRaw())->toBe(35)
        ->and($reading?->get('wind_direction_10m')?->label())->toBe('NE')
        ->and((string) $reading?->get('wind_direction_10m'))->toBe('NE')
        ->and($reading?->get('precipitation'))->toBe(0.0)
        ->and($forecast->units->hourlyUnits['temperature_2m'])->toBe('°C')
        ->and($forecast->units->dailyUnits['temperature_2m_max'])->toBe('°C');
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
    $reading = $connector->weather()->get(52.37, 4.89)->dto()->hourly()->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('temperature_2m'))->toBe(18.0)
        ->and($reading?->get('weathercode'))->toBeNull()
        ->and($reading?->get('wind_speed_10m'))->toBeNull();
});

it('returns an empty hourly reading collection when hourly data is missing', function (): void {
    $response = timeSeriesResponseFromPayload([
        'latitude' => 52.37,
        'longitude' => 4.89,
        'timezone' => 'Europe/Amsterdam',
    ]);

    expect($response->hourly()->count())->toBe(0);
});

it('tolerates null and invalid optional hourly values', function (): void {
    MockClient::global([
        GetMarineRequest::class => mockOk(marinePayload()),
    ]);

    $reading = (new MarineConnector)
        ->marine()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($reading?->datetime->format('Y-m-d\TH:i'))->toBe('2026-07-11T00:00')
        ->and($reading?->get('temperature_2m'))->toBeNull();
});

it('throws when hourly readings are requested without time data', function (): void {
    expect(fn () => timeSeriesResponseFromPayload([
        'latitude' => 52.37,
        'longitude' => 4.89,
        'timezone' => 'Europe/Amsterdam',
        'hourly' => ['temperature_2m' => [18.0]],
    ]))->toThrow(MissingSeriesTimeException::class);
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
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($reading?->get('weathercode'))->toBeNull();
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
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('temperature_2m'))->toBeNull()
        ->and($reading?->get('wind_direction_10m'))->toBeNull()
        ->and($reading?->get('is_day'))->toBeNull();
});

it('throws when forecast payload is malformed', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(['latitude' => 'invalid']),
    ]);

    $connector = new ForecastConnector;

    expect(fn () => $connector->weather()->get(52.37, 4.89)->dto())
        ->toThrow(MalformedPayloadException::class);
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
    $reading = $connector->weather()->get(52.37, 4.89)->dto()->hourly()->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('wind_speed_10m'))->toBe(5.5)
        ->and($reading?->get('wind_direction_10m')?->getRaw())->toBe(90)
        ->and($reading?->get('wind_direction_10m')?->label())->toBe('E')
        ->and($reading?->get('weathercode'))->toBe(WeatherCode::CLEAR)
        ->and($reading?->get('is_day'))->toBeTrue();
});

it('parses legacy hourly wind keys', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'windspeed_10m' => [8.0],
                'winddirection_10m' => [180],
            ],
        ])),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('wind_speed_10m'))->toBe(8.0)
        ->and($reading?->get('wind_direction_10m')?->getRaw())->toBe(180)
        ->and($reading?->get('wind_direction_10m')?->label())->toBe('S');
});

it('returns null for completely absent optional hourly keys', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'temperature_2m' => [18.0],
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
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('weathercode'))->toBeNull()
        ->and($reading?->get('apparent_temperature'))->toBeNull()
        ->and($reading?->get('wind_speed_10m'))->toBeNull()
        ->and($reading?->get('wind_direction_10m'))->toBeNull()
        ->and($reading?->get('precipitation'))->toBeNull()
        ->and($reading?->get('is_day'))->toBeNull();
});

it('returns null when weather code values are explicitly null', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'weathercode' => [null],
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
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('weathercode'))->toBeNull();
});

it('returns null when hourly value arrays omit the requested index', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T12:00'],
                'windspeed_10m' => [],
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
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T12:00'));

    expect($reading?->get('wind_speed_10m'))->toBeNull();
});

it('treats responses with non-array first index as single-location payloads', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [0 => 'not-an-array'])),
    ]);

    $forecast = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto();

    expect($forecast->latitude)->toBe(52.366);
});

it('parses single-segment array responses as a one-item collection', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([forecastPayload()]),
    ]);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $collection = $request->createDtoCollectionFromResponse($request->send());

    expect($collection->count())->toBe(1)
        ->and($collection->first()?->latitude)->toBe(52.366);
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
        ->and($collection->first()?->latitude)->toBe(52.366)
        ->and(iterator_to_array($collection)[1]?->latitude)->toBe(48.85);
});

it('parses multi-location responses via dtoCollection', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            forecastPayload(),
            array_replace(forecastPayload(), ['latitude' => 48.85, 'longitude' => 2.35]),
        ]),
    ]);

    $collection = (new ForecastConnector)
        ->weather()
        ->forPoints([[52.37, 4.89], [48.85, 2.35]])
        ->hourly(HourlyVariable::Temperature2m)
        ->dtoCollection();

    expect($collection->count())->toBe(2);
});

it('rejects multi-location dto parsing through dto', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            forecastPayload(),
            array_replace(forecastPayload(), ['latitude' => 48.85, 'longitude' => 2.35]),
        ]),
    ]);

    $request = (new ForecastConnector)
        ->weather()
        ->forPoints([[52.37, 4.89], [48.85, 2.35]]);

    expect(fn () => $request->createDtoFromResponse($request->send()))
        ->toThrow(MultiCoordinateResponseException::class);
});

it('prefers canonical weathercode when both hourly aliases are present', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => array_replace(forecastPayload()['hourly'], [
                'weather_code' => [3],
                'weathercode' => [0],
            ]),
        ])),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-11T00:00'));

    expect($reading?->get('weathercode'))->toBe(WeatherCode::CLEAR);
});

it('treats non-array hourly data as empty readings', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => 'invalid',
        ])),
    ]);

    $forecast = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto();

    expect($forecast->latitude)->toBe(52.366)
        ->and($forecast->longitude)->toBe(4.901)
        ->and($forecast->timezone)->toBe('Europe/Amsterdam')
        ->and($forecast->hourly()->count())->toBe(0);
});

it('treats non-array daily and unit data as empty arrays', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'daily' => 'invalid',
            'hourly_units' => 'invalid',
            'daily_units' => 'invalid',
        ])),
    ]);

    $forecast = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto();

    expect($forecast->daily()->count())->toBe(0)
        ->and($forecast->units->hourlyUnits)->toBe([])
        ->and($forecast->units->dailyUnits)->toBe([]);
});

it('validates forecast day ranges', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89);

    expect(fn () => $request->forecastDays(-1))->toThrow(InvalidForecastParameterException::class, 'forecast_days must be between 0 and 16')
        ->and(fn () => $request->forecastDays(17))->toThrow(InvalidForecastParameterException::class, 'forecast_days must be between 0 and 16')
        ->and(fn () => $request->pastDays(-1))->toThrow(InvalidForecastParameterException::class, 'past_days must be between 0 and 92')
        ->and(fn () => $request->pastDays(93))->toThrow(InvalidForecastParameterException::class, 'past_days must be between 0 and 92')
        ->and(fn () => $request->forecastHours(-1))->toThrow(InvalidForecastParameterException::class, 'forecast_hours must be between 0 and 384')
        ->and(fn () => $request->forecastHours(385))->toThrow(InvalidForecastParameterException::class, 'forecast_hours must be between 0 and 384');
});

it('accepts forecast range boundaries', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->forecastDays(0)
        ->pastDays(0)
        ->forecastHours(0);
    $minQuery = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    $maxRequest = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->forecastDays(16)
        ->pastDays(92)
        ->forecastHours(384);
    $maxQuery = (new ReflectionClass($maxRequest))->getMethod('defaultQuery')->invoke($maxRequest);

    expect($minQuery['forecast_days'])->toBe('0')
        ->and($minQuery['past_days'])->toBe('0')
        ->and($minQuery['forecast_hours'])->toBe('0')
        ->and($maxQuery['forecast_days'])->toBe('16')
        ->and($maxQuery['past_days'])->toBe('92')
        ->and($maxQuery['forecast_hours'])->toBe('384');
});

it('builds full forecast query from all builder options', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m, HourlyVariable::WeatherCode)
        ->daily(DailyVariable::Temperature2mMax)
        ->timezone(Timezone::EuropeAmsterdam)
        ->forecastDays(7)
        ->pastDays(1)
        ->forecastHours(48);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89')
        ->and($query['timezone'])->toBe('Europe/Amsterdam')
        ->and($query['forecast_days'])->toBe('7')
        ->and($query['past_days'])->toBe('1')
        ->and($query['forecast_hours'])->toBe('48')
        ->and($query['hourly'])->toBe('temperature_2m,weather_code')
        ->and($query['daily'])->toBe('temperature_2m_max');
});

it('validates coordinates on forecast requests', function (): void {
    expect(fn () => GetForecastRequest::forCoordinates(91.0, 4.89))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between');
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

it('exposes wind direction at any height as a value object', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'hourly' => array_replace(forecastPayload()['hourly'], [
                'wind_direction_80m' => [225],
            ]),
        ])),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly()
        ->at(0);

    expect($reading?->get('wind_direction_80m'))->toBeInstanceOf(WindDirection::class)
        ->and($reading?->get('wind_direction_80m')?->getRaw())->toBe(225)
        ->and($reading?->get('wind_direction_80m')?->label())->toBe('SW');
});

it('keeps direction anomaly fields as numeric values', function (): void {
    $reading = timeSeriesResponseFromPayload([
        'latitude' => 52.37,
        'longitude' => 4.89,
        'timezone' => 'Europe/Amsterdam',
        'hourly' => [
            'time' => ['2026-07-06T12:00'],
            'wind_direction_10m_anomaly' => [12.5],
        ],
    ])->hourly()->at(0);

    expect($reading?->get('wind_direction_10m_anomaly'))->toBe(12.5);
});

it('parses current conditions as a typed snapshot', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'current' => [
                'time' => '2026-07-11T12:00',
                'interval' => 900,
                'temperature_2m' => 21.5,
                'wind_direction_10m' => 90,
            ],
            'current_units' => [
                'time' => 'iso8601',
                'interval' => 'seconds',
                'temperature_2m' => '°C',
                'wind_direction_10m' => '°',
            ],
        ])),
    ]);

    $current = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->current(ForecastCurrentVariable::Temperature2m, ForecastCurrentVariable::WindDirection10m)
        ->dto()
        ->current()
        ->first();

    expect($current?->get('temperature_2m'))->toBe(21.5)
        ->and($current?->interval)->toBe(900)
        ->and($current?->get('wind_direction_10m')?->label())->toBe('E');
});

it('parses minutely 15 readings', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            'minutely_15' => [
                'time' => ['2026-07-11T12:00'],
                'temperature_2m' => [20.0],
            ],
            'minutely_15_units' => [
                'time' => 'iso8601',
                'temperature_2m' => '°C',
            ],
        ])),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->minutely15(ForecastMinutely15Variable::Temperature2m)
        ->dto()
        ->minutely15()
        ->at(0);

    expect($reading?->get('temperature_2m'))->toBe(20.0);
});

it('coerces marine wave direction fields', function (): void {
    $reading = timeSeriesResponseFromPayload([
        'latitude' => 52.37,
        'longitude' => 4.89,
        'timezone' => 'Europe/Amsterdam',
        'hourly' => [
            'time' => ['2026-07-06T12:00'],
            'wave_direction' => [180],
        ],
    ], MarineResponse::class)->hourly()->at(0);

    expect($reading?->get('wave_direction'))->toBeInstanceOf(WindDirection::class)
        ->and($reading?->get('wave_direction')?->label())->toBe('S');
});

it('builds current and minutely 15 query parameters', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->current(ForecastCurrentVariable::Temperature2m)
        ->minutely15(ForecastMinutely15Variable::Temperature2m);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['current'])->toBe('temperature_2m')
        ->and($query['minutely_15'])->toBe('temperature_2m');
});
