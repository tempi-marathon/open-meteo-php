<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\TimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;

abstract class AbstractCoordinateGetRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesTimeSeriesResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    final public function __construct(
        protected readonly float $latitude,
        protected readonly float $longitude,
    ) {}

    /**
     * @return class-string<TimeSeriesResponse>
     */
    abstract protected function responseClass(): string;

    public static function forCoordinates(float $latitude, float $longitude): static
    {
        ValidatesCoordinates::assert($latitude, $longitude);

        return new static($latitude, $longitude);
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey([
            'latitude' => (string) $this->latitude,
            'longitude' => (string) $this->longitude,
        ]);
    }

    public function createDtoFromResponse(Response $response): TimeSeriesResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createTimeSeriesResponseFromPayload($data, $this->responseClass());
    }
}
