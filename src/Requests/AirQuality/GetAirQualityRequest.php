<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\AirQuality;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\AirQualityResponse;
use TempiMarathon\OpenMeteo\Data\AirQualityResponseCollection;
use TempiMarathon\OpenMeteo\Enums\AirQualityCurrentVariable;
use TempiMarathon\OpenMeteo\Enums\AirQualityDomain;
use TempiMarathon\OpenMeteo\Enums\AirQualityHourlyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

use function Psl\Vec\values;

final class GetAirQualityRequest extends AbstractCoordinateGetRequest
{
    use JoinsQueryEnumValues;

    /** @var list<AirQualityHourlyVariable> */
    private array $hourly = [];

    /** @var list<AirQualityCurrentVariable> */
    private array $current = [];

    private ?AirQualityDomain $domains = null;

    public function hourly(AirQualityHourlyVariable ...$variables): static
    {
        return clone ($this, [
            'hourly' => values($variables),
        ]);
    }

    public function current(AirQualityCurrentVariable ...$variables): static
    {
        return clone ($this, [
            'current' => values($variables),
        ]);
    }

    public function domains(AirQualityDomain $domains): static
    {
        return clone ($this, [
            'domains' => $domains,
        ]);
    }

    protected function supportedForecastDaysRange(): array
    {
        return [ForecastWindowLimits::FORECAST_DAYS_MIN, ForecastWindowLimits::AIR_QUALITY_FORECAST_DAYS_MAX];
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
        return ['timeformat'];
    }

    public function resolveEndpoint(): string
    {
        return 'air-quality';
    }

    protected function responseClass(): string
    {
        return AirQualityResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->coordinateWeatherQuery();

        if ($this->hourly !== []) {
            $query['hourly'] = $this->joinEnumValues($this->hourly);
        }

        if ($this->current !== []) {
            $query['current'] = $this->joinEnumValues($this->current);
        }

        if ($this->domains !== null) {
            $query['domains'] = $this->domains->value;
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): AirQualityResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var AirQualityResponseCollection */
        return $this->createResponseCollectionFromPayload($data, AirQualityResponse::class);
    }

    public function dtoCollection(): AirQualityResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): AirQualityResponse
    {
        return $this->resolveDto(AirQualityResponse::class);
    }
}
