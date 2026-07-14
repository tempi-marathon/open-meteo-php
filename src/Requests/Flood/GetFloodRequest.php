<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Flood;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\FloodResponseCollection;
use TempiMarathon\OpenMeteo\Enums\FloodDailyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

use function Psl\Vec\values;

final class GetFloodRequest extends AbstractCoordinateGetRequest
{
    use JoinsQueryEnumValues;

    /** @var list<FloodDailyVariable> */
    private array $daily = [];

    private ?bool $ensemble = null;

    public function daily(FloodDailyVariable ...$variables): static
    {
        return clone ($this, [
            'daily' => values($variables),
        ]);
    }

    public function ensemble(bool $ensemble = true): static
    {
        return clone ($this, [
            'ensemble' => $ensemble,
        ]);
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::FLOOD_FORECAST_DAYS_MAX];
    }

    protected function supportedPastDaysRange(): array
    {
        return [ForecastWindowLimits::PAST_DAYS_MIN, ForecastWindowLimits::PAST_DAYS_MAX];
    }

    /**
     * @return list<string>
     */
    protected function weatherQueryOptionKeys(): array
    {
        return ['timeformat', 'cell_selection', 'models'];
    }

    public function resolveEndpoint(): string
    {
        return 'flood';
    }

    protected function responseClass(): string
    {
        return FloodResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->coordinateWeatherQuery();

        if ($this->daily !== []) {
            $query['daily'] = $this->joinEnumValues($this->daily);
        }

        if ($this->ensemble !== null) {
            $query['ensemble'] = $this->ensemble ? 'true' : 'false';
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): FloodResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var FloodResponseCollection */
        return $this->createResponseCollectionFromPayload($data, FloodResponse::class);
    }

    public function dtoCollection(): FloodResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): FloodResponse
    {
        return $this->resolveDto(FloodResponse::class);
    }
}
