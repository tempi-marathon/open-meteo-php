<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Seasonal;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Support\CreatesForecastResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;

final class GetSeasonalRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    private function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
    ) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public function resolveEndpoint(): string
    {
        return 'seasonal';
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey([
            'latitude' => (string) $this->latitude,
            'longitude' => (string) $this->longitude,
        ]);
    }

    public function createDtoFromResponse(Response $response): ForecastResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->createForecastResponseFromPayload($data);
    }
}
