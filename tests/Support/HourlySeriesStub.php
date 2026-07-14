<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Contracts\HasHourly;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesHourlySeries;

final readonly class HourlySeriesStub implements HasHourly
{
    use ProvidesHourlySeries;

    public function __construct(private HourlySeries $hourly) {}
}
