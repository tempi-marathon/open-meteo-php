<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Forecast;

use DateTimeInterface;
use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\ForecastResponse;
use OpenMeteo\Data\ForecastResponseCollection;
use OpenMeteo\Enums\DailyVariable;
use OpenMeteo\Enums\HourlyVariable;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Support\CreatesForecastResponse;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetForecastRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    private const int MIN_FORECAST_DAYS = 0;

    private const int MAX_FORECAST_DAYS = 16;

    private const int MIN_PAST_DAYS = 0;

    private const int MAX_PAST_DAYS = 92;

    /** @var list<HourlyVariable> */
    private array $hourly = [];

    /** @var list<DailyVariable> */
    private array $daily = [];

    private ?DateTimeInterface $startDate = null;

    private ?DateTimeInterface $endDate = null;

    private Timezone $timezone = Timezone::GMT;

    private ?int $forecastDays = null;

    private ?int $pastDays = null;

    private ?int $forecastHours = null;

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

    public function forecastDays(int $forecastDays): self
    {
        if ($forecastDays < self::MIN_FORECAST_DAYS || $forecastDays > self::MAX_FORECAST_DAYS) {
            throw new \InvalidArgumentException(
                sprintf('forecast_days must be between %d and %d, %d given.', self::MIN_FORECAST_DAYS, self::MAX_FORECAST_DAYS, $forecastDays),
            );
        }

        $clone = clone $this;
        $clone->forecastDays = $forecastDays;

        return $clone;
    }

    public function pastDays(int $pastDays): self
    {
        if ($pastDays < self::MIN_PAST_DAYS || $pastDays > self::MAX_PAST_DAYS) {
            throw new \InvalidArgumentException(
                sprintf('past_days must be between %d and %d, %d given.', self::MIN_PAST_DAYS, self::MAX_PAST_DAYS, $pastDays),
            );
        }

        $clone = clone $this;
        $clone->pastDays = $pastDays;

        return $clone;
    }

    public function forecastHours(int $forecastHours): self
    {
        $clone = clone $this;
        $clone->forecastHours = $forecastHours;

        return $clone;
    }

    public function resolveEndpoint(): string
    {
        return 'forecast';
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

        if ($this->forecastDays !== null) {
            $query['forecast_days'] = (string) $this->forecastDays;
        }

        if ($this->pastDays !== null) {
            $query['past_days'] = (string) $this->pastDays;
        }

        if ($this->forecastHours !== null) {
            $query['forecast_hours'] = (string) $this->forecastHours;
        }

        if ($this->hourly !== []) {
            $query['hourly'] = implode(',', array_map(static fn (HourlyVariable $v): string => $v->value, $this->hourly));
        }

        if ($this->daily !== []) {
            $query['daily'] = implode(',', array_map(static fn (DailyVariable $v): string => $v->value, $this->daily));
        }

        return $this->withApiKey($query);
    }

    public function createDtoFromResponse(Response $response): ForecastResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createForecastResponseFromPayload($data);
    }

    public function createDtoCollectionFromResponse(Response $response): ForecastResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createForecastResponseCollectionFromPayload($data);
    }
}
