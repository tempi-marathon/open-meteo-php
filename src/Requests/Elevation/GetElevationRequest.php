<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Elevation;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\ElevationResponse;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Support\BuildsCoordinateQuery;
use TempiMarathon\OpenMeteo\Support\Coerce;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesCoordinates;

final class GetElevationRequest extends Request implements ResolvesRequestUrlContract
{
    use BuildsCoordinateQuery;
    use HasApiKeyQuery;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    private function __construct(
        private readonly string $latitude,
        private readonly string $longitude,
    ) {}

    public static function forCoordinates(float $latitude, float $longitude): self
    {
        ValidatesCoordinates::assert($latitude, $longitude);

        return new self((string) $latitude, (string) $longitude);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public static function forPoints(array $points): self
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

        return new self(implode(',', $latitudes), implode(',', $longitudes));
    }

    public function resolveEndpoint(): string
    {
        return 'elevation';
    }

    protected function defaultQuery(): array
    {
        return $this->withApiKey([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }

    public function createDtoFromResponse(Response $response): ElevationResponse
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();
        $elevation = Coerce::toFloatList($data['elevation'] ?? []);

        return new ElevationResponse(elevation: $elevation);
    }

    public function dto(): ElevationResponse
    {
        return $this->resolveDto(ElevationResponse::class);
    }
}
