<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Forecast;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\ForecastCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\ForecastMinutely15Variable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\BuildsSolarIrradianceOptions;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

/** @pest-mutate-ignore */
final class GetForecastRequest extends AbstractCoordinateGetRequest
{
    use BuildsSolarIrradianceOptions;
    use JoinsQueryEnumValues;

    /** @var list<HourlyVariable> */
    private array $hourly = [];

    /** @var list<DailyVariable> */
    private array $daily = [];

    /** @var list<ForecastCurrentVariable> */
    private array $current = [];

    /** @var list<ForecastMinutely15Variable> */
    private array $minutely15 = [];

    public function hourly(HourlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function daily(DailyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->daily = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function current(ForecastCurrentVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->current = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function minutely15(ForecastMinutely15Variable ...$variables): static
    {
        $clone = clone $this;
        $clone->minutely15 = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::FORECAST_DAYS_MAX];
    }

    protected function supportedPastDaysRange(): array
    {
        return [ForecastWindowLimits::PAST_DAYS_MIN, ForecastWindowLimits::PAST_DAYS_MAX];
    }

    protected function supportedForecastHoursRange(): array
    {
        return [ForecastWindowLimits::FORECAST_HOURS_MIN, ForecastWindowLimits::FORECAST_HOURS_MAX];
    }

    protected function supportsPastHours(): bool
    {
        return true;
    }

    public function resolveEndpoint(): string
    {
        return 'forecast';
    }

    protected function responseClass(): string
    {
        return ForecastResponse::class;
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

        if ($this->current !== []) {
            $query['current'] = $this->joinEnumValues($this->current);
        }

        if ($this->minutely15 !== []) {
            $query['minutely_15'] = $this->joinEnumValues($this->minutely15);
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): ForecastResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var ForecastResponseCollection */
        return $this->createResponseCollectionFromPayload($data, ForecastResponse::class);
    }

    public function dtoCollection(): ForecastResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): ForecastResponse
    {
        return $this->resolveDto(ForecastResponse::class);
    }
}
