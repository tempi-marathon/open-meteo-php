<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\HistoricalConnector;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
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
        ->and($historical->hourlyReadings()->count())->toBe(1);
});
