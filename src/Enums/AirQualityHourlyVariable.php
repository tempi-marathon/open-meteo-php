<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum AirQualityHourlyVariable: string
{
    case Pm10 = 'pm10';
    case Pm25 = 'pm2_5';
    case CarbonMonoxide = 'carbon_monoxide';
    case CarbonDioxide = 'carbon_dioxide';
    case NitrogenDioxide = 'nitrogen_dioxide';
    case SulphurDioxide = 'sulphur_dioxide';
    case Ozone = 'ozone';
    case AerosolOpticalDepth = 'aerosol_optical_depth';
    case Dust = 'dust';
    case UvIndex = 'uv_index';
    case UvIndexClearSky = 'uv_index_clear_sky';
    case Ammonia = 'ammonia';
    case Methane = 'methane';
    case AlderPollen = 'alder_pollen';
    case BirchPollen = 'birch_pollen';
    case GrassPollen = 'grass_pollen';
    case MugwortPollen = 'mugwort_pollen';
    case OlivePollen = 'olive_pollen';
    case RagweedPollen = 'ragweed_pollen';
    case Formaldehyde = 'formaldehyde';
    case Glyoxal = 'glyoxal';
    case NonMethaneVolatileOrganicCompounds = 'non_methane_volatile_organic_compounds';
    case Pm10Wildfires = 'pm10_wildfires';
    case PeroxyacylNitrates = 'peroxyacyl_nitrates';
    case SecondaryInorganicAerosol = 'secondary_inorganic_aerosol';
    case ResidentialElementaryCarbon = 'residential_elementary_carbon';
    case TotalElementaryCarbon = 'total_elementary_carbon';
    case Pm25TotalOrganicMatter = 'pm2_5_total_organic_matter';
    case SeaSaltAerosol = 'sea_salt_aerosol';
    case NitrogenMonoxide = 'nitrogen_monoxide';
    case EuropeanAqi = 'european_aqi';
    case EuropeanAqiPm25 = 'european_aqi_pm2_5';
    case EuropeanAqiPm10 = 'european_aqi_pm10';
    case EuropeanAqiNitrogenDioxide = 'european_aqi_nitrogen_dioxide';
    case EuropeanAqiOzone = 'european_aqi_ozone';
    case EuropeanAqiSulphurDioxide = 'european_aqi_sulphur_dioxide';
    case UsAqi = 'us_aqi';
    case UsAqiPm25 = 'us_aqi_pm2_5';
    case UsAqiPm10 = 'us_aqi_pm10';
    case UsAqiNitrogenDioxide = 'us_aqi_nitrogen_dioxide';
    case UsAqiCarbonMonoxide = 'us_aqi_carbon_monoxide';
    case UsAqiOzone = 'us_aqi_ozone';
    case UsAqiSulphurDioxide = 'us_aqi_sulphur_dioxide';
    case IsDay = 'is_day';
}
