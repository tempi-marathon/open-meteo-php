<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\Timezone;

final readonly class GeocodingLocation
{
    /** @param list<string> $postcodes */
    public function __construct(
        public int $id,
        public string $name,
        public float $latitude,
        public float $longitude,
        public ?float $elevation,
        public Timezone $timezone,
        public ?string $featureCode,
        public ?CountryCode $countryCode,
        public ?string $country,
        public ?int $countryId,
        public ?int $population,
        public array $postcodes,
        public ?string $admin1,
        public ?string $admin2,
        public ?string $admin3,
        public ?string $admin4,
        public ?int $admin1Id,
        public ?int $admin2Id,
        public ?int $admin3Id,
        public ?int $admin4Id,
    ) {}
}
