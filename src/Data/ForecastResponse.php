<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasCurrent;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Contracts\HasMinutely15;
use TempiMarathon\OpenMeteo\Support\ProvidesCurrentSeries;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesMinutely15Series;

final readonly class ForecastResponse implements CoordinateResponse, HasCurrent, HasDaily, HasHourly, HasMinutely15
{
    use ProvidesCurrentSeries;
    use ProvidesDailySeries;
    use ProvidesHourlySeries;
    use ProvidesMinutely15Series;

    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public CoordinateMetadata $metadata,
        private HourlySeries $hourly,
        private DailySeries $daily,
        private Minutely15Series $minutely15,
        private CurrentSeries $current,
        public ForecastUnits $units,
    ) {}
}
