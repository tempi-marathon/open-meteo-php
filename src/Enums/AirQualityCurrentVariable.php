<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum AirQualityCurrentVariable: string
{
    case EuropeanAqi = 'european_aqi';
    case UsAqi = 'us_aqi';
    case Pm10 = 'pm10';
    case Pm25 = 'pm2_5';
    case CarbonMonoxide = 'carbon_monoxide';
    case NitrogenDioxide = 'nitrogen_dioxide';
    case SulphurDioxide = 'sulphur_dioxide';
    case Ozone = 'ozone';
    case AerosolOpticalDepth = 'aerosol_optical_depth';
    case Dust = 'dust';
    case UvIndex = 'uv_index';
    case UvIndexClearSky = 'uv_index_clear_sky';
    case Ammonia = 'ammonia';
    case AlderPollen = 'alder_pollen';
    case BirchPollen = 'birch_pollen';
    case GrassPollen = 'grass_pollen';
    case MugwortPollen = 'mugwort_pollen';
    case OlivePollen = 'olive_pollen';
    case RagweedPollen = 'ragweed_pollen';
}
