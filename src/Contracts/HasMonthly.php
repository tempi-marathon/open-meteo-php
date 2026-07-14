<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\MonthlySeries;

interface HasMonthly
{
    public function monthly(): MonthlySeries;
}
