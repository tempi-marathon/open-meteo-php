<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\AirQuality;

use DateTimeInterface;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Support\CreatesForecastResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;

final class GetAirQualityRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

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

    public function between(DateTimeInterface $start, DateTimeInterface $end): static
    {
        return clone ($this, [
            'startDate' => $start,
            'endDate' => $end,
        ]);
    }

    public function timezone(Timezone $timezone): static
    {
        return clone ($this, [
            'timezone' => $timezone,
        ]);
    }

    public function hourly(string ...$variables): static
    {
        return clone ($this, [
            'hourly' => array_values($variables),
        ]);
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
