<?php

declare(strict_types=1);

use OpenMeteo\Connectors\GeocodingConnector;
use OpenMeteo\Enums\CountryCode;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Requests\Geocoding\GetRequest;
use OpenMeteo\Resources\GeocodingResource;
use Saloon\Http\Faking\MockClient;

covers(
    GeocodingConnector::class,
    GeocodingResource::class,
    GetRequest::class,
);

it('gets a location by id', function (): void {
    MockClient::global([
        GetRequest::class => mockOk(geocodingGetPayload()),
    ]);

    $connector = new GeocodingConnector;
    $location = $connector->send($connector->locations()->get(2759794))->dto();

    expect($location->id)->toBe(2759794)
        ->and($location->name)->toBe('Amsterdam')
        ->and($location->timezone)->toBe(Timezone::EuropeAmsterdam)
        ->and($location->countryCode)->toBe(CountryCode::NL);
});

it('includes api key on get requests', function (): void {
    $request = (new GeocodingConnector)->locations()->get(1)->apiKey('test-key');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['apikey'])->toBe('test-key');
});
