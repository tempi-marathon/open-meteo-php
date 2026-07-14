<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum ForecastCurrentVariable: string
{
    case Temperature2m = 'temperature_2m';
    case RelativeHumidity2m = 'relative_humidity_2m';
    case ApparentTemperature = 'apparent_temperature';
    case IsDay = 'is_day';
    case Precipitation = 'precipitation';
    case Rain = 'rain';
    case Showers = 'showers';
    case Snowfall = 'snowfall';
    case WeatherCode = 'weather_code';
    case CloudCover = 'cloud_cover';
    case PressureMsl = 'pressure_msl';
    case SurfacePressure = 'surface_pressure';
    case WindSpeed10m = 'wind_speed_10m';
    case WindDirection10m = 'wind_direction_10m';
    case WindGusts10m = 'wind_gusts_10m';
}
