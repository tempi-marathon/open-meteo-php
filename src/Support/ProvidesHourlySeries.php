<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\HourlySeries;

trait ProvidesHourlySeries
{
    public function hourly(): HourlySeries
    {
        return $this->hourly;
    }
}
