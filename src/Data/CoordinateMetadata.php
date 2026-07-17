<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Support\Coerce;

final readonly class CoordinateMetadata
{
    public function __construct(
        public ?float $elevation = null,
        public ?float $generationTimeMs = null,
        public ?int $utcOffsetSeconds = null,
        public ?string $timezoneAbbreviation = null,
    ) {}

    /**
     * @param  array<int|string, mixed>  $data
     */
    public static function fromPayload(array $data): self
    {
        return new self(
            elevation: isset($data['elevation']) ? Coerce::toFloat($data['elevation']) : null,
            generationTimeMs: isset($data['generationtime_ms']) ? Coerce::toFloat($data['generationtime_ms']) : null,
            utcOffsetSeconds: isset($data['utc_offset_seconds']) ? Coerce::toInt($data['utc_offset_seconds']) : null,
            timezoneAbbreviation: isset($data['timezone_abbreviation']) ? Coerce::toString($data['timezone_abbreviation']) : null,
        );
    }
}
