<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum PrecipitationUnit: string
{
    case Mm = 'mm';
    case Inch = 'inch';
}
