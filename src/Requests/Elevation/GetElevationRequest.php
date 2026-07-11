<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Elevation;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\ElevationResponse;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;

use function Psl\Type\float;
use function Psl\Type\vec;

final class GetElevationRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    private function __construct(private readonly float $latitude, private readonly float $longitude) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        ValidatesCoordinates::assert($latitude, $longitude);

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

    public function dto(): ElevationResponse
    {
        return $this->resolveDto(ElevationResponse::class);
    }
}
