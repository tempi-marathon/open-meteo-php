<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Historical;

use DateTimeInterface;
use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\HistoricalResponse;
use OpenMeteo\Enums\DailyVariable;
use OpenMeteo\Enums\HourlyVariable;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Support\CreatesForecastResponse;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetArchiveRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    /** @var list<HourlyVariable> */
    private array $hourly = [];

    /** @var list<DailyVariable> */
    private array $daily = [];

    private ?DateTimeInterface $startDate = null;

    private ?DateTimeInterface $endDate = null;

    private Timezone $timezone = Timezone::GMT;

    private function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
    ) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public function hourly(HourlyVariable ...$variables): self
    {
        $clone = clone $this;
        $clone->hourly = array_values($variables);

        return $clone;
    }

    public function daily(DailyVariable ...$variables): self
    {
        $clone = clone $this;
        $clone->daily = array_values($variables);

        return $clone;
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

    public function resolveEndpoint(): string
    {
        return 'archive';
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
            $query['hourly'] = implode(',', array_map(static fn (HourlyVariable $v): string => $v->value, $this->hourly));
        }

        if ($this->daily !== []) {
            $query['daily'] = implode(',', array_map(static fn (DailyVariable $v): string => $v->value, $this->daily));
        }

        return $this->withApiKey($query);
    }

    public function createDtoFromResponse(Response $response): HistoricalResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        $forecast = $this->createForecastResponseFromPayload($data);

        return new HistoricalResponse(
            latitude: $forecast->latitude,
            longitude: $forecast->longitude,
            timezone: $forecast->timezone,
            hourly: $forecast->hourly,
            daily: $forecast->daily,
            units: $forecast->units,
        );
    }
}
