<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use function Psl\Type\float;
use function Psl\Type\int;
use function Psl\Type\string;

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
            elevation: isset($data['elevation']) ? float()->coerce($data['elevation']) : null,
            generationTimeMs: isset($data['generationtime_ms']) ? float()->coerce($data['generationtime_ms']) : null,
            utcOffsetSeconds: isset($data['utc_offset_seconds']) ? int()->coerce($data['utc_offset_seconds']) : null,
            timezoneAbbreviation: isset($data['timezone_abbreviation']) ? string()->coerce($data['timezone_abbreviation']) : null,
        );
    }
}
