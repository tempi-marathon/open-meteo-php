<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MarineModel: string
{
    case BestMatch = 'best_match';
    case MeteofranceWave = 'meteofrance_wave';
    case MeteofranceCurrents = 'meteofrance_currents';
    case DwdEwam = 'dwd_ewam';
    case DwdGwam = 'dwd_gwam';
    case EcmwfWam = 'ecmwf_wam';
    case EcmwfWam025 = 'ecmwf_wam025';
    case NcepGfswave025 = 'ncep_gfswave025';
    case NcepGfswave016 = 'ncep_gfswave016';
    case Era5Ocean = 'era5_ocean';
}
