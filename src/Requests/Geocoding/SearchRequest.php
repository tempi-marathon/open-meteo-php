<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Geocoding;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Data\GeocodingLocationCollection;
use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingFormat;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingCountException;
use TempiMarathon\OpenMeteo\Support\HasApiKeyQuery;
use TempiMarathon\OpenMeteo\Support\ParsesGeocodingLocation;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\SendsThroughConnector;
use TempiMarathon\OpenMeteo\Support\ValidatesGeocodingSearchName;

/** @pest-mutate-ignore */
final class SearchRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ParsesGeocodingLocation;
    use ResolvesRequestUrl;
    use SendsThroughConnector;

    protected Method $method = Method::GET;

    private const int DEFAULT_COUNT = 10;

    private const int MIN_COUNT = 1;

    private const int MAX_COUNT = 100;

    private ?GeocodingLanguage $language = null;

    private ?CountryCode $countryCode = null;

    private ?GeocodingFormat $format = null;

    private ?int $count = null;

    private readonly string $name;

    public function __construct(string $name)
    {
        $this->name = ValidatesGeocodingSearchName::normalize($name);
    }

    public function language(GeocodingLanguage $language): static
    {
        $clone = clone $this;
        $clone->language = $language;

        return $clone;
    }

    public function countryCode(CountryCode $countryCode): static
    {
        $clone = clone $this;
        $clone->countryCode = $countryCode;

        return $clone;
    }

    public function format(GeocodingFormat $format): static
    {
        $clone = clone $this;
        $clone->format = $format;

        return $clone;
    }

    public function count(int $count): static
    {
        if ($count < self::MIN_COUNT || $count > self::MAX_COUNT) {
            throw new InvalidGeocodingCountException(
                sprintf('count must be between %d and %d, %d given.', self::MIN_COUNT, self::MAX_COUNT, $count),
            );
        }

        $clone = clone $this;
        $clone->count = $count;

        return $clone;
    }

    public function resolveEndpoint(): string
    {
        return 'search';
    }

    protected function defaultQuery(): array
    {
        $query = [
            'name' => $this->name,
            'count' => (string) ($this->count ?? self::DEFAULT_COUNT),
        ];

        if ($this->language instanceof GeocodingLanguage) {
            $query['language'] = $this->language->value;
        }

        if ($this->countryCode instanceof CountryCode) {
            $query['country_code'] = $this->countryCode->value;
        }

        if ($this->format instanceof GeocodingFormat) {
            $query['format'] = $this->format->value;
        }

        return $this->withApiKey($query);
    }

    public function createDtoFromResponse(Response $response): GeocodingLocationCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();
        $results = $data['results'] ?? [];
        if (! is_array($results)) {
            return new GeocodingLocationCollection([]);
        }

        $locations = [];
        /** @var mixed $result */
        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            /** @var array<int|string, mixed> $result */
            $locations[] = $this->parseGeocodingLocation($result);
        }

        return new GeocodingLocationCollection($locations);
    }

    public function dto(): GeocodingLocationCollection
    {
        return $this->resolveDto(GeocodingLocationCollection::class);
    }
}
