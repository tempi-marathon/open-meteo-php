<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\GeocodingLocation;
use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Timezone;

use function Psl\Type\float;
use function Psl\Type\int;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Type\vec;

trait ParsesGeocodingLocation
{
    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function parseGeocodingLocation(array $data): GeocodingLocation
    {
        $item = shape([
            'id' => int(),
            'name' => string(),
            'latitude' => float(),
            'longitude' => float(),
            'timezone' => string(),
            'elevation' => optional(float()),
            'feature_code' => optional(string()),
            'country_code' => optional(string()),
            'country' => optional(string()),
            'country_id' => optional(int()),
            'population' => optional(int()),
            'postcodes' => optional(vec(string())),
            'admin1' => optional(string()),
            'admin2' => optional(string()),
            'admin3' => optional(string()),
            'admin4' => optional(string()),
            'admin1_id' => optional(int()),
            'admin2_id' => optional(int()),
            'admin3_id' => optional(int()),
            'admin4_id' => optional(int()),
        ])->coerce($data);

        $timezone = Timezone::tryFrom($item['timezone']) ?? Timezone::GMT;
        $countryCode = isset($item['country_code'])
            ? CountryCode::tryFrom($item['country_code'])
            : null;

        return new GeocodingLocation(
            id: $item['id'],
            name: $item['name'],
            latitude: $item['latitude'],
            longitude: $item['longitude'],
            elevation: $item['elevation'] ?? null,
            timezone: $timezone,
            featureCode: $item['feature_code'] ?? null,
            countryCode: $countryCode,
            country: $item['country'] ?? null,
            countryId: $item['country_id'] ?? null,
            population: $item['population'] ?? null,
            postcodes: $item['postcodes'] ?? [],
            admin1: $item['admin1'] ?? null,
            admin2: $item['admin2'] ?? null,
            admin3: $item['admin3'] ?? null,
            admin4: $item['admin4'] ?? null,
            admin1Id: $item['admin1_id'] ?? null,
            admin2Id: $item['admin2_id'] ?? null,
            admin3Id: $item['admin3_id'] ?? null,
            admin4Id: $item['admin4_id'] ?? null,
        );
    }
}
