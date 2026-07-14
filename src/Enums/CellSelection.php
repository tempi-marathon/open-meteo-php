<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum CellSelection: string
{
    case Land = 'land';
    case Sea = 'sea';
    case Nearest = 'nearest';
}
