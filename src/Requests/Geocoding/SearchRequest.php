<?php

declare(strict_types=1);

namespace OpenMeteo\Requests\Geocoding;

use OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use OpenMeteo\Data\GeocodingLocationCollection;
use OpenMeteo\Enums\CountryCode;
use OpenMeteo\Enums\Geocoding\GeocodingFormat;
use OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use OpenMeteo\Support\HasApiKeyQuery;
use OpenMeteo\Support\ParsesGeocodingLocation;
use OpenMeteo\Support\ResolvesRequestUrl;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class SearchRequest extends Request implements ResolvesRequestUrlContract
{
    use HasApiKeyQuery;
    use ParsesGeocodingLocation;
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    private const int DEFAULT_COUNT = 10;

    private const int MIN_COUNT = 1;

    private const int MAX_COUNT = 100;

    private ?GeocodingLanguage $language = null;

    private ?CountryCode $countryCode = null;

    private ?GeocodingFormat $format = null;

    private ?int $count = null;

    public function __construct(private readonly string $name) {}

    public function language(GeocodingLanguage $language): self
    {
        $clone = clone $this;
        $clone->language = $language;

        return $clone;
    }

    public function countryCode(CountryCode $countryCode): self
    {
        $clone = clone $this;
        $clone->countryCode = $countryCode;

        return $clone;
    }

    public function format(GeocodingFormat $format): self
    {
        $clone = clone $this;
        $clone->format = $format;

        return $clone;
    }

    public function count(int $count): self
    {
        if ($count < self::MIN_COUNT || $count > self::MAX_COUNT) {
            throw new \InvalidArgumentException(
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
        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            $locations[] = $this->parseGeocodingLocation($result);
        }

        return new GeocodingLocationCollection($locations);
    }
}
