<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum EnsembleModel: string
{
    case DwdIconSeamlessEps = 'dwd_icon_seamless_eps';
    case DwdIconGlobalEps = 'dwd_icon_global_eps';
    case DwdIconEuEps = 'dwd_icon_eu_eps';
    case DwdIconD2Eps = 'dwd_icon_d2_eps';
    case NcepGefsSeamless = 'ncep_gefs_seamless';
    case NcepGefs025 = 'ncep_gefs025';
    case NcepGefs05 = 'ncep_gefs05';
    case NcepAigefs025 = 'ncep_aigefs025';
    case EcmwfIfs025Ensemble = 'ecmwf_ifs025_ensemble';
    case EcmwfAifs025Ensemble = 'ecmwf_aifs025_ensemble';
    case CmcGemGeps = 'cmc_gem_geps';
    case BomAccessGlobalEnsemble = 'bom_access_global_ensemble';
    case UkmoGlobalEnsemble20km = 'ukmo_global_ensemble_20km';
    case UkmoUkEnsemble2km = 'ukmo_uk_ensemble_2km';
    case MeteoswissIconCh1Ensemble = 'meteoswiss_icon_ch1_ensemble';
    case MeteoswissIconCh2Ensemble = 'meteoswiss_icon_ch2_ensemble';
}
