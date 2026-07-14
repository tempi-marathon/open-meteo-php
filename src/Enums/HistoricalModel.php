<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum HistoricalModel: string
{
    case BestMatch = 'best_match';
    case Era5Seamless = 'era5_seamless';
    case Era5 = 'era5';
    case Era5Land = 'era5_land';
    case EcmwfIfs = 'ecmwf_ifs';
    case Cerra = 'cerra';
    case Era5Ensemble = 'era5_ensemble';
    case EcmwfIfsAnalysisLongWindow = 'ecmwf_ifs_analysis_long_window';
}
