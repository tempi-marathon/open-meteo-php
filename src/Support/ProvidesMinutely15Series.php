<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Data\Minutely15Series;

trait ProvidesMinutely15Series
{
    public function minutely15(): Minutely15Series
    {
        return $this->minutely15;
    }
}
