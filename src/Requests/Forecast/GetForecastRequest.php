<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Forecast;

use DateTimeInterface;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponseCollection;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;

use function Psl\Str\join;
use function Psl\Vec\map;
use function Psl\Vec\values;

final class GetForecastRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesTimeSeriesResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    private const int MIN_FORECAST_DAYS = 0;

    private const int MAX_FORECAST_DAYS = 16;

    private const int MIN_PAST_DAYS = 0;

    private const int MAX_PAST_DAYS = 92;

    private const int MIN_FORECAST_HOURS = 0;

    private const int MAX_FORECAST_HOURS = 384;

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
        ValidatesCoordinates::assert($latitude, $longitude);

        return new self($latitude, $longitude);
    }

    public function hourly(HourlyVariable ...$variables): static
    {
        return clone ($this, [
            'hourly' => values($variables),
        ]);
    }

    public function daily(DailyVariable ...$variables): static
    {
        return clone ($this, [
            'daily' => values($variables),
        ]);
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

    public function forecastDays(int $forecastDays): static
    {
        if ($forecastDays < self::MIN_FORECAST_DAYS || $forecastDays > self::MAX_FORECAST_DAYS) {
            throw new \InvalidArgumentException(
                sprintf('forecast_days must be between %d and %d, %d given.', self::MIN_FORECAST_DAYS, self::MAX_FORECAST_DAYS, $forecastDays),
            );
        }

        return clone ($this, [
            'forecastDays' => $forecastDays,
        ]);
    }

    public function pastDays(int $pastDays): static
    {
        if ($pastDays < self::MIN_PAST_DAYS || $pastDays > self::MAX_PAST_DAYS) {
            throw new \InvalidArgumentException(
                sprintf('past_days must be between %d and %d, %d given.', self::MIN_PAST_DAYS, self::MAX_PAST_DAYS, $pastDays),
            );
        }

        return clone ($this, [
            'pastDays' => $pastDays,
        ]);
    }

    public function forecastHours(int $forecastHours): static
    {
        if ($forecastHours < self::MIN_FORECAST_HOURS || $forecastHours > self::MAX_FORECAST_HOURS) {
            throw new \InvalidArgumentException(
                sprintf('forecast_hours must be between %d and %d, %d given.', self::MIN_FORECAST_HOURS, self::MAX_FORECAST_HOURS, $forecastHours),
            );
        }

        return clone ($this, [
            'forecastHours' => $forecastHours,
        ]);
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
            $query['hourly'] = join(map($this->hourly, static fn (HourlyVariable $v): string => $v->value), ',');
        }

        if ($this->daily !== []) {
            $query['daily'] = join(map($this->daily, static fn (DailyVariable $v): string => $v->value), ',');
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

    public function dto(): ForecastResponse
    {
        return $this->resolveDto(ForecastResponse::class);
    }
}
