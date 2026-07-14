<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum ForecastMinutely15Variable: string
{
    case Temperature2m = 'temperature_2m';
    case RelativeHumidity2m = 'relative_humidity_2m';
    case DewPoint2m = 'dew_point_2m';
    case ApparentTemperature = 'apparent_temperature';
    case Precipitation = 'precipitation';
    case Rain = 'rain';
    case Snowfall = 'snowfall';
    case SnowfallHeight = 'snowfall_height';
    case FreezingLevelHeight = 'freezing_level_height';
    case SunshineDuration = 'sunshine_duration';
    case WeatherCode = 'weather_code';
    case WindSpeed10m = 'wind_speed_10m';
    case WindSpeed20m = 'wind_speed_20m';
    case WindSpeed50m = 'wind_speed_50m';
    case WindSpeed80m = 'wind_speed_80m';
    case WindSpeed100m = 'wind_speed_100m';
    case WindDirection10m = 'wind_direction_10m';
    case WindDirection20m = 'wind_direction_20m';
    case WindDirection50m = 'wind_direction_50m';
    case WindDirection80m = 'wind_direction_80m';
    case WindDirection100m = 'wind_direction_100m';
    case WindGusts10m = 'wind_gusts_10m';
    case Visibility = 'visibility';
    case Cape = 'cape';
    case LightningPotential = 'lightning_potential';
    case IsDay = 'is_day';
}
