<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;
use TempiMarathon\OpenMeteo\Resources\HistoricalResource;

covers(
    HistoricalConnector::class,
    HistoricalResource::class,
    GetArchiveRequest::class,
    HistoricalResponse::class,
    ForecastUnits::class,
);

it('fetches historical archive data', function (): void {
    MockClient::global([
        GetArchiveRequest::class => mockOk(historicalPayload()),
    ]);

    $connector = new HistoricalConnector;
    $historical = $connector->archive()->get(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m)
        ->timezone(Timezone::EuropeAmsterdam)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->dto();

    expect($historical->latitude)->toBe(52.40773)
        ->and($historical->longitude)->toBe(4.842301)
        ->and($historical->timezone)->toBe('Europe/Amsterdam')
        ->and($historical->hourlyReadings()->count())->toBe(1)
        ->and($historical->hourlyReadings()->closestTo(new DateTimeImmutable('2024-06-01T00:00'))?->temperature2m)->toBe(14.4)
        ->and($historical->units->hourlyUnits['temperature_2m'])->toBe('°C');
});

it('builds historical archive query from all options', function (): void {
    $request = GetArchiveRequest::forCoordinates(52.37, 4.89)
        ->hourly(HourlyVariable::Temperature2m)
        ->daily(DailyVariable::Temperature2mMax)
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
        ->toThrow(InvalidArgumentException::class, 'longitude must be between');
});
