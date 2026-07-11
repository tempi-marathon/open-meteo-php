<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Connectors\MarineConnector;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Data\GeocodingLocationCollection;
use TempiMarathon\OpenMeteo\Data\HourlyReadingCollection;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\GetRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;
use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    AbstractCoordinateGetRequest::class,
    BaseResource::class,
    HourlyReadingCollection::class,
    ForecastResponseCollection::class,
    GeocodingLocationCollection::class,
    GetArchiveRequest::class,
    GetRequest::class,
    SearchRequest::class,
    OpenMeteoConfig::class,
    OpenMeteoRequestException::class,
);

it('iterates collection iterators', function (): void {
    $forecastCollection = new ForecastResponseCollection([
        GetForecastRequest::forCoordinates(1, 1)->createDtoFromResponse(
            new Response(
                new GuzzleHttp\Psr7\Response(200, [], json_encode(forecastPayload(), JSON_THROW_ON_ERROR)),
                new PendingRequest(new ForecastConnector, GetForecastRequest::forCoordinates(1, 1)),
                (new PendingRequest(new ForecastConnector, GetForecastRequest::forCoordinates(1, 1)))->createPsrRequest(),
            ),
        ),
    ]);

    $geocodingCollection = new GeocodingLocationCollection([]);

    expect(iterator_to_array($forecastCollection))->toHaveCount(1)
        ->and(iterator_to_array($geocodingCollection))->toBe([]);
});

it('skips invalid geocoding search results', function (): void {
    $request = new SearchRequest('Amsterdam');
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode(['results' => ['invalid', geocodingSearchPayload()['results'][0]]], JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    expect($request->createDtoFromResponse($response)->count())->toBe(1);
});

it('covers historical daily query building', function (): void {
    $request = GetArchiveRequest::forCoordinates(52.37, 4.89)
        ->daily(DailyVariable::Temperature2mMax);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['daily'])->toBe('temperature_2m_max');
});

it('covers resource connector accessor', function (): void {
    $connector = new ForecastConnector;
    $resource = $connector->weather();

    expect($resource->connector())->toBe($connector);
});

it('covers missing config file fallback', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH=/tmp/open-meteo-missing-config.php');

    expect(OpenMeteoConfig::host('forecast', 'fallback'))->toBe('fallback');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('handles unknown geocoding country codes', function (): void {
    $request = new SearchRequest('Test');
    $payload = geocodingSearchPayload();
    $payload['results'][0]['country_code'] = 'ZZ';
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    expect($request->createDtoFromResponse($response)->first()?->countryCode)->toBeNull();
});

it('handles geocoding results without country code', function (): void {
    $request = new GetRequest(1);
    $payload = geocodingGetPayload();
    unset($payload['country_code']);
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    expect($request->createDtoFromResponse($response)->countryCode)->toBeNull();
});

it('returns empty collection when results are not an array', function (): void {
    $request = new SearchRequest('Test');
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode(['results' => 'nope'], JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    expect($request->createDtoFromResponse($response)->count())->toBe(0);
});

it('iterates geocoding location collection items', function (): void {
    $request = new SearchRequest('Amsterdam');
    $collection = $request->createDtoFromResponse(
        new Response(
            new GuzzleHttp\Psr7\Response(200, [], json_encode(geocodingSearchPayload(), JSON_THROW_ON_ERROR)),
            new PendingRequest(new GeocodingConnector, $request),
            (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
        ),
    );

    expect(iterator_to_array($collection))->toHaveCount(1);
});

it('covers open meteo exception default message', function (): void {
    $exception = new OpenMeteoRequestException(null, null);

    expect($exception->getMessage())->toBe('Open-Meteo request failed');
});

it('throws when multi-location forecast payload contains invalid segment', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode([forecastPayload(), 'not-an-array'], JSON_THROW_ON_ERROR)),
        new PendingRequest($connector, $request),
        (new PendingRequest($connector, $request))->createPsrRequest(),
    );

    expect(fn () => $request->createDtoCollectionFromResponse($response))
        ->toThrow(UnexpectedValueException::class, 'Expected forecast segment to be an array.');
});

it('builds coordinate query for abstract requests', function (): void {
    $request = GetMarineRequest::forCoordinates(52.37, 4.89);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89');
});

it('includes api key in abstract coordinate request queries', function (): void {
    OpenMeteoConfig::configure(['apikey' => 'marine-key']);

    $request = GetMarineRequest::forCoordinates(52.37, 4.89);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['apikey'])->toBe('marine-key');
});

it('validates coordinates on abstract coordinate requests', function (): void {
    expect(fn () => GetMarineRequest::forCoordinates(91.0, 4.89))
        ->toThrow(InvalidArgumentException::class, 'latitude must be between');
});

it('throws when marine request resolves the wrong dto type', function (): void {
    MockClient::global([
        GetMarineRequest::class => mockOk(marinePayload()),
    ]);

    $request = GetMarineRequest::forCoordinates(52.37, 4.89)->using(new MarineConnector);
    $method = new ReflectionMethod(GetMarineRequest::class, 'resolveDto');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($request, ForecastResponse::class))
        ->toThrow(LogicException::class, 'Expected TempiMarathon\OpenMeteo\Data\ForecastResponse DTO.');
});

it('falls back to gmt for unknown geocoding timezones', function (): void {
    $request = new SearchRequest('Test');
    $payload = geocodingSearchPayload();
    $payload['results'][0]['timezone'] = 'Invalid/Timezone';
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    expect($request->createDtoFromResponse($response)->first()?->timezone)->toBe(Timezone::GMT);
});

it('parses minimal geocoding location payloads', function (): void {
    $request = new SearchRequest('Test');
    $response = new Response(
        new GuzzleHttp\Psr7\Response(200, [], json_encode([
            'results' => [[
                'id' => 1,
                'name' => 'Test',
                'latitude' => 1.0,
                'longitude' => 2.0,
                'timezone' => 'GMT',
            ]],
        ], JSON_THROW_ON_ERROR)),
        new PendingRequest(new GeocodingConnector, $request),
        (new PendingRequest(new GeocodingConnector, $request))->createPsrRequest(),
    );

    $location = $request->createDtoFromResponse($response)->first();

    expect($location?->elevation)->toBeNull()
        ->and($location?->featureCode)->toBeNull()
        ->and($location?->countryCode)->toBeNull()
        ->and($location?->country)->toBeNull()
        ->and($location?->countryId)->toBeNull()
        ->and($location?->population)->toBeNull()
        ->and($location?->postcodes)->toBe([])
        ->and($location?->admin1)->toBeNull()
        ->and($location?->admin2)->toBeNull()
        ->and($location?->admin3)->toBeNull()
        ->and($location?->admin4)->toBeNull()
        ->and($location?->admin1Id)->toBeNull()
        ->and($location?->admin2Id)->toBeNull()
        ->and($location?->admin3Id)->toBeNull()
        ->and($location?->admin4Id)->toBeNull();
});

it('returns null when closest reading target is queried on an empty collection', function (): void {
    expect((new HourlyReadingCollection([]))->closestTo(new DateTimeImmutable))->toBeNull();
});
