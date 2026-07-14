<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum ForecastModel: string
{
    case BestMatch = 'best_match';
    case EcmwfIfs = 'ecmwf_ifs';
    case EcmwfIfs025 = 'ecmwf_ifs025';
    case EcmwfAifs025Single = 'ecmwf_aifs025_single';
    case CmaGrapesGlobal = 'cma_grapes_global';
    case BomAccessGlobal = 'bom_access_global';
    case NcepGfsSeamless = 'ncep_gfs_seamless';
    case NcepGfsGlobal = 'ncep_gfs_global';
    case NcepHrrrConus = 'ncep_hrrr_conus';
    case NcepNbmConus = 'ncep_nbm_conus';
    case NcepNamConus = 'ncep_nam_conus';
    case NcepGfsGraphcast025 = 'ncep_gfs_graphcast025';
    case NcepAigfs025 = 'ncep_aigfs025';
    case NcepHgefs025EnsembleMean = 'ncep_hgefs025_ensemble_mean';
    case JmaSeamless = 'jma_seamless';
    case JmaMsm = 'jma_msm';
    case JmaGsm = 'jma_gsm';
    case KmaSeamless = 'kma_seamless';
    case KmaLdps = 'kma_ldps';
    case KmaGdps = 'kma_gdps';
    case IconSeamless = 'icon_seamless';
    case IconGlobal = 'icon_global';
    case IconEu = 'icon_eu';
    case IconD2 = 'icon_d2';
    case CmcGemSeamless = 'cmc_gem_seamless';
    case CmcGemGdps = 'cmc_gem_gdps';
    case CmcGemRdps = 'cmc_gem_rdps';
    case CmcGemHrdps = 'cmc_gem_hrdps';
    case CmcGemHrdpsWest = 'cmc_gem_hrdps_west';
    case MeteofranceSeamless = 'meteofrance_seamless';
    case MeteofranceArpegeWorld = 'meteofrance_arpege_world';
    case MeteofranceArpegeEurope = 'meteofrance_arpege_europe';
    case MeteofranceAromeFrance = 'meteofrance_arome_france';
    case MeteofranceAromeFranceHd = 'meteofrance_arome_france_hd';
    case ItaliaMeteoArpaeIcon2i = 'italia_meteo_arpae_icon_2i';
    case MetnoSeamless = 'metno_seamless';
    case MetnoNordic = 'metno_nordic';
    case KnmiSeamless = 'knmi_seamless';
    case KnmiHarmonieAromeEurope = 'knmi_harmonie_arome_europe';
    case KnmiHarmonieAromeNetherlands = 'knmi_harmonie_arome_netherlands';
    case DmiSeamless = 'dmi_seamless';
    case DmiHarmonieAromeEurope = 'dmi_harmonie_arome_europe';
    case UkmoSeamless = 'ukmo_seamless';
    case UkmoGlobalDeterministic10km = 'ukmo_global_deterministic_10km';
    case UkmoUkDeterministic2km = 'ukmo_uk_deterministic_2km';
    case MeteoswissIconSeamless = 'meteoswiss_icon_seamless';
    case MeteoswissIconCh1 = 'meteoswiss_icon_ch1';
    case MeteoswissIconCh2 = 'meteoswiss_icon_ch2';
    case GeosphereSeamless = 'geosphere_seamless';
    case GeosphereAromeAustria = 'geosphere_arome_austria';
}
