<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\HourlySeries;

interface HasHourly
{
    public function hourly(): HourlySeries;
}
