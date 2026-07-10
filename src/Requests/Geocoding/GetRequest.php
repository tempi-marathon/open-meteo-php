<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Geocoding;

use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\GeocodingLocation;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ParsesGeocodingLocation;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ParsesGeocodingLocation;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    public function __construct(private readonly int $id) {}

    public function resolveEndpoint(): string
    {
        return 'get';
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey(['id' => (string) $this->id]);
    }

    public function createDtoFromResponse(Response $response): GeocodingLocation
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $this->parseGeocodingLocation($data);
    }
}
