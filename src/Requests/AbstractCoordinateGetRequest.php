<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\CoordinateResponseCollection;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\MissingDateRangeException;
use TempiMarathon\OpenMeteo\Exceptions\MultiCoordinateResponseException;
use TempiMarathon\OpenMeteo\Support\BuildsCoordinateQuery;
use TempiMarathon\OpenMeteo\Support\BuildsDateAndTimezoneOptions;
use TempiMarathon\OpenMeteo\Support\BuildsExtraQuery;
use TempiMarathon\OpenMeteo\Support\BuildsForecastWindowQuery;
use TempiMarathon\OpenMeteo\Support\BuildsWeatherQueryOptions;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;

abstract class AbstractCoordinateGetRequest extends Request implements ResolvesRequestUrlContract
{
    use BuildsCoordinateQuery;
    use BuildsDateAndTimezoneOptions;
    use BuildsExtraQuery;
    use BuildsForecastWindowQuery;
    use BuildsWeatherQueryOptions;
    use CreatesTimeSeriesResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    final public function __construct(
        protected readonly string $latitude,
        protected readonly string $longitude,
    ) {}

    /**
     * @return class-string<CoordinateResponse>
     */
    abstract protected function responseClass(): string;

    public static function forCoordinates(float $latitude, float $longitude): static
    {
        ValidatesCoordinates::assert($latitude, $longitude);

        return new static((string) $latitude, (string) $longitude);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public static function forPoints(array $points): static
    {
        if ($points === []) {
            throw new InvalidCoordinateException('At least one coordinate pair is required.');
        }

        $latitudes = [];
        $longitudes = [];

        foreach ($points as $point) {
            if (! isset($point[0], $point[1])) {
                throw new InvalidCoordinateException('Each coordinate pair must contain latitude and longitude.');
            }

            ValidatesCoordinates::assert($point[0], $point[1]);
            $latitudes[] = (string) $point[0]; // @pest-mutate-ignore: RemoveStringCast
            $longitudes[] = (string) $point[1]; // @pest-mutate-ignore: RemoveStringCast
        }

        return new static(implode(',', $latitudes), implode(',', $longitudes));
    }

    protected function requiresDateRange(): bool
    {
        return false;
    }

    /**
     * @return array<string, string>
     */
    protected function coordinateWeatherQuery(): array
    {
        $this->assertDateRangeWhenRequired();

        return $this->withExtraQuery($this->withWeatherQueryOptions($this->withForecastWindowQuery(
            $this->withDateAndTimezoneQuery([
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]),
        )));
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey($this->coordinateWeatherQuery());
    }

    public function createDtoFromResponse(Response $response): CoordinateResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        if ($this->isSegmentedCoordinatePayload($data)) {
            throw new MultiCoordinateResponseException;
        }

        return $this->createTimeSeriesResponseFromPayload($data, $this->responseClass());
    }

    public function createDtoCollectionFromResponse(Response $response): CoordinateResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createTimeSeriesResponseCollectionFromPayload($data, $this->responseClass());
    }

    public function dtoCollection(): CoordinateResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    protected function assertDateRangeWhenRequired(): void
    {
        if ($this->requiresDateRange() && ($this->startDate === null || $this->endDate === null)) {
            throw new MissingDateRangeException(
                'start_date and end_date are required for this endpoint.',
            );
        }
    }
}
