<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;

final readonly class EnsembleResponse implements CoordinateResponse, HasDaily, HasHourly
{
    use ProvidesDailySeries;
    use ProvidesHourlySeries;

    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public CoordinateMetadata $metadata,
        private HourlySeries $hourly,
        private DailySeries $daily,
        public EnsembleUnits $units,
    ) {}
}
