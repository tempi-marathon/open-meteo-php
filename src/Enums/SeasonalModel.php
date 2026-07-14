<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum SeasonalModel: string
{
    case BestMatch = 'best_match';
    case EcmwfSeasonalSeamless = 'ecmwf_seasonal_seamless';
    case EcmwfSeas5 = 'ecmwf_seas5';
    case EcmwfEc46 = 'ecmwf_ec46';
    case EcmwfSeasonalEnsembleMeanSeamless = 'ecmwf_seasonal_ensemble_mean_seamless';
    case EcmwfSeas5EnsembleMean = 'ecmwf_seas5_ensemble_mean';
    case EcmwfEc46EnsembleMean = 'ecmwf_ec46_ensemble_mean';
}
