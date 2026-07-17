<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Data\GeocodingLocation;
use TempiMarathon\OpenMeteo\Data\GeocodingLocationCollection;
use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingFormat;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingCountException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingSearchException;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Requests\Geocoding\GetRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;
use TempiMarathon\OpenMeteo\Resources\GeocodingResource;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ParsesGeocodingLocation;

covers(
    GeocodingConnector::class,
    GeocodingResource::class,
    SearchRequest::class,
    GetRequest::class,
    GeocodingLocation::class,
    GeocodingLocationCollection::class,
    ParsesGeocodingLocation::class,
    HasApiKeyQuery::class,
    GeocodingLanguage::class,
    GeocodingFormat::class,
    CountryCode::class,
    Timezone::class,
    OpenMeteoRequestException::class,
);

it('searches locations', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk(geocodingSearchPayload()),
    ]);

    $connector = new GeocodingConnector;
    $collection = $connector->locations()->search('Amsterdam')
        ->language(GeocodingLanguage::English)
        ->countryCode(CountryCode::NL)
        ->format(GeocodingFormat::Json)
        ->count(5)
        ->dto();

    $location = $collection->first();

    expect($collection->count())->toBe(1)
        ->and($location?->id)->toBe(2759794)
        ->and($location?->name)->toBe('Amsterdam')
        ->and($location?->latitude)->toBe(52.37403)
        ->and($location?->longitude)->toBe(4.88969)
        ->and($location?->elevation)->toBe(4.0)
        ->and($location?->timezone)->toBe(Timezone::EuropeAmsterdam)
        ->and($location?->featureCode)->toBe('PPLA')
        ->and($location?->countryCode)->toBe(CountryCode::NL)
        ->and($location?->country)->toBe('Netherlands')
        ->and($location?->admin1)->toBe('North Holland')
        ->and($location?->population)->toBe(741636)
        ->and($location?->admin1Id)->toBe(1)
        ->and($location?->postcodes)->toBe(['1011']);
});

it('parses optional geocoding fields when present', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk([
            'results' => [[
                'id' => 2,
                'name' => 'Berlin',
                'latitude' => 52.52,
                'longitude' => 13.41,
                'timezone' => 'Europe/Berlin',
                'elevation' => 34.0,
                'feature_code' => 'PPLC',
                'country_code' => 'DE',
                'country' => 'Germany',
                'country_id' => 2921044,
                'population' => 3644826,
                'postcodes' => ['10115'],
                'admin1' => 'Berlin',
                'admin2' => 'Berlin',
                'admin3' => 'Mitte',
                'admin4' => 'Center',
                'admin1_id' => 11,
                'admin2_id' => 12,
                'admin3_id' => 13,
                'admin4_id' => 14,
            ]],
        ]),
    ]);

    $location = (new GeocodingConnector)->locations()->search('Berlin')->dto()->first();

    expect($location?->countryId)->toBe(2921044)
        ->and($location?->admin2)->toBe('Berlin')
        ->and($location?->admin3)->toBe('Mitte')
        ->and($location?->admin4)->toBe('Center')
        ->and($location?->admin2Id)->toBe(12)
        ->and($location?->admin3Id)->toBe(13)
        ->and($location?->admin4Id)->toBe(14);
});

it('parses minimal geocoding payloads with null optional fields', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk([
            'results' => [[
                'id' => 1,
                'name' => 'Test',
                'latitude' => 1.0,
                'longitude' => 2.0,
                'timezone' => 'GMT',
            ]],
        ]),
    ]);

    $location = (new GeocodingConnector)->locations()->search('Test')->dto()->first();

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

it('builds search query with all options', function (): void {
    $request = (new GeocodingConnector)->locations()->search('Berlin')
        ->language(GeocodingLanguage::German)
        ->countryCode(CountryCode::DE)
        ->format(GeocodingFormat::Json)
        ->count(25);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['name'])->toBe('Berlin')
        ->and($query['count'])->toBe('25')
        ->and($query['language'])->toBe('de')
        ->and($query['country_code'])->toBe('DE')
        ->and($query['format'])->toBe('json');
});

it('uses default search count when not configured', function (): void {
    $request = new SearchRequest('Paris');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['count'])->toBe('10')
        ->and($query)->not->toHaveKey('language')
        ->and($query)->not->toHaveKey('country_code')
        ->and($query)->not->toHaveKey('format');
});

it('validates count range', function (): void {
    expect(fn () => (new GeocodingConnector)->locations()->search('Amsterdam')->count(0))
        ->toThrow(InvalidGeocodingCountException::class, 'count must be between 1 and 100, 0 given.')
        ->and(fn () => (new GeocodingConnector)->locations()->search('Amsterdam')->count(101))
        ->toThrow(InvalidGeocodingCountException::class, 'count must be between 1 and 100, 101 given.');
});

it('accepts search count boundaries', function (): void {
    $minQuery = (new ReflectionClass((new GeocodingConnector)->locations()->search('Amsterdam')->count(1)))
        ->getMethod('defaultQuery')
        ->invoke((new GeocodingConnector)->locations()->search('Amsterdam')->count(1));
    $maxQuery = (new ReflectionClass((new GeocodingConnector)->locations()->search('Amsterdam')->count(100)))
        ->getMethod('defaultQuery')
        ->invoke((new GeocodingConnector)->locations()->search('Amsterdam')->count(100));

    expect($minQuery['count'])->toBe('1')
        ->and($maxQuery['count'])->toBe('100');
});

it('skips non-array entries in the results list', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk([
            'results' => [
                'not-an-array',
                [
                    'id' => 1,
                    'name' => 'Test',
                    'latitude' => 1.0,
                    'longitude' => 2.0,
                    'timezone' => 'GMT',
                ],
            ],
        ]),
    ]);

    $collection = (new GeocodingConnector)->locations()->search('Test')->dto();

    expect($collection->count())->toBe(1)
        ->and($collection->first()?->name)->toBe('Test');
});

it('returns empty collection when results are missing', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk([]),
    ]);

    $connector = new GeocodingConnector;

    expect($connector->locations()->search('Nowhere')->dto()->count())->toBe(0);
});

it('validates search name', function (): void {
    expect(fn () => (new GeocodingConnector)->locations()->search(''))
        ->toThrow(InvalidGeocodingSearchException::class, 'name must not be empty');
});

it('throws open meteo request exception on api errors', function (): void {
    MockClient::global([
        SearchRequest::class => mockError('Invalid name'),
    ]);

    $connector = new GeocodingConnector;

    expect(fn () => $connector->locations()->search('!')->send()->throw())
        ->toThrow(OpenMeteoRequestException::class, 'Invalid name');
});

it('builds a debug url', function (): void {
    $connector = new GeocodingConnector;
    $request = $connector->locations()->search('Berlin');

    expect($connector->locations()->debugUrl($request))->toContain('search?name=Berlin');
});
