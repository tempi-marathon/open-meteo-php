<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Geocoding;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\GeocodingLocation;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ParsesGeocodingLocation;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;

final class GetRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ParsesGeocodingLocation;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

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
