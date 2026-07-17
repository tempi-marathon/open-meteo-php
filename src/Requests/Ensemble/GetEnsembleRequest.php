<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Ensemble;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\EnsembleResponse;
use TempiMarathon\OpenMeteo\Data\EnsembleResponseCollection;
use TempiMarathon\OpenMeteo\Enums\EnsembleDailyVariable;
use TempiMarathon\OpenMeteo\Enums\EnsembleHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\EnsembleTemporalResolution;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\BuildsSolarIrradianceOptions;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

final class GetEnsembleRequest extends AbstractCoordinateGetRequest
{
    use BuildsSolarIrradianceOptions;
    use JoinsQueryEnumValues;

    /** @var list<EnsembleHourlyVariable> */
    private array $hourly = [];

    /** @var list<EnsembleDailyVariable> */
    private array $daily = [];

    private ?EnsembleTemporalResolution $temporalResolution = null;

    public function hourly(EnsembleHourlyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function daily(EnsembleDailyVariable ...$variables): static
    {
        $clone = clone $this;
        $clone->daily = array_values($variables); // @pest-mutate-ignore: UnwrapArrayValues

        return $clone;
    }

    public function temporalResolution(EnsembleTemporalResolution $temporalResolution): static
    {
        $clone = clone $this;
        $clone->temporalResolution = $temporalResolution;

        return $clone;
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::ENSEMBLE_FORECAST_DAYS_MAX];
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
        return 'ensemble';
    }

    protected function responseClass(): string
    {
        return EnsembleResponse::class;
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

        if ($this->temporalResolution !== null) {
            $query['temporal_resolution'] = $this->temporalResolution->value;
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): EnsembleResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var EnsembleResponseCollection */
        return $this->createResponseCollectionFromPayload($data, EnsembleResponse::class);
    }

    public function dtoCollection(): EnsembleResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): EnsembleResponse
    {
        return $this->resolveDto(EnsembleResponse::class);
    }
}
