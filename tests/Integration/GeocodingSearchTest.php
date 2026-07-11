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
        ->and($location?->name)->toBe('Amsterdam')
        ->and($location?->timezone)->toBe(Timezone::EuropeAmsterdam)
        ->and($location?->countryCode)->toBe(CountryCode::NL)
        ->and($location?->postcodes)->toBe(['1011']);
});

it('returns empty collection when results are missing', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk([]),
    ]);

    $connector = new GeocodingConnector;

    expect($connector->locations()->search('Nowhere')->dto()->count())->toBe(0);
});

it('validates count range', function (): void {
    expect(fn () => (new GeocodingConnector)->locations()->search('Amsterdam')->count(0))
        ->toThrow(InvalidArgumentException::class);
});

it('validates search name', function (): void {
    expect(fn () => (new GeocodingConnector)->locations()->search(''))
        ->toThrow(InvalidArgumentException::class, 'name must not be empty');
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
