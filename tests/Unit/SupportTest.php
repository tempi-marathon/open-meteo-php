<?php

declare(strict_types=1);

use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\GeocodingLocationCollection;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Exceptions\ConnectorNotConfiguredException;
use TempiMarathon\OpenMeteo\Exceptions\DebugUrlNotSupportedException;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Exceptions\ResolvesRequestUrlMisuseException;
use TempiMarathon\OpenMeteo\Exceptions\UnexpectedDtoException;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Resources\ForecastResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\ResolvesTypedDto;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Tests\Support\InvalidResolvesRequestUrlUser;
use TempiMarathon\OpenMeteo\Tests\Support\SlashEndpointRequest;

covers(
    BaseResource::class,
    OpenMeteoConfig::class,
    OpenMeteoRequestException::class,
    ForecastResponseCollection::class,
    GeocodingLocationCollection::class,
    ForecastResource::class,
    GetForecastRequest::class,
    SearchRequest::class,
    ResolvesRequestUrl::class,
    SendsThroughConnector::class,
    ResolvesTypedDto::class,
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

it('iterates forecast response collections', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $collection = $request->createDtoCollectionFromResponse($request->send());

    expect($collection)->toBeInstanceOf(ForecastResponseCollection::class)
        ->and($collection->count())->toBe(1)
        ->and($collection->first()?->latitude)->toBe(52.366);
});

it('treats payloads with a non-array zero index as a single forecast', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(array_replace(forecastPayload(), [
            0 => 'not-an-array',
        ])),
    ]);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $collection = $request->createDtoCollectionFromResponse($request->send());

    expect($collection->count())->toBe(1)
        ->and($collection->first()?->latitude)->toBe(52.366);
});

it('iterates geocoding location collections', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk(geocodingSearchPayload()),
    ]);

    $connector = new GeocodingConnector;
    $collection = $connector->locations()->search('Amsterdam')->dto();

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

    expect(fn () => $resource->debugUrl($request))->toThrow(DebugUrlNotSupportedException::class);
});

it('resolves request urls from traits', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);

    expect($request->resolveRequestUrl($connector))->toBe('https://api.open-meteo.com/v1/forecast');
});

it('normalizes leading slashes in request endpoints', function (): void {
    $connector = new ForecastConnector;
    $request = new SlashEndpointRequest;

    expect($request->resolveRequestUrl($connector))->toBe('https://api.open-meteo.com/v1/forecast');
});

it('throws when resolving request url on invalid object', function (): void {
    $invalid = new InvalidResolvesRequestUrlUser;

    expect(fn () => $invalid->resolveRequestUrl(new ForecastConnector))->toThrow(ResolvesRequestUrlMisuseException::class);
});

it('requires a connector before sending', function (): void {
    expect(fn () => GetForecastRequest::forCoordinates(52.37, 4.89)->send())
        ->toThrow(ConnectorNotConfiguredException::class, 'No connector set');
});

it('throws when typed dto resolution receives the wrong response type', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $request = GetForecastRequest::forCoordinates(52.37, 4.89)->using(new ForecastConnector);
    $method = new ReflectionMethod(GetForecastRequest::class, 'resolveDto');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($request, MarineResponse::class))
        ->toThrow(UnexpectedDtoException::class, 'Expected TempiMarathon\OpenMeteo\Data\MarineResponse DTO.');
});

it('allows attaching a connector with using', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new ForecastConnector;
    $forecast = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->using($connector)
        ->dto();

    expect($forecast->latitude)->toBe(52.366);
});
