<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum HistoricalDailyVariable: string
{
    case WeatherCode = 'weather_code';
    case Temperature2mMean = 'temperature_2m_mean';
    case Temperature2mMax = 'temperature_2m_max';
    case Temperature2mMin = 'temperature_2m_min';
    case ApparentTemperatureMean = 'apparent_temperature_mean';
    case ApparentTemperatureMax = 'apparent_temperature_max';
    case ApparentTemperatureMin = 'apparent_temperature_min';
    case Sunrise = 'sunrise';
    case Sunset = 'sunset';
    case DaylightDuration = 'daylight_duration';
    case SunshineDuration = 'sunshine_duration';
    case PrecipitationSum = 'precipitation_sum';
    case RainSum = 'rain_sum';
    case SnowfallSum = 'snowfall_sum';
    case PrecipitationHours = 'precipitation_hours';
    case WindSpeed10mMax = 'wind_speed_10m_max';
    case WindGusts10mMax = 'wind_gusts_10m_max';
    case WindDirection10mDominant = 'wind_direction_10m_dominant';
    case ShortwaveRadiationSum = 'shortwave_radiation_sum';
    case Et0FaoEvapotranspiration = 'et0_fao_evapotranspiration';
    case CloudCoverMean = 'cloud_cover_mean';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case DewPoint2mMax = 'dew_point_2m_max';
    case DewPoint2mMin = 'dew_point_2m_min';
    case RelativeHumidity2mMean = 'relative_humidity_2m_mean';
    case RelativeHumidity2mMax = 'relative_humidity_2m_max';
    case RelativeHumidity2mMin = 'relative_humidity_2m_min';
    case PressureMslMean = 'pressure_msl_mean';
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WetBulbTemperature2mMean = 'wet_bulb_temperature_2m_mean';
    case VapourPressureDeficitMax = 'vapour_pressure_deficit_max';
    case SoilMoisture0To7cmMean = 'soil_moisture_0_to_7cm_mean';
    case SoilMoisture7To28cmMean = 'soil_moisture_7_to_28cm_mean';
    case SoilMoisture28To100cmMean = 'soil_moisture_28_to_100cm_mean';
    case SoilMoisture0To100cmMean = 'soil_moisture_0_to_100cm_mean';
    case SoilTemperature0To7cmMean = 'soil_temperature_0_to_7cm_mean';
    case SoilTemperature7To28cmMean = 'soil_temperature_7_to_28cm_mean';
    case SoilTemperature28To100cmMean = 'soil_temperature_28_to_100cm_mean';
}
