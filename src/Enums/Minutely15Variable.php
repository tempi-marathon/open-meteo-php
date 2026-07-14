<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum Minutely15Variable: string
{
    case ApparentTemperature = 'apparent_temperature';
    case Cape = 'cape';
    case DewPoint2m = 'dew_point_2m';
    case FreezingLevelHeight = 'freezing_level_height';
    case IsDay = 'is_day';
    case LightningPotential = 'lightning_potential';
    case OceanCurrentDirection = 'ocean_current_direction';
    case OceanCurrentVelocity = 'ocean_current_velocity';
    case Precipitation = 'precipitation';
    case Rain = 'rain';
    case RelativeHumidity2m = 'relative_humidity_2m';
    case SeaLevelHeightMsl = 'sea_level_height_msl';
    case Snowfall = 'snowfall';
    case SnowfallHeight = 'snowfall_height';
    case SunshineDuration = 'sunshine_duration';
    case Temperature2m = 'temperature_2m';
    case Visibility = 'visibility';
    case WeatherCode = 'weather_code';
    case WindDirection100m = 'wind_direction_100m';
    case WindDirection10m = 'wind_direction_10m';
    case WindDirection20m = 'wind_direction_20m';
    case WindDirection50m = 'wind_direction_50m';
    case WindDirection80m = 'wind_direction_80m';
    case WindGusts10m = 'wind_gusts_10m';
    case WindSpeed100m = 'wind_speed_100m';
    case WindSpeed10m = 'wind_speed_10m';
    case WindSpeed20m = 'wind_speed_20m';
    case WindSpeed50m = 'wind_speed_50m';
    case WindSpeed80m = 'wind_speed_80m';
}
