<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum ClimateDailyVariable: string
{
    case Temperature2mMax = 'temperature_2m_max';
    case Temperature2mMin = 'temperature_2m_min';
    case Temperature2mMean = 'temperature_2m_mean';
    case PressureMslMean = 'pressure_msl_mean';
    case CloudCoverMean = 'cloud_cover_mean';
    case PrecipitationSum = 'precipitation_sum';
    case SnowfallWaterEquivalentSum = 'snowfall_water_equivalent_sum';
    case SnowfallSum = 'snowfall_sum';
    case RainSum = 'rain_sum';
    case RelativeHumidity2mMin = 'relative_humidity_2m_min';
    case RelativeHumidity2mMax = 'relative_humidity_2m_max';
    case RelativeHumidity2mMean = 'relative_humidity_2m_mean';
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WindSpeed10mMax = 'wind_speed_10m_max';
    case WindGusts10mMean = 'wind_gusts_10m_mean';
    case WindGusts10mMax = 'wind_gusts_10m_max';
    case SoilMoisture0To10cmMean = 'soil_moisture_0_to_10cm_mean';
    case SoilMoisture0To100cmMean = 'soil_moisture_0_to_100cm_mean';
    case SoilMoisture0To7cmMean = 'soil_moisture_0_to_7cm_mean';
    case SoilMoisture7To28cmMean = 'soil_moisture_7_to_28cm_mean';
    case SoilMoisture28To100cmMean = 'soil_moisture_28_to_100cm_mean';
    case SoilMoistureIndex0To7cmMean = 'soil_moisture_index_0_to_7cm_mean';
    case SoilMoistureIndex0To100cmMean = 'soil_moisture_index_0_to_100cm_mean';
    case SoilMoistureIndex7To28cmMean = 'soil_moisture_index_7_to_28cm_mean';
    case SoilMoistureIndex28To100cmMean = 'soil_moisture_index_28_to_100cm_mean';
    case SoilTemperature0To100cmMean = 'soil_temperature_0_to_100cm_mean';
    case SoilTemperature0To7cmMean = 'soil_temperature_0_to_7cm_mean';
    case SoilTemperature7To28cmMean = 'soil_temperature_7_to_28cm_mean';
    case SoilTemperature28To100cmMean = 'soil_temperature_28_to_100cm_mean';
    case ShortwaveRadiationSum = 'shortwave_radiation_sum';
    case Et0FaoEvapotranspirationSum = 'et0_fao_evapotranspiration_sum';
    case VapourPressureDeficitMax = 'vapour_pressure_deficit_max';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case DewPoint2mMax = 'dew_point_2m_max';
    case DewPoint2mMin = 'dew_point_2m_min';
    case GrowingDegreeDaysBase0Limit50 = 'growing_degree_days_base_0_limit_50';
    case LeafWetnessProbabilityMean = 'leaf_wetness_probability_mean';
    case DaylightDuration = 'daylight_duration';
}
