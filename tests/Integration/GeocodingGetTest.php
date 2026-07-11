<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\Geocoding\GetRequest;
use TempiMarathon\OpenMeteo\Resources\GeocodingResource;

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
    $location = $connector->locations()->get(2759794)->dto();

    expect($location->id)->toBe(2759794)
        ->and($location->name)->toBe('Amsterdam')
        ->and($location->latitude)->toBe(52.37403)
        ->and($location->longitude)->toBe(4.88969)
        ->and($location->timezone)->toBe(Timezone::EuropeAmsterdam)
        ->and($location->countryCode)->toBe(CountryCode::NL)
        ->and($location->country)->toBe('Netherlands')
        ->and($location->admin1)->toBe('North Holland');
});

it('builds geocoding get query', function (): void {
    $request = (new GeocodingConnector)->locations()->get(2759794);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['id'])->toBe('2759794');
});

it('includes api key on get requests', function (): void {
    $request = (new GeocodingConnector)->locations()->get(1)->apiKey('test-key');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['apikey'])->toBe('test-key');
});
