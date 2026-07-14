<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponseCollection;
use TempiMarathon\OpenMeteo\Data\HistoricalUnits;
use TempiMarathon\OpenMeteo\Enums\HistoricalDailyVariable;
use TempiMarathon\OpenMeteo\Enums\HistoricalHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;
use TempiMarathon\OpenMeteo\Resources\HistoricalResource;

covers(
    HistoricalConnector::class,
    HistoricalResource::class,
    GetArchiveRequest::class,
    HistoricalResponse::class,
    HistoricalUnits::class,
);

it('fetches historical archive data', function (): void {
    MockClient::global([
        GetArchiveRequest::class => mockOk(historicalPayload()),
    ]);

    $connector = new HistoricalConnector;
    $historical = $connector->archive()->get(52.37, 4.89)
        ->hourly(HistoricalHourlyVariable::Temperature2m)
        ->timezone(Timezone::EuropeAmsterdam)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->dto();

    expect($historical->latitude)->toBe(52.40773)
        ->and($historical->longitude)->toBe(4.842301)
        ->and($historical->timezone)->toBe('Europe/Amsterdam')
        ->and($historical->hourly()->count())->toBe(1)
        ->and($historical->hourly()->closestTo(new DateTimeImmutable('2024-06-01T00:00'))?->get('temperature_2m'))->toBe(14.4)
        ->and($historical->units->hourlyUnits['temperature_2m'])->toBe('°C');
});

it('builds historical archive query from all options', function (): void {
    $request = GetArchiveRequest::forCoordinates(52.37, 4.89)
        ->hourly(HistoricalHourlyVariable::Temperature2m)
        ->daily(HistoricalDailyVariable::Temperature2mMax)
        ->timezone(Timezone::EuropeAmsterdam)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'));
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89')
        ->and($query['timezone'])->toBe('Europe/Amsterdam')
        ->and($query['start_date'])->toBe('2024-06-01')
        ->and($query['end_date'])->toBe('2024-06-15')
        ->and($query['hourly'])->toBe('temperature_2m')
        ->and($query['daily'])->toBe('temperature_2m_max');
});

it('validates coordinates on historical requests', function (): void {
    expect(fn () => GetArchiveRequest::forCoordinates(52.37, 181.0))
        ->toThrow(InvalidCoordinateException::class, 'longitude must be between');
});

it('builds batch historical requests from the resource', function (): void {
    $connector = new HistoricalConnector;
    $request = $connector->archive()->forPoints([[52.5, 13.4]]);

    expect($request)->toBeInstanceOf(GetArchiveRequest::class);
});

it('parses multi-location historical responses via dtoCollection', function (): void {
    MockClient::global([
        GetArchiveRequest::class => mockOk([
            historicalPayload(),
            array_replace(historicalPayload(), ['latitude' => 48.85, 'longitude' => 2.35]),
        ]),
    ]);

    $collection = (new HistoricalConnector)
        ->archive()
        ->forPoints([[52.37, 4.89], [48.85, 2.35]])
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->hourly(HistoricalHourlyVariable::Temperature2m)
        ->dtoCollection();

    expect($collection)->toBeInstanceOf(HistoricalResponseCollection::class)
        ->and($collection->count())->toBe(2)
        ->and($collection->first())->toBeInstanceOf(HistoricalResponse::class);
});
