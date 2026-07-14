<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\CurrentSeries;

trait ProvidesCurrentSeries
{
    public function current(): CurrentSeries
    {
        return $this->current;
    }
}
