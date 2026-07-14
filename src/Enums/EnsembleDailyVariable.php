<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum EnsembleDailyVariable: string
{
    case Temperature2mMean = 'temperature_2m_mean';
    case Temperature2mMin = 'temperature_2m_min';
    case Temperature2mMax = 'temperature_2m_max';
    case ApparentTemperatureMean = 'apparent_temperature_mean';
    case ApparentTemperatureMin = 'apparent_temperature_min';
    case ApparentTemperatureMax = 'apparent_temperature_max';
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WindSpeed10mMin = 'wind_speed_10m_min';
    case WindSpeed10mMax = 'wind_speed_10m_max';
    case WindDirection10mDominant = 'wind_direction_10m_dominant';
    case WindGusts10mMean = 'wind_gusts_10m_mean';
    case WindGusts10mMin = 'wind_gusts_10m_min';
    case WindGusts10mMax = 'wind_gusts_10m_max';
    case WindSpeed100mMean = 'wind_speed_100m_mean';
    case WindSpeed100mMin = 'wind_speed_100m_min';
    case WindSpeed100mMax = 'wind_speed_100m_max';
    case WindDirection100mDominant = 'wind_direction_100m_dominant';
    case CloudCoverMean = 'cloud_cover_mean';
    case CloudCoverMin = 'cloud_cover_min';
    case CloudCoverMax = 'cloud_cover_max';
    case PrecipitationSum = 'precipitation_sum';
    case PrecipitationHours = 'precipitation_hours';
    case RainSum = 'rain_sum';
    case SnowfallSum = 'snowfall_sum';
    case PressureMslMean = 'pressure_msl_mean';
    case PressureMslMin = 'pressure_msl_min';
    case PressureMslMax = 'pressure_msl_max';
    case SurfacePressureMean = 'surface_pressure_mean';
    case SurfacePressureMin = 'surface_pressure_min';
    case SurfacePressureMax = 'surface_pressure_max';
    case RelativeHumidity2mMean = 'relative_humidity_2m_mean';
    case RelativeHumidity2mMin = 'relative_humidity_2m_min';
    case RelativeHumidity2mMax = 'relative_humidity_2m_max';
    case CapeMean = 'cape_mean';
    case CapeMin = 'cape_min';
    case CapeMax = 'cape_max';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case DewPoint2mMin = 'dew_point_2m_min';
    case DewPoint2mMax = 'dew_point_2m_max';
    case Et0FaoEvapotranspiration = 'et0_fao_evapotranspiration';
    case ShortwaveRadiationSum = 'shortwave_radiation_sum';
}
