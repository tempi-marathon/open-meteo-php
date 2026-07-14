<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MarineHourlyVariable: string
{
    case WaveHeight = 'wave_height';
    case WaveDirection = 'wave_direction';
    case WavePeriod = 'wave_period';
    case WavePeakPeriod = 'wave_peak_period';
    case WindWaveHeight = 'wind_wave_height';
    case WindWaveDirection = 'wind_wave_direction';
    case WindWavePeriod = 'wind_wave_period';
    case WindWavePeakPeriod = 'wind_wave_peak_period';
    case SwellWaveHeight = 'swell_wave_height';
    case SwellWaveDirection = 'swell_wave_direction';
    case SwellWavePeriod = 'swell_wave_period';
    case SwellWavePeakPeriod = 'swell_wave_peak_period';
    case SecondarySwellWaveHeight = 'secondary_swell_wave_height';
    case SecondarySwellWavePeriod = 'secondary_swell_wave_period';
    case SecondarySwellWaveDirection = 'secondary_swell_wave_direction';
    case TertiarySwellWaveHeight = 'tertiary_swell_wave_height';
    case TertiarySwellWavePeriod = 'tertiary_swell_wave_period';
    case TertiarySwellWaveDirection = 'tertiary_swell_wave_direction';
    case SeaLevelHeightMsl = 'sea_level_height_msl';
    case SeaSurfaceTemperature = 'sea_surface_temperature';
    case OceanCurrentVelocity = 'ocean_current_velocity';
    case OceanCurrentDirection = 'ocean_current_direction';
}
