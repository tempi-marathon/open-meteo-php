<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MarineMinutely15Variable: string
{
    case OceanCurrentVelocity = 'ocean_current_velocity';
    case OceanCurrentDirection = 'ocean_current_direction';
    case SeaLevelHeightMsl = 'sea_level_height_msl';
}
