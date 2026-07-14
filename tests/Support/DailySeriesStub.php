<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Contracts\HasDaily;
use TempiMarathon\OpenMeteo\Data\DailySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesDailySeries;

final readonly class DailySeriesStub implements HasDaily
{
    use ProvidesDailySeries;

    public function __construct(private DailySeries $daily) {}
}
