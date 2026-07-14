<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum FloodModel: string
{
    case SeamlessV4 = 'seamless_v4';
    case ForecastV4 = 'forecast_v4';
    case ConsolidatedV4 = 'consolidated_v4';
    case SeamlessV3 = 'seamless_v3';
    case ForecastV3 = 'forecast_v3';
    case ConsolidatedV3 = 'consolidated_v3';
}
