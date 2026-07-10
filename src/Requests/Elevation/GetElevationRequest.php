<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Elevation;

use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\ElevationResponse;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

use function Psl\Type\float;
use function Psl\Type\vec;

final class GetElevationRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    private function __construct(private readonly float $latitude, private readonly float $longitude) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public function resolveEndpoint(): string
    {
        return 'elevation';
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey([
            'latitude' => (string) $this->latitude,
            'longitude' => (string) $this->longitude,
        ]);
    }

    public function createDtoFromResponse(Response $response): ElevationResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();
        $elevation = vec(float())->coerce($data['elevation'] ?? []);

        return new ElevationResponse(elevation: $elevation);
    }
}
