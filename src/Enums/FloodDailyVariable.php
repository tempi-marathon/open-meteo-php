<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum FloodDailyVariable: string
{
    case RiverDischarge = 'river_discharge';
    case RiverDischargeMean = 'river_discharge_mean';
    case RiverDischargeMedian = 'river_discharge_median';
    case RiverDischargeMax = 'river_discharge_max';
    case RiverDischargeMin = 'river_discharge_min';
    case RiverDischargeP25 = 'river_discharge_p25';
    case RiverDischargeP75 = 'river_discharge_p75';
}
