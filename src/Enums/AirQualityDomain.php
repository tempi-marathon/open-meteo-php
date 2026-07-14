<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum AirQualityDomain: string
{
    case Auto = 'auto';
    case CamsEurope = 'cams_europe';
    case CamsGlobal = 'cams_global';
}
