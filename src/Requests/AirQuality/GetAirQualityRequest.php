<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\AirQuality;

use DateTimeInterface;
use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\ForecastResponse;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Support\CreatesForecastResponse;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetAirQualityRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    private ?DateTimeInterface $startDate = null;

    private ?DateTimeInterface $endDate = null;

    private Timezone $timezone = Timezone::GMT;

    /** @var list<string> */
    private array $hourly = [];

    private function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
    ) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public function between(DateTimeInterface $start, DateTimeInterface $end): self
    {
        $clone = clone $this;
        $clone->startDate = $start;
        $clone->endDate = $end;

        return $clone;
    }

    public function timezone(Timezone $timezone): self
    {
        $clone = clone $this;
        $clone->timezone = $timezone;

        return $clone;
    }

    public function hourly(string ...$variables): self
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables);

        return $clone;
    }

    public function resolveEndpoint(): string
    {
        return 'air-quality';
    }

    protected function defaultQuery(): array
    {
        $query = [
            'latitude' => (string) $this->latitude,
            'longitude' => (string) $this->longitude,
            'timezone' => $this->timezone->value,
        ];

        if ($this->startDate !== null) {
            $query['start_date'] = $this->startDate->format('Y-m-d');
        }

        if ($this->endDate !== null) {
            $query['end_date'] = $this->endDate->format('Y-m-d');
        }

        if ($this->hourly !== []) {
            $query['hourly'] = implode(',', $this->hourly);
        }

        return $this->withApiKey($query);
    }

    public function createDtoFromResponse(Response $response): ForecastResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createForecastResponseFromPayload($data);
    }
}
