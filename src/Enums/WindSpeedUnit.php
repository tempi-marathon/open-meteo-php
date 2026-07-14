<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum WindSpeedUnit: string
{
    case Kmh = 'kmh';
    case Ms = 'ms';
    case Mph = 'mph';
    case Kn = 'kn';
}
