<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Contracts\HasMonthly;
use TempiMarathon\OpenMeteo\Data\MonthlySeries;
use TempiMarathon\OpenMeteo\Support\ProvidesMonthlySeries;

final readonly class MonthlySeriesStub implements HasMonthly
{
    use ProvidesMonthlySeries;

    public function __construct(private MonthlySeries $monthly) {}
}
