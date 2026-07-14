<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\CurrentSeries;

interface HasCurrent
{
    public function current(): CurrentSeries;
}
