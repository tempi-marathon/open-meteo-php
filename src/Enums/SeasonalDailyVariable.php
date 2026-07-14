<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum SeasonalDailyVariable: string
{
    case Sunrise = 'sunrise';
    case Sunset = 'sunset';
    case WeatherCode = 'weather_code';
    case Et0FaoEvapotranspiration = 'et0_fao_evapotranspiration';
    case VapourPressureDeficitMax = 'vapour_pressure_deficit_max';
    case Temperature2mMax = 'temperature_2m_max';
    case Temperature2mMin = 'temperature_2m_min';
    case Temperature2mMean = 'temperature_2m_mean';
    case SunshineDuration = 'sunshine_duration';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case PressureMslMean = 'pressure_msl_mean';
    case SeaSurfaceTemperatureMean = 'sea_surface_temperature_mean';
    case CloudCoverMean = 'cloud_cover_mean';
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WindSpeed100mMean = 'wind_speed_100m_mean';
    case SnowDepthMean = 'snow_depth_mean';
    case SoilTemperature0To7cmMean = 'soil_temperature_0_to_7cm_mean';
}
