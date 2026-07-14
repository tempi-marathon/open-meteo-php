<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\DailySeries;

interface HasDaily
{
    public function daily(): DailySeries;
}
