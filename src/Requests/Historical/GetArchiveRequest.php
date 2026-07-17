<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Historical;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
use TempiMarathon\OpenMeteo\Data\HistoricalResponseCollection;
use TempiMarathon\OpenMeteo\Enums\HistoricalDailyVariable;
use TempiMarathon\OpenMeteo\Enums\HistoricalHourlyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\BuildsSolarIrradianceOptions;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

final class GetArchiveRequest extends AbstractCoordinateGetRequest
{
    use BuildsSolarIrradianceOptions;
    use JoinsQueryEnumValues;

    /** @var list<HistoricalHourlyVariable> */
    private array $hourly = [];

    /** @var list<HistoricalDailyVariable> */
    private array $daily = [];

    public function hourly(HistoricalHourlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function daily(HistoricalDailyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->daily = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    protected function requiresDateRange(): bool
    {
        return true;
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::ARCHIVE_FORECAST_DAYS_MAX];
    }

    protected function supportedPastDaysRange(): array
    {
        return [ForecastWindowLimits::PAST_DAYS_MIN, ForecastWindowLimits::PAST_DAYS_MAX];
    }

    protected function supportsPastHours(): bool
    {
        return true;
    }

    protected function supportedForecastHoursRange(): array
    {
        return [ForecastWindowLimits::FORECAST_HOURS_MIN, ForecastWindowLimits::FORECAST_HOURS_MAX];
    }

    /**
     * @return list<string>
     */
    protected function weatherQueryOptionKeys(): array
    {
        return [
            'temperature_unit',
            'wind_speed_unit',
            'precipitation_unit',
            'timeformat',
            'cell_selection',
            'elevation',
            'models',
        ];
    }

    public function resolveEndpoint(): string
    {
        return 'archive';
    }

    protected function responseClass(): string
    {
        return HistoricalResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->withSolarIrradianceQuery($this->coordinateWeatherQuery());

        if ($this->hourly !== []) {
            $query['hourly'] = $this->joinEnumValues($this->hourly);
        }

        if ($this->daily !== []) {
            $query['daily'] = $this->joinEnumValues($this->daily);
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): HistoricalResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var HistoricalResponseCollection */
        return $this->createResponseCollectionFromPayload($data, HistoricalResponse::class);
    }

    public function dtoCollection(): HistoricalResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): HistoricalResponse
    {
        return $this->resolveDto(HistoricalResponse::class);
    }
}
