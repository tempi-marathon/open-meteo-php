<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum CurrentVariable: string
{
    case AerosolOpticalDepth = 'aerosol_optical_depth';
    case AlderPollen = 'alder_pollen';
    case Ammonia = 'ammonia';
    case ApparentTemperature = 'apparent_temperature';
    case BirchPollen = 'birch_pollen';
    case CarbonMonoxide = 'carbon_monoxide';
    case CloudCover = 'cloud_cover';
    case Dust = 'dust';
    case EuropeanAqi = 'european_aqi';
    case GrassPollen = 'grass_pollen';
    case IsDay = 'is_day';
    case MugwortPollen = 'mugwort_pollen';
    case NitrogenDioxide = 'nitrogen_dioxide';
    case OceanCurrentDirection = 'ocean_current_direction';
    case OceanCurrentVelocity = 'ocean_current_velocity';
    case OlivePollen = 'olive_pollen';
    case Ozone = 'ozone';
    case Pm10 = 'pm10';
    case Pm25 = 'pm2_5';
    case Precipitation = 'precipitation';
    case PressureMsl = 'pressure_msl';
    case RagweedPollen = 'ragweed_pollen';
    case Rain = 'rain';
    case RelativeHumidity2m = 'relative_humidity_2m';
    case SeaSurfaceTemperature = 'sea_surface_temperature';
    case Showers = 'showers';
    case Snowfall = 'snowfall';
    case SulphurDioxide = 'sulphur_dioxide';
    case SurfacePressure = 'surface_pressure';
    case SwellWaveDirection = 'swell_wave_direction';
    case SwellWaveHeight = 'swell_wave_height';
    case SwellWavePeakPeriod = 'swell_wave_peak_period';
    case SwellWavePeriod = 'swell_wave_period';
    case Temperature2m = 'temperature_2m';
    case UsAqi = 'us_aqi';
    case UvIndex = 'uv_index';
    case UvIndexClearSky = 'uv_index_clear_sky';
    case WaveDirection = 'wave_direction';
    case WaveHeight = 'wave_height';
    case WavePeriod = 'wave_period';
    case WeatherCode = 'weather_code';
    case WindDirection10m = 'wind_direction_10m';
    case WindGusts10m = 'wind_gusts_10m';
    case WindSpeed10m = 'wind_speed_10m';
    case WindWaveDirection = 'wind_wave_direction';
    case WindWaveHeight = 'wind_wave_height';
    case WindWavePeakPeriod = 'wind_wave_peak_period';
    case WindWavePeriod = 'wind_wave_period';
}
