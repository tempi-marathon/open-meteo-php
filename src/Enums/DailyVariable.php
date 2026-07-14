<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum DailyVariable: string
{
    case WeatherCode = 'weather_code';
    case Temperature2mMax = 'temperature_2m_max';
    case Temperature2mMin = 'temperature_2m_min';
    case ApparentTemperatureMax = 'apparent_temperature_max';
    case ApparentTemperatureMin = 'apparent_temperature_min';
    case Sunrise = 'sunrise';
    case Sunset = 'sunset';
    case DaylightDuration = 'daylight_duration';
    case SunshineDuration = 'sunshine_duration';
    case UvIndexMax = 'uv_index_max';
    case UvIndexClearSkyMax = 'uv_index_clear_sky_max';
    case RainSum = 'rain_sum';
    case ShowersSum = 'showers_sum';
    case SnowfallSum = 'snowfall_sum';
    case PrecipitationSum = 'precipitation_sum';
    case PrecipitationHours = 'precipitation_hours';
    case PrecipitationProbabilityMax = 'precipitation_probability_max';
    case WindSpeed10mMax = 'wind_speed_10m_max';
    case WindGusts10mMax = 'wind_gusts_10m_max';
    case WindDirection10mDominant = 'wind_direction_10m_dominant';
    case ShortwaveRadiationSum = 'shortwave_radiation_sum';
    case Et0FaoEvapotranspiration = 'et0_fao_evapotranspiration';
}
