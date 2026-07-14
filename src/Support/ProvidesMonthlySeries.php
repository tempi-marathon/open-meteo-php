<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\MonthlySeries;

trait ProvidesMonthlySeries
{
    public function monthly(): MonthlySeries
    {
        return $this->monthly;
    }
}
