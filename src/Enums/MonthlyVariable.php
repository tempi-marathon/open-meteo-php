<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MonthlyVariable: string
{
    case AlbedoAnomaly = 'albedo_anomaly';
    case AlbedoMean = 'albedo_mean';
    case CloudCoverAnomaly = 'cloud_cover_anomaly';
    case CloudCoverLowAnomaly = 'cloud_cover_low_anomaly';
    case CloudCoverLowMean = 'cloud_cover_low_mean';
    case CloudCoverMean = 'cloud_cover_mean';
    case DewPoint2mAnomaly = 'dew_point_2m_anomaly';
    case DewPoint2mMean = 'dew_point_2m_mean';
    case EvapotranspirationAnomaly = 'evapotranspiration_anomaly';
    case EvapotranspirationMean = 'evapotranspiration_mean';
    case LatentHeatFluxAnomaly = 'latent_heat_flux_anomaly';
    case LatentHeatFluxMean = 'latent_heat_flux_mean';
    case LongwaveRadiationAnomaly = 'longwave_radiation_anomaly';
    case LongwaveRadiationMean = 'longwave_radiation_mean';
    case PrecipitationAnomaly = 'precipitation_anomaly';
    case PrecipitationMean = 'precipitation_mean';
    case PressureMslAnomaly = 'pressure_msl_anomaly';
    case PressureMslMean = 'pressure_msl_mean';
    case RunoffAnomaly = 'runoff_anomaly';
    case RunoffMean = 'runoff_mean';
    case SeaIceCoverAnomaly = 'sea_ice_cover_anomaly';
    case SeaIceCoverMean = 'sea_ice_cover_mean';
    case SeaSurfaceTemperatureAnomaly = 'sea_surface_temperature_anomaly';
    case SeaSurfaceTemperatureMean = 'sea_surface_temperature_mean';
    case SensibleHeatFluxAnomaly = 'sensible_heat_flux_anomaly';
    case SensibleHeatFluxMean = 'sensible_heat_flux_mean';
    case ShortwaveRadiationAnomaly = 'shortwave_radiation_anomaly';
    case ShortwaveRadiationMean = 'shortwave_radiation_mean';
    case ShowersAnomaly = 'showers_anomaly';
    case ShowersMean = 'showers_mean';
    case SnowDensityAnomaly = 'snow_density_anomaly';
    case SnowDensityMean = 'snow_density_mean';
    case SnowDepthAnomaly = 'snow_depth_anomaly';
    case SnowDepthMean = 'snow_depth_mean';
    case SnowDepthWaterEquivalentAnomaly = 'snow_depth_water_equivalent_anomaly';
    case SnowDepthWaterEquivalentMean = 'snow_depth_water_equivalent_mean';
    case SnowfallAnomaly = 'snowfall_anomaly';
    case SnowfallMean = 'snowfall_mean';
    case SnowfallWaterEquivalentAnomaly = 'snowfall_water_equivalent_anomaly';
    case SnowfallWaterEquivalentMean = 'snowfall_water_equivalent_mean';
    case SoilMoisture0To7cmAnomaly = 'soil_moisture_0_to_7cm_anomaly';
    case SoilMoisture0To7cmMean = 'soil_moisture_0_to_7cm_mean';
    case SoilMoisture100To255cmAnomaly = 'soil_moisture_100_to_255cm_anomaly';
    case SoilMoisture100To255cmMean = 'soil_moisture_100_to_255cm_mean';
    case SoilMoisture28To100cmAnomaly = 'soil_moisture_28_to_100cm_anomaly';
    case SoilMoisture28To100cmMean = 'soil_moisture_28_to_100cm_mean';
    case SoilMoisture7To28cmAnomaly = 'soil_moisture_7_to_28cm_anomaly';
    case SoilMoisture7To28cmMean = 'soil_moisture_7_to_28cm_mean';
    case SoilTemperature0To7cmAnomaly = 'soil_temperature_0_to_7cm_anomaly';
    case SoilTemperature0To7cmMean = 'soil_temperature_0_to_7cm_mean';
    case SoilTemperature100To255cmAnomaly = 'soil_temperature_100_to_255cm_anomaly';
    case SoilTemperature100To255cmMean = 'soil_temperature_100_to_255cm_mean';
    case SoilTemperature28To100cmAnomaly = 'soil_temperature_28_to_100cm_anomaly';
    case SoilTemperature28To100cmMean = 'soil_temperature_28_to_100cm_mean';
    case SoilTemperature7To28cmAnomaly = 'soil_temperature_7_to_28cm_anomaly';
    case SoilTemperature7To28cmMean = 'soil_temperature_7_to_28cm_mean';
    case SunshineDurationAnomaly = 'sunshine_duration_anomaly';
    case SunshineDurationMean = 'sunshine_duration_mean';
    case Temperature2mAnomaly = 'temperature_2m_anomaly';
    case Temperature2mMean = 'temperature_2m_mean';
    case TemperatureMax24h2mAnomaly = 'temperature_max24h_2m_anomaly';
    case TemperatureMax24h2mMean = 'temperature_max24h_2m_mean';
    case TemperatureMin24h2mAnomaly = 'temperature_min24h_2m_anomaly';
    case TemperatureMin24h2mMean = 'temperature_min24h_2m_mean';
    case TotalColumnIntegratedWaterVapourAnomaly = 'total_column_integrated_water_vapour_anomaly';
    case TotalColumnIntegratedWaterVapourMean = 'total_column_integrated_water_vapour_mean';
    case WindGusts10mAnomaly = 'wind_gusts_10m_anomaly';
    case WindSpeed10mAnomaly = 'wind_speed_10m_anomaly';
    case WindSpeed10mMean = 'wind_speed_10m_mean';
    case WindUComponent10mAnomaly = 'wind_u_component_10m_anomaly';
    case WindUComponent10mMean = 'wind_u_component_10m_mean';
    case WindVComponent10mAnomaly = 'wind_v_component_10m_anomaly';
    case WindVComponent10mMean = 'wind_v_component_10m_mean';
}
