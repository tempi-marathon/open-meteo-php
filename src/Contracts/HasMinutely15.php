<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\Minutely15Series;

interface HasMinutely15
{
    public function minutely15(): Minutely15Series;
}
