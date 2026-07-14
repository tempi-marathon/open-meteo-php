<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum SeasonalWeeklyVariable: string
{
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WindSpeed10mAnomaly = 'wind_speed_10m_anomaly';
    case WindSpeed100mMean = 'wind_speed_100m_mean';
    case WindSpeed100mAnomaly = 'wind_speed_100m_anomaly';
    case WindDirection10mMean = 'wind_direction_10m_mean';
    case WindDirection10mAnomaly = 'wind_direction_10m_anomaly';
    case WindDirection100mMean = 'wind_direction_100m_mean';
    case WindDirection100mAnomaly = 'wind_direction_100m_anomaly';
    case SnowDepthMean = 'snow_depth_mean';
    case SnowDepthAnomaly = 'snow_depth_anomaly';
    case SnowfallMean = 'snowfall_mean';
    case SnowfallAnomaly = 'snowfall_anomaly';
    case Temperature2mAnomalyGt0 = 'temperature_2m_anomaly_gt0';
    case Temperature2mAnomalyGt1 = 'temperature_2m_anomaly_gt1';
    case Temperature2mAnomalyGt2 = 'temperature_2m_anomaly_gt2';
    case Temperature2mAnomalyLtm1 = 'temperature_2m_anomaly_ltm1';
    case Temperature2mAnomalyLtm2 = 'temperature_2m_anomaly_ltm2';
    case PressureMslAnomalyGt0 = 'pressure_msl_anomaly_gt0';
    case SurfaceTemperatureAnomalyGt0 = 'surface_temperature_anomaly_gt0';
    case PrecipitationAnomalyGt0 = 'precipitation_anomaly_gt0';
    case PrecipitationAnomalyGt10 = 'precipitation_anomaly_gt10';
    case PrecipitationAnomalyGt20 = 'precipitation_anomaly_gt20';
    case Temperature2mSot10 = 'temperature_2m_sot10';
    case Temperature2mSot90 = 'temperature_2m_sot90';
    case Temperature2mEfi = 'temperature_2m_efi';
    case PrecipitationEfi = 'precipitation_efi';
    case PrecipitationSot90 = 'precipitation_sot90';
    case ShowersMean = 'showers_mean';
    case SnowDensityMean = 'snow_density_mean';
    case SnowDensityAnomaly = 'snow_density_anomaly';
    case SnowDepthWaterEquivalentMean = 'snow_depth_water_equivalent_mean';
    case SnowDepthWaterEquivalentAnomaly = 'snow_depth_water_equivalent_anomaly';
    case TotalColumnIntegratedWaterVapourMean = 'total_column_integrated_water_vapour_mean';
    case TotalColumnIntegratedWaterVapourAnomaly = 'total_column_integrated_water_vapour_anomaly';
    case Temperature2mMean = 'temperature_2m_mean';
    case Temperature2mAnomaly = 'temperature_2m_anomaly';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case DewPoint2mAnomaly = 'dew_point_2m_anomaly';
    case PressureMslMean = 'pressure_msl_mean';
    case PressureMslAnomaly = 'pressure_msl_anomaly';
    case SeaSurfaceTemperatureMean = 'sea_surface_temperature_mean';
    case SeaSurfaceTemperatureAnomaly = 'sea_surface_temperature_anomaly';
    case WindUComponent10mMean = 'wind_u_component_10m_mean';
    case WindUComponent10mAnomaly = 'wind_u_component_10m_anomaly';
    case WindVComponent10mMean = 'wind_v_component_10m_mean';
    case WindVComponent10mAnomaly = 'wind_v_component_10m_anomaly';
    case WindUComponent100mMean = 'wind_u_component_100m_mean';
    case WindUComponent100mAnomaly = 'wind_u_component_100m_anomaly';
    case WindVComponent100mMean = 'wind_v_component_100m_mean';
    case WindVComponent100mAnomaly = 'wind_v_component_100m_anomaly';
    case SnowfallWaterEquivalentMean = 'snowfall_water_equivalent_mean';
    case SnowfallWaterEquivalentAnomaly = 'snowfall_water_equivalent_anomaly';
    case PrecipitationMean = 'precipitation_mean';
    case PrecipitationAnomaly = 'precipitation_anomaly';
    case CloudCoverMean = 'cloud_cover_mean';
    case CloudCoverAnomaly = 'cloud_cover_anomaly';
    case SunshineDurationMean = 'sunshine_duration_mean';
    case SunshineDurationAnomaly = 'sunshine_duration_anomaly';
    case SoilTemperature0To7cmMean = 'soil_temperature_0_to_7cm_mean';
    case SoilTemperature0To7cmAnomaly = 'soil_temperature_0_to_7cm_anomaly';
    case TemperatureMax6h2mMean = 'temperature_max6h_2m_mean';
    case TemperatureMax6h2mAnomaly = 'temperature_max6h_2m_anomaly';
    case TemperatureMin6h2mMean = 'temperature_min6h_2m_mean';
    case TemperatureMin6h2mAnomaly = 'temperature_min6h_2m_anomaly';
}
