<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\GeocodingLocation;
use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Timezone;

trait ParsesGeocodingLocation
{
    /**
     * @param  array<int|string, mixed>  $data
     */
    protected function parseGeocodingLocation(array $data): GeocodingLocation
    {
        $timezone = Timezone::tryFrom(Coerce::toString($data['timezone'] ?? null)) ?? Timezone::GMT;
        $countryCode = isset($data['country_code'])
            ? CountryCode::tryFrom(Coerce::toString($data['country_code']))
            : null;

        return new GeocodingLocation(
            id: Coerce::toInt($data['id'] ?? null),
            name: Coerce::toString($data['name'] ?? null),
            latitude: Coerce::toFloat($data['latitude'] ?? null),
            longitude: Coerce::toFloat($data['longitude'] ?? null),
            elevation: isset($data['elevation']) ? Coerce::toFloat($data['elevation']) : null,
            timezone: $timezone,
            featureCode: isset($data['feature_code']) ? Coerce::toString($data['feature_code']) : null,
            countryCode: $countryCode,
            country: isset($data['country']) ? Coerce::toString($data['country']) : null,
            countryId: isset($data['country_id']) ? Coerce::toInt($data['country_id']) : null,
            population: isset($data['population']) ? Coerce::toInt($data['population']) : null,
            postcodes: isset($data['postcodes']) ? Coerce::toStringList($data['postcodes']) : [],
            admin1: isset($data['admin1']) ? Coerce::toString($data['admin1']) : null,
            admin2: isset($data['admin2']) ? Coerce::toString($data['admin2']) : null,
            admin3: isset($data['admin3']) ? Coerce::toString($data['admin3']) : null,
            admin4: isset($data['admin4']) ? Coerce::toString($data['admin4']) : null,
            admin1Id: isset($data['admin1_id']) ? Coerce::toInt($data['admin1_id']) : null,
            admin2Id: isset($data['admin2_id']) ? Coerce::toInt($data['admin2_id']) : null,
            admin3Id: isset($data['admin3_id']) ? Coerce::toInt($data['admin3_id']) : null,
            admin4Id: isset($data['admin4_id']) ? Coerce::toInt($data['admin4_id']) : null,
        );
    }
}
