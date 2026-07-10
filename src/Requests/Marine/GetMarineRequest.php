<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Marine;

use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\ForecastResponse;
use OpenMeteo\Support\CreatesForecastResponse;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetMarineRequest extends Request implements ResolvesRequestUrlContract
{
    use CreatesForecastResponse;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;

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
        return 'marine';
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
