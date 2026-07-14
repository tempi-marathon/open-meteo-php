<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\DailySeries;

trait ProvidesDailySeries
{
    public function daily(): DailySeries
    {
        return $this->daily;
    }
}
