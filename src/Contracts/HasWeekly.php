<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\WeeklySeries;

interface HasWeekly
{
    public function weekly(): WeeklySeries;
}
