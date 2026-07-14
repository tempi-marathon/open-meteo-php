<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Contracts\HasMonthly;
use TempiMarathon\OpenMeteo\Contracts\HasWeekly;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesMonthlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesWeeklySeries;

final readonly class SeasonalResponse implements CoordinateResponse, HasDaily, HasHourly, HasMonthly, HasWeekly
{
    use ProvidesDailySeries;
    use ProvidesHourlySeries;
    use ProvidesMonthlySeries;
    use ProvidesWeeklySeries;

    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public CoordinateMetadata $metadata,
        private HourlySeries $hourly,
        private DailySeries $daily,
        private WeeklySeries $weekly,
        private MonthlySeries $monthly,
        public SeasonalUnits $units,
    ) {}
}
