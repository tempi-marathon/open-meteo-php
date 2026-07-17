<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Marine;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Data\MarineResponseCollection;
use TempiMarathon\OpenMeteo\Enums\LengthUnit;
use TempiMarathon\OpenMeteo\Enums\MarineCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\MarineDailyVariable;
use TempiMarathon\OpenMeteo\Enums\MarineHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\MarineMinutely15Variable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

final class GetMarineRequest extends AbstractCoordinateGetRequest
{
    use JoinsQueryEnumValues;

    /** @var list<MarineHourlyVariable> */
    private array $hourly = [];

    /** @var list<MarineDailyVariable> */
    private array $daily = [];

    /** @var list<MarineCurrentVariable> */
    private array $current = [];

    /** @var list<MarineMinutely15Variable> */
    private array $minutely15 = [];

    private ?LengthUnit $lengthUnit = null;

    public function hourly(MarineHourlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function daily(MarineDailyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->daily = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function current(MarineCurrentVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->current = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function minutely15(MarineMinutely15Variable ...$variables): static
    {
        $clone = clone $this;
        $clone->minutely15 = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function lengthUnit(LengthUnit $unit): static
    {
        $clone = clone $this;
        $clone->lengthUnit = $unit;

        return $clone;
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::MARINE_FORECAST_DAYS_MAX];
    }

    /**
     * @return array{0: int, 1: int}
     */
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
            'timeformat',
            'cell_selection',
            'models',
        ];
    }

    public function resolveEndpoint(): string
    {
        return 'marine';
    }

    protected function responseClass(): string
    {
        return MarineResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->coordinateWeatherQuery();

        if ($this->lengthUnit !== null) {
            $query['length_unit'] = $this->lengthUnit->value;
        }

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

    public function createDtoCollectionFromResponse(Response $response): MarineResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var MarineResponseCollection */
        return $this->createResponseCollectionFromPayload($data, MarineResponse::class);
    }

    public function dtoCollection(): MarineResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): MarineResponse
    {
        return $this->resolveDto(MarineResponse::class);
    }
}
