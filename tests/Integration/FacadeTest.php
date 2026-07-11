<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\OpenMeteo;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;

covers(
    OpenMeteo::class,
);

it('exposes forecast through the facade', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk(forecastPayload()),
    ]);

    $client = new OpenMeteo;
    $forecast = $client->forecast()->weather()->get(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m)
        ->dto();

    expect($forecast->latitude)->toBe(52.37);
});

it('exposes geocoding through the facade', function (): void {
    MockClient::global([
        SearchRequest::class => mockOk(geocodingSearchPayload()),
    ]);

    $client = new OpenMeteo;
    $locations = $client->geocoding()->locations()->search('Amsterdam')->dto();

    expect($locations->count())->toBe(1);
});

it('reuses connector instances', function (): void {
    $client = new OpenMeteo;

    expect($client->forecast())->toBeInstanceOf(ForecastConnector::class)
        ->and($client->forecast())->toBe($client->forecast())
        ->and($client->historical())->toBeInstanceOf(HistoricalConnector::class)
        ->and($client->geocoding())->toBeInstanceOf(GeocodingConnector::class);
});

it('exposes all connector accessors', function (): void {
    $client = new OpenMeteo;

    expect($client->airQuality())->toBe($client->airQuality())
        ->and($client->climate())->toBe($client->climate())
        ->and($client->elevation())->toBe($client->elevation())
        ->and($client->ensemble())->toBe($client->ensemble())
        ->and($client->flood())->toBe($client->flood())
        ->and($client->marine())->toBe($client->marine())
        ->and($client->seasonal())->toBe($client->seasonal());
});
