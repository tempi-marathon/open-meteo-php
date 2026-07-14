<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum SeasonalHourlyVariable: string
{
    case Temperature2m = 'temperature_2m';
    case DewPoint2m = 'dew_point_2m';
    case PressureMsl = 'pressure_msl';
    case SeaSurfaceTemperature = 'sea_surface_temperature';
    case SnowfallWaterEquivalent = 'snowfall_water_equivalent';
    case Precipitation = 'precipitation';
    case SoilTemperature0To7cm = 'soil_temperature_0_to_7cm';
    case CloudCover = 'cloud_cover';
    case WindSpeed10m = 'wind_speed_10m';
    case WindSpeed100m = 'wind_speed_100m';
    case WindSpeed200m = 'wind_speed_200m';
    case WindDirection10m = 'wind_direction_10m';
    case WindDirection100m = 'wind_direction_100m';
    case WindDirection200m = 'wind_direction_200m';
    case WindGusts10m = 'wind_gusts_10m';
    case Rain = 'rain';
    case Showers = 'showers';
    case Snowfall = 'snowfall';
    case RelativeHumidity2m = 'relative_humidity_2m';
    case ApparentTemperature = 'apparent_temperature';
    case Et0FaoEvapotranspiration = 'et0_fao_evapotranspiration';
    case VapourPressureDeficit = 'vapour_pressure_deficit';
    case WeatherCode = 'weather_code';
    case SunshineDuration = 'sunshine_duration';
    case WaveHeight = 'wave_height';
    case WaveDirection = 'wave_direction';
    case WavePeriod = 'wave_period';
    case WavePeakPeriod = 'wave_peak_period';
    case SoilTemperature7To28cm = 'soil_temperature_7_to_28cm';
    case SoilTemperature28To100cm = 'soil_temperature_28_to_100cm';
    case SoilTemperature100To255cm = 'soil_temperature_100_to_255cm';
    case SoilMoisture0To7cm = 'soil_moisture_0_to_7cm';
    case SoilMoisture7To28cm = 'soil_moisture_7_to_28cm';
    case SoilMoisture28To100cm = 'soil_moisture_28_to_100cm';
    case SoilMoisture100To255cm = 'soil_moisture_100_to_255cm';
    case ShortwaveRadiation = 'shortwave_radiation';
    case DirectRadiation = 'direct_radiation';
    case DiffuseRadiation = 'diffuse_radiation';
    case DirectNormalIrradiance = 'direct_normal_irradiance';
    case GlobalTiltedIrradiance = 'global_tilted_irradiance';
    case TerrestrialRadiation = 'terrestrial_radiation';
    case ShortwaveRadiationInstant = 'shortwave_radiation_instant';
    case DirectRadiationInstant = 'direct_radiation_instant';
    case DiffuseRadiationInstant = 'diffuse_radiation_instant';
    case DirectNormalIrradianceInstant = 'direct_normal_irradiance_instant';
    case GlobalTiltedIrradianceInstant = 'global_tilted_irradiance_instant';
    case TerrestrialRadiationInstant = 'terrestrial_radiation_instant';
}
