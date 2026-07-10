<?php

declare(strict_types=1);

use OpenMeteo\Connectors\ForecastConnector;
use OpenMeteo\Connectors\GeocodingConnector;
use OpenMeteo\Data\ForecastResponseCollection;
use OpenMeteo\Data\GeocodingLocationCollection;
use OpenMeteo\Data\HourlySlotCollection;
use OpenMeteo\Data\HourlyWeatherSlot;
use OpenMeteo\Enums\WeatherCode;
use OpenMeteo\Exceptions\OpenMeteoRequestException;
use OpenMeteo\Requests\Forecast\GetForecastRequest;
use OpenMeteo\Requests\Geocoding\SearchRequest;
use OpenMeteo\Resources\BaseResource;
use OpenMeteo\Resources\ForecastResource;
use OpenMeteo\Support\OpenMeteoConfig;
use OpenMeteo\Support\ResolvesRequestUrl;
use OpenMeteo\Tests\Support\InvalidResolvesRequestUrlUser;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;

covers(
    BaseResource::class,
    OpenMeteoConfig::class,
    OpenMeteoRequestException::class,
    HourlySlotCollection::class,
    HourlyWeatherSlot::class,
    ForecastResponseCollection::class,
    GeocodingLocationCollection::class,
    ForecastResource::class,
    GetForecastRequest::class,
    SearchRequest::class,
    ResolvesRequestUrl::class,
);

it('configures custom hosts and headers', function (): void {
    OpenMeteoConfig::configure([
        'hosts' => ['forecast' => 'https://custom.example/v1/'],
        'apikey' => 'abc',
        'user_agent' => 'open-meteo-php-tests',
    ]);

    expect(OpenMeteoConfig::host('forecast', 'default'))->toBe('https://custom.example/v1/')
        ->and(OpenMeteoConfig::apiKey())->toBe('abc')
        ->and(OpenMeteoConfig::userAgent())->toBe('open-meteo-php-tests');

    OpenMeteoConfig::reset();

    expect(OpenMeteoConfig::apiKey())->toBeNull();
});

it('loads default hosts from config file', function (): void {
    expect(OpenMeteoConfig::host('forecast', 'missing'))->toContain('api.open-meteo.com');
});

it('exposes open meteo request exception details', function (): void {
    $exception = new OpenMeteoRequestException('Bad request', 400);

    expect($exception->reason())->toBe('Bad request')
        ->and($exception->statusCode())->toBe(400)
        ->and($exception->getMessage())->toBe('Bad request');
});

it('finds the closest hourly slot', function (): void {
    $slots = new HourlySlotCollection([
        new HourlyWeatherSlot(
            datetime: new DateTimeImmutable('2026-07-06T10:00'),
            weatherCode: WeatherCode::CLEAR,
            temperature2m: 16.0,
            apparentTemperature: 15.0,
            windSpeed10m: 3.0,
            windDirection10m: 90,
            precipitation: 0.0,
            isDay: true,
        ),
        new HourlyWeatherSlot(
            datetime: new DateTimeImmutable('2026-07-06T12:00'),
            weatherCode: WeatherCode::RAIN,
            temperature2m: 18.0,
            apparentTemperature: 17.0,
            windSpeed10m: 5.0,
            windDirection10m: 180,
            precipitation: 1.0,
            isDay: true,
        ),
    ]);

    $closest = $slots->closestTo(new DateTimeImmutable('2026-07-06T11:30'));

    expect($closest?->weatherCode)->toBe(WeatherCode::RAIN)
        ->and($slots->closestTo(new DateTimeImmutable('2026-07-06T10:15'))?->weatherCode)->toBe(WeatherCode::CLEAR)
        ->and($slots->count())->toBe(2)
        ->and(iterator_to_array($slots))->toHaveCount(2);
});

it('returns null for empty slot collections', function (): void {
    expect((new HourlySlotCollection([]))->closestTo(new DateTimeImmutable))->toBeNull();
});

it('iterates forecast response collections', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $collection = $request->createDtoCollectionFromResponse($connector->send($request));

    expect($collection)->toBeInstanceOf(ForecastResponseCollection::class)
        ->and($collection->count())->toBe(1)
        ->and($collection->first()?->latitude)->toBe(52.37);
});

it('iterates geocoding location collections', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk(geocodingSearchPayload()),
    ]);

    $connector = new GeocodingConnector;
    $collection = $connector->send($connector->locations()->search('Amsterdam'))->dto();

    expect($collection)->toBeInstanceOf(GeocodingLocationCollection::class)
        ->and($collection->count())->toBe(1);
});

it('rejects debug urls for unsupported requests', function (): void {
    $resource = new ForecastResource(new ForecastConnector);
    $request = new class extends Request
    {
        protected Method $method = Method::GET;

        public function resolveEndpoint(): string
        {
            return '/';
        }
    };

    expect(fn () => $resource->debugUrl($request))->toThrow(LogicException::class);
});

it('resolves request urls from traits', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);

    expect($request->resolveRequestUrl($connector))->toContain('https://api.open-meteo.com/v1/forecast');
});

it('throws when resolving request url on invalid object', function (): void {
    $invalid = new InvalidResolvesRequestUrlUser;

    expect(fn () => $invalid->resolveRequestUrl(new ForecastConnector))->toThrow(LogicException::class);
});
