<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasCurrent;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Support\ProvidesCurrentSeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;

final readonly class AirQualityResponse implements CoordinateResponse, HasCurrent, HasHourly
{
    use ProvidesCurrentSeries;
    use ProvidesHourlySeries;

    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public CoordinateMetadata $metadata,
        private HourlySeries $hourly,
        private CurrentSeries $current,
        public AirQualityUnits $units,
    ) {}
}
