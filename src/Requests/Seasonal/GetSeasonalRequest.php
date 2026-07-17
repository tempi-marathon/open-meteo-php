<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Seasonal;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\SeasonalResponse;
use TempiMarathon\OpenMeteo\Data\SeasonalResponseCollection;
use TempiMarathon\OpenMeteo\Enums\MonthlyVariable;
use TempiMarathon\OpenMeteo\Enums\SeasonalDailyVariable;
use TempiMarathon\OpenMeteo\Enums\SeasonalHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\SeasonalWeeklyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

final class GetSeasonalRequest extends AbstractCoordinateGetRequest
{
    use JoinsQueryEnumValues;

    /** @var list<SeasonalHourlyVariable> */
    private array $hourly = [];

    /** @var list<SeasonalDailyVariable> */
    private array $daily = [];

    /** @var list<SeasonalWeeklyVariable> */
    private array $weekly = [];

    /** @var list<MonthlyVariable> */
    private array $monthly = [];

    public function hourly(SeasonalHourlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function daily(SeasonalDailyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->daily = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function weekly(SeasonalWeeklyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->weekly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function monthly(MonthlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->monthly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::SEASONAL_FORECAST_DAYS_MAX];
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
        return [
            'temperature_unit',
            'wind_speed_unit',
            'precipitation_unit',
            'timeformat',
            'cell_selection',
            'models',
        ];
    }

    public function resolveEndpoint(): string
    {
        return 'seasonal';
    }

    protected function responseClass(): string
    {
        return SeasonalResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->coordinateWeatherQuery();

        if ($this->hourly !== []) {
            $query['hourly'] = $this->joinEnumValues($this->hourly);
        }

        if ($this->daily !== []) {
            $query['daily'] = $this->joinEnumValues($this->daily);
        }

        if ($this->weekly !== []) {
            $query['weekly'] = $this->joinEnumValues($this->weekly);
        }

        if ($this->monthly !== []) {
            $query['monthly'] = $this->joinEnumValues($this->monthly);
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): SeasonalResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var SeasonalResponseCollection */
        return $this->createResponseCollectionFromPayload($data, SeasonalResponse::class);
    }

    public function dtoCollection(): SeasonalResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): SeasonalResponse
    {
        return $this->resolveDto(SeasonalResponse::class);
    }
}
