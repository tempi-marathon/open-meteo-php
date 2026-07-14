<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;

final readonly class ClimateResponse implements CoordinateResponse, HasDaily
{
    use ProvidesDailySeries;

    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public CoordinateMetadata $metadata,
        private DailySeries $daily,
        public DailyUnits $units,
    ) {}
}
