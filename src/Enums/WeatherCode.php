<?php

declare(strict_types=1);

namespace OpenMeteo\Enums;

enum WeatherCode: int
{
    case CLEAR = 0;
    case MAINLY_CLEAR = 1;
    case PARTYLY_CLOUDY = 2;
    case CLOUDY = 3;
    case FOGGY = 45;
    case RIME_FOG = 48;
    case LIGHT_DRIZZLE = 51;
    case DRIZZLE = 53;
    case HEAVY_DRIZZLE = 55;
    case LIGHT_FREEZING_DRIZZLE = 56;
    case FREEZING_DRIZZLE = 57;
    case LIGHT_RAIN = 61;
    case RAIN = 63;
    case HEAVY_RAIN = 65;
    case LIGHT_FREEZING_RAIN = 66;
    case FREEZING_RAIN = 67;
    case LIGHT_SNOW = 71;
    case SNOW = 73;
    case HEAVY_SNOW = 75;
    case SNOW_GRAINS = 77;
    case LIGHT_SHOWERS = 80;
    case SHOWERS = 81;
    case HEAVY_SHOWERS = 82;
    case LIGHT_SNOW_SHOWERS = 85;
    case SNOW_SHOWERS = 86;
    case THUNDERSTORM = 95;
    case LIGHT_THUNDERSTORM_WITH_HAIL = 96;
    case THUNDERSTORM_WITH_HAIL = 99;

    public function description(bool $isDay = true): string
    {
        return $this->label($isDay);
    }

    public function label(bool $isDay = true): string
    {
        return match ($this) {
            WeatherCode::CLEAR => $isDay ? 'Sunny' : 'Clear',
            WeatherCode::MAINLY_CLEAR => $isDay ? 'Mainly sunny' : 'Mainly clear',
            WeatherCode::PARTYLY_CLOUDY => 'Partly cloudy',
            WeatherCode::CLOUDY => 'Cloudy',
            WeatherCode::FOGGY => 'Foggy',
            WeatherCode::RIME_FOG => 'Rime fog',
            WeatherCode::LIGHT_DRIZZLE => 'Light drizzle',
            WeatherCode::DRIZZLE => 'Drizzle',
            WeatherCode::HEAVY_DRIZZLE => 'Heavy drizzle',
            WeatherCode::LIGHT_FREEZING_DRIZZLE => 'Light freezing drizzle',
            WeatherCode::FREEZING_DRIZZLE => 'Freezing drizzle',
            WeatherCode::LIGHT_RAIN => 'Light rain',
            WeatherCode::RAIN => 'Rain',
            WeatherCode::HEAVY_RAIN => 'Heavy rain',
            WeatherCode::LIGHT_FREEZING_RAIN => 'Light freezing rain',
            WeatherCode::FREEZING_RAIN => 'Freezing rain',
            WeatherCode::LIGHT_SNOW => 'Light snow',
            WeatherCode::SNOW => 'Snow',
            WeatherCode::HEAVY_SNOW => 'Heavy snow',
            WeatherCode::SNOW_GRAINS => 'Snow grains',
            WeatherCode::LIGHT_SHOWERS => 'Light showers',
            WeatherCode::SHOWERS => 'Showers',
            WeatherCode::HEAVY_SHOWERS => 'Heavy showers',
            WeatherCode::LIGHT_SNOW_SHOWERS => 'Light snow showers',
            WeatherCode::SNOW_SHOWERS => 'Snow showers',
            WeatherCode::THUNDERSTORM => 'Thunderstorm',
            WeatherCode::LIGHT_THUNDERSTORM_WITH_HAIL => 'Light thunderstorm with hail',
            WeatherCode::THUNDERSTORM_WITH_HAIL => 'Thunderstorm with hail',
        };
    }
}
