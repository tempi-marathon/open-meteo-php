<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

final readonly class ElevationResponse
{
    /** @param list<float> $elevation */
    public function __construct(public array $elevation) {}
}
