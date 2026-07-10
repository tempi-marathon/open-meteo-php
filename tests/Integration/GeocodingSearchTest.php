<?php

declare(strict_types=1);

use OpenMeteo\Connectors\GeocodingConnector;
use OpenMeteo\Data\GeocodingLocation;
use OpenMeteo\Data\GeocodingLocationCollection;
use OpenMeteo\Enums\CountryCode;
use OpenMeteo\Enums\Geocoding\GeocodingFormat;
use OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Exceptions\OpenMeteoRequestException;
use OpenMeteo\Requests\Geocoding\GetRequest;
use OpenMeteo\Requests\Geocoding\SearchRequest;
use OpenMeteo\Resources\GeocodingResource;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ParsesGeocodingLocation;
use Saloon\Http\Faking\MockClient;

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
    $collection = $connector->send(
        $connector->locations()->search('Amsterdam')
            ->language(GeocodingLanguage::English)
            ->countryCode(CountryCode::NL)
            ->format(GeocodingFormat::Json)
            ->count(5),
    )->dto();

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

    expect($connector->send($connector->locations()->search('Nowhere'))->dto()->count())->toBe(0);
});

it('validates count range', function (): void {
    expect(fn () => (new GeocodingConnector)->locations()->search('Amsterdam')->count(0))
        ->toThrow(InvalidArgumentException::class);
});

it('throws open meteo request exception on api errors', function (): void {
    MockClient::global([
        SearchRequest::class => mockError('Invalid name'),
    ]);

    $connector = new GeocodingConnector;

    expect(fn () => $connector->send($connector->locations()->search(''))->throw())
        ->toThrow(OpenMeteoRequestException::class, 'Invalid name');
});

it('builds a debug url', function (): void {
    $connector = new GeocodingConnector;
    $request = $connector->locations()->search('Berlin');

    expect($connector->locations()->debugUrl($request))->toContain('search?name=Berlin');
});
