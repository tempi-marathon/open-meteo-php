<?php

declare(strict_types=1);

use OpenMeteo\Connectors\HistoricalConnector;
use OpenMeteo\Data\ForecastUnits;
use OpenMeteo\Data\HistoricalResponse;
use OpenMeteo\Enums\HourlyVariable;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Requests\Historical\GetArchiveRequest;
use OpenMeteo\Resources\HistoricalResource;
use Saloon\Http\Faking\MockClient;

covers(
    HistoricalConnector::class,
    HistoricalResource::class,
    GetArchiveRequest::class,
    HistoricalResponse::class,
    ForecastUnits::class,
);

it('fetches historical archive data', function (): void {
    MockClient::global([
        GetArchiveRequest::class => mockOk(forecastPayload()),
    ]);

    $connector = new HistoricalConnector;
    $historical = $connector->send(
        $connector->archive()->get(52.37, 4.89)
            ->hourly(HourlyVariable::Temperature2m)
            ->timezone(Timezone::EuropeAmsterdam)
            ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')),
    )->dto();

    expect($historical->latitude)->toBe(52.37)
        ->and($historical->hourlySlots()->count())->toBe(1);
});
