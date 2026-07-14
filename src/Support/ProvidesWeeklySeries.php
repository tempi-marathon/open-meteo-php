<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\WeeklySeries;

trait ProvidesWeeklySeries
{
    public function weekly(): WeeklySeries
    {
        return $this->weekly;
    }
}
