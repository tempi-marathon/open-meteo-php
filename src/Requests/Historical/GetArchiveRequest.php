<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Historical;

use DateTimeInterface;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\HistoricalResponse;
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

final class GetArchiveRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesTimeSeriesResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

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
            $query['hourly'] = join(map($this->hourly, static fn (HourlyVariable $v): string => $v->value), ',');
        }

        if ($this->daily !== []) {
            $query['daily'] = join(map($this->daily, static fn (DailyVariable $v): string => $v->value), ',');
        }

        return $this->withApiKey($query);
    }

    public function createDtoFromResponse(Response $response): HistoricalResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createTimeSeriesResponseFromPayload($data, HistoricalResponse::class);
    }

    public function dto(): HistoricalResponse
    {
        return $this->resolveDto(HistoricalResponse::class);
    }
}
