<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum LengthUnit: string
{
    case Metric = 'metric';
    case Imperial = 'imperial';
}
